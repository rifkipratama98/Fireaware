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

    {{-- Status Tiga Tingkat (Koneksi, Sinkronisasi, Ketersediaan Data) --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        {{-- Status Koneksi --}}
        @php
            $connClass = $src['connection_status'] === 'Terkonfigurasi' ? 'status-ok' : 'status-warn';
            $syncClass = match($src['sync_status']) {
                'Mutakhir' => 'status-ok',
                'Perlu Perhatian' => 'status-warn',
                'Terlambat (> 3 hari)' => 'status-err',
                default => 'status-empty',
            };
            $dataClass = $src['data_status'] === 'Tersedia' ? 'status-ok' : 'status-empty';
        @endphp

        <span class="status-pill {{ $connClass }}" title="Status konfigurasi koneksi sumber data">
            <i class="bi {{ $src['connection_status'] === 'Terkonfigurasi' ? 'bi-check-circle' : 'bi-exclamation-circle' }}"></i>
            Koneksi: {{ $src['connection_status'] }}
        </span>

        <span class="status-pill {{ $syncClass }}" title="Status ketepatan waktu sinkronisasi">
            <i class="bi bi-clock-history"></i>
            Sinkron: {{ $src['sync_status'] }}
        </span>

        <span class="status-pill {{ $dataClass }}" title="Status keberadaan rekaman di basis data">
            <i class="bi bi-database"></i>
            Data: {{ $src['data_status'] }} ({{ number_format($src['total_records']) }})
        </span>
    </div>

    {{-- Waktu Sinkronisasi Terakhir & Data Terakhir --}}
    <div class="bg-soft rounded-3 p-2 p-md-3 mb-3 small">
        <div class="row g-2">
            <div class="col-sm-6">
                <div class="text-secondary-soft">Sinkronisasi Terakhir</div>
                <div class="fw-semibold text-truncate">
                    @if ($src['last_sync_at'])
                        {{ $src['last_sync_at']->translatedFormat('d M Y, H:i') }}
                        <span class="text-secondary-soft fw-normal">({{ $src['last_sync_at']->diffForHumans() }})</span>
                    @else
                        <span class="text-secondary-soft italic">Belum pernah sinkron</span>
                    @endif
                </div>
            </div>
            <div class="col-sm-6">
                <div class="text-secondary-soft">Waktu Data Terakhir</div>
                <div class="fw-semibold text-truncate">
                    @if ($src['last_data_at'])
                        {{ $src['last_data_at']->translatedFormat('d M Y, H:i') }}
                    @else
                        <span class="text-secondary-soft italic">Tidak ada rekaman</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan Kesalahan Jika Ada --}}
    @if (!empty($src['errors']))
        <div class="alert alert-warning py-2 px-3 small mb-3" role="alert">
            <div class="fw-semibold mb-1 d-flex align-items-center gap-1 text-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>Perhatian Integrasi:</span>
            </div>
            <ul class="mb-0 ps-3">
                @foreach ($src['errors'] as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="small text-secondary-soft d-flex align-items-center gap-1 mb-3">
            <i class="bi bi-shield-check text-success"></i>
            <span>Tidak ada kendala integrasi yang terdeteksi.</span>
        </div>
    @endif

    {{-- Riwayat Sinkronisasi Terbaru --}}
    <div class="mt-auto pt-2 border-top">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="small fw-semibold">Riwayat 7 Hari Terakhir</span>
            <span class="small text-secondary-soft">{{ number_format($src['recent_records']) }} rekaman</span>
        </div>

        @if (!empty($src['sync_history']))
            <div class="table-responsive">
                <table class="table table-sm table-borderless small mb-0">
                    <tbody>
                        @foreach (array_slice($src['sync_history'], 0, 4) as $h)
                            <tr>
                                <td class="text-secondary-soft p-1">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ \Illuminate\Support\Carbon::parse($h['date'])->translatedFormat('d M Y') }}
                                </td>
                                <td class="text-end fw-semibold p-1">
                                    {{ number_format($h['count']) }} rekaman
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="small text-secondary-soft py-1">
                Belum ada rekaman pada periode 7 hari terakhir.
            </div>
        @endif
    </div>
</div>
