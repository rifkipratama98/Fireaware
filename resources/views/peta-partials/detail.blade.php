<div class="panel detail-panel">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="h6 fw-semibold mb-0">Detail Hotspot</h2>
        <span class="badge-demo">DEMO</span>
    </div>

    <div id="detailPlaceholder" class="detail-placeholder">
        <i class="bi bi-cursor"></i>
        <p class="small text-secondary-soft mb-0">Klik salah satu titik pada peta untuk melihat detail.</p>
    </div>

    <dl id="detailContent" class="detail-list row gx-2 gy-3" hidden>
        <div class="col-12">
            <dt>Waktu deteksi</dt>
            <dd data-field="detected_at">—</dd>
        </div>
        <div class="col-12">
            <dt>Koordinat</dt>
            <dd data-field="coord">—</dd>
        </div>
        <div class="col-12">
            <dt>Provinsi</dt>
            <dd data-field="province">—</dd>
        </div>
        <div class="col-12">
            <dt>Confidence</dt>
            <dd><span data-field="confidence">—</span></dd>
        </div>
        <div class="col-12 d-none" data-row="sensor">
            <dt>Sensor / Satelit</dt>
            <dd data-field="sensor">—</dd>
        </div>
        <div class="col-12 d-none" data-row="frp">
            <dt>FRP</dt>
            <dd data-field="frp">—</dd>
        </div>
        <div class="col-12">
            <dt>Sumber Data</dt>
            <dd data-field="source">—</dd>
        </div>
    </dl>

    <hr class="my-3">

    <div class="map-source">
        <div class="small text-secondary-soft">
            <i class="bi bi-database me-1"></i><span data-slot="source-label">Data DEMO internal</span>
        </div>
        <div class="small text-secondary-soft mt-1">
            <i class="bi bi-clock-history me-1"></i>Pembaruan: <span data-slot="updated-at">—</span>
        </div>
        <div class="small text-warning mt-2">
            <i class="bi bi-info-circle me-1"></i>Hotspot bukan bukti pasti kebakaran.
        </div>
    </div>
</div>
