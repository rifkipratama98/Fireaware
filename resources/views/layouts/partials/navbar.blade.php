<nav class="navbar navbar-expand-lg site-navbar sticky-top shadow-none" aria-label="Navigasi utama">
    <div class="container">
        <a class="navbar-brand brand" href="{{ route('home') }}">
            <span class="brand-mark" aria-hidden="true"><i class="bi bi-tree-fill"></i></span>
            <span class="brand-text d-flex flex-column lh-1">
                <span class="brand-name">FireAware</span>
                <span class="brand-sub">Kalimantan</span>
            </span>
        </a>

        <button class="navbar-toggler border-0 shadow-none p-2" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Buka menu navigasi">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-lg-1 align-items-lg-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('hotspot.map*') ? 'active' : '' }}" href="{{ route('hotspot.map') }}">Peta Hotspot</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('air.quality*') ? 'active' : '' }}"
                       href="{{ route('air.quality') }}">Kualitas Udara</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}#hotspot-terbaru">Data Terbaru</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}"
                       href="{{ route('announcements.index') }}">Pengumuman</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('edukasi.*') ? 'active' : '' }}"
                       href="{{ route('edukasi.index') }}">Edukasi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guides.*') ? 'active' : '' }}"
                       href="{{ route('guides.index') }}">Panduan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about*') ? 'active' : '' }}"
                       href="{{ route('about') }}">Tentang</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 pt-2 pt-lg-0 border-top border-lg-0 mt-2 mt-lg-0">
                <button type="button" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1 px-3 py-1-5" data-bs-toggle="modal" data-bs-target="#browserNotificationModal" title="Notifikasi Peringatan Peramban">
                    <i class="bi bi-bell"></i>
                    <span>Peringatan</span>
                </button>
                <a class="btn btn-primary btn-sm px-3 py-1-5 fw-semibold d-inline-flex align-items-center gap-1" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-shield-lock-fill small"></i>
                    <span>Area Admin</span>
                </a>
            </div>
        </div>
    </div>
</nav>
