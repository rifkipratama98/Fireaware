@forelse ($logs as $log)
    @php
        $badgeClass = match ($log->status) {
            \App\Models\NotificationLog::STATUS_SENT => 'badge-status-published',
            \App\Models\NotificationLog::STATUS_FAILED => 'badge-infra-failed',
            \App\Models\NotificationLog::STATUS_SUPPRESSED => 'badge-infra-stale',
            \App\Models\NotificationLog::STATUS_PENDING => 'badge-infra-pending',
            default => 'bg-light text-dark border',
        };
    @endphp
    <tr>
        <td class="ps-3 py-3">
            <div class="fw-semibold text-dark small font-monospace">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</div>
            <div class="text-secondary small" style="font-size: .75rem;">{{ $log->created_at->diffForHumans() }}</div>
        </td>
        <td class="py-3">
            <div class="fw-semibold text-dark small mb-0-5">{{ $log->title }}</div>
            <div class="small text-secondary text-truncate" style="max-width: 300px;" title="{{ $log->message }}">
                {{ $log->message }}
            </div>
        </td>
        <td class="py-3">
            <span class="badge {{ $log->levelBadgeClass() }} small px-2 py-1">
                {{ $log->categoryLabel() }}
            </span>
        </td>
        <td class="py-3">
            <span class="small fw-semibold text-dark d-block">{{ $log->channelLabel() }}</span>
            @if ($log->recipient_target)
                <span class="text-secondary font-monospace" style="font-size: .72rem;">{{ Str::limit($log->recipient_target, 32) }}</span>
            @endif
        </td>
        <td class="py-3">
            <span class="small text-dark d-inline-flex align-items-center gap-1">
                <i class="bi bi-geo-alt text-primary" style="font-size: 0.8rem;"></i>
                <span>{{ $log->province?->name ?? 'Seluruh Kalimantan' }}</span>
            </span>
        </td>
        <td class="text-center py-3">
            <span class="badge {{ $badgeClass }} px-2 py-1 small d-inline-flex align-items-center gap-1">
                <i class="bi {{ $log->status === 'sent' ? 'bi-check-circle-fill' : ($log->status === 'failed' ? 'bi-x-circle-fill' : 'bi-slash-circle') }}"></i>
                <span>{{ $log->statusLabel() }}</span>
            </span>
        </td>
        <td class="py-3 pe-3">
            @if ($log->error_message)
                <div class="infra-error-box py-1 px-2 small text-danger text-break" style="max-width: 260px; font-size: 0.78rem;">
                    <i class="bi bi-info-circle me-1"></i>{{ $log->error_message }}
                </div>
            @else
                <span class="text-muted small fst-italic">Terkirim normal</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-5 text-secondary">
            <i class="bi bi-bell-slash fs-2 d-block mb-2 text-muted"></i>
            <span class="fw-semibold d-block">Belum ada catatan riwayat pengiriman notifikasi.</span>
            <span class="small text-muted">Kirim notifikasi uji coba untuk menguji konektivitas kanal yang aktif.</span>
        </td>
    </tr>
@endforelse
