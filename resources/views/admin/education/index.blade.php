@extends('layouts.admin')

@section('title', 'Kelola Edukasi Karhutla — Admin FireAware')
@section('page_title', 'Edukasi Karhutla')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1 fw-semibold">Kelola Artikel Edukasi Karhutla</h1>
            <p class="text-secondary-soft mb-0">
                Materi literasi sains, ekosistem gambut, dan pencegahan kebakaran hutan.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.education.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Artikel Edukasi</span>
            </a>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-primary">
                        <i class="bi bi-book"></i>
                    </span>
                    <div>
                        <div class="stat-value">{{ $stats['total'] }}</div>
                        <div class="stat-label text-secondary-soft">Total Artikel</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-success">
                        <i class="bi bi-check-circle"></i>
                    </span>
                    <div>
                        <div class="stat-value text-success">{{ $stats['published'] }}</div>
                        <div class="stat-label text-secondary-soft">Published</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-warning">
                        <i class="bi bi-pencil-square"></i>
                    </span>
                    <div>
                        <div class="stat-value text-warning">{{ $stats['draft'] }}</div>
                        <div class="stat-label text-secondary-soft">Draft</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="panel p-3 mb-4">
        <form method="GET" action="{{ route('admin.education.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-secondary-soft">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="search"
                           name="q"
                           value="{{ $search }}"
                           class="form-control border-start-0 ps-0"
                           placeholder="Cari judul atau penulis...">
                </div>
            </div>

            <div class="col-md-3">
                <select name="kategori" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $slug => $meta)
                        <option value="{{ $slug }}" {{ $filterCategory === $slug ? 'selected' : '' }}>
                            {{ $meta['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="published" {{ $filterStatus === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ $filterStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
                @if ($search !== '' || $filterCategory !== '' || $filterStatus !== '')
                    <a href="{{ route('admin.education.index') }}" class="btn btn-outline-secondary" title="Reset filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Artikel Edukasi --}}
    <div class="panel overflow-hidden">
        @if ($articles->isNotEmpty())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-soft">
                        <tr>
                            <th scope="col" style="width: 80px;" class="ps-3 text-center">Visual</th>
                            <th scope="col" style="min-width: 280px;">Judul &amp; Penulis</th>
                            <th scope="col" style="min-width: 160px;">Kategori</th>
                            <th scope="col" class="text-center" style="width: 120px;">Status</th>
                            <th scope="col" style="width: 130px;">Waktu Terbit</th>
                            <th scope="col" class="text-end pe-3" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($articles as $art)
                            <tr>
                                <td class="ps-3 text-center">
                                    <img src="{{ $art->imageUrl() ?? $art->displayImageUrl() }}"
                                         alt="{{ $art->displayImageAlt() }}"
                                         class="content-table-thumb shadow-xs"
                                         loading="lazy">
                                </td>
                                <td>
                                    <a href="{{ route('admin.education.show', $art) }}" class="fw-semibold text-dark text-decoration-none d-block mb-1">
                                        {{ $art->title }}
                                        @if ($art->is_demo)
                                            <span class="badge-demo py-0 px-2 ms-1">DEMO</span>
                                        @endif
                                    </a>
                                    <div class="small text-secondary-soft">
                                        @if ($art->source_name)
                                            <span class="text-dark fw-medium">{{ $art->source_name }}</span>
                                            @if ($art->author)
                                                &bull; <span class="text-secondary-soft">{{ $art->author }}</span>
                                            @endif
                                        @else
                                            Oleh: {{ $art->author ?? 'Tim Redaksi' }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border border-soft fw-medium">
                                        <i class="bi {{ $art->categoryIcon() }} me-1"></i>
                                        {{ $art->categoryLabel() }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($art->status === 'published')
                                        <span class="badge badge-status-published d-inline-flex align-items-center gap-1 px-2 py-1">
                                            <i class="bi bi-check-circle-fill"></i>
                                            <span>Published</span>
                                        </span>
                                    @else
                                        <span class="badge badge-status-draft d-inline-flex align-items-center gap-1 px-2 py-1">
                                            <i class="bi bi-clock"></i>
                                            <span>Draft</span>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small text-secondary-soft fw-medium">
                                        {{ $art->effectiveDate()->isoFormat('D MMM Y') }}
                                    </div>
                                    <div class="small text-muted" style="font-size: .75rem;">
                                        <i class="bi bi-clock me-1"></i>{{ $art->reading_time_minutes }} mnt baca
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-action-group" role="group" aria-label="Aksi">
                                        <a href="{{ route('admin.education.show', $art) }}"
                                           class="btn-action btn-action-view"
                                           title="Lihat Detail Artikel">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.education.edit', $art) }}"
                                           class="btn-action btn-action-edit"
                                           title="Edit Artikel">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn-action btn-action-delete"
                                                data-delete-url="{{ route('admin.education.destroy', $art) }}"
                                                data-delete-title="{{ $art->title }}"
                                                data-delete-type="artikel edukasi"
                                                title="Hapus Artikel">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($articles->hasPages())
                <div class="p-3 border-top border-soft d-flex justify-content-center">
                    {{ $articles->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <div class="p-5 text-center">
                <i class="bi bi-book mb-2 text-secondary-soft" style="font-size: 2.2rem;"></i>
                <h3 class="h6 fw-semibold mb-1">Belum ada artikel edukasi</h3>
                <p class="text-secondary-soft small mb-3">
                    @if ($search !== '' || $filterCategory !== '' || $filterStatus !== '')
                        Tidak ada artikel yang cocok dengan filter yang Anda gunakan.
                    @else
                        Mulai tambahkan materi literasi karhutla dan ekosistem gambut.
                    @endif
                </p>
                <div class="d-flex justify-content-center gap-2">
                    @if ($search !== '' || $filterCategory !== '' || $filterStatus !== '')
                        <a href="{{ route('admin.education.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                        </a>
                    @endif
                    <a href="{{ route('admin.education.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Artikel Sekarang
                    </a>
                </div>
            </div>
        @endif
    </div>

    @include('admin.partials.delete-modal')
@endsection
