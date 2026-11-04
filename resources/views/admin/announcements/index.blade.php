@extends('layouts.admin')

@section('title', 'Kelola Pengumuman & Peringatan — Admin FireAware')
@section('page_title', 'Pengumuman')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1 fw-semibold">Kelola Pengumuman &amp; Peringatan Dini</h1>
            <p class="text-secondary-soft mb-0">
                Pemberitahuan resmi kebencanaan, status kedaruratan karhutla, dan peringatan cuaca.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Pengumuman Baru</span>
            </a>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-primary">
                        <i class="bi bi-megaphone"></i>
                    </span>
                    <div>
                        <div class="stat-value">{{ $stats['total'] }}</div>
                        <div class="stat-label text-secondary-soft">Total Pengumuman</div>
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
        <form method="GET" action="{{ route('admin.announcements.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-secondary-soft">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="search"
                           name="q"
                           value="{{ $search }}"
                           class="form-control border-start-0 ps-0"
                           placeholder="Cari pengumuman...">
                </div>
            </div>

            <div class="col-md-3">
                <select name="provinsi_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Wilayah</option>
                    @foreach ($provinces as $prov)
                        <option value="{{ $prov->id }}" {{ (string)$filterProvince === (string)$prov->id ? 'selected' : '' }}>
                            {{ $prov->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="level" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Level</option>
                    @foreach ($levels as $lvlKey => $meta)
                        <option value="{{ $lvlKey }}" {{ (string)$filterLevel === (string)$lvlKey ? 'selected' : '' }}>
                            {{ $meta['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="published" {{ $filterStatus === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ $filterStatus === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
                @if ($search !== '' || $filterProvince || $filterLevel || $filterStatus !== '')
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary" title="Reset filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Pengumuman --}}
    <div class="panel overflow-hidden">
        @if ($announcements->isNotEmpty())
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-soft">
                        <tr>
                            <th scope="col" style="min-width: 260px;">Judul &amp; Ringkasan</th>
                            <th scope="col">Level</th>
                            <th scope="col">Wilayah</th>
                            <th scope="col">Penerbit Resmi</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col">Tanggal Rilis</th>
                            <th scope="col" class="text-end" style="min-width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($announcements as $ann)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.announcements.show', $ann) }}" class="fw-semibold text-dark text-decoration-none d-block mb-1">
                                        {{ $ann->title }}
                                        @if ($ann->is_demo)
                                            <span class="badge-demo py-0 px-2 ms-1">DEMO</span>
                                        @endif
                                    </a>
                                    <div class="small text-secondary-soft text-truncate" style="max-width: 320px;">
                                        {{ $ann->excerpt(100) }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $ann->levelBadgeClass() }} px-2 py-1">
                                        <i class="bi {{ $ann->levelIcon() }} me-1"></i>
                                        {{ $ann->levelLabel() }}
                                    </span>
                                </td>
                                <td>
                                    @if ($ann->province)
                                        <span class="small fw-medium text-dark">
                                            <i class="bi bi-geo-alt me-1 text-primary"></i>{{ $ann->province->name }}
                                        </span>
                                    @else
                                        <span class="small text-secondary-soft">Seluruh Kalimantan</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="small fw-medium text-dark">{{ $ann->source_name ?? '—' }}</div>
                                    @if ($ann->source_url)
                                        <a href="{{ $ann->source_url }}" target="_blank" rel="noopener noreferrer" class="small text-primary text-decoration-none">
                                            <i class="bi bi-link-45deg"></i> tautan
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($ann->status === 'published')
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
                                        {{ $ann->effectiveDate()->isoFormat('D MMM Y, HH:mm') }}
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Aksi">
                                        <a href="{{ route('admin.announcements.show', $ann) }}"
                                           class="btn btn-outline-secondary"
                                           title="Lihat Detail Pengumuman">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.announcements.edit', $ann) }}"
                                           class="btn btn-outline-secondary"
                                           title="Edit Pengumuman">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button"
                                                class="btn btn-outline-danger"
                                                data-delete-url="{{ route('admin.announcements.destroy', $ann) }}"
                                                data-delete-title="{{ $ann->title }}"
                                                data-delete-type="pengumuman resmi"
                                                title="Hapus Pengumuman">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($announcements->hasPages())
                <div class="p-3 border-top border-soft d-flex justify-content-center">
                    {{ $announcements->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @else
            <div class="p-5 text-center">
                <i class="bi bi-megaphone mb-2 text-secondary-soft" style="font-size: 2.2rem;"></i>
                <h3 class="h6 fw-semibold mb-1">Belum ada pengumuman</h3>
                <p class="text-secondary-soft small mb-3">
                    @if ($search !== '' || $filterProvince || $filterLevel || $filterStatus !== '')
                        Tidak ada pengumuman yang cocok dengan filter yang Anda gunakan.
                    @else
                        Mulai terbitkan warta resmi atau status kewaspadaan karhutla.
                    @endif
                </p>
                <div class="d-flex justify-content-center gap-2">
                    @if ($search !== '' || $filterProvince || $filterLevel || $filterStatus !== '')
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
                        </a>
                    @endif
                    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Pengumuman Sekarang
                    </a>
                </div>
            </div>
        @endif
    </div>

    @include('admin.partials.delete-modal')
@endsection
