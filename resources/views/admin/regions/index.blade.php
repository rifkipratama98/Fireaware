@extends('layouts.admin')

@section('title', 'Informasi Wilayah — Admin FireAware Kalimantan')
@section('page_title', 'Informasi Wilayah')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1 fw-bold">Informasi Wilayah Kalimantan</h1>
            <p class="text-secondary-soft mb-0">
                Pemantauan cakupan 5 provinsi, stasiun kualitas udara, dan persebaran titik panas.
            </p>
        </div>
        <div>
            <a href="{{ route('hotspot.map') }}" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1" target="_blank" rel="noopener">
                <i class="bi bi-map"></i>
                <span>Buka Peta Interaktif</span>
            </a>
        </div>
    </div>

    {{-- Ringkasan Wilayah --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-primary"><i class="bi bi-geo-alt"></i></span>
                    <div>
                        <div class="stat-value">{{ $totals['provinces'] }}</div>
                        <div class="stat-label text-secondary-soft">Provinsi Terdaftar</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-danger"><i class="bi bi-fire"></i></span>
                    <div>
                        <div class="stat-value">{{ number_format($totals['hotspots_7d']) }}</div>
                        <div class="stat-label text-secondary-soft">Titik Panas (7 Hari)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-primary"><i class="bi bi-database"></i></span>
                    <div>
                        <div class="stat-value">{{ number_format($totals['hotspots_total']) }}</div>
                        <div class="stat-label text-secondary-soft">Total Titik Panas</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">

    {{-- Tabel Wilayah --}}
    <div class="panel p-0 overflow-hidden mb-4">
        <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
            <h2 class="h6 fw-bold mb-0">Daftar Provinsi &amp; Metrik Terkait</h2>
            <span class="badge bg-mist text-primary border">Data Spasial Aktual</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light small">
                    <tr>
                        <th class="ps-3 py-3">Provinsi</th>
                        <th class="py-3">Kode</th>
                        <th class="py-3">Titik Panas (7 Hari)</th>
                        <th class="py-3">Total Titik Panas</th>
                        <th class="py-3">Stasiun ISPU</th>
                        <th class="py-3">Pengumuman Aktif</th>
                        <th class="py-3 pe-3 text-end">Koordinat Pusat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($provinces as $prov)
                        <tr>
                            <td class="ps-3 fw-semibold">
                                <i class="bi bi-geo me-1 text-primary"></i>
                                {{ $prov->name }}
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                    {{ $prov->code ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if ($prov->hotspots_7d_count > 0)
                                    <span class="fw-bold text-danger">{{ number_format($prov->hotspots_7d_count) }} titik</span>
                                @else
                                    <span class="text-secondary-soft">0 titik</span>
                                @endif
                            </td>
                            <td class="text-secondary-soft">{{ number_format($prov->hotspots_total_count) }}</td>
                            <td>
                                @if ($prov->stations_count > 0)
                                    <span class="badge bg-success bg-opacity-10 text-success border">{{ $prov->stations_count }} Stasiun</span>
                                @else
                                    <span class="text-secondary-soft small">Belum ada</span>
                                @endif
                            </td>
                            <td>
                                @if ($prov->active_announcements_count > 0)
                                    <span class="badge bg-warning bg-opacity-10 text-warning border">{{ $prov->active_announcements_count }} Rilis</span>
                                @else
                                    <span class="text-secondary-soft small">-</span>
                                @endif
                            </td>
                            <td class="pe-3 text-end text-secondary-soft small font-monospace">{{ $prov->center_lat ?? '-' }}, {{ $prov->center_lng ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary-soft">Belum ada data provinsi yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

            <div class="stat-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-primary"><i class="bi bi-cloud-haze2"></i></span>
                    <div>
                        <div class="stat-value">{{ $totals['stations'] }}</div>
                        <div class="stat-label text-secondary-soft">Stasiun Udara Aktif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
