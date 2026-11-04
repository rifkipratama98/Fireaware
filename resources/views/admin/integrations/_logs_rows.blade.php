@forelse ($logs as $log)
    <tr>
        <td>
            <div class="fw-medium text-dark small">{{ $log->created_at->translatedFormat('d M Y, H:i:s') }}</div>
            <div class="text-secondary-soft" style="font-size: .75rem;">{{ $log->created_at->diffForHumans() }}</div>
        </td>
        <td>
            <span class="fw-semibold text-dark small d-block">{{ $log->source_name }}</span>
            <code class="small text-secondary">{{ $log->source_key }}</code>
        </td>
        <td class="text-center">
            <span class="badge {{ $log->statusBadgeClass() }} px-2 py-1 small">
                <i class="bi {{ $log->statusIcon() }} me-1"></i>{{ $log->statusLabel() }}
            </span>
        </td>
        <td class="text-center">
            @if ($log->records_processed > 0)
                <div class="fw-semibold text-dark small">{{ number_format($log->records_processed) }}</div>
                <div class="text-secondary-soft" style="font-size: .75rem;">
                    <span class="text-success">+{{ $log->records_added }}</span> /
                    <span>~{{ $log->records_updated }}</span>
                </div>
            @else
                <span class="text-muted small">0</span>
            @endif
        </td>
        <td>
            @if ($log->observation_at)
                <div class="small fw-medium text-dark">{{ $log->observation_at->translatedFormat('d M Y, H:i') }}</div>
            @else
                <span class="text-muted small">—</span>
            @endif
        </td>
        <td>
            <span class="badge bg-light text-secondary border small">{{ $log->durationFormatted() }}</span>
        </td>
        <td>
            <span class="small text-secondary-soft">
                @if ($log->trigger === 'manual')
                    <i class="bi bi-person me-1"></i>Manual ({{ $log->user?->name ?? 'Admin' }})
                @elseif ($log->trigger === 'scheduled')
                    <i class="bi bi-clock me-1"></i>Scheduler
                @else
                    <i class="bi bi-terminal me-1"></i>Artisan CLI
                @endif
            </span>
        </td>
        <td>
            @if ($log->error_message)
                <div class="small text-danger text-break" style="max-width: 280px;">
                    <i class="bi bi-exclamation-triangle me-1"></i>{{ $log->error_message }}
                </div>
            @elseif ($log->payload_summary)
                <div class="small text-secondary-soft text-truncate" style="max-width: 280px;" title="{{ $log->payload_summary }}">
                    {{ $log->payload_summary }}
                </div>
            @else
                <span class="text-muted small">—</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center py-4 text-secondary-soft">
            <i class="bi bi-inbox fs-4 d-block mb-1"></i>
            Belum ada riwayat sinkronisasi data yang tercatat.
        </td>
    </tr>
@endforelse
