<div class="d-flex flex-column h-100">

    <div class="offcanvas-header px-3 pt-3 pb-2">
        <a class="brand sidebar-brand" href="{{ route('admin.dashboard') }}">
            <span class="brand-mark"><i class="bi bi-tree"></i></span>
            <span class="brand-name">FireAware<span class="brand-sub"> Admin</span></span>
        </a>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                data-bs-target="#adminSidebar" aria-label="Tutup menu"></button>
    </div>

    <nav class="flex-grow-1 px-3 pb-3" style="overflow-y:auto" aria-label="Menu admin">
        <div class="sidebar-section-title">Utama</div>
        <div class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="bi bi-grid"></i>Dashboard
            </a>
            <a class="nav-link" href="#"><i class="bi bi-map"></i>Peta Risiko</a>
        </div>

        <div class="sidebar-section-title mt-4">Data &amp; Konten</div>
        <div class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('admin.guides.*') ? 'active' : '' }}"
               href="{{ route('admin.guides.index') }}">
                <i class="bi bi-shield-check"></i>Panduan Keselamatan
            </a>
            <a class="nav-link {{ request()->routeIs('admin.education.*') ? 'active' : '' }}"
               href="{{ route('admin.education.index') }}">
                <i class="bi bi-mortarboard"></i>Edukasi Karhutla
            </a>
            <a class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}"
               href="{{ route('admin.announcements.index') }}">
                <i class="bi bi-megaphone"></i>Pengumuman Resmi
            </a>
            <a class="nav-link" href="#"><i class="bi bi-journal-text"></i>Laporan Kebakaran</a>
            <a class="nav-link" href="#"><i class="bi bi-fire"></i>Titik Panas</a>
            <a class="nav-link" href="#"><i class="bi bi-bounding-box-circles"></i>Wilayah</a>
        </div>

        <div class="sidebar-section-title mt-4">Manajemen</div>
        <div class="nav flex-column">
            <a class="nav-link" href="#"><i class="bi bi-people"></i>Pengguna</a>
            <a class="nav-link" href="#"><i class="bi bi-gear"></i>Pengaturan</a>
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
