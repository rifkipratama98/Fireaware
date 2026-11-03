@extends('layouts.app')

@section('title', 'Peta Hotspot — FireAware Kalimantan')

@section('content')
    <div class="map-page">
        <div class="map-header">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <p class="eyebrow mb-1">Peta Interaktif</p>
                        <h1 class="h3 fw-semibold mb-1">Peta Hotspot Kalimantan</h1>
                        <p class="text-secondary-soft mb-0 small" style="max-width:640px">
                            Sebaran titik panas terdeteksi (hotspot) di lima provinsi Kalimantan.
                            Titik ini menandai lokasi yang dideteksi sensor satelit — <strong>bukan bukti pasti kebakaran</strong>.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge-demo">Data DEMO</span>
                        <button class="btn btn-outline-primary d-lg-none" type="button"
                                data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas"
                                aria-controls="filterOffcanvas">
                            <i class="bi bi-sliders me-1"></i> Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container map-body">
            <div class="row g-3">
                {{-- Filter — kolom kiri (desktop) / offcanvas (mobile) --}}
                <div class="col-lg-3 d-none d-lg-block">
                    @include('peta-partials.filter')
                </div>

                {{-- Peta utama --}}
                <div class="col-lg-6">
                    <div class="leaflet-frame">
                        <div id="leafletMap" class="leaflet-map" aria-label="Peta interaktif hotspot"></div>

                        <div class="map-overlay map-loading" data-map-state="loading" hidden>
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status" aria-hidden="true"></div>
                            Memuat titik panas…
                        </div>

                        <div class="map-overlay map-state" data-map-state="error" hidden>
                            <i class="bi bi-exclamation-triangle me-2"></i> Gagal memuat data. Coba lagi.
                        </div>

                        <div class="map-overlay map-state" data-map-state="empty" hidden>
                            <i class="bi bi-inboxes me-2"></i> Tidak ada titik panas pada filter ini.
                        </div>
                    </div>

                    {{-- Detail hotspot (mobile tampil di bawah peta) --}}
                    <div class="detail-panel d-lg-none mt-3">
                        @include('peta-partials.detail')
                    </div>
                </div>

                {{-- Panel detail + legenda (desktop) --}}
                <div class="col-lg-3 d-none d-lg-block">
                    @include('peta-partials.detail')
                    @include('peta-partials.legend')
                </div>
            </div>
        </div>
    </div>

    {{-- Offcanvas filter untuk layar kecil --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filterOffcanvas"
         aria-labelledby="filterOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="filterOffcanvasLabel">Filter Peta</h5>
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
