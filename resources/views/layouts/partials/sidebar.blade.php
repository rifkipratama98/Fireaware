<div class="d-flex flex-column h-100">

    {{-- Header Sidebar & Branding --}}
    <div class="sidebar-brand-header px-3 pt-3 pb-2 border-bottom border-white border-opacity-10 d-flex align-items-center justify-content-between">
        <a class="brand sidebar-brand text-decoration-none d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
            <span class="brand-mark bg-primary text-white d-grid place-items-center rounded-3 p-2 shadow-sm" aria-hidden="true" style="width: 2.4rem; height: 2.4rem;">
                <i class="bi bi-tree-fill fs-5"></i>
            </span>
            <span class="brand-text d-flex flex-column lh-1">
                <span class="brand-name text-white fw-bold fs-6">FireAware</span>
                <span class="brand-sub text-mist opacity-75 small">Monitoring Kalimantan</span>
            </span>
        </a>
        <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas"
                data-bs-target="#adminSidebar" aria-label="Tutup menu navigasi"></button>
    </div>

    {{-- Menu Navigasi Sidebar --}}
    <nav class="flex-grow-1 px-3 py-3" style="overflow-y:auto" aria-label="Menu navigasi admin">
        
        <div class="sidebar-section-title">Monitoring Utama</div>
        <div class="nav flex-column mb-3">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="sidebar-section-title">Publikasi Konten</div>
        <div class="nav flex-column mb-3">
            <a class="nav-link {{ request()->routeIs('admin.guides.*') ? 'active' : '' }}"
               href="{{ route('admin.guides.index') }}">
                <i class="bi bi-shield-check"></i>
                <span>Panduan Keselamatan</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.education.*') ? 'active' : '' }}"
               href="{{ route('admin.education.index') }}">
                <i class="bi bi-mortarboard"></i>
                <span>Artikel Edukasi</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}"
               href="{{ route('admin.announcements.index') }}">
                <i class="bi bi-megaphone"></i>
                <span>Pengumuman</span>
            </a>
        </div>

        <div class="sidebar-section-title">Sistem &amp; Pemantauan</div>
        <div class="nav flex-column mb-3">
            <a class="nav-link {{ request()->routeIs('admin.regions.*') ? 'active' : '' }}"
               href="{{ route('admin.regions.index') }}">
                <i class="bi bi-geo-alt"></i>
                <span>Informasi Wilayah</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.integrations.*') ? 'active' : '' }}"
               href="{{ route('admin.integrations.index') }}">
                <i class="bi bi-arrow-repeat"></i>
                <span>Integrasi Data API</span>
            </a>
            <a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}"
               href="{{ route('admin.notifications.index') }}">
                <i class="bi bi-bell"></i>
                <span>Konfigurasi Notifikasi</span>
            </a>
        </div>

        <div class="sidebar-section-title">Akses Luar</div>
        <div class="nav flex-column">
            <a class="nav-link text-white-50 hover-text-white" href="{{ route('home') }}" target="_blank" rel="noopener">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Portal Publik</span>
            </a>
        </div>

    </nav>

    {{-- Footer Akun Pengguna Administrator --}}
    <div class="sidebar-user p-3 border-top border-white border-opacity-10">
        <div class="d-flex align-items-center gap-2 px-1 mb-2">
            <span class="avatar" aria-hidden="true">
                {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
            </span>
            <div class="lh-sm flex-grow-1 min-w-0">
                <div class="small text-white fw-semibold text-truncate">
                    {{ auth()->user()->name ?? 'Administrator' }}
                </div>
                <div class="small opacity-50 text-truncate" style="font-size: 0.75rem;">
                    {{ auth()->user()->email ?? '' }}
                </div>
            </div>
            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25" style="font-size: 0.65rem;">
                Admin
            </span>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}" id="sidebar-logout-form">
            @csrf
            <button type="submit" class="btn btn-sm w-100 sidebar-logout-btn d-flex align-items-center justify-content-center gap-1" id="btn-sidebar-logout">
                <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                <span>Keluar dari Sesi</span>
            </button>
        </form>
    </div>

</div>
