@extends('layouts.app')

@section('title', $guide->title . ' — Panduan Keselamatan FireAware')

@section('content')
    {{-- Header Artikel (Editorial Header) --}}
    <header class="guide-detail-header">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    {{-- Navigasi Kembali --}}
                    <div>
                        <a href="{{ route('guides.index', ['kategori' => $guide->category]) }}" class="guide-back-nav">
                            <i class="bi bi-arrow-left"></i> Kembali ke Panduan {{ $guide->categoryLabel() }}
                        </a>
                    </div>

                    {{-- Tag Kategori & Waktu Baca --}}
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <a href="{{ route('guides.index', ['kategori' => $guide->category]) }}"
                           class="guide-category-tag">
                            <i class="bi {{ $guide->categoryIcon() }}"></i>
                            {{ $guide->categoryLabel() }}
                        </a>
                        <span class="text-secondary-soft small">&bull;</span>
                        <span class="text-secondary-soft small">
                            <i class="bi bi-clock me-1"></i>{{ $guide->reading_time_minutes }} menit baca
                        </span>
                    </div>

                    {{-- Judul Panduan --}}
                    <h1 class="guide-detail-title">
                        {{ $guide->title }}
                    </h1>

                    {{-- Metadata Publikasi & Sumber --}}
                    <div class="guide-detail-meta">
                        <div>
                            <span class="text-secondary-soft">Diterbitkan:</span>
                            <strong class="text-dark ms-1">
                                {{ $guide->effectiveDate()->isoFormat('D MMMM Y') }}
                            </strong>
                        </div>

                        @if ($guide->updated_at && $guide->updated_at->gt($guide->effectiveDate()->addDay()))
                            <div>
                                <span class="text-secondary-soft">Pembaruan:</span>
                                <strong class="text-dark ms-1">
                                    {{ $guide->updated_at->isoFormat('D MMMM Y') }}
                                </strong>
                            </div>
                        @endif

                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-shield-check text-success"></i>
                            <span class="text-secondary-soft">Rujukan:</span>
                            <span class="fw-semibold text-dark ms-1">{{ $guide->source_name }}</span>
                        </div>
                    </div>

                    {{-- Ringkasan Pengantar (Lead Text) --}}
                    <p class="guide-lead-text">
                        {{ $guide->summary }}
                    </p>
                </div>
            </div>
        </div>
    </header>

    {{-- Konten Artikel Panjang (Prose) --}}
    <main class="bg-soft">
        <div class="container">
            <div class="guide-prose-container">

                {{-- Banner Peringatan Keselamatan Khusus untuk Indikasi Kebakaran --}}
                @if ($guide->category === 'tindakan-ketika-melihat-indikasi-kebakaran' || Str::contains(strtolower($guide->title), ['kebakaran', 'titik api', 'lapor']))
                    <div class="safety-callout safety-danger">
                        <div class="safety-callout-icon">
                            <i class="bi bi-exclamation-octagon-fill"></i>
                        </div>
                        <div class="safety-callout-body">
                            <h4>Peringatan Keselamatan Nyawa</h4>
                            <p>
                                <strong>Dilarang mendekati titik api atau melakukan pemadaman mandiri di lahan berbahaya.</strong>
                                Kebakaran lahan gambut memiliki bara api di bawah tanah bersuhu tinggi dan asap beracun yang mematikan.
                                Utamakan evakuasi diri menjauhi arah tiupan asap dan segera laporkan koordinat/patokan lokasi ke pihak berwenang.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Badan Artikel --}}
                <article class="guide-prose">
                    @php
                        // Pemecahan paragraf dan markdown formatting sederhana yang aman
                        $paragraphs = $guide->contentParagraphs();
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
                                        // Format bold **text** secara aman
                                        $formatted = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', e($cleanItem));
                                    @endphp
                                    @if ($cleanItem !== '')
                                        <li>{!! $formatted !!}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            @php
                                // Format bold **text** dan italic *text* secara aman
                                $formattedPara = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', e($para));
                                $formattedPara = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $formattedPara);
                            @endphp
                            <p>{!! nl2br($formattedPara) !!}</p>
                        @endif
                    @endforeach
                </article>

                {{-- Komponen Sumber Rujukan Resmi --}}
                <section class="guide-source-panel" aria-label="Dasar rujukan resmi panduan ini">
                    <div class="source-label">
                        <i class="bi bi-patch-check-fill text-success"></i>
                        Dasar Rujukan Edukasi Keselamatan
                    </div>
                    <div class="source-title">
                        {{ $guide->source_name }}
                    </div>

                    @if ($guide->source_notes)
                        <div class="source-notes">
                            {{ $guide->source_notes }}
                        </div>
                    @endif

                    @if ($guide->source_url)
                        <div class="mb-3">
                            <a href="{{ $guide->source_url }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="source-link">
                                <span>Buka portal / rujukan asli</span>
                                <i class="bi bi-box-arrow-up-right"></i>
                            </a>
                        </div>
                    @endif

                    <div class="source-disclaimer">
                        <strong>Catatan Integritas Informasi:</strong>
                        Panduan ini disusun untuk memberikan pemahaman langkah awal perlindungan diri kepada masyarakat
                        Kalimantan berdasarkan literatur otoritas terkait. FireAware Kalimantan tidak menggantikan otoritas kedaruratan.
                        Dalam kondisi genting, selalu prioritaskan instruksi dari BPBD, Manggala Agni KLHK, dan Dinas Kesehatan setempat.
                    </div>
                </section>

                {{-- Tombol Navigasi Bawah --}}
                <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top border-soft">
                    <a href="{{ route('guides.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                        <i class="bi bi-arrow-left me-1"></i> Semua Panduan
                    </a>

                    <a href="{{ route('guides.index', ['kategori' => $guide->category]) }}" class="btn btn-primary btn-sm px-3">
                        Topik {{ $guide->categoryLabel() }} <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

            </div>
        </div>
    </main>

    {{-- Panduan Terkait (Jika Ada) --}}
    @if ($relatedGuides->isNotEmpty())
        <section class="guide-related-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-9">
                        <div class="d-flex align-items-baseline justify-content-between mb-4">
                            <h2 class="h5 fw-bold mb-0">Panduan Terkait Lainnya</h2>
                            <a href="{{ route('guides.index') }}" class="small fw-semibold text-primary text-decoration-none">
                                Lihat Semua <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>

                        <div class="row g-3">
                            @foreach ($relatedGuides as $related)
                                <div class="col-md-4">
                                    <article class="guide-related-card">
                                        <div>
                                            <span class="guide-category-tag mb-2 d-inline-block">
                                                {{ $related->categoryLabel() }}
                                            </span>
                                            <h3 class="related-title">
                                                <a href="{{ route('guides.show', $related->slug) }}">
                                                    {{ $related->title }}
                                                </a>
                                            </h3>
                                        </div>

                                        <div class="related-meta">
                                            <span>
                                                <i class="bi bi-clock me-1"></i>{{ $related->reading_time_minutes }} menit
                                            </span>
                                            <a href="{{ route('guides.show', $related->slug) }}" class="text-primary text-decoration-none fw-semibold">
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
