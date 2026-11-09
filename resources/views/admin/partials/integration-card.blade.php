@php
    $isConfigured = $src['is_configured'] ?? ($src['connection_status'] === 'Terkonfigurasi');
    $lastLog = $src['last_log'] ?? null;
    $lastSyncAt = !empty($src['last_sync_at']) ? \Illuminate\Support\Carbon::parse($src['last_sync_at']) : null;
    $lastObsAt = !empty($src['last_observation_at']) ? \Illuminate\Support\Carbon::parse($src['last_observation_at']) : null;
    $totalRec = $src['records_count'] ?? $src['total_records'] ?? 0;

    // Klasifikasi status infrastruktur data: Connected, Failed, Pending, Stale, Disconnected
    if (! $isConfigured) {
        $infraStatusKey = 'disconnected';
        $infraStatusLabel = 'Disconnected';
        $infraStatusClass = 'badge-infra-disconnected';
        $infraStatusIcon = 'bi-plug';
    } elseif ($lastLog && $lastLog->status === \App\Models\SyncLog::STATUS_RUNNING) {
        $infraStatusKey = 'pending';
        $infraStatusLabel = 'Pending (Berjalan)';
        $infraStatusClass = 'badge-infra-pending';
        $infraStatusIcon = 'bi-arrow-repeat';
    } elseif ($lastLog && in_array($lastLog->status, [\App\Models\SyncLog::STATUS_FAILED, \App\Models\SyncLog::STATUS_TIMEOUT])) {
        $infraStatusKey = 'failed';
        $infraStatusLabel = $lastLog->status === \App\Models\SyncLog::STATUS_TIMEOUT ? 'Failed (Timeout)' : 'Failed (Error)';
        $infraStatusClass = 'badge-infra-failed';
        $infraStatusIcon = 'bi-exclamation-octagon-fill';
    } elseif (! $lastSyncAt || $lastSyncAt->lt(now()->subHours(24))) {
        $infraStatusKey = 'stale';
        $infraStatusLabel = ! $lastSyncAt ? 'Stale (Belum Sinkron)' : 'Stale (>24 Jam)';
        $infraStatusClass = 'badge-infra-stale';
        $infraStatusIcon = 'bi-clock-history';
    } else {
        $infraStatusKey = 'connected';
        $infraStatusLabel = 'Connected (Aktif)';
        $infraStatusClass = 'badge-infra-connected';
        $infraStatusIcon = 'bi-check-circle-fill';
    }

    $connClass = $isConfigured ? 'status-ok' : 'status-warn';
    $syncStatusText = is_string($src['sync_status']) ? $src['sync_status'] : 'Belum Pernah Sinkron';
    $syncClass = match(true) {
        str_contains($syncStatusText, 'Berhasil') || $syncStatusText === 'Mutakhir' => 'status-ok',
        str_contains($syncStatusText, 'Timeout') || str_contains($syncStatusText, 'Perlu Perhatian') => 'status-warn',
        str_contains($syncStatusText, 'Gagal') || str_contains($syncStatusText, 'Terlambat') => 'status-err',
        default => 'status-empty',
    };
    $dataClass = $totalRec > 0 ? 'status-ok' : 'status-empty';
@endphp

