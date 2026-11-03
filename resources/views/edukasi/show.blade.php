@extends('layouts.app')

@section('title', $article->title . ' — Edukasi FireAware')

@section('content')
    {{-- Header Artikel Edukasi --}}
    <header class="edu-detail-hero">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <a href="{{ route('edukasi.index', ['kategori' => $article->category]) }}" class="guide-back-nav">
                        <i class="bi bi-arrow-left"></i> Kembali ke Edukasi {{ $article->categoryLabel() }}
                    </a>

                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <a href="{{ route('edukasi.index', ['kategori' => $article->category]) }}" class="guide-category-tag">
                            <i class="bi {{ $article->categoryIcon() }}"></i>
                            {{ $article->categoryLabel() }}
                        </a>
                        <span class="text-secondary-soft small">&bull;</span>
                        <span class="text-secondary-soft small">
                            <i class="bi bi-clock me-1"></i>{{ $article->reading_time_minutes }} menit baca
                        </span>
                        @if ($article->is_demo)
                            <span class="badge-demo">DEMO</span>
                        @endif
                    </div>

                    <h1 class="guide-detail-title">
                        {{ $article->title }}
                    </h1>

                    <div class="guide-detail-meta">
                        <div>
                            <span class="text-secondary-soft">Penulis:</span>
                            <strong class="text-dark ms-1">{{ $article->author ?? 'Tim Redaksi FireAware' }}</strong>
                        </div>

                        <div>
                            <span class="text-secondary-soft">Diterbitkan:</span>
                            <strong class="text-dark ms-1">{{ $article->effectiveDate()->isoFormat('D MMMM Y') }}</strong>
                        </div>

                        @if ($article->updated_at && $article->updated_at->gt($article->effectiveDate()->addDay()))
                            <div>
                                <span class="text-secondary-soft">Pembaruan:</span>
                                <strong class="text-dark ms-1">{{ $article->updated_at->isoFormat('D MMMM Y') }}</strong>
                            </div>
                        @endif
                    </div>

                    <p class="guide-lead-text">
                        {{ $article->summary }}
                    </p>

                    {{-- Gambar Cover jika tersedia --}}
                    @if ($article->imageUrl())
                        <div class="edu-detail-img-wrap">
                            <img src="{{ $article->imageUrl() }}" alt="{{ $article->title }}">
                            @if ($article->image_caption)
                                <div class="img-caption">
                                    <i class="bi bi-camera me-1"></i> {{ $article->image_caption }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </header>

    {{-- Konten Artikel Panjang --}}
    <main class="bg-soft">
        <div class="container">
            <div class="guide-prose-container">

                <article class="guide-prose">
                    @php
                        $paragraphs = $article->contentParagraphs();
                    @endphp

                    @foreach ($paragraphs as $para)
                        @if (Str::startsWith($para, '### '))
                            <h3>{{ Str::after($para, '### ') }}</h3>
                        @elseif (Str::startsWith($para, '## '))
                            <h2>{{ Str::after($para, '## ') }}</h2>
                        @elseif (Str::startsWith($para, '> '))
                            <blockquote>
                                {!! nl2br(e(Str::after($para, '> '))) !!}
                            </blockquote>
                        @elseif (Str::startsWith($para, '- ') || Str::startsWith($para, '* '))
                            @php
                                $items = explode("\n", $para);
                            @endphp
                            <ul>
                                @foreach ($items as $item)
                                    @php
                                        $cleanItem = preg_replace('/^[\-\*]\s+/', '', trim($item));
                                        $formatted = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', e($cleanItem));
                                    @endphp
                                    @if ($cleanItem !== '')
                                        <li>{!! $formatted !!}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            @php
                                $formattedPara = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', e($para));
                                $formattedPara = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $formattedPara);
                            @endphp
                            <p>{!! nl2br($formattedPara) !!}</p>
                        @endif
                    @endforeach
                </article>

                {{-- Penutup Artikel & Navigasi --}}
                <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top border-soft">
                    <a href="{{ route('edukasi.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="bi bi-arrow-left me-1"></i> Semua Materi Edukasi
                    </a>

                    <a href="{{ route('edukasi.index', ['kategori' => $article->category]) }}" class="btn btn-primary btn-sm px-3">
                        Topik {{ $article->categoryLabel() }} <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

            </div>
        </div>
    </main>

    {{-- Artikel Terkait --}}
    @if ($relatedArticles->isNotEmpty())
        <section class="guide-related-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-9">
                        <div class="d-flex align-items-baseline justify-content-between mb-4">
                            <h2 class="h5 fw-bold mb-0">Materi Edukasi Terkait</h2>
                            <a href="{{ route('edukasi.index') }}" class="small fw-semibold text-primary text-decoration-none">
                                Lihat Semua <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>

                        <div class="row g-3">
                            @foreach ($relatedArticles as $related)
                                <div class="col-md-4">
                                    <article class="guide-related-card">
                                        <div>
                                            <span class="guide-category-tag mb-2 d-inline-block">
                                                {{ $related->categoryLabel() }}
                                            </span>
                                            <h3 class="related-title">
                                                <a href="{{ route('edukasi.show', $related->slug) }}">
                                                    {{ $related->title }}
                                                </a>
                                            </h3>
                                        </div>

                                        <div class="related-meta">
                                            <span>
                                                <i class="bi bi-clock me-1"></i>{{ $related->reading_time_minutes }} mnt
                                            </span>
                                            <a href="{{ route('edukasi.show', $related->slug) }}" class="text-primary text-decoration-none fw-semibold">
                                                Baca <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
