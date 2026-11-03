@extends('layouts.app')

@section('title', 'Kualitas Udara — FireAware Kalimantan')

@section('content')
    <div class="aq-page">
        <div class="aq-header">
            <div class="container">
                <p class="eyebrow mb-1">Informasi Lingkungan</p>
                <h1 class="h3 fw-semibold mb-2">Kualitas Udara</h1>
                <p class="text-secondary-soft mb-0 small" style="max-width:680px">
                    Nilai PM2.5 dari stasiun pemantauan yang terdaftar. Semua angka berasal dari
                    sumber data resmi — halaman ini tidak memperkirakan nilai dari jumlah hotspot.
                </p>
            </div>
        </div>

        <div class="container aq-body">
            <div class="row g-4">
                <div class="col-lg-4">
                    {{-- Pemilih lokasi --}}
                    <div class="panel p-3 p-lg-4 mb-4">
                        <h2 class="h6 fw-semibold mb-3">Lokasi pemantauan</h2>

                        <form method="get" action="{{ route('air.quality') }}" id="aqFilterForm">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold" for="aqProvince">Provinsi</label>
                                <select class="form-select" id="aqProvince" name="provinsi">
                                    <option value="">Semua provinsi</option>
                                    @foreach ($provinces as $province)
                                        <option value="{{ $province->slug }}"
                                            @if($activeProvince?->id === $province->id) selected @endif>
                                            {{ $province->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold" for="aqStation">Stasiun</label>
                                <select class="form-select" id="aqStation" name="stasiun"
                                        @if($stations->isEmpty()) disabled @endif>
                                    @forelse ($stations as $station)
                                        <option value="{{ $station->slug }}"
                                            @if($activeStation?->id === $station->id) selected @endif>
                                            {{ $station->name }}
                                        </option>
                                    @empty
                                        <option value="">Belum ada stasiun</option>
                                    @endforelse
                                </select>
                                @if ($stations->isEmpty())
                                    <div class="form-text">Belum ada stasiun terdaftar untuk wilayah ini.</div>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary w-100" @if($stations->isEmpty()) disabled @endif>
                                <i class="bi bi-arrow-repeat me-1"></i>Tampilkan
                            </button>
                        </form>
                    </div>

                    {{-- Tautan sumber --}}
                    <div class="panel p-3 p-lg-4">
                        <h2 class="h6 fw-semibold mb-2">Sumber data</h2>
                        <p class="small text-secondary-soft mb-2">
                            Nilai PM2.5 diambil dari {{ $dataSourceName }} — jaringan stasiun
                            pemantauan udara terbuka.
                        </p>
                        <a class="small fw-semibold" href="{{ $dataSourceUrl }}" target="_blank" rel="noopener noreferrer">
                            Buka {{ $dataSourceName }} <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>

                        @unless ($isConfigured)
                            <div class="alert alert-warning small mt-3 mb-0" role="alert">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Kunci API sumber data belum dikonfigurasi, sehingga
                                pengambilan otomatis belum berjalan.
                            </div>
                        @endunless
                    </div>
                </div>

                <div class="col-lg-8">
                    {{-- <<<MAIN>>> --}}
                    @if ($activeStation && $latest)
                        {{-- Nilai utama --}}
                        <div class="panel p-4 mb-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                                <div>
                                    <h2 class="h5 fw-semibold mb-1">{{ $activeStation->name }}</h2>
                                    <p class="small text-secondary-soft mb-0">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $activeStation->province?->name ?? '—' }}
                                        @if ($activeStation->latitude && $activeStation->longitude)
                                            · {{ number_format($activeStation->latitude, 4) }}, {{ number_format($activeStation->longitude, 4) }}
                                        @endif
                                    </p>
                                </div>
                                <span class="aq-chip {{ $categoryChip }}">{{ $category['label'] }}</span>
                            </div>

                            <div class="aq-primary mt-4">
                                <span class="aq-value">{{ rtrim(rtrim(number_format((float) $latest->value, 1, ',', '.'), '0'), ',') }}</span>
                                <span class="aq-unit">{{ $latest->unit }}</span>
                                <span class="aq-label">PM2.5</span>
                            </div>

                            <p class="small text-secondary-soft mt-2 mb-0">{{ $category['advice'] }}</p>

                            <dl class="aq-meta row gx-3 gy-3 mt-3 mb-0">
                                <div class="col-sm-6">
                                    <dt>Waktu pengukuran</dt>
                                    <dd>{{ $latest->measured_at?->isoFormat('D MMMM Y, HH:mm') ?? '—' }} WITA</dd>
                                </div>
                                <div class="col-sm-6">
                                    <dt>Waktu pengambilan data</dt>
                                    <dd>{{ $latest->fetched_at?->isoFormat('D MMMM Y, HH:mm') ?? '—' }} WITA</dd>
                                </div>
                                <div class="col-sm-6">
                                    <dt>Standar kategori</dt>
                                    <dd>{{ $latest->standard ?? '—' }}</dd>
                                </div>
                                <div class="col-sm-6">
                                    <dt>Sumber</dt>
                                    <dd>{{ ucfirst($latest->source ?? '—') }}</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Grafik tren --}}
                        <div class="panel p-4 mb-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                <h2 class="h6 fw-semibold mb-0">Tren PM2.5 (7 hari)</h2>
                                @if ($history['available'])
                                    <span class="small text-secondary-soft">{{ count($history['points']) }} titik data</span>
                                @endif
                            </div>

                            @if ($history['available'])
                                <div class="aq-chart-wrap">
                                    <canvas id="aqTrendChart" height="120"
                                            aria-label="Grafik tren PM2.5 tujuh hari terakhir"></canvas>
                                </div>
                            @else
                                <div class="aq-empty-inline">
                                    <i class="bi bi-graph-up"></i>
                                    <p class="small mb-0">{{ $history['message'] }}</p>
                                </div>
                            @endif
                        </div>
                    @elseif ($activeStation)
                        {{-- Stasiun ada tapi belum ada pembacaan --}}
                        <div class="aq-empty">
                            <i class="bi bi-cloud-slash"></i>
                            <h2 class="h5 fw-semibold mb-2">Data kualitas udara belum tersedia</h2>
                            <p class="text-secondary-soft mb-3" style="max-width:520px">
                                Stasiun <strong>{{ $activeStation->name }}</strong> terdaftar, tetapi belum ada
                                nilai PM2.5 yang tercatat. Data akan muncul setelah sumber resmi
                                tersinkron.
                            </p>
                            @unless ($isConfigured)
                                <p class="small text-secondary-soft mb-0">
                                    Pengambilan otomatis belum aktif karena kunci API sumber data
                                    belum dikonfigurasi.
                                </p>
                            @endunless
                        </div>
                    @else
                        {{-- Tidak ada stasiun sama sekali --}}
                        <div class="aq-empty">
                            <i class="bi bi-inboxes"></i>
                            <h2 class="h5 fw-semibold mb-2">Belum ada stasiun pemantauan</h2>
                            <p class="text-secondary-soft mb-0" style="max-width:520px">
                                Belum ada stasiun kualitas udara terdaftar
                                {{ $activeProvince ? 'di ' . $activeProvince->name : 'untuk Kalimantan' }}.
                                FireAware tidak menampilkan angka perkiraan.
                            </p>
                        </div>
                    @endif

                    {{-- Penjelasan arti nilai --}}
                    <div class="panel p-4 mb-4">
                        <h2 class="h6 fw-semibold mb-3">Apa arti nilai PM2.5?</h2>
                        <p class="small text-secondary-soft mb-3">
                            PM2.5 adalah partikel udara berukuran sangat kecil (≤ 2,5 mikrometer) yang
                            dapat terhirup masuk ke paru-paru. Nilai dinyatakan dalam mikrogram per
                            meter kubik (µg/m³); semakin tinggi nilainya, semakin banyak partikel di udara.
                        </p>
                        <div class="table-responsive">
                            <table class="table aq-standard-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">PM2.5 (µg/m³)</th>
                                        <th scope="col">Kategori</th>
                                        <th scope="col">Anjuran singkat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>0–50</td><td><span class="aq-chip aq-good">Baik</span></td><td>Aktivitas luar ruang normal.</td></tr>
                                    <tr><td>51–100</td><td><span class="aq-chip aq-moderate">Sedang</span></td><td>Kurangi aktivitas berat berkelanjutan.</td></tr>
                                    <tr><td>101–200</td><td><span class="aq-chip aq-unhealthy">Tidak Sehat</span></td><td>Batasi aktivitas luar; gunakan masker.</td></tr>
                                    <tr><td>201–300</td><td><span class="aq-chip aq-very-unhealthy">Sangat Tidak Sehat</span></td><td>Hindari luar ruang; masker N95.</td></tr>
                                    <tr><td>&gt; 300</td><td><span class="aq-chip aq-hazardous">Berbahaya</span></td><td>Tetap dalam ruangan, tutup ventilasi.</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="small text-secondary-soft mt-2 mb-0">
                            Rentang mengikuti kategori ISPU untuk PM2.5. Kategori yang tersedia dari
                            sumber data selalu diutamakan.
                        </p>
                    </div>

                    {{-- Panduan perlindungan diri --}}
                    <div class="panel p-4">
                        <h2 class="h6 fw-semibold mb-3">Panduan perlindungan diri</h2>
                        <ul class="aq-guide-list list-unstyled mb-0">
                            <li>
                                <i class="bi bi-mask"></i>
                                <div>
                                    <strong class="d-block small">Gunakan masker yang tepat</strong>
                                    <span class="small text-secondary-soft">Masker N95 menyaring partikel halus lebih baik daripada masker kain.</span>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-house-door"></i>
                                <div>
                                    <strong class="d-block small">Tutup ventilasi saat asap pekat</strong>
                                    <span class="small text-secondary-soft">Tutup jendela dan gunakan pembersih udara bila tersedia.</span>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-activity"></i>
                                <div>
                                    <strong class="d-block small">Batasi aktivitas fisik berat</strong>
                                    <span class="small text-secondary-soft">Terutama bagi anak, lansia, ibu hamil, dan penderita gangguan pernapasan.</span>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-droplet-half"></i>
                                <div>
                                    <strong class="d-block small">Jaga hidrasi</strong>
                                    <span class="small text-secondary-soft">Minum air cukup membantu menjaga saluran pernapasan tetap lembap.</span>
                                </div>
                            </li>
                            <li>
                                <i class="bi bi-telephone-outbound"></i>
                                <div>
                                    <strong class="d-block small">Segera cari bantuan bila sesak</strong>
                                    <span class="small text-secondary-soft">Hubungi fasilitas kesehatan bila muncul sesak napas atau iritasi mata berat.</span>
                                </div>
                            </li>
                        </ul>
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