<div class="stat-card p-3 p-md-4 h-100 d-flex flex-column shadow-xs">
    {{-- Header Sumber Data & Status Badge Infrastruktur --}}
    <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
        <div class="d-flex align-items-center gap-2 min-w-0">
            <span class="stat-icon bg-mist text-primary fs-5 flex-shrink-0" aria-hidden="true">
                <i class="bi {{ $src['icon'] }}"></i>
            </span>
            <div class="min-w-0">
                <h3 class="h6 mb-0 fw-bold text-dark text-truncate">{{ $src['name'] }}</h3>
                <span class="small text-secondary text-truncate d-block">{{ $src['provider'] }}</span>
            </div>
        </div>
        <span class="badge {{ $infraStatusClass }} px-2 py-1 small flex-shrink-0 d-inline-flex align-items-center gap-1" title="Kondisi Status Infrastruktur Data">
            <i class="bi {{ $infraStatusIcon }}"></i>
            <span>{{ $infraStatusLabel }}</span>
        </span>
    </div>

    {{-- Indikator Status Teknis (Diferensiasi Koneksi, Sinkron, Data) --}}
    <div class="d-flex flex-wrap gap-1 mb-3">
        <span class="status-pill {{ $connClass }}" title="Status konfigurasi koneksi">
            <i class="bi {{ $isConfigured ? 'bi-check-circle' : 'bi-exclamation-circle' }}"></i>
            Koneksi: {{ $src['connection_status'] }}
        </span>

        <span class="status-pill {{ $syncClass }}" title="Status sinkronisasi terakhir">
            <i class="bi bi-clock-history"></i>
            Sinkron: {{ $src['sync_status'] }}
        </span>

        <span class="status-pill {{ $dataClass }}" title="Total data tersimpan di basis data">
            <i class="bi bi-database"></i>
            Data: {{ number_format($totalRec) }} rekaman
        </span>
    </div>

    {{-- Waktu Sinkronisasi Terakhir & Observasi Sensor --}}
    <div class="bg-soft rounded-3 p-2-5 p-md-3 mb-3 small border border-soft">
        <div class="row g-2">
            <div class="col-sm-6">
                <div class="text-secondary small fw-medium">Waktu Sinkronisasi Terakhir</div>
                <div class="fw-semibold text-dark text-truncate">
                    @if ($lastSyncAt)
                        {{ $lastSyncAt->translatedFormat('d M Y, H:i') }}
                        <span class="text-secondary fw-normal d-block" style="font-size: 0.75rem;">
                            ({{ $lastSyncAt->diffForHumans() }})
                        </span>
                    @else
                        <span class="text-muted fst-italic">Belum pernah sinkron</span>
                    @endif
                </div>
            </div>
            <div class="col-sm-6">
                <div class="text-secondary small fw-medium">Waktu Observasi Sensor</div>
                <div class="fw-semibold text-dark text-truncate">
                    @if ($lastObsAt)
                        {{ $lastObsAt->translatedFormat('d M Y, H:i') }}
                        <span class="text-secondary fw-normal d-block" style="font-size: 0.75rem;">
                            ({{ $lastObsAt->diffForHumans() }})
                        </span>
                    @else
                        <span class="text-muted fst-italic">Belum tersedia</span>
                    @endif
                </div>
            </div>

            @if (isset($src['records_added_last']) || isset($src['records_updated_last']))
                <div class="col-12 pt-2 border-top border-soft d-flex justify-content-between align-items-center" style="font-size: 0.78rem;">
                    <span class="text-secondary">Batch Terakhir:</span>
                    <span>
                        <span class="text-success fw-semibold">+{{ $src['records_added_last'] ?? 0 }} baru</span>
                        <span class="mx-1 text-muted">&bull;</span>
                        <span class="text-dark fw-medium">{{ $src['records_updated_last'] ?? 0 }} diperbarui</span>
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- Detail Error API secara Rapi --}}
    @if (!empty($src['errors']))
        <div class="infra-error-box mb-3" role="alert">
            <div class="fw-bold mb-1 d-flex align-items-center gap-1 text-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>Catatan Kendala Telemetri:</span>
            </div>
            <ul class="mb-0 ps-3">
                @foreach ($src['errors'] as $err)
                    <li class="text-break">{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="small text-secondary d-flex align-items-center gap-1 mb-3">
            <i class="bi bi-shield-check text-success"></i>
            <span>Parameter integrasi beroperasi normal tanpa kendala.</span>
        </div>
    @endif

    {{-- Parameter Spesifikasi Sumber Data --}}
    @if (!empty($src['meta']))
        <div class="mb-3 small">
            @foreach ($src['meta'] as $metaLabel => $metaVal)
                <div class="d-flex justify-content-between py-1 border-bottom border-soft" style="font-size: 0.8rem;">
                    <span class="text-secondary">{{ $metaLabel }}:</span>
                    <span class="fw-medium text-dark text-end ms-2">{{ $metaVal }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Tombol Sinkronisasi Manual --}}
    <div class="mt-auto pt-3 border-top border-soft">
        <button type="button"
                class="btn btn-outline-primary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1 rounded-3"
                data-sync-trigger
                data-sync-key="{{ $src['key'] ?? $src['id'] }}"
                data-sync-name="{{ $src['name'] }}"
                data-sync-provider="{{ $src['provider'] }}"
                data-sync-configured="{{ $isConfigured ? '1' : '0' }}">
            <i class="bi bi-arrow-repeat"></i>
            <span>Sinkronisasi Manual</span>
        </button>
    </div>
</div>
