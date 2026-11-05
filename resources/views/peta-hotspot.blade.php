@extends('layouts.app')

@section('title', 'Peta Hotspot — FireAware Kalimantan')

@section('content')
    <div class="map-page bg-soft">

        {{-- Header Peta Geospasial --}}
        <div class="map-header py-4 bg-white border-bottom border-soft shadow-none">
            <div class="container-fluid px-lg-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="eyebrow mb-0">Platform Geospasial Lingkungan</span>
                            <span class="badge bg-mist text-primary border border-soft px-2 py-0-5 small fw-semibold">
                                <i class="bi bi-satellite me-1"></i>Telemetri Satelit Kalimantan
                            </span>
                            <span id="dataStalenessBadge" data-slot="staleness-badge">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 d-inline-flex align-items-center gap-1">
                                    <span class="pulse-indicator"></span>Pembaruan Terjadwal
                                </span>
                            </span>
                        </div>
                        <h1 class="h3 fw-bold mb-1 text-dark">Peta Hotspot Kalimantan</h1>
                        <p class="text-secondary-soft mb-0 small" style="max-width:760px">
                            Pemantauan anomali termal permukaan di 5 provinsi Kalimantan (Kalbar, Kalteng, Kalsel, Kaltim, Kaltara).
                            Titik koordinat terdeteksi oleh instrumen sensor satelit — <strong>bukan bukti pasti kebakaran</strong>.
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="d-none d-md-flex align-items-center gap-2 px-3 py-2 bg-soft rounded-3 border border-soft small">
                            <i class="bi bi-clock-history text-primary"></i>
                            <span class="text-secondary-soft">Pembaruan:</span>
                            <strong class="text-dark" data-slot="updated-at">{{ $lastUpdatedAt?->isoFormat('D MMM Y, HH:mm') ?? '—' }} WITA</strong>
                        </div>

                        <span class="badge-demo">Data DEMO</span>

                        {{-- Tombol Buka Filter Mobile --}}
                        <button class="btn btn-outline-primary d-lg-none d-inline-flex align-items-center gap-1" type="button"
                                data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas"
                                aria-controls="filterOffcanvas">
                            <i class="bi bi-sliders"></i>
                            <span>Filter &amp; Wilayah</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Statistik Spasial Cepat --}}
        <div class="py-2 bg-white border-bottom border-soft map-stats-bar">
            <div class="container-fluid px-lg-4">
                <div class="row g-2 align-items-center text-center text-md-start">
                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center gap-2 justify-content-center justify-content-md-start p-1">
                            <span class="map-stat-bullet bg-primary"></span>
                            <div>
                                <span class="small text-secondary-soft d-block lh-1" style="font-size: .72rem;">Total Titik Terdeteksi</span>
                                <strong class="fs-6 fw-bold text-dark lh-sm" data-stat="total">—</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center gap-2 justify-content-center justify-content-md-start p-1">
                            <span class="map-stat-bullet bg-danger"></span>
                            <div>
                                <span class="small text-secondary-soft d-block lh-1" style="font-size: .72rem;">Kepercayaan Tinggi (≥80%)</span>
                                <strong class="fs-6 fw-bold text-danger lh-sm" data-stat="high">—</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center gap-2 justify-content-center justify-content-md-start p-1">
                            <span class="map-stat-bullet bg-warning"></span>
                            <div>
                                <span class="small text-secondary-soft d-block lh-1" style="font-size: .72rem;">Kepercayaan Sedang (60-79%)</span>
                                <strong class="fs-6 fw-bold text-warning lh-sm" data-stat="mid">—</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="d-flex align-items-center gap-2 justify-content-center justify-content-md-start p-1">
                            <span class="map-stat-bullet bg-success"></span>
                            <div>
                                <span class="small text-secondary-soft d-block lh-1" style="font-size: .72rem;">Kepercayaan Rendah (&lt;60%)</span>
                                <strong class="fs-6 fw-bold text-success lh-sm" data-stat="low">—</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Area Geospasial Utama --}}
        <div class="container-fluid px-lg-4 py-3 map-body">
            <div class="row g-3">

                {{-- Kolom Peta Utama (Area Luas di Desktop: 75% Lebar Layar) --}}
                <div class="col-lg-8 col-xl-9 order-1">
                    <div class="leaflet-frame shadow-sm position-relative">
                        <div id="leafletMap" class="leaflet-map" aria-label="Peta interaktif hotspot"></div>

                        {{-- Floating HUD Controls --}}
                        <div class="map-floating-controls">
                            <button type="button" class="btn btn-light btn-sm shadow-sm map-hud-btn" id="mapRecenterBtn" title="Pusatkan Peta Kalimantan">
                                <i class="bi bi-arrows-fullscreen"></i>
                                <span class="d-none d-sm-inline ms-1">Pusatkan</span>
                            </button>
                        </div>

                        {{-- Overlay Loading --}}
                        <div class="map-overlay map-loading" data-map-state="loading" hidden>
                            <div class="map-state-box text-center">
                                <div class="spinner-border text-primary mb-2" role="status" aria-hidden="true" style="width: 2.2rem; height: 2.2rem;"></div>
                                <div class="fw-bold text-dark">Memuat Data Spasial…</div>
                                <div class="small text-secondary-soft">Menarik telemetri titik panas satelit</div>
                            </div>
                        </div>

                        {{-- Overlay Error --}}
                        <div class="map-overlay map-state" data-map-state="error" hidden>
                            <div class="map-state-box text-center">
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-2 mb-2 d-block"></i>
                                <div class="fw-bold text-dark">Gagal Memuat Data Spasial</div>
                                <p class="small text-secondary-soft mb-3">Terjadi gangguan koneksi data titik panas.</p>
                                <button type="button" class="btn btn-primary btn-sm px-3" id="mapRetryBtn">
                                    <i class="bi bi-arrow-repeat me-1"></i>Coba Lagi
                                </button>
                            </div>
                        </div>

                        {{-- Overlay Empty --}}
                        <div class="map-overlay map-state" data-map-state="empty" hidden>
                            <div class="map-state-box text-center">
                                <i class="bi bi-check-circle-fill text-success fs-2 mb-2 d-block"></i>
                                <div class="fw-bold text-dark">Tidak Ada Titik Panas</div>
                                <p class="small text-secondary-soft mb-3">Tidak ditemukan anomali termal untuk kriteria wilayah dan rentang tanggal yang dipilih.</p>
                                <button type="button" class="btn btn-outline-secondary btn-sm px-3" id="mapEmptyResetBtn">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Disclaimer Legal / Geospasial --}}
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2 px-1 small text-secondary-soft">
                        <div>
                            <i class="bi bi-shield-check text-primary me-1"></i>
                            Peta memadukan telemetri satelit NASA FIRMS &amp; BMKG dengan peta dasar OpenStreetMap.
                        </div>
                        <div>
                            Proyeksi Web Mercator (WGS 84).
                        </div>
                    </div>
                </div>

                {{-- Kolom Samping / Panel Kontrol (Desktop: Kolom Kanan Tanpa Menutupi Peta) --}}
                <div class="col-lg-4 col-xl-3 order-2">
                    <div class="d-none d-lg-block">
                        @include('peta-partials.filter')
                        @include('peta-partials.detail')
                        @include('peta-partials.legend')
                    </div>

                    {{-- Mobile View: Tampilkan Detail di bawah peta --}}
                    <div class="d-lg-none mt-3">
                        @include('peta-partials.detail')
                        @include('peta-partials.legend')
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Offcanvas filter untuk layar kecil --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas"
         aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header border-bottom border-soft">
            <h5 class="offcanvas-title fw-bold text-dark d-flex align-items-center gap-2" id="filterOffcanvasLabel">
                <i class="bi bi-sliders text-primary"></i>
                <span>Filter Parameter Spasial</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            @include('peta-partials.filter')
        </div>
    </div>

    @push('scripts')
        <script>
            window.fireawareMap = {
                endpoint: @json(route('hotspot.map.data')),
                initialFilter: @json($filter),
                provinces: @json($provinces->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'slug' => $p->slug])),
                lastUpdated: @json($lastUpdatedAt?->toIso8601String()),
                sourceLabel: 'Data DEMO internal'
            };
        </script>
        @vite('resources/js/map.js')
    @endpush
@endsection
