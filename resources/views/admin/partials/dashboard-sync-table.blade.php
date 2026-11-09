{{-- Ringkasan Status Sinkronisasi Telemetri --}}
<div class="row g-2 g-md-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card p-3 d-flex align-items-center gap-3">
            <span class="stat-icon bg-mist text-primary fs-5" aria-hidden="true"><i class="bi bi-arrow-repeat"></i></span>
            <div>
                <div class="stat-value fs-5">{{ number_format($syncStats['total']) }}</div>
                <div class="stat-label small">Total Sinkronisasi</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card p-3 d-flex align-items-center gap-3">
            <span class="stat-icon bg-success bg-opacity-10 text-success fs-5" aria-hidden="true"><i class="bi bi-check-circle"></i></span>
            <div>
                <div class="stat-value fs-5 text-success">{{ number_format($syncStats['success']) }}</div>
                <div class="stat-label small">Sinkron Berhasil</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card p-3 d-flex align-items-center gap-3">
            <span class="stat-icon bg-danger bg-opacity-10 text-danger fs-5" aria-hidden="true"><i class="bi bi-exclamation-octagon"></i></span>
            <div>
                <div class="stat-value fs-5 {{ $syncStats['failed'] > 0 ? 'text-danger' : 'text-dark' }}">{{ number_format($syncStats['failed']) }}</div>
                <div class="stat-label small">Kendala / Gagal</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card p-3 d-flex align-items-center gap-3">
            <span class="stat-icon bg-secondary bg-opacity-10 text-secondary fs-5" aria-hidden="true"><i class="bi bi-gear"></i></span>
            <div>
                <div class="stat-value fs-5 text-secondary">{{ number_format($syncStats['not_configured']) }}</div>
                <div class="stat-label small">Belum Dikonfigurasi</div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Aktivitas Terbaru Sinkronisasi --}}
<div class="table-responsive rounded-3 border border-soft">
    <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
        <thead class="table-light text-secondary text-uppercase" style="font-size: 0.725rem; letter-spacing: 0.04em;">
            <tr>
                <th scope="col" class="ps-3 py-2">Waktu Sinkronisasi</th>
                <th scope="col" class="py-2">Sumber Data</th>
                <th scope="col" class="py-2">Status</th>
                <th scope="col" class="py-2">Hasil Rekaman</th>
                <th scope="col" class="py-2">Durasi</th>
                <th scope="col" class="pe-3 py-2 text-end">Pemicu</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recentSyncLogs as $log)
                <tr>
                    <td class="ps-3 py-3">
                        <div class="fw-semibold text-dark">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</div>
                        <div class="text-secondary small">{{ $log->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="py-3">
                        <span class="badge bg-mist text-primary border border-soft me-1">{{ strtoupper($log->source_key) }}</span>
                        <span class="fw-medium text-dark">{{ $log->source_name }}</span>
                    </td>
                    <td class="py-3">
                        <span class="badge {{ $log->statusBadgeClass() }} d-inline-flex align-items-center gap-1 px-2 py-1">
                            <i class="bi {{ $log->statusIcon() }}"></i>
                            <span>{{ $log->statusLabel() }}</span>
                        </span>
                    </td>
                    <td class="py-3">
                        @if ($log->status === \App\Models\SyncLog::STATUS_SUCCESS)
                            <span class="text-success fw-semibold">+{{ number_format($log->records_added) }} baru</span>
                            <span class="text-muted">&bull;</span>
                            <span class="text-secondary">{{ number_format($log->records_updated) }} diperbarui</span>
                        @elseif ($log->error_message)
                            <span class="text-danger small" title="{{ $log->error_message }}">
                                <i class="bi bi-info-circle me-1"></i>{{ \Illuminate\Support\Str::limit($log->error_message, 45) }}
                            </span>
                        @else
                            <span class="text-muted small fst-italic">Tidak ada penambahan data</span>
                        @endif
                    </td>
                    <td class="py-3 text-secondary">{{ $log->durationFormatted() }}</td>
                    <td class="pe-3 py-3 text-end">
                        <span class="badge {{ $log->trigger === 'manual' ? 'bg-light text-dark border' : 'bg-mist text-primary' }}">
                            {{ $log->trigger === 'manual' ? 'Manual' : 'Otomatis' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-secondary">
                        <i class="bi bi-clock-history fs-4 d-block mb-1 text-muted" aria-hidden="true"></i>
                        <span>Belum ada riwayat aktivitas sinkronisasi yang tercatat di basis data.</span>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>