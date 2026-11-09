@extends('layouts.admin')

@section('title', 'Detail Pengumuman: ' . $announcement->title . ' — Admin FireAware')
@section('page_title', 'Detail Pengumuman & Peringatan')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('admin.announcements.index') }}" class="text-decoration-none small text-secondary fw-semibold d-inline-flex align-items-center gap-1 mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pengumuman
            </a>
            <h1 class="h4 mb-1 fw-semibold text-dark">{{ $announcement->title }}</h1>
            <div class="d-flex flex-wrap align-items-center gap-2 small text-secondary-soft">
                <span class="badge {{ $announcement->levelBadgeClass() }} border px-2 py-1">
                    <i class="bi {{ $announcement->levelIcon() }} me-1"></i> {{ $announcement->levelLabel() }}
                </span>
                <span>&bull;</span>
                <span>Wilayah: <strong>{{ $announcement->province?->name ?? 'Seluruh Wilayah Kalimantan' }}</strong></span>
                <span>&bull;</span>
                <span>Slug: <code>{{ $announcement->slug }}</code></span>
                @if ($announcement->is_demo)
                    <span>&bull;</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary">DEMO</span>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if ($announcement->status === 'published')
                <a href="{{ route('announcements.show', $announcement->slug) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Lihat Halaman Publik</span>
                </a>
            @endif
            <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
                <i class="bi bi-pencil"></i>
                <span>Edit Pengumuman</span>
            </a>
            <button type="button"
                    class="btn btn-outline-danger d-inline-flex align-items-center gap-1"
                    data-delete-url="{{ route('admin.announcements.destroy', $announcement) }}"
                    data-delete-title="{{ $announcement->title }}"
                    data-delete-type="pengumuman resmi">
                <i class="bi bi-trash"></i>
                <span>Hapus</span>
            </button>
        </div>
    </div>

    {{-- Banner Status Publikasi --}}
    @if ($announcement->status === 'published')
        <div class="alert alert-success border-success border-opacity-25 bg-soft p-3 mb-4 rounded-3 d-flex align-items-center gap-3">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <div class="small">
                <strong class="d-block text-success">Status: Published (Tayang untuk Publik)</strong>
                <span class="text-secondary-soft">
                    Pengumuman resmi ini telah terbit sejak {{ $announcement->effectiveDate()->isoFormat('D MMMM Y, HH:mm') }} WIB dan dapat dilihat warga di kanal pengumuman FireAware.
                </span>
            </div>
        </div>
    @else
        <div class="alert alert-warning border-warning border-opacity-25 bg-soft p-3 mb-4 rounded-3 d-flex align-items-center gap-3">
            <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
            <div class="small">
                <strong class="d-block text-warning">Status: Draf (Belum Terbit)</strong>
                <span class="text-secondary-soft">
                    Pengumuman ini berstatus draf. URL publik <code>/pengumuman/{{ $announcement->slug }}</code> akan merespons 404 Not Found hingga status diubah menjadi Published.
                </span>
            </div>
        </div>
    @endif

    <div class="row g-4">
        {{-- Kolom Kiri: Isi Pengumuman & Sumber --}}
        <div class="col-lg-8">
            {{-- Kartu Ringkasan --}}
            @if ($announcement->summary)
                <div class="panel p-4 mb-4">
                    <h2 class="h6 fw-semibold text-secondary-soft text-uppercase tracking-wider mb-2">Ringkasan Warta</h2>
                    <p class="fs-6 text-dark mb-0 lh-base">
                        {{ $announcement->summary }}
                    </p>
                </div>
            @endif

            {{-- Kartu Isi Pengumuman --}}
            <div class="panel p-4 mb-4">
                <h2 class="h6 fw-semibold pb-2 border-bottom border-soft mb-3">Isi Teks Lengkap Pengumuman</h2>
                <div class="article-body">
                    @forelse ($announcement->bodyParagraphs() as $paragraph)
                        @if (str_starts_with($paragraph, '### '))
                            <h3 class="h5 fw-semibold text-dark mt-4 mb-2">{{ trim(substr($paragraph, 4)) }}</h3>
                        @elseif (str_starts_with($paragraph, '- '))
                            <div class="d-flex align-items-start gap-2 mb-2">
                                <i class="bi bi-chevron-right text-primary mt-1 small"></i>
                                <span>{{ trim(substr($paragraph, 2)) }}</span>
                            </div>
                        @else
                            <p class="mb-3 lh-base text-dark">{{ $paragraph }}</p>
                        @endif
                    @empty
                        <p class="text-secondary-soft fst-italic mb-0">Isi warta belum dicantumkan.</p>
                    @endforelse
                </div>
            </div>

            {{-- Kartu Instansi Penerbit --}}
            <div class="panel p-4">
                <h2 class="h6 fw-semibold pb-2 border-bottom border-soft mb-3">Instansi Penerbit &amp; Rujukan Resmi</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="small text-secondary-soft mb-1">Nama Instansi / Otoritas:</div>
                        <div class="fw-semibold text-dark">{{ $announcement->source_name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-secondary-soft mb-1">Tautan Rilis Asli:</div>
                        @if ($announcement->source_url)
                            <a href="{{ $announcement->source_url }}" target="_blank" rel="noopener noreferrer" class="text-primary text-break small text-decoration-none">
                                <i class="bi bi-link-45deg"></i> {{ $announcement->source_url }}
                            </a>
                        @else
                            <span class="text-secondary-soft small fst-italic">Tidak ada tautan eksternal</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Metadata & Status --}}
        <div class="col-lg-4">
            <div class="panel p-4 mb-4">
                <h2 class="h6 fw-semibold pb-2 border-bottom border-soft mb-3">Atribut Pengumuman</h2>
                <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small">
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Tingkat Urgensi</span>
                        <span class="badge {{ $announcement->levelBadgeClass() }} border px-2 py-1">
                            {{ $announcement->levelLabel() }}
                        </span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Cakupan Wilayah</span>
                        <span class="fw-semibold text-dark text-end">
                            {{ $announcement->province?->name ?? 'Seluruh Kalimantan' }}
                        </span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Status Publikasi</span>
                        @if ($announcement->status === 'published')
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
                        <span class="text-secondary-soft">Tanggal Publikasi</span>
                        <span class="fw-semibold text-dark">{{ $announcement->effectiveDate()->isoFormat('D MMM Y, HH:mm') }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Dibuat Pada</span>
                        <span class="text-dark">{{ $announcement->created_at->isoFormat('D MMM Y, HH:mm') }}</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center">
                        <span class="text-secondary-soft">Terakhir Diperbarui</span>
                        <span class="text-dark">{{ $announcement->updated_at->isoFormat('D MMM Y, HH:mm') }}</span>
                    </li>
                </ul>
            </div>

            <div class="panel p-4 bg-soft border-soft">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-shield-exclamation text-primary fs-5"></i>
                    <h3 class="h6 mb-0 fw-semibold text-dark">Pedoman Penyiaran Warta</h3>
                </div>
                <p class="small text-secondary-soft mb-0 lh-base">
                    Pengumuman tingkat <strong>Waspada</strong> atau <strong>Siaga Darurat</strong> akan ditampilkan pada bilah peringatan utama di portal publik FireAware untuk keselamatan warga sekitar.
                </p>
            </div>
        </div>
    </div>

    @include('admin.partials.delete-modal')
@endsection
