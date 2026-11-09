@extends('layouts.admin')

@section('title', 'Kelola Panduan Keselamatan — Admin FireAware')
@section('page_title', 'Panduan Keselamatan')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1 fw-semibold">Kelola Panduan Keselamatan</h1>
            <p class="text-secondary-soft mb-0">
                Kelola artikel edukasi mitigasi asap dan protokol keselamatan masyarakat Kalimantan.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.guides.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Panduan Baru</span>
            </a>
        </div>
    </div>

    {{-- Statistik Ringkas --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-primary">
                        <i class="bi bi-journal-text"></i>
                    </span>
                    <div>
                        <div class="stat-value">{{ $stats['total'] }}</div>
                        <div class="stat-label text-secondary-soft">Total Panduan</div>
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
                        <div class="stat-label text-secondary-soft">Published (Tayang)</div>
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

    {{-- Filter & Pencarian Admin --}}
    <div class="panel p-3 mb-4">
        <form method="GET" action="{{ route('admin.guides.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-secondary-soft">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="search"
                           name="q"
                           value="{{ $search }}"
                           class="form-control border-start-0 ps-0"
                           placeholder="Cari judul atau sumber...">
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
                    <a href="{{ route('admin.guides.index') }}" class="btn btn-outline-secondary" title="Reset filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Daftar Panduan --}}
    <div class="panel overflow-hidden">
        @if ($guides->isNotEmpty())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-soft">
                        <tr>
                            <th scope="col" style="width: 80px;" class="ps-3 text-center">Visual</th>
                            <th scope="col" style="min-width: 280px;">Judul &amp; Ringkasan</th>
                            <th scope="col" style="min-width: 160px;">Kategori</th>
                            <th scope="col" style="min-width: 160px;">Sumber Rujukan</th>
                            <th scope="col" class="text-center" style="width: 120px;">Status</th>
                            <th scope="col" style="width: 130px;">Waktu Terbit</th>
                            <th scope="col" class="text-end pe-3" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($guides as $guide)
                            <tr>
                                <td class="ps-3 text-center">
                                    <img src="{{ $guide->thumbnailImage() }}"
                                         alt="{{ $guide->thumbnailAlt() }}"
                                         class="content-table-thumb shadow-xs"
                                         loading="lazy">
                                </td>
                                <td>
                                    <a href="{{ route('admin.guides.show', $guide) }}" class="fw-semibold text-dark text-decoration-none d-block mb-1">
                                        {{ $guide->title }}
                                    </a>
                                    <div class="small text-secondary-soft text-truncate" style="max-width: 360px;">
                                        {{ $guide->summary }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border border-soft fw-medium">
                                        <i class="bi {{ $guide->categoryIcon() }} me-1"></i>
                                        {{ $guide->categoryLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small fw-medium text-dark">{{ $guide->source_name }}</div>
                                    @if ($guide->source_url)
                                        <a href="{{ $guide->source_url }}" target="_blank" rel="noopener noreferrer" class="small text-primary text-decoration-none d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-box-arrow-up-right" style="font-size: 0.75rem;"></i>
                                            <span>Rujukan</span>
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($guide->status === 'published')
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
                                        {{ $guide->effectiveDate()->isoFormat('D MMM Y') }}
                                    </div>
                                    <div class="small text-muted" style="font-size: .75rem;">
                                        <i class="bi bi-clock me-1"></i>{{ $guide->reading_time_minutes }} mnt baca
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-action-group" role="group" aria-label="Aksi">
                                        <a href="{{ route('admin.guides.show', $guide) }}"
                                           class="btn-action btn-action-view"
                                           title="Lihat Detail Panduan">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.guides.edit', $guide) }}"
                                           class="btn-action btn-action-edit"
                                           title="Edit Panduan">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn-action btn-action-delete"
                                                data-delete-url="{{ route('admin.guides.destroy', $guide) }}"
                                                data-delete-title="{{ $guide->title }}"
                                                data-delete-type="panduan keselamatan"
                                                title="Hapus Panduan">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($guides->hasPages())
                <div class="p-3 border-top border-soft d-flex justify-content-center">
                    {{ $guides->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <div class="p-5 text-center">
                <i class="bi bi-journal-x mb-2 text-secondary-soft" style="font-size: 2.2rem;"></i>
                <h3 class="h6 fw-semibold mb-1">Belum ada panduan keselamatan</h3>
                <p class="text-secondary-soft small mb-3">
                    @if ($search !== '' || $filterCategory !== '' || $filterStatus !== '')
                        Tidak ada panduan yang cocok dengan filter yang Anda gunakan.
                    @else
                        Mulai tambahkan panduan edukasi keselamatan masyarakat.
                    @endif
                </p>
                <div class="d-flex justify-content-center gap-2">
                    @if ($search !== '' || $filterCategory !== '' || $filterStatus !== '')
                        <a href="{{ route('admin.guides.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                        </a>
                    @endif
                    <a href="{{ route('admin.guides.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Panduan Sekarang
                    </a>
                </div>
            </div>
        @endif
    </div>

    @include('admin.partials.delete-modal')
@endsection
