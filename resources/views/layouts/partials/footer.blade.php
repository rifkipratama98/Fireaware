<footer class="site-footer">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-5">
                <a class="brand mb-3" href="{{ route('home') }}">
                    <span class="brand-mark"><i class="bi bi-tree"></i></span>
                    <span class="brand-name">FireAware<span class="brand-sub"> Kalimantan</span></span>
                </a>
                <p class="text-secondary-soft small mt-3 mb-0 pe-lg-5">
                    Sistem informasi untuk memantau titik panas dan risiko kebakaran hutan di Kalimantan,
                    mendukung mitigasi dan respons yang lebih cepat dan tepat.
                </p>
                <p class="small text-secondary-soft mt-3 mb-0">
                    <i class="bi bi-database me-1"></i> Sumber data: <span class="fw-semibold">data DEMO internal</span>
                    (rencana integrasi: VIIRS/MODIS &amp; BMKG).
                </p>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <h6 class="fw-semibold mb-3">Navigasi</h6>
                <ul class="list-unstyled d-grid gap-2 small">
                    <li><a href="{{ route('home') }}">Beranda</a></li>
                    <li><a href="{{ route('hotspot.map') }}">Peta Hotspot</a></li>
                    <li><a href="{{ route('home') }}#hotspot-terbaru">Data Terbaru</a></li>
                    <li><a href="{{ route('home') }}#pengumuman">Pengumuman</a></li>
                    <li><a href="{{ route('home') }}#panduan">Panduan</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <h6 class="fw-semibold mb-3">Informasi</h6>
                <ul class="list-unstyled d-grid gap-2 small">
                    <li><a href="{{ route('home') }}#panduan">Panduan Keselamatan</a></li>
                    <li><a href="{{ route('air.quality') }}">Kualitas Udara</a></li>
                    <li><a href="{{ route('admin.dashboard') }}">Area Admin</a></li>
                </ul>
            </div>

            <div class="col-lg-3">
                <h6 class="fw-semibold mb-3">Hubungi Kami</h6>
                <ul class="list-unstyled d-grid gap-2 small">
                    <li><i class="bi bi-envelope me-2"></i>halo@fireaware.id</li>
                    <li><i class="bi bi-geo-alt me-2"></i>Banjarmasin, Kalimantan Selatan</li>
                </ul>
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <p class="small text-secondary-soft mb-0">© {{ date('Y') }} FireAware Kalimantan.</p>
            <p class="small text-secondary-soft mb-0">Dibangun untuk lingkungan yang lebih aman.</p>
        </div>
    </div>
</footer>
