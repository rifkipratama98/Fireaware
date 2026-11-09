@forelse ($logs as $log)
    @php
        $badgeClass = match ($log->status) {
            \App\Models\SyncLog::STATUS_SUCCESS => 'badge-infra-connected',
            \App\Models\SyncLog::STATUS_FAILED => 'badge-infra-failed',
            \App\Models\SyncLog::STATUS_TIMEOUT => 'badge-infra-stale',
            \App\Models\SyncLog::STATUS_NOT_CONFIGURED => 'badge-infra-disconnected',
            \App\Models\SyncLog::STATUS_RUNNING => 'badge-infra-pending',
            default => 'bg-light text-dark border',
        };
    @endphp
    <tr>
        <td class="ps-3 py-3">
            <div class="fw-semibold text-dark small font-monospace">{{ $log->created_at->translatedFormat('d M Y, H:i:s') }}</div>
            <div class="text-secondary small" style="font-size: .75rem;">{{ $log->created_at->diffForHumans() }}</div>
        </td>
        <td class="py-3">
            <div class="d-flex align-items-center gap-1-5">
                <span class="badge bg-mist text-primary border border-soft fw-bold" style="font-size: 0.7rem;">
                    {{ strtoupper($log->source_key) }}
                </span>
                <span class="fw-medium text-dark small">{{ $log->source_name }}</span>
            </div>
        </td>
        <td class="text-center py-3">
            <span class="badge {{ $badgeClass }} px-2 py-1 small d-inline-flex align-items-center gap-1">
                <i class="bi {{ $log->statusIcon() }}"></i>
                <span>{{ $log->statusLabel() }}</span>
            </span>
        </td>
        <td class="text-center py-3">
            @if ($log->records_processed > 0)
                <div class="fw-bold text-dark small">{{ number_format($log->records_processed) }}</div>
                <div class="small" style="font-size: .72rem;">
                    <span class="text-success fw-semibold">+{{ number_format($log->records_added) }}</span>
                    <span class="text-muted mx-0-5">&bull;</span>
                    <span class="text-secondary">~{{ number_format($log->records_updated) }}</span>
                </div>
            @else
                <span class="text-muted small">0 rekaman</span>
            @endif
        </td>
        <td class="py-3">
            @if ($log->observation_at)
                <div class="small fw-medium text-dark">{{ $log->observation_at->translatedFormat('d M Y, H:i') }}</div>
                <div class="text-muted" style="font-size: 0.72rem;">{{ $log->observation_at->diffForHumans() }}</div>
            @else
                <span class="text-muted small">—</span>
            @endif
        </td>
        <td class="py-3">
            <span class="badge bg-light text-secondary border border-soft small font-monospace">
                <i class="bi bi-stopwatch me-1 text-muted"></i>{{ $log->durationFormatted() }}
            </span>
        </td>
        <td class="py-3">
            <span class="badge {{ $log->trigger === 'manual' ? 'bg-light text-dark border border-soft' : ($log->trigger === 'scheduled' ? 'bg-mist text-primary' : 'bg-secondary bg-opacity-10 text-secondary') }}" style="font-size: 0.75rem;">
                @if ($log->trigger === 'manual')
                    <i class="bi bi-person me-1"></i>Manual ({{ $log->user?->name ?? 'Admin' }})
                @elseif ($log->trigger === 'scheduled')
                    <i class="bi bi-clock me-1"></i>Scheduler Cron
                @else
                    <i class="bi bi-terminal me-1"></i>CLI
                @endif
            </span>
        </td>
        <td class="py-3 pe-3">
            @if ($log->error_message)
                <div class="infra-error-box py-1 px-2 small text-danger text-break" style="max-width: 320px; font-size: 0.78rem;" title="{{ $log->error_message }}">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ \Illuminate\Support\Str::limit($log->error_message, 85) }}
                </div>
            @elseif ($log->payload_summary)
                <div class="small text-secondary text-truncate" style="max-width: 320px;" title="{{ $log->payload_summary }}">
                    <i class="bi bi-info-circle me-1 text-primary"></i>{{ $log->payload_summary }}
                </div>
            @else
                <span class="text-muted small fst-italic">Diproses normal</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center py-5 text-secondary">
            <i class="bi bi-inbox fs-2 d-block mb-2 text-muted" aria-hidden="true"></i>
            <span class="fw-semibold d-block">Belum ada riwayat log sinkronisasi yang tercatat.</span>
            <span class="small text-muted">Gunakan tombol "Sinkronkan Semua Sumber Data" atau jalankan cron scheduler server.</span>
        </td>
    </tr>
@endforelse

