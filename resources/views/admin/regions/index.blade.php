@extends('layouts.admin')

@section('title', 'Informasi Wilayah — Admin FireAware Kalimantan')
@section('page_title', 'Informasi Wilayah')

@section('content')
    {{-- 1. Header Halaman yang Informatif --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 small text-secondary mb-1">
                <span>Cakupan Pemantauan Terpadu</span>
                <span class="text-muted">&bull;</span>
                <span class="badge badge-status-published d-inline-flex align-items-center gap-1">
                    <i class="bi bi-shield-check"></i>
                    <span>5/5 Provinsi Aktif Dipantau</span>
                </span>
            </div>
            <h1 class="h4 mb-1 fw-bold text-dark">Pengelolaan Wilayah &amp; Telemetri Kalimantan</h1>
            <p class="text-secondary small mb-0">
                Pusat data cakupan 5 provinsi Pulau Kalimantan untuk pengawasan titik panas karhutla, jaringan sensor ISPU, dan rilis kebencanaan terpadu.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1 rounded-3" data-bs-toggle="modal" data-bs-target="#createRegionModal">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Wilayah Baru</span>
            </button>
            <a href="{{ route('hotspot.map') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1 rounded-3" target="_blank" rel="noopener">
                <i class="bi bi-map"></i>
                <span>Peta Spasial Publik</span>
            </a>
        </div>
    </div>

    {{-- 2. Ringkasan Statistik Spasial (4 Kartu) --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-primary fs-5"><i class="bi bi-geo-alt"></i></span>
                    <div>
                        <div class="stat-value">{{ $totals['provinces'] }}</div>
                        <div class="stat-label">Provinsi Terdaftar</div>
                    </div>
                </div>
                <div class="stat-sub mt-2 text-muted small">Cakupan regional Pulau Kalimantan</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-danger bg-opacity-10 text-danger fs-5"><i class="bi bi-fire"></i></span>
                    <div>
                        <div class="stat-value text-danger">{{ number_format($totals['hotspots_7d']) }}</div>
                        <div class="stat-label">Titik Panas (7 Hari)</div>
                    </div>
                </div>
                <div class="stat-sub mt-2 text-muted small">Deteksi anomali satelit NASA FIRMS</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-primary fs-5"><i class="bi bi-database"></i></span>
                    <div>
                        <div class="stat-value">{{ number_format($totals['hotspots_total']) }}</div>
                        <div class="stat-label">Total Arsip Hotspot</div>
                    </div>
                </div>
                <div class="stat-sub mt-2 text-muted small">Basis data historis titik panas</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-primary fs-5"><i class="bi bi-wind"></i></span>
                    <div>
                        <div class="stat-value">{{ $totals['stations'] }}</div>
                        <div class="stat-label">Stasiun Sensor ISPU</div>
                    </div>
                </div>
                <div class="stat-sub mt-2 text-muted small">Stasiun pemantau aktif PM2.5</div>
            </div>
        </div>
    </div>

    {{-- 3. Tata Letak Terstruktur 5 Provinsi Cakupan --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h2 class="h5 fw-bold mb-0 text-dark">Lima Provinsi Cakupan Sistem FireAware</h2>
            <p class="text-secondary small mb-0">Jangkar geospasial observasi dan pengawasan karhutla di seluruh penjuru Kalimantan.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach ($provinces as $prov)
            <div class="col-md-6 col-xl">
                <div class="region-card shadow-xs">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="region-code-badge">{{ $prov->code ?? 'KOD' }}</span>
                            <span class="badge badge-status-published d-inline-flex align-items-center gap-1" style="font-size: 0.68rem;">
                                <i class="bi bi-check-circle-fill"></i> Aktif
                            </span>
                        </div>
                        <h3 class="h6 fw-bold text-dark mb-2">{{ $prov->name }}</h3>
                        
                        <div class="d-flex flex-column gap-1 small mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-secondary">Hotspot 7 Hari:</span>
                                <span class="fw-bold {{ $prov->hotspots_7d_count > 0 ? 'text-danger' : 'text-dark' }}">
                                    {{ number_format($prov->hotspots_7d_count) }} titik
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-secondary">Stasiun Udara:</span>
                                <span class="fw-semibold text-dark">{{ $prov->stations_count }} unit</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-secondary">Warta Aktif:</span>
                                <span class="fw-semibold text-dark">{{ $prov->active_announcements_count }} rilis</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 border-top border-soft">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="region-coord-chip" title="Koordinat Titik Tengah Wilayah">
                                {{ number_format((float)$prov->center_lat, 2) }}, {{ number_format((float)$prov->center_lng, 2) }}
                            </span>
                            <div class="btn-action-group">
                                <button type="button" class="btn-action btn-action-view"
                                        title="Detail Wilayah"
                                        data-detail-trigger
                                        data-id="{{ $prov->id }}"
                                        data-name="{{ $prov->name }}"
                                        data-code="{{ $prov->code }}"
                                        data-slug="{{ $prov->slug }}"
                                        data-lat="{{ $prov->center_lat }}"
                                        data-lng="{{ $prov->center_lng }}"
                                        data-hotspots-7d="{{ $prov->hotspots_7d_count }}"
                                        data-hotspots-total="{{ $prov->hotspots_total_count }}"
                                        data-stations="{{ $prov->stations_count }}"
                                        data-announcements="{{ $prov->active_announcements_count }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn-action btn-action-edit"
                                        title="Edit Koordinat Wilayah"
                                        data-edit-trigger
                                        data-id="{{ $prov->id }}"
                                        data-name="{{ $prov->name }}"
                                        data-code="{{ $prov->code }}"
                                        data-lat="{{ $prov->center_lat }}"
                                        data-lng="{{ $prov->center_lng }}"
                                        data-update-url="{{ route('admin.regions.update', $prov) }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


    {{-- 4. Visual Peta Spasial Regional Kalimantan --}}
    <div class="panel p-3 p-md-4 mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h2 class="h6 fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-map text-primary"></i>
                    <span>Peta Jangkar Geospasial Provinsi Kalimantan</span>
                </h2>
                <p class="text-secondary small mb-0">
                    Titik koordinat pusat geografis yang menjadi referensi agregasi data spasial titik panas satelit NASA FIRMS dan stasiun sensor kualitas udara.
                </p>
            </div>
            <span class="badge bg-mist text-primary border border-soft px-2 py-1 small">
                <i class="bi bi-broadcast me-1"></i>Observasi Aktif
            </span>
        </div>
        <div id="kalimantanRegionMap" class="regional-map-container shadow-xs"></div>
    </div>

    {{-- 5. Tabel Wilayah & Metrik Terkait --}}
    <div class="panel p-0 overflow-hidden mb-4">
        <div class="p-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <h2 class="h6 fw-bold mb-0 text-dark">Daftar Provinsi &amp; Metrik Terkait</h2>
                <span class="small text-secondary">Data spasial, telemetri satelit, dan stasiun pengukuran ISPU</span>
            </div>
            <span class="badge bg-mist text-primary border border-soft px-2 py-1 small">
                <i class="bi bi-check2-circle me-1"></i>Data Spasial Aktual
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-soft">
                    <tr>
                        <th class="ps-3 py-3" style="min-width: 220px;">Provinsi</th>
                        <th class="py-3" style="width: 130px;">Status</th>
                        <th class="py-3" style="width: 150px;">Hotspot (7 Hari)</th>
                        <th class="py-3" style="width: 140px;">Total Arsip</th>
                        <th class="py-3" style="width: 130px;">Stasiun ISPU</th>
                        <th class="py-3" style="width: 130px;">Warta Aktif</th>
                        <th class="py-3" style="min-width: 160px;">Koordinat Pusat</th>
                        <th class="py-3 pe-3 text-end" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($provinces as $prov)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-mist text-primary border border-soft fw-bold" style="font-size: 0.72rem;">
                                        {{ $prov->code ?? '-' }}
                                    </span>
                                    <span class="fw-semibold text-dark">{{ $prov->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-status-published d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-check-circle-fill"></i> Aktif Terpantau
                                </span>
                            </td>
                            <td>
                                @if ($prov->hotspots_7d_count > 0)
                                    <span class="fw-bold text-danger">{{ number_format($prov->hotspots_7d_count) }} titik</span>
                                @else
                                    <span class="text-secondary small">0 titik</span>
                                @endif
                            </td>
                            <td class="text-secondary fw-medium">{{ number_format($prov->hotspots_total_count) }}</td>
                            <td>
                                @if ($prov->stations_count > 0)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                        {{ $prov->stations_count }} Stasiun
                                    </span>
                                @else
                                    <span class="text-muted small fst-italic">0 Stasiun</span>
                                @endif
                            </td>
                            <td>
                                @if ($prov->active_announcements_count > 0)
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                                        {{ $prov->active_announcements_count }} Rilis
                                    </span>
                                @else
                                    <span class="text-muted small fst-italic">0 Rilis</span>
                                @endif
                            </td>
                            <td>
                                <span class="region-coord-chip">
                                    {{ $prov->center_lat ?? '-' }}, {{ $prov->center_lng ?? '-' }}
                                </span>
                            </td>
                            <td class="pe-3 text-end">
                                <div class="btn-action-group">
                                    <button type="button" class="btn-action btn-action-view"
                                            title="Detail Wilayah"
                                            data-detail-trigger
                                            data-id="{{ $prov->id }}"
                                            data-name="{{ $prov->name }}"
                                            data-code="{{ $prov->code }}"
                                            data-slug="{{ $prov->slug }}"
                                            data-lat="{{ $prov->center_lat }}"
                                            data-lng="{{ $prov->center_lng }}"
                                            data-hotspots-7d="{{ $prov->hotspots_7d_count }}"
                                            data-hotspots-total="{{ $prov->hotspots_total_count }}"
                                            data-stations="{{ $prov->stations_count }}"
                                            data-announcements="{{ $prov->active_announcements_count }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-action-edit"
                                            title="Edit Koordinat Wilayah"
                                            data-edit-trigger
                                            data-id="{{ $prov->id }}"
                                            data-name="{{ $prov->name }}"
                                            data-code="{{ $prov->code }}"
                                            data-lat="{{ $prov->center_lat }}"
                                            data-lng="{{ $prov->center_lng }}"
                                            data-update-url="{{ route('admin.regions.update', $prov) }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="{{ route('hotspot.map', ['provinsi' => $prov->slug]) }}"
                                       class="btn-action"
                                       title="Buka Peta Hotspot Provinsi Ini"
                                       target="_blank">
                                        <i class="bi bi-map"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-secondary">Belum ada data provinsi yang terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Edit Wilayah --}}
    <div class="modal fade" id="editRegionModal" tabindex="-1" aria-labelledby="editRegionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <form id="editRegionForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-bottom border-soft pb-3">
                        <h5 class="modal-title h6 fw-bold text-dark d-flex align-items-center gap-2 mb-0" id="editRegionModalLabel">
                            <i class="bi bi-pencil-square text-primary"></i>
                            <span>Edit Koordinat &amp; Informasi Wilayah</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body py-3">
                        <div class="alert alert-info border-info border-opacity-25 bg-soft py-2 px-3 small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Koordinat titik pusat digunakan oleh algoritma sistem sebagai acuan geospasial agregasi data titik panas satelit NASA FIRMS.
                        </div>

                        <div class="mb-3">
                            <label for="edit_name" class="form-label small fw-semibold">Nama Provinsi <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_code" class="form-label small fw-semibold">Kode Singkatan Wilayah <small class="text-muted fw-normal">(Maks. 10 karakter)</small></label>
                            <input type="text" name="code" id="edit_code" class="form-control" placeholder="Contoh: KALBAR" maxlength="10">
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-sm-6">
                                <label for="edit_lat" class="form-label small fw-semibold">Garis Lintang (Latitude)</label>
                                <input type="number" step="any" name="center_lat" id="edit_lat" class="form-control font-monospace small" placeholder="-1.500000">
                            </div>
                            <div class="col-sm-6">
                                <label for="edit_lng" class="form-label small fw-semibold">Garis Bujur (Longitude)</label>
                                <input type="number" step="any" name="center_lng" id="edit_lng" class="form-control font-monospace small" placeholder="113.000000">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-soft pt-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                            <i class="bi bi-check-lg"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Wilayah Baru --}}
    <div class="modal fade" id="createRegionModal" tabindex="-1" aria-labelledby="createRegionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <form method="POST" action="{{ route('admin.regions.store') }}">
                    @csrf
                    <div class="modal-header border-bottom border-soft pb-3">
                        <h5 class="modal-title h6 fw-bold text-dark d-flex align-items-center gap-2 mb-0" id="createRegionModalLabel">
                            <i class="bi bi-plus-circle text-primary"></i>
                            <span>Tambah Wilayah Pemantauan Baru</span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body py-3">
                        <div class="mb-3">
                            <label for="create_name" class="form-label small fw-semibold">Nama Wilayah / Provinsi <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="create_name" class="form-control" placeholder="Contoh: Kalimantan Baru" required>
                        </div>

                        <div class="mb-3">
                            <label for="create_code" class="form-label small fw-semibold">Kode Singkatan</label>
                            <input type="text" name="code" id="create_code" class="form-control" placeholder="Contoh: KALBAR" maxlength="10">
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-sm-6">
                                <label for="create_lat" class="form-label small fw-semibold">Latitude</label>
                                <input type="number" step="any" name="center_lat" id="create_lat" class="form-control font-monospace small" placeholder="0.000000">
                            </div>
                            <div class="col-sm-6">
                                <label for="create_lng" class="form-label small fw-semibold">Longitude</label>
                                <input type="number" step="any" name="center_lng" id="create_lng" class="form-control font-monospace small" placeholder="114.000000">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-soft pt-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                            <i class="bi bi-plus-lg"></i>
                            <span>Simpan Wilayah</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Detail Wilayah Spasial --}}
    <div class="modal fade" id="detailRegionModal" tabindex="-1" aria-labelledby="detailRegionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header border-bottom border-soft pb-3">
                    <h5 class="modal-title h6 fw-bold text-dark d-flex align-items-center gap-2 mb-0" id="detailRegionModalLabel">
                        <i class="bi bi-geo-alt text-primary"></i>
                        <span id="detailRegionTitle">Detail Wilayah</span>
                        <span class="badge bg-mist text-primary border border-soft ms-1" id="detailRegionCode"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-soft border border-soft mb-3">
                        <span class="small text-secondary">Status Pemantauan Sistem:</span>
                        <span class="badge badge-status-published d-inline-flex align-items-center gap-1">
                            <i class="bi bi-check-circle-fill"></i> Aktif Terpantau
                        </span>
                    </div>

                    <div class="row g-2 text-center mb-3">
                        <div class="col-6">
                            <div class="p-2 border border-soft rounded-2 bg-light">
                                <div class="h5 fw-bold text-danger mb-0" id="detailHotspots7d">0</div>
                                <div class="small text-secondary" style="font-size: 0.75rem;">Hotspot (7 Hari)</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border border-soft rounded-2 bg-light">
                                <div class="h5 fw-bold text-dark mb-0" id="detailHotspotsTotal">0</div>
                                <div class="small text-secondary" style="font-size: 0.75rem;">Total Arsip Hotspot</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border border-soft rounded-2 bg-light">
                                <div class="h5 fw-bold text-success mb-0" id="detailStations">0</div>
                                <div class="small text-secondary" style="font-size: 0.75rem;">Stasiun Udara Aktif</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border border-soft rounded-2 bg-light">
                                <div class="h5 fw-bold text-warning mb-0" id="detailAnnouncements">0</div>
                                <div class="small text-secondary" style="font-size: 0.75rem;">Pengumuman Aktif</div>
                            </div>
                        </div>
                    </div>

                    <div class="small text-secondary mb-3">
                        <div class="d-flex justify-content-between py-1 border-bottom border-soft">
                            <span>Titik Pusat (Latitude, Longitude):</span>
                            <span class="font-monospace fw-semibold text-dark" id="detailCoordinates">-</span>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="#" id="detailMapLink" target="_blank" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-1">
                            <i class="bi bi-map"></i>
                            <span>Buka Hotspot Wilayah Ini di Peta Spasial</span>
                        </a>
                        <a href="#" id="detailAirQualityLink" target="_blank" class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center gap-1">
                            <i class="bi bi-wind"></i>
                            <span>Pantau Kualitas Udara Wilayah Ini</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Inisialisasi Peta Spasial Regional Kalimantan
        const mapEl = document.getElementById('kalimantanRegionMap');
        if (mapEl && typeof L !== 'undefined') {
            const map = L.map('kalimantanRegionMap', {
                scrollWheelZoom: false,
            }).setView([-0.2, 114.5], 5.4);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                attribution: '&copy; OpenStreetMap contributors | FireAware Kalimantan'
            }).addTo(map);

            const provincesData = @json($provinces);

            provincesData.forEach(function (prov) {
                if (prov.center_lat && prov.center_lng) {
                    const marker = L.circleMarker([parseFloat(prov.center_lat), parseFloat(prov.center_lng)], {
                        radius: 9,
                        fillColor: '#214D3A',
                        color: '#ffffff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.92
                    }).addTo(map);

                    const popupContent = `
                        <div style="font-family: Inter, sans-serif; min-width: 170px; padding: 2px;">
                            <div style="font-weight: 700; color: #17392D; font-size: 13px; margin-bottom: 3px;">
                                ${prov.name} <span style="background: #E7EFE7; color: #214D3A; font-size: 10px; padding: 1px 5px; border-radius: 3px;">${prov.code || '-'}</span>
                            </div>
                            <div style="font-size: 11px; color: #516257; margin-bottom: 6px;">
                                Koordinat: ${parseFloat(prov.center_lat).toFixed(2)}, ${parseFloat(prov.center_lng).toFixed(2)}
                            </div>
                            <div style="font-size: 11px; margin-bottom: 8px;">
                                <span style="color: #b94035; font-weight: 600;">${prov.hotspots_7d_count} Hotspot</span> (7 hari)<br>
                                <span style="color: #477b59; font-weight: 600;">${prov.stations_count} Stasiun Udara</span> &bull; 
                                <span style="color: #d99227; font-weight: 600;">${prov.active_announcements_count} Warta</span>
                            </div>
                            <a href="/peta-hotspot?provinsi=${prov.slug}" target="_blank"
                               style="display: inline-block; font-size: 11px; font-weight: 600; background: #214D3A; color: #fff; padding: 4px 10px; border-radius: 4px; text-decoration: none;">
                               Buka di Peta Publik &rarr;
                            </a>
                        </div>
                    `;
                    marker.bindPopup(popupContent);
                }
            });
        }
        // 2. Modal Handler: Edit Wilayah
        const editModalEl = document.getElementById('editRegionModal');
        const editForm = document.getElementById('editRegionForm');
        const editName = document.getElementById('edit_name');
        const editCode = document.getElementById('edit_code');
        const editLat = document.getElementById('edit_lat');
        const editLng = document.getElementById('edit_lng');

        document.querySelectorAll('[data-edit-trigger]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const url = this.getAttribute('data-update-url');
                const name = this.getAttribute('data-name');
                const code = this.getAttribute('data-code');
                const lat = this.getAttribute('data-lat');
                const lng = this.getAttribute('data-lng');

                if (editForm) editForm.action = url;
                if (editName) editName.value = name || '';
                if (editCode) editCode.value = code || '';
                if (editLat) editLat.value = lat || '';
                if (editLng) editLng.value = lng || '';

                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const bsModal = bootstrap.Modal.getOrCreateInstance(editModalEl);
                    bsModal.show();
                }
            });
        });

        // 3. Modal Handler: Detail Wilayah
        const detailModalEl = document.getElementById('detailRegionModal');
        const detailTitle = document.getElementById('detailRegionTitle');
        const detailCode = document.getElementById('detailRegionCode');
        const detailHotspots7d = document.getElementById('detailHotspots7d');
        const detailHotspotsTotal = document.getElementById('detailHotspotsTotal');
        const detailStations = document.getElementById('detailStations');
        const detailAnnouncements = document.getElementById('detailAnnouncements');
        const detailCoordinates = document.getElementById('detailCoordinates');
        const detailMapLink = document.getElementById('detailMapLink');
        const detailAirQualityLink = document.getElementById('detailAirQualityLink');

        document.querySelectorAll('[data-detail-trigger]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const name = this.getAttribute('data-name');
                const code = this.getAttribute('data-code') || '-';
                const slug = this.getAttribute('data-slug');
                const lat = this.getAttribute('data-lat') || '-';
                const lng = this.getAttribute('data-lng') || '-';
                const h7d = this.getAttribute('data-hotspots-7d') || '0';
                const hTot = this.getAttribute('data-hotspots-total') || '0';
                const st = this.getAttribute('data-stations') || '0';
                const ann = this.getAttribute('data-announcements') || '0';

                if (detailTitle) detailTitle.textContent = name;
                if (detailCode) detailCode.textContent = code;
                if (detailHotspots7d) detailHotspots7d.textContent = Number(h7d).toLocaleString();
                if (detailHotspotsTotal) detailHotspotsTotal.textContent = Number(hTot).toLocaleString();
                if (detailStations) detailStations.textContent = st + ' Stasiun';
                if (detailAnnouncements) detailAnnouncements.textContent = ann + ' Rilis';
                if (detailCoordinates) detailCoordinates.textContent = lat + ', ' + lng;

                if (detailMapLink) detailMapLink.href = '/peta-hotspot?provinsi=' + slug;
                if (detailAirQualityLink) detailAirQualityLink.href = '/kualitas-udara?provinsi=' + slug;

                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const bsModal = bootstrap.Modal.getOrCreateInstance(detailModalEl);
                    bsModal.show();
                }
            });
        });

    });
</script>
@endpush

