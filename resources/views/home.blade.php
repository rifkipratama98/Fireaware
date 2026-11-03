@extends('layouts.app')

@section('title', 'Beranda — FireAware Kalimantan')

@section('content')

    @php
        $hasAnyHotspot = $summary['total_7d'] > 0;
        $lastUpdated = $summary['last_detected_at'];
    @endphp


    {{-- ===================== HERO ===================== --}}
    <section class="hero hero-home">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <p class="eyebrow mb-2">Sistem Informasi Karhutla Kalimantan</p>
                    <h1 class="hero-title mb-3">Pantau titik panas dan risiko kebakaran hutan di lima provinsi Kalimantan.</h1>
                    <p class="text-secondary-soft mb-4 pe-lg-4">
                        FireAware merangkum data titik panas, kualitas udara, dan pengumuman resmi
                        menjadi informasi yang cepat dipahami masyarakat.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <a class="btn btn-primary btn-lg px-4" href="{{ route('home') }}#peta">Lihat Peta Hotspot</a>
                    </div>
                    <p class="small text-secondary-soft mb-0">
                        <i class="bi bi-clock-history me-1"></i>
                        Pembaruan terakhir:
                        <span data-js="updated-at">{{ $lastUpdated?->isoFormat('D MMMM Y, HH:mm') ?? '—' }}</span> WITA
                    </p>
                </div>

                <div class="col-lg-6">
                    <div class="panel p-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                            <h2 class="h6 fw-semibold mb-0">Ringkasan titik panas</h2>
                            <span class="badge-demo">DEMO</span>
                        </div>

                        <label class="form-label small fw-semibold mb-1" for="provinceSelect">Pilih wilayah</label>
                        <select id="provinceSelect" class="form-select mb-3" data-js="province-select" aria-label="Pilih provinsi">
                            <option value="">Seluruh Kalimantan</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province->slug }}"
                                    @if($activeProvince?->id === $province->id) selected @endif>
                                    {{ $province->name }} ({{ $province->hotspots_7d_count }})
                                </option>
                            @endforeach
                        </select>

                        <dl class="row summary-list mb-0" data-js="summary-panel">
                            <div class="col-6">
                                <dt class="small text-secondary-soft fw-normal">Total 7 hari</dt>
                                <dd class="fs-4 fw-bold mb-0" data-js="stat-total">{{ $summary['total_7d'] }}</dd>
                            </div>
                            <div class="col-6">
                                <dt class="small text-secondary-soft fw-normal">24 jam terakhir</dt>
                                <dd class="fs-4 fw-bold mb-0" data-js="stat-24h">{{ $summary['last_24h'] }}</dd>
                            </div>
                            <div class="col-6">
                                <dt class="small text-secondary-soft fw-normal">Kepercayaan tinggi (≥80%)</dt>
                                <dd class="fs-4 fw-bold mb-0" data-js="stat-confidence">{{ $summary['high_confidence'] }}</dd>
                            </div>
                            <div class="col-6">
                                <dt class="small text-secondary-soft fw-normal">Provinsi aktif</dt>
                                <dd class="fs-6 fw-semibold mb-0" data-js="stat-province">{{ $activeProvince?->name ?? 'Seluruh Kalimantan' }}</dd>
                            </div>
                        </dl>

                        <div class="text-secondary-soft small" data-js="summary-error" hidden>
                            <i class="bi bi-wifi-off me-1"></i> Gagal memuat ringkasan. Coba muat ulang halaman.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== PETA / PERSEBARAN ===================== --}}
    <section class="section-block" id="peta">
        <div class="container">
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="h4 fw-semibold mb-1">Persebaran titik panas</h2>
                    <p class="text-secondary-soft mb-0 small">
                        Sebaran titik panas 7 hari terakhir{{ $activeProvince ? ' — ' . $activeProvince->name : '' }}.
                    </p>
                </div>
                <span class="badge-demo">Data DEMO</span>
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
            <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="h4 fw-semibold mb-1">Titik panas terbaru</h2>
                    <p class="text-secondary-soft mb-0 small">Delapan deteksi terakhir dari data DEMO.</p>
                </div>
                <span class="badge-demo">DEMO</span>
            </div>

            @if ($hotspots->isNotEmpty())
                <div class="table-responsive">
                    <table class="table hotspot-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Waktu deteksi</th>
                                <th scope="col">Provinsi</th>
                                <th scope="col">Koordinat</th>
                                <th scope="col" class="text-end">Kepercayaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hotspots as $hotspot)
                                <tr>
                                    <td>{{ $hotspot->detected_at?->isoFormat('D MMM, HH:mm') ?? '—' }}</td>
                                    <td>{{ $hotspot->province?->name ?? '—' }}</td>
                                    <td class="font-monospace small">
                                        {{ number_format($hotspot->latitude, 4) }}, {{ number_format($hotspot->longitude, 4) }}
                                    </td>
                                    <td class="text-end">
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
                    <i class="bi bi-fire mb-2"></i>
                    <p class="mb-0">Belum ada data titik panas {{ $activeProvince ? 'untuk ' . $activeProvince->name : '' }}.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ===================== KUALITAS UDARA + PENGUMUMAN ===================== --}}
    <section class="section-block" id="informasi">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <h2 class="h4 fw-semibold mb-3">Kualitas udara</h2>
                    <div class="empty-state py-4 px-3">
                        <i class="bi bi-wind mb-2"></i>
                        <p class="mb-1 fw-semibold">Data kualitas udara belum tersedia</p>
                        <p class="small text-secondary-soft mb-0">
                            Sumber data AQI (mis. BMKG atau OpenAQ) belum terhubung.
                            Bagian ini akan aktif ketika sumber data tersedia.
                        </p>
                    </div>
                </div>

                <div class="col-lg-7" id="pengumuman">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="h4 fw-semibold mb-0">Kewaspadaan &amp; pengumuman</h2>
                        <a href="{{ route('announcements.index') }}" class="small fw-semibold text-primary text-decoration-none">
                            Lihat Semua <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                    @if ($announcements->isNotEmpty())
                        <div class="d-grid gap-3">
                            @foreach ($announcements as $item)
                                <article class="announcement-item">
                                    <div class="d-flex gap-3">
                                        <span class="level-dot level-{{ $item->level }}" aria-hidden="true"></span>
                                        <div class="flex-grow-1">
                                            <h3 class="h6 fw-semibold mb-1">
                                                <a href="{{ route('announcements.show', $item->slug) }}" class="text-dark text-decoration-none">
                                                    {{ $item->title }}
                                                </a>
                                                @if ($item->is_demo)
                                                    <span class="badge-demo py-0 px-2 ms-1">DEMO</span>
                                                @endif
                                            </h3>
                                            <p class="small text-secondary-soft mb-1">{{ $item->excerpt(140) }}</p>
                                            <span class="small text-secondary-soft">
                                                {{ $item->effectiveDate()->isoFormat('D MMMM Y') }}
                                                &bull; {{ $item->levelLabel() }}
                                                @if ($item->source_name)
                                                    &bull; {{ $item->source_name }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state py-4 px-3">
                            <i class="bi bi-megaphone mb-2"></i>
                            <p class="mb-0 small">Belum ada pengumuman yang diterbitkan.</p>
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
                    <h2 class="h4 fw-semibold mb-1">Panduan keselamatan pilihan</h2>
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
                            <article class="guide-item h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <span class="guide-icon"><i class="bi {{ $guide->categoryIcon() }}"></i></span>
                                    <span class="small fw-semibold text-primary d-block mb-1">{{ $guide->categoryLabel() }}</span>
                                    <h3 class="h6 fw-semibold mb-2">
                                        <a href="{{ route('guides.show', $guide->slug) }}" class="text-dark text-decoration-none">
                                            {{ $guide->title }}
                                        </a>
                                    </h3>
                                    <p class="small text-secondary-soft mb-3 line-clamp-3">{{ Str::limit($guide->summary, 120) }}</p>
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
                            <article class="guide-item h-100">
                                <span class="guide-icon"><i class="bi {{ $guide->icon ?? 'bi-book' }}"></i></span>
                                <h3 class="h6 fw-semibold mb-1">{{ $guide->title }}</h3>
                                <p class="small text-secondary-soft mb-2">{{ $guide->summary }}</p>
                                <span class="small fw-semibold text-primary">{{ $guide->audienceLabel() }}</span>
                            </article>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state py-4 px-3 text-center">
                    <i class="bi bi-journal-text mb-2"></i>
                    <p class="mb-0 small">Panduan keselamatan belum dipublikasikan.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
