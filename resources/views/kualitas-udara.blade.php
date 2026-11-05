@extends('layouts.app')

@section('title', 'Kualitas Udara — FireAware Kalimantan')

@section('content')
    <div class="aq-page">
        {{-- 1. Header Banner dengan Judul & Penjelasan Faktual --}}
        <div class="aq-header-banner">
            <div class="container">
                <span class="aq-badge-header mb-2">
                    <i class="bi bi-clouds" aria-hidden="true"></i> Sensor Udara Terpadu
                </span>
                <h1 class="aq-title h2 mb-2">Kualitas Udara Kalimantan</h1>
                <p class="aq-subtitle mb-0">
                    Pantau konsentrasi partikel debu halus (PM2.5) dan status kelayakan udara secara faktual dari stasiun pemantau resmi di Kalimantan. Semua informasi bersumber dari sensor terverifikasi tanpa angka rekaan atau estimasi buatan.
                </p>

                <div class="aq-header-pill-row">
                    <span class="aq-header-pill">
                        <i class="bi bi-geo-alt-fill" aria-hidden="true"></i> Cakupan 5 Provinsi Kalimantan
                    </span>
                    <span class="aq-header-pill">
                        <i class="bi bi-patch-check-fill" aria-hidden="true"></i> Standar Nasional ISPU (KLHK No. 14/2020)
                    </span>
                    <span class="aq-header-pill">
                        <i class="bi bi-shield-check" aria-hidden="true"></i> 100% Data Sensor Terverifikasi
                    </span>
                </div>
            </div>
        </div>

        <div class="container aq-body">
            <div class="row g-4">
                <div class="col-lg-4">
                    {{-- Panel Pemilih Lokasi --}}
                    <div class="aq-sidebar-panel mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-geo-alt text-primary fs-5" aria-hidden="true"></i>
                            <h2 class="h6 fw-bold mb-0 text-dark">Pilih Wilayah Pemantauan</h2>
                        </div>

                        <form method="get" action="{{ route('air.quality') }}" id="aqFilterForm">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold" for="aqProvince">Provinsi</label>
                                <select class="form-select" id="aqProvince" name="provinsi">
                                    <option value="">Seluruh Kalimantan</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->slug }}"
                                            @if($activeProvince?->id === $province->id) selected @endif>
                                            {{ $province->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold" for="aqStation">Stasiun Pemantau</label>
                                <select class="form-select" id="aqStation" name="stasiun"
                                        @if($stations->isEmpty()) disabled @endif>
                                    @forelse ($stations as $station)
                                        <option value="{{ $station->slug }}"
                                            @if($activeStation?->id === $station->id) selected @endif>
                                            {{ $station->name }} ({{ $station->province?->name ?? 'Kalimantan' }})
                                        </option>
                                    @empty
                                        <option value="">Belum ada stasiun terdaftar</option>
                                    @endforelse
                                </select>
                                @if ($stations->isEmpty())
                                    <div class="form-text text-secondary small">Belum ada stasiun aktif di wilayah yang dipilih.</div>
                                @else
                                    <div class="form-text text-secondary small">Tersedia {{ $stations->count() }} stasiun di wilayah ini.</div>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill" @if($stations->isEmpty()) disabled @endif>
                                    <i class="bi bi-search me-1" aria-hidden="true"></i>Tampilkan Data
                                </button>
                                @if ($activeProvince || request('stasiun'))
                                    <a href="{{ route('air.quality') }}" class="btn btn-outline-secondary" title="Reset Pilihan" aria-label="Reset Filter">
                                        <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i>Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    {{-- 8. Konteks Editorial Lingkungan & Lahan Gambut (Foto Pendukung) --}}
                    <div class="aq-editorial-card">
                        <img src="{{ asset('images/smoke-haze.jpg') }}"
                             alt="Kabut asap karhutla menyelimuti vegetasi lahan gambut di Kalimantan"
                             class="aq-editorial-img"
                             loading="lazy"
                             width="640"
                             height="360">
                        <h3>Dinamika Partikel PM2.5 di Kalimantan</h3>
                        <p>
                            Kebakaran di lahan gambut sering terjadi secara perlahan di bawah permukaan tanah (smoldering). Proses ini melepaskan partikel debu mikro PM2.5 berkonsentrasi tinggi yang bertahan lama di atmosfer dan mudah terbawa angin melintasi batas kota.
                        </p>
                    </div>

                    {{-- Edukasi Ukuran Partikel PM2.5 Bagi Masyarakat Umum --}}
                    <div class="aq-infographic-card">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-info-circle text-primary fs-5" aria-hidden="true"></i>
                            <h3 class="mb-0">Memahami Ukuran Partikel PM2.5</h3>
                        </div>
                        <div class="aq-particle-scale">
                            <div class="aq-particle-row">
                                <span class="aq-particle-dot bg-secondary"></span>
                                <span>Pasir Pantai Halus: <strong>~90 µm</strong></span>
                            </div>
                            <div class="aq-particle-row">
                                <span class="aq-particle-dot bg-warning"></span>
                                <span>Sehelai Rambut Manusia: <strong>~50–70 µm</strong></span>
                            </div>
                            <div class="aq-particle-row">
                                <span class="aq-particle-dot bg-danger"></span>
                                <span>Partikel Polutan PM2.5: <strong>2,5 µm</strong></span>
                            </div>
                        </div>
                        <p class="mb-0">
                            Karena ukurannya yang 30x lebih kecil dari rambut, partikel PM2.5 tidak dapat disaring bulu hidung dan mampu menembus alveolus paru-paru hingga ke aliran darah.
                        </p>
                    </div>

                    {{-- Informasi Sumber Data --}}
                    <div class="aq-sidebar-panel mb-4">
                        <h2 class="h6 fw-bold mb-2 text-dark">Transparansi Sumber Data</h2>
                        <p class="small text-secondary mb-3">
                            Pengukuran PM2.5 diambil dari jaringan sensor terbuka <strong>{{ $dataSourceName }}</strong> dan stasiun ISPU Kementerian Lingkungan Hidup RI.
                        </p>
                        <a class="small fw-semibold text-primary d-inline-flex align-items-center" href="{{ $dataSourceUrl }}" target="_blank" rel="noopener noreferrer">
                            Portal Data Terbuka {{ $dataSourceName }} <i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i>
                        </a>

                        @unless ($isConfigured)
                            <div class="alert alert-warning small mt-3 mb-0" role="alert">
                                <i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i>
                                Kunci API integrasi belum diaktifkan di server. Sinkronisasi data otomatis terjadwal sementara dinonaktifkan.
                            </div>
                        @endunless
                    </div>
                </div>

                <div class="col-lg-8">
                    @php
                        $activeCat = isset($category) && $category ? collect($allCategories)->firstWhere('label', $category['label']) : null;
                    @endphp

                    @if ($activeStation && $latest)
                        {{-- 2 & 3. Indikator Kualitas Udara Faktual Utama --}}
                        <div class="aq-main-card mb-4 aq-status-{{ $activeCat['key'] ?? 'good' }}">
                            <div class="aq-station-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-light text-primary border border-soft px-2 py-1 small fw-semibold">
                                            {{ $activeStation->province?->name ?? 'Kalimantan' }}
                                        </span>
                                        @if ($activeStation->latitude && $activeStation->longitude)
                                            <span class="small text-secondary font-monospace">
                                                <i class="bi bi-geo me-1" aria-hidden="true"></i>{{ number_format((float) $activeStation->latitude, 3) }}, {{ number_format((float) $activeStation->longitude, 3) }}
                                            </span>
                                        @endif
                                    </div>
                                    <h2 class="h4 fw-bold mb-0 text-dark">{{ $activeStation->name }}</h2>
                                </div>

                                <span class="aq-live-pill" title="Data pembacaan langsung dari sensor stasiun resmi">
                                    <i class="bi bi-check-circle-fill text-success" aria-hidden="true"></i> Sensor Terverifikasi
                                </span>
                            </div>

                            <div class="aq-indicator-hero">
                                <div class="aq-value-group">
                                    <span class="aq-value">{{ rtrim(rtrim(number_format((float) $latest->value, 1, ',', '.'), '0'), ',') }}</span>
                                    <div class="aq-unit-block">
                                        <span class="aq-unit">{{ $latest->unit ?? 'µg/m³' }}</span>
                                        <span class="aq-pollutant-tag">PM2.5</span>
                                    </div>
                                </div>

                                <div class="d-flex flex-column align-items-lg-end gap-1">
                                    <span class="aq-chip {{ $categoryChip ?? 'aq-good' }}">
                                        <i class="bi {{ $activeCat['icon'] ?? 'bi-shield-check' }}" aria-hidden="true"></i>
                                        {{ $category['label'] ?? 'Tidak Diketahui' }}
                                    </span>
                                    <span class="small text-secondary fw-semibold">
                                        {{ $category['risk'] ?? 'Status Kualitas Udara' }}
                                    </span>
                                </div>
                            </div>

                            {{-- 3. Visualisasi Spektrum Tingkat Pencemaran ISPU --}}
                            <div class="aq-spectrum-wrap">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-bold text-dark">Spektrum Tingkat Pencemaran ISPU</span>
                                    <span class="small text-secondary font-monospace">Nilai Sekarang: {{ rtrim(rtrim(number_format((float) $latest->value, 1, ',', '.'), '0'), ',') }} µg/m³</span>
                                </div>

                                <div class="aq-spectrum-bar" role="img" aria-label="Skala spektrum ISPU 5 tingkat polusi PM2.5">
                                    @foreach ($allCategories as $cat)
                                        <div class="aq-spectrum-segment seg-{{ $cat['key'] }} {{ ($activeCat && $activeCat['key'] === $cat['key']) ? 'is-active' : '' }}"
                                             title="{{ $cat['label'] }} ({{ $cat['range'] }} µg/m³)"></div>
                                    @endforeach
                                </div>

                                <div class="aq-spectrum-labels">
                                    <span>0 Baik</span>
                                    <span>50 Sedang</span>
                                    <span>100 Tdk Sehat</span>
                                    <span>200 Sangat Tdk Sehat</span>
                                    <span>300+ Berbahaya</span>
                                </div>
                            </div>

                            {{-- 7. Kotak Anjuran Cepat untuk Masyarakat Berdasarkan Kategori --}}
                            @if (!empty($category['advice']))
                                <div class="aq-advisory-box">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="bi bi-shield-exclamation text-primary fs-5 mt-n0.5" aria-hidden="true"></i>
                                        <div>
                                            <strong class="d-block text-dark small mb-0.5">Anjuran untuk Masyarakat:</strong>
                                            <p class="mb-0">{{ $category['advice'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- 5. Informasi Wilayah & Waktu Pembaruan --}}
                            <dl class="aq-meta-list">
                                <div>
                                    <dt><i class="bi bi-clock" aria-hidden="true"></i>Waktu Pengukuran Sensor</dt>
                                    <dd>
                                        <time datetime="{{ $latest->measured_at?->toIso8601String() }}">
                                            {{ $latest->measured_at?->isoFormat('D MMMM Y, HH:mm') ?? '—' }} WITA
                                        </time>
                                        @if ($latest->measured_at)
                                            <span class="text-secondary fw-normal d-block small">({{ $latest->measured_at->diffForHumans() }})</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt><i class="bi bi-arrow-repeat" aria-hidden="true"></i>Waktu Sinkronisasi Sistem</dt>
                                    <dd>{{ $latest->fetched_at?->isoFormat('D MMMM Y, HH:mm') ?? '—' }} WITA</dd>
                                </div>
                                <div>
                                    <dt><i class="bi bi-shield-check" aria-hidden="true"></i>Baku Mutu Acuan</dt>
                                    <dd>{{ $latest->standard ?? 'ISPU KLHK 2020' }}</dd>
                                </div>
                                <div>
                                    <dt><i class="bi bi-hdd-network" aria-hidden="true"></i>Jaringan Penyedia</dt>
                                    <dd class="text-capitalize">{{ $latest->source ?? $dataSourceName }}</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- 4. Visualisasi Grafik Historis --}}
                        <div class="aq-chart-card">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                <div>
                                    <h2 class="h6 fw-bold mb-0 text-dark">Tren PM2.5 (7 Hari Terakhir)</h2>
                                    <span class="text-secondary small">Pantauan fluktuasi partikel harian dari stasiun ini</span>
                                </div>

                                @if ($history['available'] && $historyStats)
                                    <div class="aq-stats-pill-group">
                                        <span class="aq-stat-pill">Terendah: <strong>{{ $historyStats['min'] }}</strong> µg/m³</span>
                                        <span class="aq-stat-pill">Rata-rata: <strong>{{ $historyStats['avg'] }}</strong> µg/m³</span>
                                        <span class="aq-stat-pill">Tertinggi: <strong>{{ $historyStats['max'] }}</strong> µg/m³</span>
                                        <span class="aq-stat-pill"><i class="bi bi-clock-history me-1" aria-hidden="true"></i><strong>{{ $historyStats['count'] }}</strong> data</span>
                                    </div>
                                @endif
                            </div>

                            @if ($history['available'])
                                <div class="aq-chart-wrap">
                                    <canvas id="aqTrendChart" aria-label="Grafik garis konsentrasi PM2.5 tujuh hari terakhir"></canvas>
                                </div>
                            @else
                                <div class="aq-empty-inline">
                                    <i class="bi bi-graph-up" aria-hidden="true"></i>
                                    <div>
                                        <strong class="d-block text-dark">Grafik tren belum dapat ditampilkan</strong>
                                        <p class="mb-0">{{ $history['message'] ?? 'Dibutuhkan minimal 3 rekaman data berkala dari stasiun untuk memetakan tren.' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                    @elseif ($activeStation)
                        {{-- 9. Tampilan Ramah & Menarik Ketika Stasiun Terdaftar Namun Belum Ada Data --}}
                        <div class="aq-empty">
                            <div class="aq-empty-icon">
                                <i class="bi bi-cloud-slash" aria-hidden="true"></i>
                            </div>
                            <h2>Data Pembacaan Belum Tersedia</h2>
                            <p style="max-width: 540px;">
                                Stasiun <strong>{{ $activeStation->name }}</strong> terdaftar dalam basis data pemantauan FireAware, namun pembacaan telemetri terkini belum diterima dari sumber resmi. Data akan tampil otomatis saat sinkronisasi berikutnya berhasil.
                            </p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center mb-3">
                                <a href="#ispuGuideSection" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-book me-1" aria-hidden="true"></i>Pelajari Standar ISPU
                                </a>
                                <a href="#actionGuideSection" class="btn btn-primary btn-sm">
                                    <i class="bi bi-shield-check me-1" aria-hidden="true"></i>Panduan Perlindungan Diri
                                </a>
                            </div>
                            @unless ($isConfigured)
                                <div class="alert alert-info small py-2 px-3 mb-0 text-start" role="alert">
                                    <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                                    Kunci API sumber data eksternal belum disetel pada server. Halaman ini tetap menyajikan standar kualitas dan edukasi pencegahan yang lengkap.
                                </div>
                            @endunless
                        </div>

                    @else
                        {{-- 9. Tampilan Ramah & Menarik Ketika Belum Ada Stasiun Sama Sekali --}}
                        <div class="aq-empty">
                            <div class="aq-empty-icon">
                                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                            </div>
                            <h2>Stasiun Pemantau Belum Tersedia</h2>
                            <p style="max-width: 540px;">
                                Belum ada stasiun pemantau kualitas udara yang aktif tercatat {{ $activeProvince ? 'di ' . $activeProvince->name : 'di wilayah ini' }}. FireAware Kalimantan berkomitmen hanya menyajikan data faktual dari sensor resmi, tanpa rekaan perkiraan.
                            </p>
                            @if ($activeProvince)
                                <a href="{{ route('air.quality') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Tampilkan Seluruh Kalimantan
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- 6. Penjelasan Kategori Kualitas Udara Bahasa Sederhana --}}
                    <div id="ispuGuideSection">
                        @include('partials.air-quality-ispu-guide')
                    </div>

                    {{-- 7. Panduan Singkat Tindakan Masyarakat --}}
                    <div id="actionGuideSection">
                        @include('partials.air-quality-action-guide')
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($history['available'])
        @push('scripts')
            <script id="aq-history-data" type="application/json">@json($history['points'])</script>
            @vite('resources/js/air-quality.js')
        @endpush
    @endif
@endsection

