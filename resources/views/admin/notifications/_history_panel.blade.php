{{-- Bagian Riwayat Pengiriman Notifikasi --}}
<div class="panel p-3 p-md-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h2 class="h5 fw-bold mb-1 text-dark">Riwayat Log Pengiriman Notifikasi</h2>
            <p class="text-secondary-soft small mb-0">Catatan pengiriman notifikasi, evaluasi ambang batas bahaya, dan penekanan anti-spam.</p>
        </div>
    </div>

    {{-- Toolbar Filter --}}
    <form method="GET" action="{{ route('admin.notifications.index') }}" class="row g-2 align-items-center mb-4">
        <div class="col-md-3">
            <select name="kanal" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Kanal</option>
                <option value="browser" {{ $channelFilter === 'browser' ? 'selected' : '' }}>Browser (Masyarakat)</option>
                <option value="email" {{ $channelFilter === 'email' ? 'selected' : '' }}>Email Posko</option>
                <option value="webhook" {{ $channelFilter === 'webhook' ? 'selected' : '' }}>Webhook Koordinasi</option>
                <option value="system" {{ $channelFilter === 'system' ? 'selected' : '' }}>Sistem Internal</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="kategori" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                <option value="hotspot" {{ $categoryFilter === 'hotspot' ? 'selected' : '' }}>Titik Panas (Hotspot)</option>
                <option value="air_quality" {{ $categoryFilter === 'air_quality' ? 'selected' : '' }}>Kualitas Udara (PM2.5)</option>
                <option value="announcement" {{ $categoryFilter === 'announcement' ? 'selected' : '' }}>Pengumuman Resmi</option>
                <option value="test" {{ $categoryFilter === 'test' ? 'selected' : '' }}>Uji Coba Pengiriman</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="sent" {{ $statusFilter === 'sent' ? 'selected' : '' }}>Terkirim</option>
                <option value="suppressed" {{ $statusFilter === 'suppressed' ? 'selected' : '' }}>Ditekan Anti-Spam</option>
                <option value="failed" {{ $statusFilter === 'failed' ? 'selected' : '' }}>Gagal</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary btn-sm w-100">Filter Riwayat</button>
            @if ($channelFilter !== '' || $statusFilter !== '' || $categoryFilter !== '')
                <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset filter">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>

    {{-- Tabel Riwayat --}}
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="bg-soft">
                <tr>
                    <th scope="col" style="min-width: 160px;">Waktu Kirim</th>
                    <th scope="col">Judul &amp; Pesan</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Kanal</th>
                    <th scope="col">Wilayah</th>
                    <th scope="col" class="text-center">Status</th>
                    <th scope="col" style="min-width: 180px;">Keterangan / Error</th>
                </tr>
            </thead>
            <tbody>
                @include('admin.notifications._history_rows')
            </tbody>
        </table>
    </div>

    @if ($logs->hasPages())
        <div class="p-3 border-top border-soft d-flex justify-content-center">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
