<div class="stat-card p-3 p-md-4 h-100 d-flex flex-column">
    <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <span class="stat-icon bg-mist text-primary fs-5" aria-hidden="true">
                <i class="bi {{ $src['icon'] }}"></i>
            </span>
            <div>
                <h3 class="h6 mb-0 fw-bold">{{ $src['name'] }}</h3>
                <span class="small text-secondary-soft">{{ $src['provider'] }}</span>
            </div>
        </div>
    </div>

    {{-- Indikator Status --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        @php
            $isConfigured = $src['is_configured'] ?? ($src['connection_status'] === 'Terkonfigurasi');
            $connClass = $isConfigured ? 'status-ok' : 'status-warn';

            $syncStatusText = is_string($src['sync_status']) ? $src['sync_status'] : 'Belum Pernah Sinkron';
            $syncClass = match(true) {
                str_contains($syncStatusText, 'Berhasil') || $syncStatusText === 'Mutakhir' => 'status-ok',
                str_contains($syncStatusText, 'Timeout') || str_contains($syncStatusText, 'Perlu Perhatian') => 'status-warn',
                str_contains($syncStatusText, 'Gagal') || str_contains($syncStatusText, 'Terlambat') => 'status-err',
                default => 'status-empty',
            };

            $totalRec = $src['records_count'] ?? $src['total_records'] ?? 0;
            $dataClass = $totalRec > 0 ? 'status-ok' : 'status-empty';
        @endphp

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

    {{-- Waktu Sinkronisasi & Waktu Observasi Sensor --}}
    <div class="bg-soft rounded-3 p-3 mb-3 small">
        <div class="row g-2">
            <div class="col-sm-6">
                <div class="text-secondary-soft">Waktu Sinkronisasi Terakhir</div>
                <div class="fw-semibold text-truncate">
                    @if (!empty($src['last_sync_at']))
                        {{ \Illuminate\Support\Carbon::parse($src['last_sync_at'])->translatedFormat('d M Y, H:i') }}
                        <span class="text-secondary-soft fw-normal">({{ \Illuminate\Support\Carbon::parse($src['last_sync_at'])->diffForHumans() }})</span>
                    @else
                        <span class="text-secondary-soft fst-italic">Belum pernah sinkron</span>
                    @endif
                </div>
            </div>
            <div class="col-sm-6">
                <div class="text-secondary-soft">Waktu Observasi Data</div>
                <div class="fw-semibold text-truncate">
                    @if (!empty($src['last_observation_at']))
                        {{ \Illuminate\Support\Carbon::parse($src['last_observation_at'])->translatedFormat('d M Y, H:i') }}
                    @else
                        <span class="text-secondary-soft fst-italic">Belum tersedia</span>
                    @endif
                </div>
            </div>
            @if (isset($src['records_added_last']) || isset($src['records_updated_last']))
                <div class="col-12 pt-2 border-top border-soft d-flex justify-content-between align-items-center">
                    <span class="text-secondary-soft">Batch Terakhir:</span>
                    <span>
                        <span class="text-success fw-semibold">+{{ $src['records_added_last'] ?? 0 }} baru</span>
                        <span class="mx-1">&bull;</span>
                        <span class="text-dark">{{ $src['records_updated_last'] ?? 0 }} diperbarui</span>
                    </span>
                </div>
            @endif
        </div>
    </div>


    {{-- Pesan Kesalahan / Peringatan --}}
    @if (!empty($src['errors']))
        <div class="alert alert-warning py-2 px-3 small mb-3" role="alert">
            <div class="fw-semibold mb-1 d-flex align-items-center gap-1 text-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>Catatan Kendala:</span>
            </div>
            <ul class="mb-0 ps-3">
                @foreach ($src['errors'] as $err)
                    <li class="text-break">{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="small text-secondary-soft d-flex align-items-center gap-1 mb-3">
            <i class="bi bi-shield-check text-success"></i>
            <span>Parameter integrasi beroperasi normal tanpa kendala.</span>
        </div>
    @endif

    {{-- Parameter Tambahan --}}
    @if (!empty($src['meta']))
        <div class="mb-3 small">
            @foreach ($src['meta'] as $metaLabel => $metaVal)
                <div class="d-flex justify-content-between py-1 border-bottom border-soft">
                    <span class="text-secondary-soft">{{ $metaLabel }}:</span>
                    <span class="fw-medium text-dark">{{ $metaVal }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Tombol Sinkronisasi Manual --}}
    <div class="mt-auto pt-3 border-top d-flex gap-2">
        <button type="button"
                class="btn btn-outline-primary btn-sm w-100 d-inline-flex align-items-center justify-content-center gap-1"
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
