<div class="panel detail-panel p-3 p-lg-4 shadow-sm">
    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-soft">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-geo-alt-fill text-danger"></i>
            <h2 class="h6 fw-bold mb-0 text-dark">Detail Hotspot</h2>
        </div>
        <span class="badge-demo">DEMO</span>
    </div>

    <div id="detailPlaceholder" class="detail-placeholder py-4 text-center">
        <i class="bi bi-cursor fs-3 text-secondary-soft mb-2 d-block"></i>
        <p class="small text-secondary-soft mb-0">Klik salah satu titik pada peta untuk melihat detail telemetri satelit.</p>
    </div>

    <dl id="detailContent" class="detail-list row gx-2 gy-2 mb-0" hidden>
        <div class="col-12 p-2 bg-soft rounded-2 border border-soft mb-1">
            <dt class="small text-secondary-soft fw-medium">Waktu deteksi</dt>
            <dd data-field="detected_at" class="fw-bold text-dark mb-0">—</dd>
        </div>
        <div class="col-12 p-2 bg-soft rounded-2 border border-soft mb-1">
            <dt class="small text-secondary-soft fw-medium">Koordinat</dt>
            <dd data-field="coord" class="font-monospace small fw-bold text-dark mb-0">—</dd>
        </div>
        <div class="col-12 p-2 bg-soft rounded-2 border border-soft mb-1">
            <dt class="small text-secondary-soft fw-medium">Provinsi</dt>
            <dd data-field="province" class="fw-bold text-dark mb-0">—</dd>
        </div>
        <div class="col-12 p-2 bg-soft rounded-2 border border-soft mb-1">
            <dt class="small text-secondary-soft fw-medium">Tingkat Kepercayaan</dt>
            <dd class="mb-0"><span data-field="confidence" class="fw-bold">—</span></dd>
        </div>
        <div class="col-12 p-2 bg-soft rounded-2 border border-soft mb-1 d-none" data-row="sensor">
            <dt class="small text-secondary-soft fw-medium">Sensor / Satelit</dt>
            <dd data-field="sensor" class="fw-bold text-dark mb-0">—</dd>
        </div>
        <div class="col-12 p-2 bg-soft rounded-2 border border-soft mb-1 d-none" data-row="frp">
            <dt class="small text-secondary-soft fw-medium">Fire Radiative Power (FRP)</dt>
            <dd data-field="frp" class="fw-bold text-dark mb-0">—</dd>
        </div>
        <div class="col-12 p-2 bg-soft rounded-2 border border-soft mb-1">
            <dt class="small text-secondary-soft fw-medium">Sumber Data</dt>
            <dd data-field="source" class="small fw-semibold text-dark mb-0">—</dd>
        </div>
    </dl>

    <hr class="my-3 border-soft">

    <div class="map-source small text-secondary-soft">
        <div class="d-flex align-items-center gap-1 mb-1">
            <i class="bi bi-database text-primary"></i>
            <span data-slot="source-label">Data DEMO internal</span>
        </div>
        <div class="d-flex align-items-center gap-1 mb-2">
            <i class="bi bi-clock-history text-primary"></i>
            <span>Pembaruan: <span data-slot="updated-at" class="text-dark fw-semibold">—</span></span>
        </div>
        <div class="p-2 bg-warning bg-opacity-10 rounded-2 border border-warning border-opacity-25 text-warning small lh-sm">
            <i class="bi bi-info-circle-fill me-1"></i>
            <strong>Catatan:</strong> Titik panas adalah anomali suhu termal satelit, bukan bukti pasti kebakaran lahan aktif.
        </div>
    </div>
</div>
