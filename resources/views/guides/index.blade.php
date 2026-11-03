@extends('layouts.app')

@section('title', 'Panduan Keselamatan — FireAware Kalimantan')

@section('content')
    {{-- Header / Hero Panduan --}}
    <header class="guide-hero">
        <div class="container">
            <div class="row align-items-center justify-content-between g-4">
                <div class="col-lg-8">
                    <span class="badge-demo mb-2 text-uppercase">
                        <i class="bi bi-shield-shaded me-1"></i> Literasi Mitigasi Karhutla
                    </span>
                    <h1 class="guide-hero-title mb-2">Panduan Keselamatan Asap &amp; Karhutla</h1>
                    <p class="guide-hero-lead mb-0">
                        Prosedur perlindungan pernapasan, kesiapsiagaan keluarga, dan protokol pelaporan
                        berbasis rujukan kesehatan dan kebencanaan untuk masyarakat Kalimantan.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="p-3 bg-soft rounded-3 border border-soft text-start d-inline-block w-100" style="max-width: 360px;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-info-circle text-primary"></i>
                            <span class="fw-semibold small">Prinsip Keselamatan</span>
                        </div>
                        <p class="small text-secondary-soft mb-0">
                            Utamakan evakuasi diri dan perlindungan napas. Hindari mendekati api atau memadamkan kebakaran lahan secara mandiri.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Filter Kategori & Pencarian --}}
    <section class="py-4 bg-white border-bottom border-soft">
        <div class="container">
            <div class="row g-3 align-items-center justify-content-between">
                {{-- Daftar Kategori --}}
                <div class="col-xl-8 col-lg-7">
                    <nav class="guide-category-nav" aria-label="Filter kategori panduan">
                        <a href="{{ route('guides.index', array_filter(['q' => $search])) }}"
                           class="guide-category-pill {{ $activeCategory === null ? 'active' : '' }}">
                            <span>Semua Topik</span>
                            <span class="badge-count">{{ $totalPublishedCount }}</span>
                        </a>

                        @foreach ($categories as $slug => $meta)
                            <a href="{{ route('guides.index', array_filter(['kategori' => $slug, 'q' => $search])) }}"
                               class="guide-category-pill {{ $activeCategory === $slug ? 'active' : '' }}">
                                <i class="bi {{ $meta['icon'] }}"></i>
                                <span>{{ $meta['name'] }}</span>
                                @if (isset($categoryCounts[$slug]))
                                    <span class="badge-count">{{ $categoryCounts[$slug] }}</span>
                                @endif
                            </a>
                        @endforeach
                    </nav>
                </div>

                {{-- Kotak Pencarian --}}
                <div class="col-xl-4 col-lg-5">
                    <form method="GET" action="{{ route('guides.index') }}" class="guide-search-box">
                        @if ($activeCategory)
                            <input type="hidden" name="kategori" value="{{ $activeCategory }}">
                        @endif
                        <i class="bi bi-search search-icon"></i>
                        <input type="search"
                               name="q"
                               value="{{ $search }}"
                               class="form-control"
                               placeholder="Cari panduan keselamatan..."
                               aria-label="Cari panduan keselamatan">
                        @if ($search !== '')
                            <a href="{{ route('guides.index', array_filter(['kategori' => $activeCategory])) }}"
                               class="btn btn-link btn-sm position-absolute end-0 top-50 translate-middle-y text-secondary text-decoration-none me-2"
                               title="Hapus pencarian">
                                <i class="bi bi-x-circle-fill"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Indikator Filter Aktif --}}
            @if ($activeCategory || $search !== '')
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-3 mt-3 border-top border-soft small">
                    <div class="text-secondary-soft">
                        Menampilkan panduan untuk:
                        @if ($activeCategory)
                            <span class="fw-semibold text-dark">{{ $categories[$activeCategory]['name'] ?? $activeCategory }}</span>
                        @endif
                        @if ($activeCategory && $search !== '')
                            <span class="mx-1">dan</span>
                        @endif
                        @if ($search !== '')
                            kata kunci <span class="fw-semibold text-dark">"{{ $search }}"</span>
                        @endif
                        <span class="ms-1">({{ $guides->total() }} artikel)</span>
                    </div>

                    <a href="{{ route('guides.index') }}" class="text-decoration-none text-danger fw-semibold">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- Konten Utama: Daftar Editorial Artikel --}}
    <main class="section-block">
        <div class="container">
            @if ($guides->isNotEmpty())
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-9">
                        <div class="guide-editorial-feed">
                            @foreach ($guides as $guide)
                                <article class="guide-editorial-item">
                                    {{-- Meta Atas --}}
                                    <div class="guide-meta-top">
                                        <a href="{{ route('guides.index', ['kategori' => $guide->category]) }}"
                                           class="guide-category-tag">
                                            <i class="bi {{ $guide->categoryIcon() }}"></i>
                                            {{ $guide->categoryLabel() }}
                                        </a>
                                        <span>&bull;</span>
                                        <time datetime="{{ $guide->effectiveDate()->toIso8601String() }}">
                                            {{ $guide->effectiveDate()->isoFormat('D MMMM Y') }}
                                        </time>
                                        <span>&bull;</span>
                                        <span>
                                            <i class="bi bi-clock me-1"></i>{{ $guide->reading_time_minutes }} menit baca
                                        </span>
                                    </div>

                                    {{-- Judul Artikel --}}
                                    <h2 class="guide-editorial-title">
                                        <a href="{{ route('guides.show', $guide->slug) }}">
                                            {{ $guide->title }}
                                        </a>
                                    </h2>

                                    {{-- Ringkasan Editorial --}}
                                    <p class="guide-editorial-summary">
                                        {{ $guide->summary }}
                                    </p>

                                    {{-- Footer: Sumber Rujukan & Link Lanjut --}}
                                    <div class="guide-editorial-footer">
                                        <div class="guide-source-badge">
                                            <i class="bi bi-shield-check"></i>
                                            <span>Rujukan: <strong>{{ $guide->source_name }}</strong></span>
                                        </div>

                                        <a href="{{ route('guides.show', $guide->slug) }}" class="guide-read-more">
                                            Baca panduan lengkap <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if ($guides->hasPages())
                            <div class="d-flex justify-content-center mt-5">
                                {{ $guides->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    </div>
                </div>
            @else
                {{-- Empty State yang Jelas & Informatif --}}
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="empty-state text-center py-5 px-4 bg-white">
                            <i class="bi bi-journal-x mb-3 text-secondary-soft" style="font-size: 2.5rem;"></i>
                            <h3 class="h5 fw-semibold mb-2">Tidak Ada Panduan yang Ditemukan</h3>
                            <p class="text-secondary-soft small mb-4 mx-auto" style="max-width: 480px;">
                                @if ($search !== '' || $activeCategory)
                                    Tidak ada artikel panduan keselamatan yang cocok dengan filter atau kata kunci pencarian Anda.
                                @else
                                    Saat ini belum ada artikel panduan keselamatan yang dipublikasikan. Silakan periksa kembali nanti.
                                @endif
                            </p>
                            @if ($search !== '' || $activeCategory)
                                <a href="{{ route('guides.index') }}" class="btn btn-primary btn-sm px-4">
                                    <i class="bi bi-arrow-left me-1"></i> Lihat Semua Panduan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>
@endsection
