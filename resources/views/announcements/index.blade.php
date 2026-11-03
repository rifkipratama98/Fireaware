@extends('layouts.app')

@section('title', 'Pengumuman & Peringatan Dini Karhutla — FireAware Kalimantan')

@section('content')
    {{-- Header Pengumuman (Official Bulletin Hero) --}}
    <header class="announcement-hero">
        <div class="container">
            <div class="row align-items-center justify-content-between g-4">
                <div class="col-lg-8">
                    <span class="badge-demo mb-2 text-uppercase">
                        <i class="bi bi-broadcast me-1"></i> Warta &amp; Peringatan Dini
                    </span>
                    <h1 class="guide-hero-title mb-2">Pusat Pengumuman Karhutla Kalimantan</h1>
                    <p class="guide-hero-lead mb-0">
                        Rilis informasi resmi, peringatan cuaca ekstrem, dan status kesiapsiagaan kebakaran hutan/lahan
                        dari otoritas kebencanaan, BMKG, dan pemerintah daerah.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="p-3 bg-white rounded-3 border border-soft text-start d-inline-block w-100" style="max-width: 360px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-semibold text-secondary-soft">Tingkat Kewaspadaan</span>
                            <span class="badge-demo">Status Terkini</span>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 flex-fill py-2 text-center">
                                <strong class="d-block fs-6">{{ $stats['darurat'] }}</strong>
                                <small>Siaga</small>
                            </span>
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 flex-fill py-2 text-center">
                                <strong class="d-block fs-6">{{ $stats['waspada'] }}</strong>
                                <small>Waspada</small>
                            </span>
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 flex-fill py-2 text-center">
                                <strong class="d-block fs-6">{{ $stats['info'] }}</strong>
                                <small>Info</small>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Filter Bar: Wilayah, Level, dan Pencarian --}}
    <section class="py-3 bg-white border-bottom border-soft">
        <div class="container">
            <form method="GET" action="{{ route('announcements.index') }}" class="row g-2 align-items-center">
                {{-- Filter Wilayah --}}
                <div class="col-md-3 col-sm-6">
                    <select name="provinsi" class="form-select form-select-sm" onchange="this.form.submit()">
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
                    <select name="level" class="form-select form-select-sm" onchange="this.form.submit()">
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
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-secondary-soft">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="search"
                               name="q"
                               value="{{ $search }}"
                               class="form-control border-start-0 ps-0"
                               placeholder="Cari judul, instansi penerbit, atau nomor rilis...">
                    </div>
                </div>

                {{-- Submit / Reset --}}
                <div class="col-md-1 col-sm-2 text-end">
                    @if ($activeProvince || $activeLevel || $search !== '')
                        <a href="{{ route('announcements.index') }}" class="btn btn-outline-secondary btn-sm w-100" title="Reset filter">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @else
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            Cari
                        </button>
                    @endif
                </div>
            </form>

            @if ($activeProvince || $activeLevel || $search !== '')
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-2 mt-2 border-top border-soft small">
                    <div class="text-secondary-soft">
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
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Hapus Filter
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- Daftar Aliran Pengumuman (Official Bulletin Stream) --}}
    <main class="section-block">
        <div class="container">
            @if ($announcements->isNotEmpty())
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-9">
                        <div class="announcement-stream">
                            @foreach ($announcements as $item)
                                <article class="bulletin-card level-border-{{ $item->level }}">
                                    <div class="bulletin-header">
                                        <span class="badge {{ $item->levelBadgeClass() }} px-2 py-1">
                                            <i class="bi {{ $item->levelIcon() }} me-1"></i>
                                            {{ $item->levelLabel() }}
                                        </span>

                                        @if ($item->province)
                                            <span class="region-chip">
                                                <i class="bi bi-geo-alt-fill"></i>
                                                {{ $item->province->name }}
                                            </span>
                                        @else
                                            <span class="region-chip">
                                                <i class="bi bi-globe-asia-australia"></i>
                                                Seluruh Wilayah Kalimantan
                                            </span>
                                        @endif

                                        <time class="small text-secondary-soft ms-auto" datetime="{{ $item->effectiveDate()->toIso8601String() }}">
                                            <i class="bi bi-clock me-1"></i>{{ $item->effectiveDate()->isoFormat('D MMMM Y, HH:mm') }}
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
                                            <i class="bi bi-patch-check-fill text-success"></i>
                                            <span>Penerbit: <strong>{{ $item->source_name ?? 'Otoritas Wilayah' }}</strong></span>
                                        </div>

                                        <a href="{{ route('announcements.show', $item->slug) }}" class="fw-semibold text-primary text-decoration-none">
                                            Rincian Pengumuman <i class="bi bi-arrow-right"></i>
                                        </a>
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
                </div>
            @else
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="empty-state text-center py-5 px-4 bg-white">
                            <i class="bi bi-megaphone mb-3 text-secondary-soft" style="font-size: 2.5rem;"></i>
                            <h3 class="h5 fw-semibold mb-2">Tidak Ada Pengumuman</h3>
                            <p class="text-secondary-soft small mb-4 mx-auto" style="max-width: 480px;">
                                @if ($activeProvince || $activeLevel || $search !== '')
                                    Tidak ada pengumuman yang sesuai dengan filter wilayah atau status yang dipilih.
                                @else
                                    Saat ini belum ada pengumuman atau peringatan dini yang sedang aktif.
                                @endif
                            </p>
                            @if ($activeProvince || $activeLevel || $search !== '')
                                <a href="{{ route('announcements.index') }}" class="btn btn-primary btn-sm px-4">
                                    <i class="bi bi-arrow-left me-1"></i> Tampilkan Semua Pengumuman
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>
@endsection
