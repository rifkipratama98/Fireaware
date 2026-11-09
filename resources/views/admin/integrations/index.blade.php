@extends('layouts.admin')

@section('title', 'Pemantauan Integrasi Data — Admin FireAware')
@section('page_title', 'Integrasi Data Eksternal')

@section('content')
    {{-- Header Pemantauan Infrastruktur Data --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 small text-secondary mb-1">
                <span>Infrastruktur Pipeline Data</span>
                <span class="text-muted">&bull;</span>
                <span class="badge {{ $healthMetrics['success_rate'] >= 90 ? 'badge-infra-connected' : ($healthMetrics['success_rate'] >= 70 ? 'badge-infra-stale' : 'badge-infra-failed') }} d-inline-flex align-items-center gap-1">
                    <i class="bi {{ $healthMetrics['success_rate'] >= 90 ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' }}"></i>
                    <span>{{ $healthMetrics['success_rate'] }}% Tingkat Sukses</span>
                </span>
                <span class="text-muted">&bull;</span>
                <span class="text-secondary small">Latensi Rata-Rata: <strong>{{ $healthMetrics['avg_duration_ms'] }} ms</strong></span>
            </div>
            <h1 class="h4 mb-1 fw-bold text-dark">Pemantauan Integrasi &amp; Pembaruan Data</h1>
            <p class="text-secondary small mb-0">
                Pusat pengawasan konektivitas API telemetri Karhutla Kalimantan, kepatuhan jadwal sinkronisasi, dan audit aliran data satelit.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button"
                    class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-3 shadow-xs"
                    data-sync-trigger
                    data-sync-key="all"
                    data-sync-name="Semua Sumber Data (NASA FIRMS, BMKG, Kualitas Udara)"
                    data-sync-provider="Penyedia Eksternal Gabungan"
                    data-sync-configured="1">
                <i class="bi bi-arrow-repeat"></i>
                <span>Sinkronkan Semua Sumber Data</span>
            </button>
        </div>
    </div>

    {{-- Panduan Status Klasifikasi Infrastruktur --}}
    <div class="alert alert-info py-2-5 px-3 small d-flex align-items-start gap-2 mb-4 rounded-3 border-info border-opacity-25 bg-soft" role="alert">
        <i class="bi bi-info-circle-fill fs-5 text-primary mt-0-5 flex-shrink-0"></i>
        <div class="lh-sm">
            <strong class="text-dark d-block mb-1">Pedoman Status Aliran Data Telemetri:</strong>
            <div class="d-flex flex-wrap gap-2 text-secondary" style="font-size: 0.8rem;">
                <span><strong class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Connected:</strong> Terhubung aktif, kredensial valid, data mutakhir dalam 24 jam.</span>
                <span><strong class="text-warning"><i class="bi bi-clock-history me-1"></i>Stale:</strong> Konfigurasi aktif namun data belum diperbarui &gt; 24 jam.</span>
                <span><strong class="text-danger"><i class="bi bi-exclamation-octagon-fill me-1"></i>Failed:</strong> Terjadi kendala HTTP, batas waktu timeout, atau parsing payload.</span>
                <span><strong class="text-secondary"><i class="bi bi-plug me-1"></i>Disconnected:</strong> Kunci API atau URL endpoint belum disetel di file environment (.env).</span>
            </div>
        </div>
    </div>

    {{-- Kartu Ringkasan Metrik Infrastruktur --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-primary fs-5"><i class="bi bi-clock-history"></i></span>
                    <div>
                        <div class="stat-value">{{ number_format($stats['total']) }}</div>
                        <div class="stat-label">Total Riwayat</div>
                    </div>
                </div>
                <div class="stat-sub mt-2 text-muted small">Log eksekusi tercatat</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-success bg-opacity-10 text-success fs-5"><i class="bi bi-check-circle"></i></span>
                    <div>
                        <div class="stat-value text-success">{{ number_format($stats['success']) }}</div>
                        <div class="stat-label">Sukses Terhubung</div>
                    </div>
                </div>
                <div class="stat-sub mt-2 text-muted small">Aliran data berhasil</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-danger bg-opacity-10 text-danger fs-5"><i class="bi bi-exclamation-octagon"></i></span>
                    <div>
                        <div class="stat-value text-danger">{{ number_format($stats['failed']) }}</div>
                        <div class="stat-label">Gagal / Error</div>
                    </div>
                </div>
                <div class="stat-sub mt-2 text-muted small">Kendala HTTP / payload</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-warning bg-opacity-10 text-warning fs-5"><i class="bi bi-hourglass-split"></i></span>
                    <div>
                        <div class="stat-value text-warning">{{ number_format($stats['timeout']) }}</div>
                        <div class="stat-label">Batas Waktu (Timeout)</div>
                    </div>
                </div>
                <div class="stat-sub mt-2 text-muted small">Koneksi melebihi batas</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-mist text-primary fs-5"><i class="bi bi-speedometer2"></i></span>
                    <div>
                        <div class="stat-value">{{ $healthMetrics['avg_duration_ms'] }} <span style="font-size: 0.9rem; font-weight: 500;">ms</span></div>
                        <div class="stat-label">Latensi Rata-Rata</div>
                    </div>
                </div>
                <div class="stat-sub mt-2 text-muted small">Respons time endpoint</div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <span class="stat-icon bg-secondary bg-opacity-10 text-secondary fs-5"><i class="bi bi-gear"></i></span>
                    <div>
                        <div class="stat-value text-secondary">{{ number_format($stats['not_configured']) }}</div>
                        <div class="stat-label">Belum Diatur</div>
                    </div>
                </div>
                <div class="stat-sub mt-2 text-muted small">Parameter env kosong</div>
            </div>
        </div>
    </div>

    {{-- Kartu Integrasi Rinci Tiap Sumber --}}
    <div class="row g-4 mb-4">
        @foreach ($integrations as $src)
            <div class="col-lg-4">
                @include('admin.partials.integration-card', ['src' => $src])
            </div>
        @endforeach
    </div>
    {{-- Visualisasi Aktivitas Sinkronisasi 7 Hari --}}
    <div class="panel p-3 p-md-4 mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div>
                <h2 class="h6 fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-activity text-primary"></i>
                    <span>Aktivitas &amp; Throughput Sinkronisasi Telemetri (7 Hari Terakhir)</span>
                </h2>
                <p class="text-secondary small mb-0">
                    Visualisasi volume eksekusi penarikan data berkala dan rasio keberhasilan koneksi ke server penyedia.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 small">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                    <i class="bi bi-check-circle-fill me-1"></i>Sukses: {{ array_sum($chartData['success'] ?? []) }}
                </span>
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">
                    <i class="bi bi-x-circle-fill me-1"></i>Kendala: {{ array_sum($chartData['failed'] ?? []) }}
                </span>
            </div>
        </div>
        <div style="height: 220px; position: relative;">
            <canvas id="syncActivityChart"></canvas>
        </div>
    </div>

    {{-- Bagian Riwayat Sinkronisasi Data --}}
    <div class="panel p-3 p-md-4 mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div>
                <h2 class="h5 fw-bold mb-1 text-dark">Riwayat Log Sinkronisasi</h2>
                <p class="text-secondary-soft small mb-0">Catatan eksekusi integrasi data eksternal, waktu observasi, dan rekaman yang diproses.</p>
            </div>
        </div>

        {{-- Toolbar Filter Log --}}
        <form method="GET" action="{{ route('admin.integrations.index') }}" class="row g-2 align-items-center mb-4">
            <div class="col-md-4">
                <select name="sumber" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Sumber Data</option>
                    <option value="nasa_firms" {{ $sourceFilter === 'nasa_firms' ? 'selected' : '' }}>NASA FIRMS (Titik Panas)</option>
                    <option value="bmkg_karhutla" {{ $sourceFilter === 'bmkg_karhutla' ? 'selected' : '' }}>BMKG Karhutla</option>
                    <option value="air_quality" {{ $sourceFilter === 'air_quality' ? 'selected' : '' }}>Kualitas Udara (OpenAQ/ISPU)</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="success" {{ $statusFilter === 'success' ? 'selected' : '' }}>Berhasil</option>
                    <option value="failed" {{ $statusFilter === 'failed' ? 'selected' : '' }}>Gagal</option>
                    <option value="timeout" {{ $statusFilter === 'timeout' ? 'selected' : '' }}>Timeout</option>
                    <option value="not_configured" {{ $statusFilter === 'not_configured' ? 'selected' : '' }}>Belum Dikonfigurasi</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary btn-sm w-100">Filter Riwayat</button>
                @if ($sourceFilter !== '' || $statusFilter !== '')
                    <a href="{{ route('admin.integrations.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Tabel Log Sinkronisasi --}}
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-soft">
                    <tr>
                        <th scope="col" style="min-width: 170px;">Waktu Eksekusi</th>
                        <th scope="col">Sumber Data</th>
                        <th scope="col" class="text-center">Status</th>
                        <th scope="col" class="text-center">Rekaman Diproses</th>
                        <th scope="col">Waktu Observasi</th>
                        <th scope="col">Durasi</th>
                        <th scope="col">Pemicu</th>
                        <th scope="col" style="min-width: 200px;">Keterangan / Error</th>
                    </tr>
                </thead>
                <tbody>
                    @include('admin.integrations._logs_rows')
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="p-3 border-top border-soft d-flex justify-content-center">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
    {{-- Dokumentasi Scheduler & Parameter Spasial --}}
    <div class="panel p-3 p-md-4 mb-4">
        <h2 class="h6 fw-bold mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-calendar-check text-primary"></i>
            <span>Dokumentasi Konfigurasi Scheduler &amp; Parameter Spasial</span>
        </h2>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="small fw-semibold text-dark mb-2">Penjadwalan Otomatis (Cron Server)</div>
                <p class="small text-secondary-soft mb-2">
                    Jadwalkan perintah Laravel Scheduler pada crontab server produksi dengan menambahkan baris:
                </p>
                <pre class="p-2 bg-soft rounded border border-soft text-dark small mb-3"><code>* * * * * cd {{ base_path() }} &amp;&amp; php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</code></pre>
                <div class="small text-secondary-soft">
                    <ul class="mb-0 ps-3">
                        <li><strong>NASA FIRMS:</strong> Dijalankan otomatis tiap 3 jam (<code>everyThreeHours</code>)</li>
                        <li><strong>BMKG Karhutla:</strong> Dijalankan otomatis tiap 4 jam (<code>everyFourHours</code>)</li>
                        <li><strong>Kualitas Udara:</strong> Dijalankan otomatis tiap 1 jam (<code>hourly</code>)</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="small fw-semibold text-dark mb-2">Perintah Manual Artisan CLI</div>
                <pre class="p-2 bg-soft rounded border border-soft text-dark small mb-3"><code># Sinkronkan seluruh sumber data:
php artisan fireaware:sync --all

# Sinkronkan sumber tertentu:
php artisan fireaware:sync firms
php artisan fireaware:sync bmkg
php artisan fireaware:sync airquality</code></pre>
                <div class="small text-secondary-soft">
                    Batas wilayah ingesti telemetri (Kalimantan Bounding Box):
                    <span class="font-monospace fw-semibold d-block mt-1">SW: -4.500000°, 108.500000° &bull; NE: +4.500000°, 119.500000°</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Sinkronisasi Manual dengan State Loading --}}
    <div class="modal fade" id="syncConfirmModal" tabindex="-1" aria-labelledby="syncConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-semibold text-dark d-flex align-items-center gap-2" id="syncConfirmModalLabel">
                        <i class="bi bi-arrow-repeat text-primary"></i>
                        <span>Konfirmasi Sinkronisasi Manual</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Batal"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="mb-2 text-dark">
                        Apakah Anda ingin memulai sinkronisasi manual untuk:
                    </p>
                    <div class="p-3 bg-soft rounded-2 border border-soft mb-3">
                        <div class="fw-semibold text-dark" id="syncTargetName">-</div>
                        <div class="small text-secondary-soft" id="syncTargetProvider">-</div>
                    </div>
                    <div class="small text-secondary-soft">
                        <i class="bi bi-info-circle me-1"></i> Proses ini akan langsung melakukan panggilan HTTP ke endpoint API penyedia eksternal dengan batas waktu dan proteksi duplikasi data.
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" id="btnCancelSync">
                        Batal
                    </button>
                    <form id="syncConfirmForm" method="POST" action="" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2" id="btnSubmitSync">
                            <i class="bi bi-play-circle" id="syncIconNormal"></i>
                            <span class="spinner-border spinner-border-sm d-none" id="syncSpinner" role="status" aria-hidden="true"></span>
                            <span id="syncBtnText">Mulai Sinkronisasi</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi Grafik Aktivitas Sinkronisasi 7 Hari
            const chartCanvas = document.getElementById('syncActivityChart');
            if (chartCanvas && typeof Chart !== 'undefined') {
                const activityData = @json($chartData);
                new Chart(chartCanvas, {
                    type: 'bar',
                    data: {
                        labels: activityData.labels,
                        datasets: [
                            {
                                label: 'Sinkronisasi Sukses',
                                data: activityData.success,
                                backgroundColor: '#477B59',
                                borderRadius: 4,
                                barPercentage: 0.6,
                            },
                            {
                                label: 'Kendala / Timeout / Gagal',
                                data: activityData.failed,
                                backgroundColor: '#B94035',
                                borderRadius: 4,
                                barPercentage: 0.6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    font: { family: 'Inter', size: 11 }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#17392D',
                                padding: 10,
                                callbacks: {
                                    label: function (ctx) {
                                        return `${ctx.dataset.label}: ${ctx.parsed.y} sesi eksekusi`;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Inter', size: 11 } }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0, font: { family: 'Inter', size: 11 } },
                                grid: { color: 'rgba(0,0,0,0.05)' }
                            }
                        }
                    }
                });
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl = document.getElementById('syncConfirmModal');
            if (!modalEl) return;

            const syncForm = document.getElementById('syncConfirmForm');
            const targetNameEl = document.getElementById('syncTargetName');
            const targetProviderEl = document.getElementById('syncTargetProvider');
            const btnSubmit = document.getElementById('btnSubmitSync');
            const btnCancel = document.getElementById('btnCancelSync');
            const syncSpinner = document.getElementById('syncSpinner');
            const syncIcon = document.getElementById('syncIconNormal');
            const syncBtnText = document.getElementById('syncBtnText');

            document.querySelectorAll('[data-sync-trigger]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const key = this.getAttribute('data-sync-key');
                    const name = this.getAttribute('data-sync-name');
                    const provider = this.getAttribute('data-sync-provider');

                    if (targetNameEl) targetNameEl.textContent = name;
                    if (targetProviderEl) targetProviderEl.textContent = provider;

                    const actionUrl = "{{ url('/admin/integrasi/sync') }}/" + key;
                    if (syncForm) syncForm.setAttribute('action', actionUrl);

                    if (btnSubmit) btnSubmit.removeAttribute('disabled');
                    if (btnCancel) btnCancel.removeAttribute('disabled');
                    if (syncSpinner) syncSpinner.classList.add('d-none');
                    if (syncIcon) syncIcon.classList.remove('d-none');
                    if (syncBtnText) syncBtnText.textContent = 'Mulai Sinkronisasi';

                    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        bsModal.show();
                    } else {
                        if (confirm('Jalankan sinkronisasi manual untuk ' + name + '?')) {
                            syncForm.submit();
                        }
                    }
                });
            });

            if (syncForm) {
                syncForm.addEventListener('submit', function () {
                    if (btnSubmit) {
                        btnSubmit.setAttribute('disabled', 'disabled');
                    }
                    if (btnCancel) {
                        btnCancel.setAttribute('disabled', 'disabled');
                    }
                    if (syncSpinner) syncSpinner.classList.remove('d-none');
                    if (syncIcon) syncIcon.classList.add('d-none');
                    if (syncBtnText) syncBtnText.textContent = 'Menghubungkan & Memproses...';
                });
            }
        });
    </script>
    @endpush


@endsection

