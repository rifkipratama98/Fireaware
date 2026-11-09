@extends('layouts.app')

@section('title', $article->title . ' — Edukasi FireAware Kalimantan')

@section('content')
    {{-- Header Artikel Edukasi --}}
    <header class="edu-detail-hero">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <a href="{{ route('edukasi.index', ['kategori' => $article->category]) }}" class="guide-back-nav">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Edukasi {{ $article->categoryLabel() }}
                    </a>

                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <a href="{{ route('edukasi.index', ['kategori' => $article->category]) }}" class="guide-category-tag">
                            <i class="bi {{ $article->categoryIcon() }}" aria-hidden="true"></i>
                            {{ $article->categoryLabel() }}
                        </a>
                        <span class="text-secondary small">&bull;</span>
                        <span class="text-secondary small">
                            <i class="bi bi-clock me-1" aria-hidden="true"></i>{{ $article->reading_time_minutes }} menit baca
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
                            <span class="text-secondary">Penulis:</span>
                            <strong class="text-dark ms-1">{{ $article->author ?? 'Tim Redaksi FireAware' }}</strong>
                        </div>

                        <div>
                            <span class="text-secondary">Diterbitkan:</span>
                            <strong class="text-dark ms-1">{{ $article->effectiveDate()->isoFormat('D MMMM Y') }}</strong>
                        </div>

                        @if ($article->updated_at && $article->updated_at->gt($article->effectiveDate()->addDay()))
                            <div>
                                <span class="text-secondary">Pembaruan:</span>
                                <strong class="text-dark ms-1">{{ $article->updated_at->isoFormat('D MMMM Y') }}</strong>
                            </div>
                        @endif

                        @if ($article->source_name)
                            <div class="d-flex align-items-center gap-1">
                                <i class="bi bi-patch-check-fill text-success" aria-hidden="true"></i>
                                <span class="text-secondary">Sumber:</span>
                                <strong class="text-dark ms-1">{{ $article->source_name }}</strong>
                            </div>
                        @endif
                    </div>

                    <p class="guide-lead-text">
                        {{ $article->summary }}
                    </p>

                    {{-- Gambar Cover / Ilustrasi Artikel Edukasi --}}
                    <div class="edu-detail-img-wrap">
                        <img src="{{ $article->displayImageUrl() }}"
                             alt="{{ $article->displayImageAlt() }}"
                             loading="lazy"
                             width="860"
                             height="440">
                        <div class="img-caption">
                            <i class="bi bi-camera me-1" aria-hidden="true"></i> {{ $article->displayImageAlt() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Konten Artikel Panjang --}}
    <main class="bg-soft">
        <div class="container">
            <div class="guide-prose-container">

                {{-- 6. Ekstraksi Daftar Isi (Table of Contents) --}}
                @php
                    $paragraphs = $article->contentParagraphs();
                    $toc = [];
                    foreach ($paragraphs as $idx => $p) {
                        if (Str::startsWith($p, '### ') || Str::startsWith($p, '## ')) {
                            $hText = Str::startsWith($p, '### ') ? Str::after($p, '### ') : Str::after($p, '## ');
                            $toc[] = [
                                'title' => trim($hText),
                                'anchor' => 'bagian-' . ($idx + 1),
                                'level' => Str::startsWith($p, '## ') ? 2 : 3,
                            ];
                        }
                    }
                @endphp

                @if (count($toc) >= 2)
                    <div class="guide-toc-card">
                        <div class="toc-title">
                            <i class="bi bi-list-nested text-primary" aria-hidden="true"></i>
                            <span>Daftar Isi Artikel</span>
                            <span class="badge bg-light text-secondary border border-soft ms-auto small fw-normal">{{ count($toc) }} Bagian</span>
                        </div>
                        <ol>
                            @foreach ($toc as $item)
                                <li>
                                    <a href="#{{ $item['anchor'] }}">{{ $item['title'] }}</a>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif

                <article class="guide-prose">
                    @foreach ($paragraphs as $para)
                        @if (Str::startsWith($para, '### '))
                            <h3 id="bagian-{{ $loop->index + 1 }}">{{ Str::after($para, '### ') }}</h3>
                        @elseif (Str::startsWith($para, '## '))
                            <h2 id="bagian-{{ $loop->index + 1 }}">{{ Str::after($para, '## ') }}</h2>
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

                {{-- Sumber Eksternal jika ada --}}
                @if ($article->source_url)
                    <div class="p-3 bg-white rounded-3 border border-soft mt-4 d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-sm">
                        <div class="small text-secondary">
                            <i class="bi bi-link-45deg me-1"></i>
                            Rujukan ilmiah asli: <strong>{{ $article->source_name ?? 'Portal Penelitian Lingkungan' }}</strong>
                        </div>
                        <a href="{{ $article->source_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary btn-sm">
                            Buka Rujukan Asli <i class="bi bi-box-arrow-up-right ms-1"></i>
                        </a>
                    </div>
                @endif

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

    {{-- 7. Artikel Terkait dengan Thumbnail Gambar --}}
    @if ($relatedArticles->isNotEmpty())
        <section class="guide-related-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-9">
                        <div class="d-flex align-items-baseline justify-content-between mb-4">
                            <h2 class="h5 fw-bold mb-0 text-dark">
                                <i class="bi bi-mortarboard me-1 text-primary" aria-hidden="true"></i>Materi Edukasi Terkait
                            </h2>
                            <a href="{{ route('edukasi.index') }}" class="small fw-semibold text-primary text-decoration-none">
                                Lihat Semua <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>

                        <div class="row g-3">
                            @foreach ($relatedArticles as $related)
                                <div class="col-md-4">
                                    <article class="guide-related-card">
                                        <div class="related-img-wrap">
                                            <a href="{{ route('edukasi.show', $related->slug) }}" tabindex="-1" aria-hidden="true">
                                                <img src="{{ $related->displayImageUrl() }}"
                                                     alt="{{ $related->displayImageAlt() }}"
                                                     loading="lazy"
                                                     width="320"
                                                     height="140">
                                            </a>
                                        </div>

                                        <div class="related-body">
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
