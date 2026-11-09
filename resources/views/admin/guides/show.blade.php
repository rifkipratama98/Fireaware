@extends('layouts.admin')

@section('title', 'Detail Panduan: ' . $guide->title . ' — Admin FireAware')
@section('page_title', 'Detail Panduan Keselamatan')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.guides.index') }}" class="text-decoration-none small text-secondary fw-semibold d-inline-flex align-items-center gap-1 mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Panduan
            </a>
            <h1 class="h4 mb-1 fw-semibold text-dark">{{ $guide->title }}</h1>
            <div class="d-flex flex-wrap align-items-center gap-2 small text-secondary-soft">
                <span class="badge bg-light text-primary border border-soft fw-medium">
                    <i class="bi {{ $guide->categoryIcon() }} me-1"></i> {{ $guide->categoryLabel() }}
                </span>
                <span>&bull;</span>
                <span>Slug: <code>{{ $guide->slug }}</code></span>
                <span>&bull;</span>
                <span><i class="bi bi-clock me-1"></i>{{ $guide->reading_time_minutes }} menit baca</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if ($guide->status === 'published')
                <a href="{{ route('guides.show', $guide->slug) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Lihat Halaman Publik</span>
                </a>
            @endif
            <a href="{{ route('admin.guides.edit', $guide) }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-pencil"></i>
                <span>Edit Panduan</span>
            </a>
            <button type="button"
                    class="btn btn-outline-danger d-inline-flex align-items-center gap-1"
                    data-delete-url="{{ route('admin.guides.destroy', $guide) }}"
                    data-delete-title="{{ $guide->title }}"
                    data-delete-type="panduan keselamatan">
                <i class="bi bi-trash"></i>
                <span>Hapus</span>
            </button>
        </div>
    </div>

    {{-- Banner Status Publikasi --}}
    @if ($guide->status === 'published')
        <div class="alert alert-success border-success border-opacity-25 bg-soft p-3 mb-4 rounded-3 d-flex align-items-center gap-3">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <div class="small">
                <strong class="d-block text-success">Status: Published (Tayang untuk Publik)</strong>
                <span class="text-secondary-soft">
                    Panduan ini telah terbit sejak {{ $guide->effectiveDate()->isoFormat('D MMMM Y, HH:mm') }} WIB dan dapat dibaca oleh masyarakat Kalimantan di portal FireAware.
                </span>
            </div>
        </div>
    @else
        <div class="alert alert-warning border-warning border-opacity-25 bg-soft p-3 mb-4 rounded-3 d-flex align-items-center gap-3">
            <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
            <div class="small">
                <strong class="d-block text-warning">Status: Draf (Hanya Dapat Dilihat Admin)</strong>
                <span class="text-secondary-soft">
                    Konten ini belum dipublikasikan dan terlindungi dari akses publik. URL publik akan mengembalikan respons 404 Not Found hingga Anda menerbitkannya.
                </span>
            </div>
        </div>
    @endif

    <div class="row g-4">
        {{-- Kolom Kiri: Isi Lengkap & Rujukan --}}
        <div class="col-lg-8">
            {{-- Preview Gambar Tematik --}}
            <div class="panel p-3 mb-4">
                <img src="{{ $guide->thumbnailImage() }}"
                     alt="{{ $guide->thumbnailAlt() }}"
                     class="img-fluid rounded-2 w-100"
                     style="max-height: 320px; object-fit: cover;">
                <div class="small text-secondary-soft text-center mt-2">
                    <i class="bi bi-image me-1"></i> {{ $guide->thumbnailAlt() }}
                </div>
            </div>
            <div class="panel p-4 mb-4">
                <h2 class="h6 fw-semibold text-secondary-soft text-uppercase tracking-wider mb-2">Ringkasan / Pengantar Singkat</h2>
                <p class="fs-6 text-dark mb-0 lh-base">
                    {{ $guide->summary }}
                </p>
            </div>

            <div class="panel p-4 mb-4">
                <h2 class="h6 fw-semibold pb-2 border-bottom border-soft mb-3">Isi Teks Panduan Keselamatan</h2>
                <div class="article-body">
                    @forelse ($guide->contentParagraphs() as $paragraph)
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
                        <p class="text-secondary-soft fst-italic mb-0">Konten panduan belum diisi.</p>
                    @endforelse
                </div>
            </div>

            <div class="panel p-4">
                <h2 class="h6 fw-semibold pb-2 border-bottom border-soft mb-3">Dasar Sumber Rujukan Resmi</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="small text-secondary-soft mb-1">Instansi / Lembaga Sumber:</div>
                        <div class="fw-semibold text-dark">{{ $guide->source_name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-secondary-soft mb-1">Tautan Rujukan Dokumen:</div>
                        @if ($guide->source_url)
                            <a href="{{ $guide->source_url }}" target="_blank" rel="noopener noreferrer" class="text-primary text-break small text-decoration-none">
                                <i class="bi bi-link-45deg"></i> {{ $guide->source_url }}
                            </a>
                        @else
                            <span class="text-secondary-soft small fst-italic">Tidak ada tautan eksternal</span>
                        @endif
                    </div>
                    @if ($guide->source_notes)
                        <div class="col-12 pt-2 border-top border-soft">
                            <div class="small text-secondary-soft mb-1">Catatan Dokumen / Nomor Edaran:</div>
                            <div class="small text-dark">{{ $guide->source_notes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- Kolom Kanan: Metadata & Atribut --}}
        <div class="col-lg-4">
            <div class="panel p-4 mb-4">
                <h2 class="h6 fw-semibold pb-2 border-bottom border-soft mb-3">Atribut Dokumen</h2>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small">
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Kategori</span>
                        <span class="fw-semibold text-dark text-end">{{ $guide->categoryLabel() }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Status</span>
                        @if ($guide->status === 'published')
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
                        <span class="fw-semibold text-dark">{{ $guide->reading_time_minutes }} Menit</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Tanggal Terbit</span>
                        <span class="fw-semibold text-dark">{{ $guide->effectiveDate()->isoFormat('D MMM Y, HH:mm') }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Dibuat Pada</span>
                        <span class="text-dark">{{ $guide->created_at->isoFormat('D MMM Y, HH:mm') }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Terakhir Diperbarui</span>
                        <span class="text-dark">{{ $guide->updated_at->isoFormat('D MMM Y, HH:mm') }}</span>
                    </li>
                </ul>
            </div>

            <div class="panel p-4 bg-soft border-soft">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi {{ $guide->categoryIcon() }} text-primary fs-5"></i>
                    <h3 class="h6 mb-0 fw-semibold text-dark">{{ $guide->categoryLabel() }}</h3>
                </div>
                <p class="small text-secondary-soft mb-0 lh-base">
                    {{ $guide->categoryDescription() }}
                </p>
            </div>
        </div>
    </div>

    @include('admin.partials.delete-modal')
@endsection
