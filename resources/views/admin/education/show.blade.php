@extends('layouts.admin')

@section('title', 'Detail Edukasi: ' . $article->title . ' — Admin FireAware')
@section('page_title', 'Detail Artikel Edukasi')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.education.index') }}" class="text-decoration-none small text-secondary fw-semibold d-inline-flex align-items-center gap-1 mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Edukasi
            </a>
            <h1 class="h4 mb-1 fw-semibold text-dark">{{ $article->title }}</h1>
            <div class="d-flex flex-wrap align-items-center gap-2 small text-secondary-soft">
                <span class="badge bg-light text-primary border border-soft fw-medium">
                    <i class="bi {{ $article->categoryIcon() }} me-1"></i> {{ $article->categoryLabel() }}
                </span>
                <span>&bull;</span>
                <span>Slug: <code>{{ $article->slug }}</code></span>
                <span>&bull;</span>
                <span><i class="bi bi-clock me-1"></i>{{ $article->reading_time_minutes }} menit baca</span>
                @if ($article->is_demo)
                    <span>&bull;</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary">DEMO</span>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if ($article->status === 'published')
                <a href="{{ route('edukasi.show', $article->slug) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Lihat Halaman Publik</span>
                </a>
            @endif
            <a href="{{ route('admin.education.edit', $article) }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-pencil"></i>
                <span>Edit Artikel</span>
            </a>
            <button type="button"
                    class="btn btn-outline-danger d-inline-flex align-items-center gap-1"
                    data-delete-url="{{ route('admin.education.destroy', $article) }}"
                    data-delete-title="{{ $article->title }}"
                    data-delete-type="artikel edukasi">
                <i class="bi bi-trash"></i>
                <span>Hapus</span>
            </button>
        </div>
    </div>

    {{-- Banner Status Publikasi --}}
    @if ($article->status === 'published')
        <div class="alert alert-success border-success border-opacity-25 bg-soft p-3 mb-4 rounded-3 d-flex align-items-center gap-3">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <div class="small">
                <strong class="d-block text-success">Status: Published (Tayang untuk Publik)</strong>
                <span class="text-secondary-soft">
                    Artikel ini tayang untuk publik sejak {{ $article->effectiveDate()->isoFormat('D MMMM Y, HH:mm') }} WIB.
                </span>
            </div>
        </div>
    @else
        <div class="alert alert-warning border-warning border-opacity-25 bg-soft p-3 mb-4 rounded-3 d-flex align-items-center gap-3">
            <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
            <div class="small">
                <strong class="d-block text-warning">Status: Draf (Hanya Dapat Dilihat Admin)</strong>
                <span class="text-secondary-soft">
                    Artikel ini berstatus draf. Akses publik ke URL <code>/edukasi/{{ $article->slug }}</code> akan diblokir dengan respons 404 Not Found.
                </span>
    <div class="row g-4">
        {{-- Kolom Kiri: Konten Utama & Media --}}
        <div class="col-lg-8">
            @if ($article->imageUrl())
                <div class="panel p-3 mb-4">
                    <img src="{{ $article->imageUrl() }}"
                         alt="{{ $article->title }}"
                         class="img-fluid rounded-2 w-100"
                         style="max-height: 380px; object-fit: cover;">
                    @if ($article->image_caption)
                        <div class="small text-secondary-soft text-center mt-2">
                            <i class="bi bi-camera me-1"></i> {{ $article->image_caption }}
                        </div>
                    @endif
                </div>
            @endif

            <div class="panel p-4 mb-4">
                <h2 class="h6 fw-semibold text-secondary-soft text-uppercase tracking-wider mb-2">Ringkasan / Abstrak</h2>
                <p class="fs-6 text-dark mb-0 lh-base">
                    {{ $article->summary }}
                </p>
            </div>

            <div class="panel p-4 mb-4">
                <h2 class="h6 fw-semibold pb-2 border-bottom border-soft mb-3">Isi Lengkap Artikel Edukasi</h2>
                <div class="article-body">
                    @forelse ($article->contentParagraphs() as $paragraph)
                        @if (str_starts_with($paragraph, '### '))
                            <h3 class="h5 fw-semibold text-dark mt-4 mb-2">{{ trim(substr($paragraph, 4)) }}</h3>
                        @elseif (str_starts_with($paragraph, '- '))
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <i class="bi bi-check2 text-primary mt-1"></i>
                                <span>{{ trim(substr($paragraph, 2)) }}</span>
                            </div>
                        @else
                            <p class="mb-3 lh-base text-dark">{{ $paragraph }}</p>
                        @endif
                    @empty
                        <p class="text-secondary-soft fst-italic mb-0">Konten artikel belum diisi.</p>
                    @endforelse
                </div>
            </div>

            <div class="panel p-4">
                <h2 class="h6 fw-semibold pb-2 border-bottom border-soft mb-3">Dasar Sumber Rujukan Ilmiah</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="small text-secondary-soft mb-1">Lembaga / Sumber Rujukan:</div>
                        <div class="fw-semibold text-dark">{{ $article->source_name ?: 'Tidak dicantumkan' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-secondary-soft mb-1">Penulis / Kontributor:</div>
                        <div class="fw-semibold text-dark">{{ $article->author ?: 'Tim Redaksi FireAware' }}</div>
                    </div>
                    @if ($article->source_url)
                        <div class="col-12 pt-2 border-top border-soft">
                            <div class="small text-secondary-soft mb-1">Tautan Sumber:</div>
                            <a href="{{ $article->source_url }}" target="_blank" rel="noopener noreferrer" class="text-primary text-break small text-decoration-none">
                                <i class="bi bi-link-45deg"></i> {{ $article->source_url }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

            </div>
        </div>
    @endif

        {{-- Kolom Kanan: Metadata --}}
        <div class="col-lg-4">
            <div class="panel p-4 mb-4">
                <h2 class="h6 fw-semibold pb-2 border-bottom border-soft mb-3">Atribut Artikel</h2>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small">
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Kategori</span>
                        <span class="fw-semibold text-dark text-end">{{ $article->categoryLabel() }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Status</span>
                        @if ($article->status === 'published')
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                <i class="bi bi-check-circle me-1"></i> Published
                            </span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">
                                <i class="bi bi-clock me-1"></i> Draft
                            </span>
                        @endif
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Estimasi Baca</span>
                        <span class="fw-semibold text-dark">{{ $article->reading_time_minutes }} Menit</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Tanggal Terbit</span>
                        <span class="fw-semibold text-dark">{{ $article->effectiveDate()->isoFormat('D MMM Y, HH:mm') }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Dibuat Pada</span>
                        <span class="text-dark">{{ $article->created_at->isoFormat('D MMM Y, HH:mm') }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Terakhir Diperbarui</span>
                        <span class="text-dark">{{ $article->updated_at->isoFormat('D MMM Y, HH:mm') }}</span>
                    </li>
                </ul>
            </div>

            <div class="panel p-4 bg-soft border-soft">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi {{ $article->categoryIcon() }} text-primary fs-5"></i>
                    <h3 class="h6 mb-0 fw-semibold text-dark">{{ $article->categoryLabel() }}</h3>
                </div>
                <p class="small text-secondary-soft mb-0 lh-base">
                    {{ $article->categoryDescription() }}
                </p>
            </div>
        </div>
    </div>

    @include('admin.partials.delete-modal')
@endsection
