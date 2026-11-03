@extends('layouts.app')

@section('title', 'Edukasi Karhutla & Ekosistem Gambut — FireAware Kalimantan')

@section('content')
    {{-- Header Edukasi (Magazine Hero) --}}
    <header class="edu-hero">
        <div class="container">
            <div class="row align-items-center justify-content-between g-4">
                <div class="col-lg-8">
                    <span class="badge-demo mb-2 text-uppercase">
                        <i class="bi bi-mortarboard-fill me-1"></i> Pusat Literasi Kebencanaan
                    </span>
                    <h1 class="guide-hero-title mb-2">Edukasi Karhutla &amp; Gambut Kalimantan</h1>
                    <p class="guide-hero-lead mb-0">
                        Pelajari karakteristik ilmiah lahan gambut, dinamika iklim El Niño, dampak lingkungan,
                        serta metode restorasi ekologis untuk mencegah karhutla berulang.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="p-3 bg-white rounded-3 border border-soft text-start d-inline-block w-100" style="max-width: 360px;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-lightbulb text-warning"></i>
                            <span class="fw-semibold small">Tahukah Anda?</span>
                        </div>
                        <p class="small text-secondary-soft mb-0">
                            Gambut Kalimantan dapat menyimpan air hingga 13 kali bobotnya, namun rentan mengalami <em>ground fire</em> bila dikeringkan lewat kanal buatan.
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
                <div class="col-xl-8 col-lg-7">
                    <nav class="guide-category-nav" aria-label="Kategori edukasi">
                        <a href="{{ route('edukasi.index', array_filter(['q' => $search])) }}"
                           class="guide-category-pill {{ $activeCategory === null ? 'active' : '' }}">
                            <span>Semua Topik</span>
                            <span class="badge-count">{{ $totalCount }}</span>
                        </a>

                        @foreach ($categories as $slug => $meta)
                            <a href="{{ route('edukasi.index', array_filter(['kategori' => $slug, 'q' => $search])) }}"
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

                <div class="col-xl-4 col-lg-5">
                    <form method="GET" action="{{ route('edukasi.index') }}" class="guide-search-box">
                        @if ($activeCategory)
                            <input type="hidden" name="kategori" value="{{ $activeCategory }}">
                        @endif
                        <i class="bi bi-search search-icon"></i>
                        <input type="search"
                               name="q"
                               value="{{ $search }}"
                               class="form-control"
                               placeholder="Cari artikel edukasi..."
                               aria-label="Cari artikel edukasi">
                        @if ($search !== '')
                            <a href="{{ route('edukasi.index', array_filter(['kategori' => $activeCategory])) }}"
                               class="btn btn-link btn-sm position-absolute end-0 top-50 translate-middle-y text-secondary text-decoration-none me-2"
                               title="Hapus pencarian">
                                <i class="bi bi-x-circle-fill"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            @if ($activeCategory || $search !== '')
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pt-3 mt-3 border-top border-soft small">
                    <div class="text-secondary-soft">
                        Menampilkan materi edukasi untuk:
                        @if ($activeCategory)
                            <span class="fw-semibold text-dark">{{ $categories[$activeCategory]['name'] ?? $activeCategory }}</span>
                        @endif
                        @if ($activeCategory && $search !== '')
                            <span class="mx-1">dan</span>
                        @endif
                        @if ($search !== '')
                            kata kunci <span class="fw-semibold text-dark">"{{ $search }}"</span>
                        @endif
                        <span class="ms-1">({{ $articles->total() }} artikel)</span>
                    </div>

                    <a href="{{ route('edukasi.index') }}" class="text-decoration-none text-danger fw-semibold">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset Filter
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- Daftar Artikel Edukasi --}}
    <main class="section-block">
        <div class="container">
            @if ($articles->isNotEmpty())
                {{-- Artikel Unggulan (Hanya di Halaman Awal tanpa filter) --}}
                @if ($featuredArticle)
                    <article class="edu-featured-card">
                        <div class="row g-0">
                            <div class="col-lg-5">
                                <div class="featured-img-wrap">
                                    @if ($featuredArticle->imageUrl())
                                        <img src="{{ $featuredArticle->imageUrl() }}" alt="{{ $featuredArticle->title }}">
                                    @else
                                        <div class="img-placeholder text-center p-4">
                                            <i class="bi {{ $featuredArticle->categoryIcon() }}"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="featured-body">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="guide-category-tag">
                                            <i class="bi {{ $featuredArticle->categoryIcon() }}"></i>
                                            {{ $featuredArticle->categoryLabel() }}
                                        </span>
                                        <span class="text-secondary-soft small">&bull;</span>
                                        <span class="text-secondary-soft small">
                                            <i class="bi bi-clock me-1"></i>{{ $featuredArticle->reading_time_minutes }} menit baca
                                        </span>
                                        @if ($featuredArticle->is_demo)
                                            <span class="badge-demo">DEMO</span>
                                        @endif
                                    </div>

                                    <h2 class="featured-title">
                                        <a href="{{ route('edukasi.show', $featuredArticle->slug) }}">
                                            {{ $featuredArticle->title }}
                                        </a>
                                    </h2>

                                    <p class="text-secondary-soft mb-3" style="line-height: 1.65;">
                                        {{ $featuredArticle->summary }}
                                    </p>

                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top border-soft">
                                        <div class="small text-secondary">
                                            <span>Oleh: <strong>{{ $featuredArticle->author ?? 'Tim Redaksi FireAware' }}</strong></span>
                                            <span class="mx-1">&bull;</span>
                                            <span>{{ $featuredArticle->effectiveDate()->isoFormat('D MMMM Y') }}</span>
                                        </div>

                                        <a href="{{ route('edukasi.show', $featuredArticle->slug) }}" class="btn btn-primary btn-sm px-3">
                                            Baca Artikel <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                @endif

                {{-- Grid Artikel Edukasi --}}
                <div class="row g-4">
                    @foreach ($articles as $article)
                        @if ($featuredArticle && $article->id === $featuredArticle->id)
                            @continue
                        @endif

                        <div class="col-md-6 col-lg-4">
                            <article class="edu-card">
                                <div class="edu-thumb">
                                    <span class="thumb-tag">
                                        <i class="bi {{ $article->categoryIcon() }} me-1"></i>
                                        {{ $article->categoryLabel() }}
                                    </span>

                                    @if ($article->imageUrl())
                                        <img src="{{ $article->imageUrl() }}" alt="{{ $article->title }}" loading="lazy">
                                    @else
                                        <div class="thumb-placeholder">
                                            <i class="bi {{ $article->categoryIcon() }}"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="edu-card-body">
                                    <div class="d-flex align-items-center gap-2 mb-2 small text-secondary-soft">
                                        <time datetime="{{ $article->effectiveDate()->toIso8601String() }}">
                                            {{ $article->effectiveDate()->isoFormat('D MMM Y') }}
                                        </time>
                                        <span>&bull;</span>
                                        <span><i class="bi bi-clock me-1"></i>{{ $article->reading_time_minutes }} mnt</span>
                                        @if ($article->is_demo)
                                            <span class="badge-demo py-0 px-2 ms-auto">DEMO</span>
                                        @endif
                                    </div>

                                    <h3 class="edu-card-title">
                                        <a href="{{ route('edukasi.show', $article->slug) }}">
                                            {{ $article->title }}
                                        </a>
                                    </h3>

                                    <p class="edu-card-summary">
                                        {{ Str::limit($article->summary, 130) }}
                                    </p>

                                    <div class="edu-card-footer">
                                        <span class="text-truncate" style="max-width: 160px;">
                                            <i class="bi bi-person me-1"></i>{{ $article->author ?? 'Redaksi' }}
                                        </span>
                                        <a href="{{ route('edukasi.show', $article->slug) }}" class="fw-semibold text-primary text-decoration-none">
                                            Baca <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($articles->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        {{ $articles->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @else
                {{-- Empty State Edukasi --}}
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="empty-state text-center py-5 px-4 bg-white">
                            <i class="bi bi-journal-album mb-3 text-secondary-soft" style="font-size: 2.5rem;"></i>
                            <h3 class="h5 fw-semibold mb-2">Tidak Ada Materi Edukasi yang Ditemukan</h3>
                            <p class="text-secondary-soft small mb-4 mx-auto" style="max-width: 480px;">
                                @if ($search !== '' || $activeCategory)
                                    Tidak ada artikel edukasi yang cocok dengan kata kunci atau filter topik yang dipilih.
                                @else
                                    Materi edukasi belum dipublikasikan oleh tim redaksi. Silakan periksa kembali nanti.
                                @endif
                            </p>
                            @if ($search !== '' || $activeCategory)
                                <a href="{{ route('edukasi.index') }}" class="btn btn-primary btn-sm px-4">
                                    <i class="bi bi-arrow-left me-1"></i> Tampilkan Semua Topik
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>
@endsection
