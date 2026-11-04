@forelse ($logs as $log)
    <tr>
        <td>
            <div class="fw-medium text-dark small">{{ $log->created_at->translatedFormat('d M Y, H:i:s') }}</div>
            <div class="text-secondary-soft" style="font-size: .75rem;">{{ $log->created_at->diffForHumans() }}</div>
        </td>
        <td>
            <div class="fw-semibold text-dark small">{{ $log->title }}</div>
            <div class="text-secondary-soft text-truncate" style="max-width: 280px;" title="{{ $log->message }}">
                {{ $log->message }}
            </div>
        </td>
        <td>
            <span class="badge {{ $log->levelBadgeClass() }} small px-2 py-1">
                {{ $log->categoryLabel() }}
            </span>
        </td>
        <td>
            <span class="small fw-medium text-dark d-block">{{ $log->channelLabel() }}</span>
            @if ($log->recipient_target)
                <span class="text-secondary-soft" style="font-size: .75rem;">{{ Str::limit($log->recipient_target, 35) }}</span>
            @endif
        </td>
        <td>
            <span class="small text-dark">
                {{ $log->province?->name ?? 'Seluruh Kalimantan' }}
            </span>
        </td>
        <td class="text-center">
            <span class="badge {{ $log->statusBadgeClass() }} px-2 py-1 small">
                {{ $log->statusLabel() }}
            </span>
        </td>
        <td>
            @if ($log->error_message)
                <div class="small text-danger text-break" style="max-width: 240px;">
                    <i class="bi bi-info-circle me-1"></i>{{ $log->error_message }}
                </div>
            @else
                <span class="text-muted small">—</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-4 text-secondary-soft">
            <i class="bi bi-bell-slash fs-4 d-block mb-1"></i>
            Belum ada catatan riwayat pengiriman notifikasi.
        </td>
    </tr>
@endforelse
