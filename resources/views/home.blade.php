@extends('layouts.app')

@section('title', 'Beranda — FireAware Kalimantan')

@section('content')

    @php
        $hasAnyHotspot = $summary['total_7d'] > 0;
        $lastUpdated = $summary['last_detected_at'];
    @endphp


    {{-- ===================== HERO ===================== --}}
    <section class="hero hero-environmental" style="background-image: linear-gradient(135deg, rgba(23, 57, 45, 0.94) 0%, rgba(33, 77, 58, 0.88) 60%, rgba(23, 57, 45, 0.96) 100%), url('{{ asset('images/hero-forest.jpg') }}');">
        <div class="container position-relative">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="hero-eyebrow-badge">
                        <i class="bi bi-shield-check"></i>
                        <span>Sistem Informasi Karhutla Kalimantan</span>
                    </div>

                    <h1 class="hero-title mb-3">
                        Pantau titik panas dan risiko kebakaran hutan di lima provinsi Kalimantan.
                    </h1>

                    <p class="hero-lead mb-4 pe-lg-4">
                        FireAware merangkum data titik panas, kualitas udara, dan pengumuman resmi
                        menjadi informasi yang cepat dipahami masyarakat.
                    </p>

                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <a class="btn btn-emerald btn-lg px-4 shadow-sm" href="{{ route('hotspot.map') }}">
                            <i class="bi bi-map-fill me-2"></i>Buka Peta Hotspot
                        </a>
                        <a class="btn btn-outline-light btn-lg px-3" href="#peta">
                            Lihat Data Terbaru
                        </a>
                    </div>

                    <div class="hero-meta-bar d-flex align-items-center gap-2">
                        <span class="pulse-indicator" aria-hidden="true"></span>
                        <p class="small text-white-50 mb-0">
                            Pembaruan terakhir:
                            <span data-js="updated-at" class="text-white fw-semibold">{{ $lastUpdated?->isoFormat('D MMMM Y, HH:mm') ?? '—' }}</span> WITA
                        </p>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="hero-telemetry-card">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 pb-2 border-bottom border-soft">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-mist text-primary border border-soft p-1-5 rounded-2">
                                    <i class="bi bi-broadcast fs-6"></i>
                                </span>
                                <div>
                                    <h2 class="h6 fw-bold mb-0 text-dark">Ringkasan titik panas</h2>
                                    <span class="small text-secondary-soft">Telemetri Satelit NASA &amp; BMKG</span>
                                </div>
                            </div>
                            <span class="badge-demo">DEMO</span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary-soft mb-1" for="provinceSelect">Pilih wilayah</label>
                            <select id="provinceSelect" class="form-select" data-js="province-select" aria-label="Pilih provinsi">
                                <option value="">Seluruh Kalimantan</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->slug }}"
                                        @if($activeProvince?->id === $province->id) selected @endif>
                                        {{ $province->name }} ({{ $province->hotspots_7d_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <dl class="row summary-list mb-0 g-3" data-js="summary-panel">
                            <div class="col-6">
                                <div class="summary-metric-card p-3 rounded-3 bg-soft border border-soft h-100">
                                    <dt class="small text-secondary-soft fw-medium mb-1">Total 7 hari</dt>
                                    <dd class="fs-3 fw-bold mb-0 text-dark" data-js="stat-total">{{ $summary['total_7d'] }}</dd>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="summary-metric-card p-3 rounded-3 bg-soft border border-soft h-100">
                                    <dt class="small text-secondary-soft fw-medium mb-1">24 jam terakhir</dt>
                                    <dd class="fs-3 fw-bold mb-0 text-primary" data-js="stat-24h">{{ $summary['last_24h'] }}</dd>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="summary-metric-card p-3 rounded-3 bg-soft border border-soft h-100">
                                    <dt class="small text-secondary-soft fw-medium mb-1">Kepercayaan tinggi (≥80%)</dt>
                                    <dd class="fs-3 fw-bold mb-0 text-danger" data-js="stat-confidence">{{ $summary['high_confidence'] }}</dd>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="summary-metric-card p-3 rounded-3 bg-soft border border-soft h-100">
                                    <dt class="small text-secondary-soft fw-medium mb-1">Provinsi aktif</dt>
                                    <dd class="fs-6 fw-semibold mb-0 text-dark" data-js="stat-province">{{ $activeProvince?->name ?? 'Seluruh Kalimantan' }}</dd>
                                </div>
                            </div>
                        </dl>

                        <div class="alert alert-warning mt-3 mb-0 py-2 px-3 small" data-js="summary-error" hidden>
                            <i class="bi bi-wifi-off me-1"></i> Gagal memuat ringkasan. Coba muat ulang halaman.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== 4 PILAR INFORMASI LINGKUNGAN ===================== --}}
    <section class="section-block bg-white border-bottom border-soft py-4 py-lg-5">
        <div class="container">
            <div class="row g-3 g-lg-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="feature-pillar-card">
                        <div class="pillar-icon-wrap">
                            <i class="bi bi-map"></i>
                        </div>
                        <h3 class="pillar-title">Peta Interaktif Hotspot</h3>
                        <p class="pillar-desc">
                            Visualisasi spasial titik panas terdeteksi satelit di 5 provinsi Kalimantan dengan filter kepercayaan dan rentang waktu.
                        </p>
                        <a href="{{ route('hotspot.map') }}" class="pillar-link">
                            Buka Peta Satelit <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="feature-pillar-card">
                        <div class="pillar-icon-wrap">
                            <i class="bi bi-wind"></i>
                        </div>
                        <h3 class="pillar-title">Kualitas Udara ISPU</h3>
                        <p class="pillar-desc">
                            Pantau konsentrasi partikel PM2.5 dari stasiun sensor resmi dan rekomendasi proteksi pernapasan kelompok rentan.
                        </p>
                        <a href="{{ route('air.quality') }}" class="pillar-link">
                            Cek Indeks Udara <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="feature-pillar-card">
                        <div class="pillar-icon-wrap">
                            <i class="bi bi-megaphone"></i>
                        </div>
                        <h3 class="pillar-title">Warta &amp; Peringatan Dini</h3>
                        <p class="pillar-desc">
                            Rilis resmi status kedaruratan, peringatan cuaca kemarau ekstrem, dan imbauan dari BMKG serta BPBD Kalimantan.
                        </p>
                        <a href="{{ route('announcements.index') }}" class="pillar-link">
                            Lihat Pengumuman <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="feature-pillar-card">
                        <div class="pillar-icon-wrap">
                            <i class="bi bi-mortarboard"></i>
                        </div>
                        <h3 class="pillar-title">Edukasi Ekosistem Gambut</h3>
                        <p class="pillar-desc">
                            Pelajari anatomi kebakaran bawah tanah (*ground fire*), restorasi hidrologis 3R, dan panduan mitigasi kabut asap.
                        </p>
                        <a href="{{ route('edukasi.index') }}" class="pillar-link">
                            Pelajari Literasi <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== PETA / PERSEBARAN ===================== --}}
    <section class="section-block" id="peta">
        <div class="container">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
                <div>
                    <span class="eyebrow d-block mb-1">Telemetri Spasial</span>
                    <h2 class="h3 fw-bold mb-1 text-dark">Persebaran titik panas</h2>
                    <p class="text-secondary-soft mb-0 small">
                        Sebaran titik panas 7 hari terakhir{{ $activeProvince ? ' — ' . $activeProvince->name : '' }} di wilayah Kalimantan.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-demo">Data DEMO</span>
                    <a href="{{ route('hotspot.map') }}" class="btn btn-outline-primary btn-sm px-3">
                        <i class="bi bi-sliders me-1"></i>Peta Interaktif Lengkap
                    </a>
                </div>
            </div>

            <div class="map-frame">
                <div id="hotspotMap" class="hotspot-map" role="img"
                     aria-label="Peta persebaran titik panas Kalimantan"></div>
                <div class="map-empty text-center" data-js="map-empty" @if($hasAnyHotspot) hidden @endif>
                    <i class="bi bi-map mb-2"></i>
                    <p class="mb-0 small">Belum ada titik panas untuk ditampilkan.</p>
                </div>
            </div>
            <script id="map-hotspots-data" type="application/json">{{ json_encode($mapHotspots->map(fn ($h) => [
                'lat' => (float) $h->latitude,
                'lng' => (float) $h->longitude,
                'confidence' => $h->confidence,
                'province' => $h->province?->name,
                'detected_at' => $h->detected_at?->toIso8601String(),
            ])) }}</script>
        </div>
    </section>

    {{-- ===================== HOTSPOT TERBARU ===================== --}}
    <section class="section-block bg-white-bordered" id="hotspot-terbaru">
        <div class="container">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
                <div>
                    <span class="eyebrow d-block mb-1">Tabel Deteksi</span>
                    <h2 class="h4 fw-bold mb-1 text-dark">Titik panas terbaru</h2>
                    <p class="text-secondary-soft mb-0 small">Delapan deteksi terakhir dari telemetri satelit Kalimantan.</p>
                </div>
                <span class="badge-demo">DEMO</span>
            </div>

            @if ($hotspots->isNotEmpty())
                <div class="table-responsive rounded-3 border border-soft shadow-sm">
                    <table class="table hotspot-table align-middle mb-0 table-hover">
                        <thead class="bg-soft">
                            <tr>
                                <th scope="col" class="ps-3 py-3">Waktu deteksi</th>
                                <th scope="col" class="py-3">Provinsi</th>
                                <th scope="col" class="py-3">Koordinat</th>
                                <th scope="col" class="text-end pe-3 py-3">Kepercayaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hotspots as $hotspot)
                                <tr>
                                    <td class="ps-3 fw-medium text-dark">
                                        <i class="bi bi-clock me-1 text-secondary-soft"></i>
                                        {{ $hotspot->detected_at?->isoFormat('D MMM, HH:mm') ?? '—' }}
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $hotspot->province?->name ?? '—' }}</span>
                                    </td>
                                    <td class="font-monospace small text-secondary-soft">
                                        {{ number_format($hotspot->latitude, 4) }}, {{ number_format($hotspot->longitude, 4) }}
                                    </td>
                                    <td class="text-end pe-3">
                                        <span class="conf-chip conf-{{ $hotspot->confidence >= 80 ? 'high' : ($hotspot->confidence >= 60 ? 'mid' : 'low') }}">
                                            {{ $hotspot->confidence ?? '—' }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state text-center py-5">
                    <i class="bi bi-fire mb-2 fs-1 text-secondary-soft"></i>
                    <p class="mb-0 fw-semibold">Belum ada data titik panas {{ $activeProvince ? 'untuk ' . $activeProvince->name : '' }}.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ===================== KUALITAS UDARA + PENGUMUMAN ===================== --}}
    <section class="section-block" id="informasi">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="eyebrow d-block mb-1">Sensor Lingkungan</span>
                            <h2 class="h4 fw-bold mb-0 text-dark">Kualitas udara</h2>
                        </div>
                        <a href="{{ route('air.quality') }}" class="small fw-semibold text-primary text-decoration-none">
                            Lihat Stasiun <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    @if ($latestAirQuality)
                        <div class="panel p-4 h-100 d-flex flex-column justify-content-between shadow-sm">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h3 class="h6 fw-bold text-dark mb-0">{{ $latestAirQuality->station?->name ?? 'Stasiun Pemantau' }}</h3>
                                        <span class="small text-secondary-soft">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $latestAirQuality->station?->province?->name ?? 'Kalimantan' }}
                                        </span>
                                    </div>
                                    <span class="badge bg-mist text-primary border border-soft px-2 py-1 small fw-bold">
                                        {{ $latestAirQuality->aqi_category ?? 'ISPU' }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-baseline gap-2 my-3 p-3 bg-soft rounded-3 border border-soft">
                                    <span class="display-6 fw-bold text-dark lh-1">{{ number_format($latestAirQuality->value, 1) }}</span>
                                    <span class="text-secondary-soft small fw-medium">{{ $latestAirQuality->unit ?? 'µg/m³' }} (PM2.5)</span>
                                </div>
                                <div class="small text-secondary-soft mb-3">
                                    <i class="bi bi-clock me-1"></i> Diukur {{ $latestAirQuality->measured_at?->diffForHumans() ?? 'Baru saja' }}
                                </div>
                            </div>
                            <a href="{{ route('air.quality') }}" class="btn btn-outline-primary btn-sm w-100 py-2 fw-semibold">
                                Buka Grafik Tren &amp; Rekomendasi
                            </a>
                        </div>
                    @else
                        <div class="empty-state py-5 px-3 text-center h-100 d-flex flex-column justify-content-center">
                            <i class="bi bi-wind mb-2 fs-2 text-secondary-soft"></i>
                            <p class="mb-1 fw-semibold text-dark">Data kualitas udara belum tersedia</p>
                            <p class="small text-secondary-soft mb-0">
                                Sumber data AQI (mis. BMKG atau OpenAQ) belum terhubung.
                                Bagian ini akan aktif ketika sumber data tersedia.
                            </p>
                        </div>
                    @endif
                </div>

                <div class="col-lg-7" id="pengumuman">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <span class="eyebrow d-block mb-1">Rilis Resmi</span>
                            <h2 class="h4 fw-bold mb-0 text-dark">Kewaspadaan &amp; pengumuman</h2>
                        </div>
                        <a href="{{ route('announcements.index') }}" class="small fw-semibold text-primary text-decoration-none">
                            Lihat Semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                    @if ($announcements->isNotEmpty())
                        <div class="d-grid gap-3">
                            @foreach ($announcements as $item)
                                <article class="announcement-item shadow-sm">
                                    <div class="d-flex gap-3 align-items-start">
                                        <span class="level-dot level-{{ $item->level }}" aria-hidden="true"></span>
                                        <div class="flex-grow-1">
                                            <h3 class="h6 fw-bold mb-1">
                                                <a href="{{ route('announcements.show', $item->slug) }}" class="text-dark text-decoration-none">
                                                    {{ $item->title }}
                                                </a>
                                                @if ($item->is_demo)
                                                    <span class="badge-demo py-0 px-2 ms-1">DEMO</span>
                                                @endif
                                            </h3>
                                            <p class="small text-secondary-soft mb-2 lh-base">{{ $item->excerpt(140) }}</p>
                                            <div class="d-flex align-items-center gap-2 small text-secondary-soft flex-wrap">
                                                <span>{{ $item->effectiveDate()->isoFormat('D MMMM Y') }}</span>
                                                <span>&bull;</span>
                                                <span class="fw-semibold text-dark">{{ $item->levelLabel() }}</span>
                                                @if ($item->source_name)
                                                    <span>&bull;</span>
                                                    <span>{{ $item->source_name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state py-5 px-3 text-center">
                            <i class="bi bi-megaphone mb-2 fs-2 text-secondary-soft"></i>
                            <p class="mb-0 small fw-semibold text-secondary-soft">Belum ada pengumuman yang diterbitkan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== PANDUAN KESELAMATAN ===================== --}}
    <section class="section-block bg-white-bordered" id="panduan">
        <div class="container">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
                <div>
                    <span class="eyebrow d-block mb-1">Mitigasi &amp; Edukasi</span>
                    <h2 class="h4 fw-bold mb-1 text-dark">Panduan keselamatan pilihan</h2>
                    <p class="text-secondary-soft small mb-0">Edukasi mitigasi asap dan kewaspadaan kebakaran hutan berbasis sumber terpercaya.</p>
                </div>
                <div>
                    <a href="{{ route('guides.index') }}" class="btn btn-outline-primary btn-sm px-3">
                        Lihat Semua Panduan <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            @if ($publishedGuides->isNotEmpty())
                <div class="row g-3">
                    @foreach ($publishedGuides as $guide)
                        <div class="col-sm-6 col-lg-3">
                            <article class="guide-item h-100 d-flex flex-column justify-content-between shadow-sm">
                                <div>
                                    <span class="guide-icon"><i class="bi {{ $guide->categoryIcon() }}"></i></span>
                                    <span class="small fw-bold text-primary d-block mb-1">{{ $guide->categoryLabel() }}</span>
                                    <h3 class="h6 fw-bold mb-2">
                                        <a href="{{ route('guides.show', $guide->slug) }}" class="text-dark text-decoration-none">
                                            {{ $guide->title }}
                                        </a>
                                    </h3>
                                    <p class="small text-secondary-soft mb-3 line-clamp-3 lh-base">{{ Str::limit($guide->summary, 120) }}</p>
                                </div>
                                <div class="pt-2 border-top border-soft d-flex align-items-center justify-content-between">
                                    <span class="small text-secondary-soft" style="font-size: .75rem;">
                                        <i class="bi bi-clock me-1"></i>{{ $guide->reading_time_minutes }} mnt
                                    </span>
                                    <a href="{{ route('guides.show', $guide->slug) }}" class="small fw-semibold text-primary text-decoration-none">
                                        Baca <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @elseif ($safetyGuides->isNotEmpty())
                <div class="row g-3">
                    @foreach ($safetyGuides as $guide)
                        <div class="col-sm-6 col-lg-3">
                            <article class="guide-item h-100 shadow-sm">
                                <span class="guide-icon"><i class="bi {{ $guide->icon ?? 'bi-book' }}"></i></span>
                                <h3 class="h6 fw-bold mb-1">{{ $guide->title }}</h3>
                                <p class="small text-secondary-soft mb-2">{{ $guide->summary }}</p>
                                <span class="small fw-bold text-primary">{{ $guide->audienceLabel() }}</span>
                            </article>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state py-5 px-3 text-center">
                    <i class="bi bi-journal-text mb-2 fs-2 text-secondary-soft"></i>
                    <p class="mb-0 small fw-semibold text-secondary-soft">Panduan keselamatan belum dipublikasikan.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
