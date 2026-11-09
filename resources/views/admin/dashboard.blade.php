@extends('layouts.admin')

@section('title', 'Dashboard — Admin FireAware Kalimantan')
@section('page_title', 'Dashboard')

@section('content')
    {{-- Header Dashboard --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1 fw-bold text-dark">Dashboard Pengelola</h1>
            <p class="text-secondary small mb-0">
                Ringkasan publikasi konten, pantauan wilayah Kalimantan, dan status telemetri data.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-mist text-primary border border-soft px-3 py-2 small d-none d-sm-inline-flex align-items-center gap-1">
                <i class="bi bi-geo-alt-fill text-danger"></i> 5 Provinsi Terpantau
            </span>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1" target="_blank" rel="noopener">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Lihat Portal Publik</span>
            </a>
        </div>
    </div>

    {{-- 1. Kartu Ringkasan Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4 col-xxl-2">
            <a href="{{ route('admin.guides.index') }}" class="dash-stat-link h-100">
                <div class="stat-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <span class="stat-icon bg-mist text-primary"><i class="bi bi-shield-check"></i></span>
                        <div>
                            <div class="stat-value">{{ $contentStats['guides']['published'] }}</div>
                            <div class="stat-label">Panduan Published</div>
                        </div>
                    </div>
                    @if ($contentStats['guides']['draft'] > 0)
                        <div class="stat-sub mt-2 text-warning small">{{ $contentStats['guides']['draft'] }} draf</div>
                    @endif
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-4 col-xxl-2">
            <a href="{{ route('admin.education.index') }}" class="dash-stat-link h-100">
                <div class="stat-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <span class="stat-icon bg-mist text-primary"><i class="bi bi-mortarboard"></i></span>
                        <div>
                            <div class="stat-value">{{ $contentStats['education']['published'] }}</div>
                            <div class="stat-label">Artikel Edukasi</div>
                        </div>
                    </div>
                    @if ($contentStats['education']['draft'] > 0)
                        <div class="stat-sub mt-2 text-warning small">{{ $contentStats['education']['draft'] }} draf</div>
                    @endif
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-4 col-xxl-2">
            <a href="{{ route('admin.announcements.index') }}" class="dash-stat-link h-100">
                <div class="stat-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <span class="stat-icon bg-mist text-primary"><i class="bi bi-megaphone"></i></span>
                        <div>
                            <div class="stat-value">{{ $contentStats['announcements']['active'] }}</div>
                            <div class="stat-label">Pengumuman Aktif</div>
                        </div>
                    </div>
                    @if ($contentStats['announcements']['draft'] > 0)
                        <div class="stat-sub mt-2 text-warning small">{{ $contentStats['announcements']['draft'] }} draf</div>
                    @endif
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-4 col-xxl-2">
            <a href="{{ route('admin.regions.index') }}" class="dash-stat-link h-100">
                <div class="stat-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <span class="stat-icon bg-mist text-primary"><i class="bi bi-geo-alt"></i></span>
                        <div>
                            <div class="stat-value">{{ $regionStats['provinces'] }}</div>
                            <div class="stat-label">Provinsi Pantauan</div>
                        </div>
                    </div>
                    <div class="stat-sub mt-2 text-muted small">Cakupan Kalimantan</div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-4 col-xxl-2">
            <a href="{{ route('admin.regions.index') }}" class="dash-stat-link h-100">
                <div class="stat-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <span class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-fire"></i></span>
                        <div>
                            <div class="stat-value text-danger">{{ number_format($regionStats['hotspots']['recent']) }}</div>
                            <div class="stat-label">Hotspot (7 Hari)</div>
                        </div>
                    </div>
                    <div class="stat-sub mt-2 text-muted small">Arsip: {{ number_format($regionStats['hotspots']['total']) }} titik</div>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-4 col-xxl-2">
            <a href="{{ route('admin.integrations.index') }}" class="dash-stat-link h-100">
                <div class="stat-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <span class="stat-icon bg-mist text-primary"><i class="bi bi-wind"></i></span>
                        <div>
                            <div class="stat-value">{{ $regionStats['stations'] }}</div>
                            <div class="stat-label">Stasiun Sensor</div>
                        </div>
                    </div>
                    <div class="stat-sub mt-2 text-muted small">ISPU &amp; PM2.5 Aktif</div>
                </div>
            </a>
        </div>
    </div>


    {{-- 2. Visualisasi Tren Hotspot & Ringkasan Wilayah --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="panel p-3 p-md-4 h-100">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h2 class="h6 fw-bold mb-0 text-dark">Tren Titik Panas (7 Hari Terakhir)</h2>
                        <span class="small text-secondary">Aktivitas deteksi satelit NASA FIRMS &amp; BMKG</span>
                    </div>
                    <span class="badge bg-mist text-primary border border-soft px-2 py-1 small">
                        <i class="bi bi-database-check me-1"></i>Basis Data Aktual
                    </span>
                </div>
                <div style="height: 250px; position: relative;">
                    <canvas id="hotspotTrendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel p-3 p-md-4 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="h6 fw-bold mb-0 text-dark">Ringkasan Wilayah</h2>
                        <span class="badge bg-light text-secondary border border-soft small">7 Hari</span>
                    </div>

                    <ul class="list-group list-group-flush small mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-secondary">Cakupan Provinsi</span>
                            <span class="fw-bold text-dark">{{ $regionStats['provinces'] }} Provinsi</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-secondary">Titik Panas (7 Hari)</span>
                            <span class="fw-bold text-danger">{{ number_format($regionStats['hotspots']['recent']) }} titik</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-secondary">Total Titik Panas Arsip</span>
                            <span class="fw-bold text-dark">{{ number_format($regionStats['hotspots']['total']) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                            <span class="text-secondary">Stasiun Udara Aktif</span>
                            <span class="fw-bold text-dark">{{ $regionStats['stations'] }} stasiun</span>
                        </li>
                    </ul>

                    @if ($provinceHotspots->isNotEmpty())
                        <div class="border-top pt-2">
                            <div class="text-secondary small fw-semibold mb-2">Sebaran Hotspot per Wilayah:</div>
                            @foreach ($provinceHotspots as $prov)
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="text-dark">{{ $prov->name }}</span>
                                        <span class="fw-semibold {{ $prov->hotspots_count > 0 ? 'text-danger' : 'text-muted' }}">
                                            {{ number_format($prov->hotspots_count) }} titik
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 4px;">
                                        @php
                                            $pct = $regionStats['hotspots']['recent'] > 0
                                                ? min(100, round(($prov->hotspots_count / $regionStats['hotspots']['recent']) * 100))
                                                : 0;
                                        @endphp
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="pt-3 border-top mt-3">
                    <a href="{{ route('admin.regions.index') }}" class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1 rounded-3">
                        <span>Lihat Distribusi Wilayah Lengkap</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Status Integrasi Sumber Data --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h2 class="h5 fw-bold mb-0 text-dark">Status Integrasi Sumber Data</h2>
            <p class="text-secondary small mb-0">
                Membedakan status koneksi, ketepatan sinkronisasi, dan ketersediaan data aktual di basis data.
            </p>
        </div>
        <a href="{{ route('admin.integrations.index') }}" class="btn btn-sm btn-outline-secondary d-none d-sm-inline-flex align-items-center gap-1 rounded-3">
            <span>Rincian Integrasi</span>
            <i class="bi bi-chevron-right"></i>
        </a>
    </div>

    <div class="row g-3 mb-4">
        @foreach ($integrations as $src)
            <div class="col-lg-4">
                @include('admin.partials.integration-card', ['src' => $src])
            </div>
        @endforeach
    </div>

    {{-- 4. Aktivitas Sinkronisasi & Telemetri Terbaru --}}
    <div class="panel p-3 p-md-4 mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h2 class="h6 fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-activity text-primary"></i>
                    <span>Aktivitas Sinkronisasi &amp; Telemetri Terbaru</span>
                </h2>
                <p class="text-secondary small mb-0">
                    Rekaman audit proses pertukaran data API NASA FIRMS, BMKG, dan OpenAQ ke dalam sistem.
                </p>
            </div>
            <a href="{{ route('admin.integrations.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 rounded-3">
                <i class="bi bi-list-ul"></i>
                <span>Lihat Seluruh Log</span>
            </a>
        </div>

        @include('admin.partials.dashboard-sync-table')
    </div>

    {{-- 5. Navigasi Seluruh Halaman Pengelolaan --}}
    <div class="panel p-3 p-md-4 mb-4">
        <h2 class="h6 fw-bold mb-3 d-flex align-items-center gap-2 text-dark">
            <i class="bi bi-compass text-primary"></i>
            <span>Navigasi Seluruh Halaman Pengelolaan</span>
        </h2>
        <div class="row g-2 g-md-3">
            <div class="col-6 col-md-3 col-lg">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm w-100 py-2 d-flex flex-column align-items-center gap-1 active rounded-3">
                    <i class="bi bi-grid fs-5"></i>
                    <span class="small fw-semibold">Dashboard</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg">
                <a href="{{ route('admin.guides.index') }}" class="btn btn-outline-secondary btn-sm w-100 py-2 d-flex flex-column align-items-center gap-1 rounded-3">
                    <i class="bi bi-shield-check fs-5"></i>
                    <span class="small fw-semibold">Panduan</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg">
                <a href="{{ route('admin.education.index') }}" class="btn btn-outline-secondary btn-sm w-100 py-2 d-flex flex-column align-items-center gap-1 rounded-3">
                    <i class="bi bi-mortarboard fs-5"></i>
                    <span class="small fw-semibold">Edukasi</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg">
                <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary btn-sm w-100 py-2 d-flex flex-column align-items-center gap-1 rounded-3">
                    <i class="bi bi-megaphone fs-5"></i>
                    <span class="small fw-semibold">Pengumuman</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg">
                <a href="{{ route('admin.regions.index') }}" class="btn btn-outline-secondary btn-sm w-100 py-2 d-flex flex-column align-items-center gap-1 rounded-3">
                    <i class="bi bi-map fs-5"></i>
                    <span class="small fw-semibold">Wilayah</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg">
                <a href="{{ route('admin.integrations.index') }}" class="btn btn-outline-secondary btn-sm w-100 py-2 d-flex flex-column align-items-center gap-1 rounded-3">
                    <i class="bi bi-arrow-repeat fs-5"></i>
                    <span class="small fw-semibold">Integrasi</span>
                </a>
            </div>
            <div class="col-6 col-md-3 col-lg">
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary btn-sm w-100 py-2 d-flex flex-column align-items-center gap-1 rounded-3">
                    <i class="bi bi-bell fs-5"></i>
                    <span class="small fw-semibold">Notifikasi</span>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('hotspotTrendChart');
        if (!ctx) return;

        const trendData = @json($trendChart);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendData.labels,
                datasets: [{
                    label: 'Jumlah Titik Panas',
                    data: trendData.values,
                    borderColor: '#214D3A',
                    backgroundColor: 'rgba(33, 77, 58, 0.08)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2,
                    pointBackgroundColor: '#214D3A',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#17392D',
                        padding: 10,
                        callbacks: {
                            label: (context) => `${context.parsed.y} titik panas`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: { size: 11, family: 'Inter' }
                        },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                    },
                    x: {
                        ticks: {
                            font: { size: 11, family: 'Inter' }
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endpush