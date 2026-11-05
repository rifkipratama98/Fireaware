<div class="d-flex flex-column h-100">

    <div class="offcanvas-header px-3 pt-3 pb-2">
        <a class="brand sidebar-brand" href="{{ route('admin.dashboard') }}">
            <span class="brand-mark" aria-hidden="true"><i class="bi bi-tree-fill"></i></span>
            <span class="brand-text d-flex flex-column lh-1">
                <span class="brand-name text-white">FireAware</span>
                <span class="brand-sub text-mist opacity-75">Admin Panel</span>
            </span>
        </a>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                data-bs-target="#adminSidebar" aria-label="Tutup menu"></button>
    </div>

    <nav class="flex-grow-1 px-3 pb-3" style="overflow-y:auto" aria-label="Menu admin">
        <div class="sidebar-section-title">Utama</div>
        <div class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="bi bi-grid-1x2-fill"></i>Dashboard
            </a>
        </div>

        <div class="sidebar-section-title mt-4">Konten Publikasi</div>
        <div class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('admin.guides.*') ? 'active' : '' }}"
               href="{{ route('admin.guides.index') }}">
                <i class="bi bi-shield-check"></i>Panduan Keselamatan
            </a>
            <a class="nav-link {{ request()->routeIs('admin.education.*') ? 'active' : '' }}"
               href="{{ route('admin.education.index') }}">
                <i class="bi bi-mortarboard-fill"></i>Edukasi
            </a>
            <a class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}"
               href="{{ route('admin.announcements.index') }}">
                <i class="bi bi-megaphone-fill"></i>Pengumuman
            </a>
        </div>

        <div class="sidebar-section-title mt-4">Sistem &amp; Pemantauan</div>
        <div class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('admin.regions.*') ? 'active' : '' }}"
               href="{{ route('admin.regions.index') }}">
                <i class="bi bi-map-fill"></i>Informasi Wilayah
            </a>
            <a class="nav-link {{ request()->routeIs('admin.integrations.*') ? 'active' : '' }}"
               href="{{ route('admin.integrations.index') }}">
                <i class="bi bi-arrow-repeat"></i>Integrasi Data
            </a>
            <a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}"
               href="{{ route('admin.notifications.index') }}">
                <i class="bi bi-bell-fill"></i>Konfigurasi Notifikasi
            </a>
        </div>
    </nav>

    <div class="sidebar-user p-3">
        <div class="d-flex align-items-center gap-2 px-2">
            <span class="avatar" aria-hidden="true">
                {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
            </span>
            <div class="lh-sm flex-grow-1 min-w-0">
                <div class="small text-white fw-semibold text-truncate">
                    {{ auth()->user()->name ?? 'Administrator' }}
                </div>
                <div class="small opacity-50 text-truncate">
                    {{ auth()->user()->email ?? '' }}
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}" class="mt-2 px-2" id="sidebar-logout-form">
            @csrf
            <button type="submit" class="btn btn-sm w-100 sidebar-logout-btn" id="btn-sidebar-logout">
                <i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>Keluar
            </button>
        </form>
    </div>

</div>
