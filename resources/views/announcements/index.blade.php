@extends('layouts.app')

@section('title', 'Informasi Kewaspadaan & Peringatan Dini — FireAware Kalimantan')

@section('content')
    {{-- Header Pengumuman & Pusat Peringatan Dini --}}
    <header class="announcement-hero">
        <div class="container">
            <div class="row align-items-center justify-content-between g-4">
                <div class="col-lg-8">
                    <span class="aq-badge-header mb-2 text-uppercase">
                        <i class="bi bi-shield-exclamation me-1" aria-hidden="true"></i> Pusat Peringatan Dini &amp; Warta Karhutla
                    </span>
                    <h1 class="guide-hero-title mb-2">Informasi Kewaspadaan &amp; Peringatan Dini</h1>
                    <p class="guide-hero-lead mb-0">
                        Rilis informasi resmi, peringatan cuaca ekstrem, dan status kesiapsiagaan kebakaran hutan/lahan
                        dari otoritas kebencanaan, BMKG, dan pemerintah daerah di 5 provinsi Kalimantan.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="announcement-stat-card text-start d-inline-block w-100" style="max-width: 380px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-semibold text-white-50">Status Kewaspadaan Aktif</span>
                            <span class="badge bg-light text-primary border border-soft py-0.5 px-2 small fw-semibold">
                                Total: {{ $stats['total'] }} Rilis
                            </span>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="stat-badge-darurat rounded-2 p-2 flex-fill text-center">
                                <strong class="d-block fs-5 lh-1 mb-1">{{ $stats['darurat'] }}</strong>
                                <small class="fw-semibold" style="font-size: .75rem;">Siaga Darurat</small>
                            </div>
                            <div class="stat-badge-waspada rounded-2 p-2 flex-fill text-center">
                                <strong class="d-block fs-5 lh-1 mb-1">{{ $stats['waspada'] }}</strong>
                                <small class="fw-semibold" style="font-size: .75rem;">Waspada</small>
                            </div>
                            <div class="stat-badge-info rounded-2 p-2 flex-fill text-center">
                                <strong class="d-block fs-5 lh-1 mb-1">{{ $stats['info'] }}</strong>
                                <small class="fw-semibold" style="font-size: .75rem;">Informasi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Filter Bar: Wilayah, Level, dan Pencarian --}}
    <section class="announcement-filter-bar">
        <div class="container">
            <form method="GET" action="{{ route('announcements.index') }}" class="row g-2 align-items-center">
                {{-- Filter Wilayah --}}
                <div class="col-md-3 col-sm-6">
                    <label for="filterProvinsi" class="visually-hidden">Wilayah Provinsi</label>
                    <select id="filterProvinsi" name="provinsi" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Wilayah Kalimantan</option>
                        @foreach ($provinces as $prov)
                            <option value="{{ $prov->slug }}" {{ $activeProvince && $activeProvince->slug === $prov->slug ? 'selected' : '' }}>
                                {{ $prov->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Level --}}
                <div class="col-md-3 col-sm-6">
                    <label for="filterLevel" class="visually-hidden">Tingkat Status</label>
                    <select id="filterLevel" name="level" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Tingkat Status</option>
                        @foreach ($levels as $lvlKey => $meta)
                            <option value="{{ $lvlKey }}" {{ $activeLevel === $lvlKey ? 'selected' : '' }}>
                                {{ $meta['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pencarian --}}
                <div class="col-md-5 col-sm-10">
                    <label for="filterSearch" class="visually-hidden">Cari Pengumuman</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-secondary">
                            <i class="bi bi-search" aria-hidden="true"></i>
                        </span>
                        <input type="search"
                               id="filterSearch"
                               name="q"
                               value="{{ $search }}"
                               class="form-control border-start-0 ps-0"
                               placeholder="Cari judul, kata kunci, instansi penerbit...">
                    </div>
                </div>

                {{-- Submit / Reset --}}
                <div class="col-md-1 col-sm-2 text-end">
                    @if ($activeProvince || $activeLevel || $search !== '')
                        <a href="{{ route('announcements.index') }}" class="btn btn-outline-secondary w-100" title="Reset filter" aria-label="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                        </a>
                    @else
                        <button type="submit" class="btn btn-primary w-100">
                            Cari
                        </button>
                    @endif
                </div>
            </form>

            @if ($activeProvince || $activeLevel || $search !== '')
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 mt-2 border-top border-soft small">
                    <div class="text-secondary">
                        Menampilkan pengumuman untuk:
                        @if ($activeProvince)
                            wilayah <strong class="text-dark">{{ $activeProvince->name }}</strong>
                        @endif
                        @if ($activeProvince && $activeLevel)
                            <span class="mx-1">&bull;</span>
                        @endif
                        @if ($activeLevel)
                            status <strong class="text-dark">{{ $levels[$activeLevel]['label'] ?? '' }}</strong>
                        @endif
                        @if ($search !== '')
                            <span class="mx-1">&bull;</span>
                            kata kunci <strong class="text-dark">"{{ $search }}"</strong>
                        @endif
                        <span class="ms-1">({{ $announcements->total() }} rilis)</span>
                    </div>

                    <a href="{{ route('announcements.index') }}" class="text-decoration-none text-danger fw-semibold">
                        <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i>Hapus Filter
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- Daftar Aliran Pengumuman (Official Bulletin Stream) --}}
    <main class="section-block bg-soft">
        <div class="container">
            @if ($announcements->isNotEmpty())
                <div class="row g-4">
                    <div class="col-lg-8">
                        @php
                            $firstAlert = ($announcements->currentPage() === 1 && empty($search) && !$activeLevel && !$activeProvince)
                                ? $announcements->first()
                                : null;
                            $threshold = now()->subDays(2);
                            $recentItems = $announcements->filter(fn ($item) => $item->effectiveDate()->gt($threshold));
                            $olderItems = $announcements->filter(fn ($item) => $item->effectiveDate()->lte($threshold));
                        @endphp

                        {{-- 2. Peringatan Terkini / Rilis Prioritas Menonjol --}}
                        @if ($firstAlert)
                            <div class="announcement-featured featured-{{ $firstAlert->level == 3 ? 'darurat' : ($firstAlert->level == 2 ? 'waspada' : 'info') }}">
                                <div class="featured-badge-bar">
                                    <span class="badge {{ $firstAlert->levelBadgeClass() }} px-3 py-1 fw-bold">
                                        <i class="bi {{ $firstAlert->levelIcon() }} me-1" aria-hidden="true"></i>
                                        {{ $firstAlert->levelLabel() }}
                                    </span>
                                    @if ($firstAlert->province)
                                        <span class="region-chip">
                                            <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                                            {{ $firstAlert->province->name }}
                                        </span>
                                    @else
                                        <span class="region-chip">
                                            <i class="bi bi-globe-asia-australia" aria-hidden="true"></i>
                                            Seluruh Kalimantan
                                        </span>
                                    @endif
                                    <span class="badge bg-danger text-white py-1 px-2.5 small fw-semibold">
                                        <i class="bi bi-broadcast me-1" aria-hidden="true"></i>Rilis Prioritas Terkini
                                    </span>
                                    <time class="small text-secondary ms-auto" datetime="{{ $firstAlert->effectiveDate()->toIso8601String() }}">
                                        <i class="bi bi-clock me-1" aria-hidden="true"></i>{{ $firstAlert->effectiveDate()->isoFormat('D MMMM Y, HH:mm') }} WITA
                                    </time>
                                </div>

                                <div class="featured-body">
                                    <h2 class="featured-title">
                                        <a href="{{ route('announcements.show', $firstAlert->slug) }}">
                                            {{ $firstAlert->title }}
                                        </a>
                                    </h2>

                                    <p class="featured-summary">
                                        {{ $firstAlert->excerpt(240) }}
                                    </p>

                                    <div class="featured-footer">
                                        <div class="d-flex align-items-center gap-1.5 text-secondary small">
                                            <i class="bi bi-patch-check-fill text-success fs-6" aria-hidden="true"></i>
                                            <span>Penerbit: <strong>{{ $firstAlert->source_name ?? 'Otoritas Kebencanaan Wilayah' }}</strong></span>
                                        </div>

                                        <div class="d-flex gap-2">
                                            @if ($firstAlert->source_url)
                                                <a href="{{ $firstAlert->source_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Sumber Resmi
                                                </a>
                                            @endif
                                            <a href="{{ route('announcements.show', $firstAlert->slug) }}" class="btn btn-primary btn-sm">
                                                Rincian Pengumuman <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- 4 & 9. Aliran Warta Editorial --}}
                        <div class="announcement-stream">
                            @php $currentHeading = null; @endphp
                            @foreach ($announcements as $item)
                                @php
                                    $isRecent = $item->effectiveDate()->gt($threshold);
                                    $headingType = $isRecent ? 'recent' : 'older';
                                @endphp
                                @if ($currentHeading !== $headingType)
                                    @php $currentHeading = $headingType; @endphp
                                    <div class="bulletin-timeline-heading {{ $loop->first ? 'mt-1' : 'mt-4' }}">
                                        @if ($isRecent)
                                            <h2 class="timeline-title">
                                                <i class="bi bi-lightning-charge text-danger" aria-hidden="true"></i> Peringatan &amp; Warta Terkini
                                            </h2>
                                            <span class="timeline-count">48 Jam Terakhir</span>
                                        @else
                                            <h2 class="timeline-title">
                                                <i class="bi bi-archive text-secondary" aria-hidden="true"></i> Arsip Peringatan Terdahulu
                                            </h2>
                                            <span class="timeline-count">Rilis Sebelumnya</span>
                                        @endif
                                    </div>
                                @endif

                                <article class="bulletin-card level-border-{{ $item->level }} {{ $isRecent ? 'is-recent-alert' : 'is-older-alert' }}">
                                    <div class="bulletin-header">
                                        <span class="badge {{ $item->levelBadgeClass() }} px-2 py-1">
                                            <i class="bi {{ $item->levelIcon() }} me-1" aria-hidden="true"></i>
                                            {{ $item->levelLabel() }}
                                        </span>

                                        @if ($item->province)
                                            <span class="region-chip">
                                                <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                                                {{ $item->province->name }}
                                            </span>
                                        @else
                                            <span class="region-chip">
                                                <i class="bi bi-globe-asia-australia" aria-hidden="true"></i>
                                                Seluruh Kalimantan
                                            </span>
                                        @endif

                                        @if ($isRecent)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle py-0.5 px-2 small fw-semibold">
                                                Terbaru
                                            </span>
                                        @endif

                                        <time class="small text-secondary ms-auto" datetime="{{ $item->effectiveDate()->toIso8601String() }}">
                                            <i class="bi bi-clock me-1" aria-hidden="true"></i>{{ $item->effectiveDate()->isoFormat('D MMMM Y, HH:mm') }} WITA
                                        </time>

                                        @if ($item->is_demo)
                                            <span class="badge-demo">DEMO</span>
                                        @endif
                                    </div>

                                    <h2 class="bulletin-title">
                                        <a href="{{ route('announcements.show', $item->slug) }}">
                                            {{ $item->title }}
                                        </a>
                                    </h2>

                                    <p class="bulletin-summary">
                                        {{ $item->excerpt(200) }}
                                    </p>

                                    <div class="bulletin-footer">
                                        <div class="d-flex align-items-center gap-1 text-secondary">
                                            <i class="bi bi-patch-check-fill text-success" aria-hidden="true"></i>
                                            <span>Penerbit: <strong>{{ $item->source_name ?? 'Otoritas Wilayah' }}</strong></span>
                                        </div>

                                        <div class="d-flex align-items-center gap-3">
                                            @if ($item->source_url)
                                                <a href="{{ $item->source_url }}" target="_blank" rel="noopener noreferrer" class="text-secondary text-decoration-none small d-none d-sm-inline-flex align-items-center">
                                                    <i class="bi bi-box-arrow-up-right me-1" aria-hidden="true"></i>Sumber Asli
                                                </a>
                                            @endif
                                            <a href="{{ route('announcements.show', $item->slug) }}" class="fw-semibold text-primary text-decoration-none">
                                                Rincian Pengumuman <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if ($announcements->hasPages())
                            <div class="d-flex justify-content-center mt-5">
                                {{ $announcements->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>

                    {{-- Sidebar Kolom Konteks & Layanan Darurat --}}
                    <div class="col-lg-4">
                        {{-- 8. Foto Dokumentasi Karhutla & Patroli Terpadu --}}
                        <div class="aq-editorial-card mb-4">
                            <img src="{{ asset('images/conservation-action.jpg') }}"
                                 alt="Patroli satgas darat penanggulangan karhutla dan pembasahan lahan gambut Kalimantan"
                                 class="aq-editorial-img"
                                 loading="lazy"
                                 width="640"
                                 height="360">
                            <h3>Patroli Terpadu Kesiapsiagaan</h3>
                            <p>
                                Posko satgas darurat BPBD dan Manggala Agni secara berkala melakukan patroli lapangan, pemantauan menara api, dan pembasahan lahan gambut guna mencegah eskalasi titik panas liar.
                            </p>
                        </div>

                        {{-- Penjelasan Kategori Kewaspadaan --}}
                        <div class="aq-sidebar-panel mb-4">
                            <h3 class="h6 fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-shield-check text-primary" aria-hidden="true"></i>
                                Standar Tingkat Kewaspadaan
                            </h3>

                            <div class="d-flex flex-column gap-2.5">
                                <div class="p-2.5 rounded bg-danger-subtle border border-danger-subtle">
                                    <div class="d-flex align-items-center gap-1.5 mb-1">
                                        <i class="bi bi-shield-exclamation text-danger fw-bold" aria-hidden="true"></i>
                                        <strong class="text-danger small">Siaga Darurat</strong>
                                    </div>
                                    <p class="mb-0 small text-danger-emphasis" style="font-size: .8rem; line-height: 1.45;">
                                        Terdeteksi kebakaran aktif meluas mendekati pemukiman, atau kabut asap pekat pada kategori berbahaya.
                                    </p>
                                </div>

                                <div class="p-2.5 rounded bg-warning-subtle border border-warning-subtle">
                                    <div class="d-flex align-items-center gap-1.5 mb-1">
                                        <i class="bi bi-exclamation-triangle text-warning-emphasis fw-bold" aria-hidden="true"></i>
                                        <strong class="text-warning-emphasis small">Waspada</strong>
                                    </div>
                                    <p class="mb-0 small text-dark" style="font-size: .8rem; line-height: 1.45;">
                                        Kenaikan anomali suhu/titik panas di atas rata-rata historis atau kondisi hari tanpa hujan ekstrem.
                                    </p>
                                </div>

                                <div class="p-2.5 rounded bg-success-subtle border border-success-subtle">
                                    <div class="d-flex align-items-center gap-1.5 mb-1">
                                        <i class="bi bi-info-circle text-success fw-bold" aria-hidden="true"></i>
                                        <strong class="text-success small">Informasi</strong>
                                    </div>
                                    <p class="mb-0 small text-success-emphasis" style="font-size: .8rem; line-height: 1.45;">
                                        Pembaruan rutin cuaca, sosialisasi kesiapsiagaan masyarakat, dan imbauan pencegahan harian.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Saluran Siaga Darurat --}}
                        <div class="aq-sidebar-panel">
                            <h3 class="h6 fw-bold mb-2 text-dark">Layanan Kedaruratan Warga</h3>
                            <p class="small text-secondary mb-3">
                                Jika Anda melihat kepulan asap pekat atau indikasi kebakaran hutan dan lahan yang tidak terkendali, segera hubungi:
                            </p>
                            <a href="tel:112" class="btn btn-danger w-100 mb-2">
                                <i class="bi bi-telephone-fill me-1" aria-hidden="true"></i>Panggilan Darurat Bebas Pulsa 112
                            </a>
                            <a href="{{ route('guides.index') }}" class="btn btn-outline-primary w-100 btn-sm">
                                <i class="bi bi-book me-1" aria-hidden="true"></i>Panduan Evakuasi &amp; Keselamatan
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="empty-state text-center py-5 px-4 bg-white rounded-3 border border-soft shadow-sm">
                            <i class="bi bi-shield-check mb-3 text-success" style="font-size: 3rem;" aria-hidden="true"></i>
                            <h3 class="h5 fw-bold mb-2">Tidak Ada Pengumuman Aktif</h3>
                            <p class="text-secondary small mb-4 mx-auto" style="max-width: 480px;">
                                @if ($activeProvince || $activeLevel || $search !== '')
                                    Tidak ada rilis pengumuman yang sesuai dengan filter wilayah atau kriteria pencarian yang Anda tentukan.
                                @else
                                    Saat ini kondisi terpantau kondusif. Belum ada peringatan darurat atau pengumuman khusus yang diterbitkan oleh otoritas.
                                @endif
                            </p>
                            @if ($activeProvince || $activeLevel || $search !== '')
                                <a href="{{ route('announcements.index') }}" class="btn btn-primary btn-sm px-4">
                                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Tampilkan Semua Pengumuman
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>
@endsection
