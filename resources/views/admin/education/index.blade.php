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
                            <th scope="col" style="min-width: 280px;">Judul &amp; Penulis</th>
                            <th scope="col">Kategori</th>
                            <th scope="col" class="text-center">Gambar</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col">Waktu Terbit</th>
                            <th scope="col" class="text-end" style="min-width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($articles as $art)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark mb-1">
                                        {{ $art->title }}
                                        @if ($art->is_demo)
                                            <span class="badge-demo py-0 px-2 ms-1">DEMO</span>
                                        @endif
                                    </div>
                                    <div class="small text-secondary-soft">
                                        Oleh: {{ $art->author ?? 'Redaksi' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border border-soft fw-medium">
                                        <i class="bi {{ $art->categoryIcon() }} me-1"></i>
                                        {{ $art->categoryLabel() }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($art->imageUrl())
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                            <i class="bi bi-image"></i> Ada
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($art->status === 'published')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                                            <i class="bi bi-check-circle me-1"></i> Published
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">
                                            <i class="bi bi-clock me-1"></i> Draft
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small text-secondary-soft">
                                        {{ $art->effectiveDate()->isoFormat('D MMM Y') }}
                                    </div>
                                    <div class="small text-muted" style="font-size: .75rem;">
                                        {{ $art->reading_time_minutes }} menit baca
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        @if ($art->status === 'published')
                                            <a href="{{ route('edukasi.show', $art->slug) }}"
                                               target="_blank"
                                               class="btn btn-outline-secondary"
                                               title="Lihat di Publik">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.education.edit', $art) }}"
                                           class="btn btn-outline-primary"
                                           title="Edit Artikel">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                onclick="if(confirm('Yakin ingin menghapus artikel edukasi: {{ addslashes($art->title) }}?')) document.getElementById('del-art-{{ $art->id }}').submit();"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <form id="del-art-{{ $art->id }}"
                                          action="{{ route('admin.education.destroy', $art) }}"
                                          method="POST"
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
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
                <p class="text-secondary-soft small mb-3">Mulai tambahkan materi literasi karhutla dan ekosistem gambut.</p>
                <a href="{{ route('admin.education.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Artikel Sekarang
                </a>
            </div>
        @endif
    </div>
@endsection
