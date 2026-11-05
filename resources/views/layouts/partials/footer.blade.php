<footer class="site-footer">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-5">
                <a class="brand mb-3" href="{{ route('home') }}">
                    <span class="brand-mark" aria-hidden="true"><i class="bi bi-tree-fill"></i></span>
                    <span class="brand-text d-flex flex-column lh-1">
                        <span class="brand-name">FireAware</span>
                        <span class="brand-sub">Kalimantan</span>
                    </span>
                </a>
                <p class="text-secondary-soft small mt-3 mb-0 pe-lg-5 lh-base">
                    Platform informasi lingkungan dan peringatan dini kebakaran hutan di Pulau Kalimantan.
                    Mendukung mitigasi, deteksi cepat titik panas satelit, dan literasi kesiapsiagaan masyarakat.
                </p>
                <div class="d-flex align-items-center gap-2 mt-3 p-2 bg-mist rounded-3 border border-soft text-dark small" style="max-width: 440px;">
                    <i class="bi bi-shield-check text-primary fs-5 ms-1"></i>
                    <div class="small lh-sm">
                        <strong class="d-block text-primary">Data Terpadu 5 Provinsi Kalimantan</strong>
                        <span class="text-secondary-soft">Telemetri NASA FIRMS, BMKG Karhutla, &amp; ISPU Udara KLHK.</span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <h6 class="fw-bold mb-3 text-dark text-uppercase small letter-spacing-1">Navigasi</h6>
                <ul class="list-unstyled d-grid gap-2 small mb-0">
                    <li><a href="{{ route('home') }}">Beranda</a></li>
                    <li><a href="{{ route('hotspot.map') }}">Peta Hotspot</a></li>
                    <li><a href="{{ route('air.quality') }}">Kualitas Udara</a></li>
                    <li><a href="{{ route('announcements.index') }}">Pengumuman</a></li>
                    <li><a href="{{ route('edukasi.index') }}">Edukasi Gambut</a></li>
                    <li><a href="{{ route('guides.index') }}">Panduan Mitigasi</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <h6 class="fw-bold mb-3 text-dark text-uppercase small letter-spacing-1">Kesiapsiagaan</h6>
                <ul class="list-unstyled d-grid gap-2 small mb-0">
                    <li><a href="{{ route('guides.index') }}">Panduan Perlindungan Napas</a></li>
                    <li><a href="{{ route('edukasi.index') }}">Literasi Restorasi Gambut</a></li>
                    <li><a href="{{ route('air.quality') }}">Indeks PM2.5 Standar ISPU</a></li>
                    <li><a href="{{ route('announcements.index') }}">Peringatan Dini Cuaca</a></li>
                    <li><a href="{{ route('admin.dashboard') }}">Area Pengelola (Admin)</a></li>
                </ul>
            </div>

            <div class="col-lg-3">
                <h6 class="fw-bold mb-3 text-dark text-uppercase small letter-spacing-1">Hubungi Kami</h6>
                <ul class="list-unstyled d-grid gap-2 small mb-0">
                    <li class="d-flex align-items-start gap-2">
                        <i class="bi bi-envelope text-primary mt-0-5"></i>
                        <span>halo@fireaware.id</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="bi bi-geo-alt text-primary mt-0-5"></i>
                        <span>Banjarmasin, Kalimantan Selatan</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="bi bi-info-circle text-primary mt-0-5"></i>
                        <span>Layanan pemantauan karhutla berbasis keterbukaan informasi publik.</span>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="my-4 border-soft opacity-100">

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <p class="small text-secondary-soft mb-0">© {{ date('Y') }} FireAware Kalimantan. Seluruh hak cipta dilindungi.</p>
            <p class="small text-secondary-soft mb-0 d-inline-flex align-items-center gap-1">
                <i class="bi bi-heart-fill text-danger small"></i> Dibangun untuk kelestarian hutan tropis Kalimantan.
            </p>
        </div>
    </div>
</footer>
