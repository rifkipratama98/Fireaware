<header class="topbar d-flex align-items-center gap-3 px-3 px-md-4 py-2 bg-white border-bottom border-soft shadow-xs sticky-top">

    {{-- Tombol Buka Menu Mobile (Offcanvas) --}}
    <button class="btn btn-outline-secondary d-lg-none p-2 rounded-3 border-soft d-inline-flex align-items-center justify-content-center"
            type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar"
            aria-controls="adminSidebar" aria-label="Buka menu navigasi">
        <i class="bi bi-list fs-5 lh-1" aria-hidden="true"></i>
    </button>

    {{-- Tombol Ciutkan Sidebar Desktop --}}
    <button class="btn btn-light d-none d-lg-inline-flex p-2 align-items-center justify-content-center rounded-3 border border-soft text-secondary"
            type="button" id="sidebarCollapseBtn" aria-label="Ciutkan atau rentangkan sidebar"
            title="Ciutkan / Buka Sidebar">
        <i class="bi bi-layout-sidebar-inset fs-5 lh-1" aria-hidden="true"></i>
    </button>

    {{-- Judul Halaman & Konteks --}}
    <div class="me-auto min-w-0">
        <div class="d-none d-md-flex align-items-center gap-2 small text-secondary">
            <span>Sistem Monitoring Lingkungan Kalimantan</span>
            <span class="text-muted">&bull;</span>
            <span class="text-primary fw-medium">Admin Console</span>
        </div>
        <h1 class="h5 mb-0 fw-bold text-dark text-truncate">@yield('page_title', 'Dashboard')</h1>
    </div>

    {{-- Telemetri Status Ringkas --}}
    <div class="d-none d-xl-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-mist border border-soft text-primary small">
        <span class="pulse-indicator" aria-hidden="true"></span>
        <span class="fw-semibold">Telemetri Aktif: 5 Provinsi</span>
    </div>

    {{-- Tombol Cepat Portal Publik --}}
    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm d-none d-sm-inline-flex align-items-center gap-1 rounded-3" target="_blank" rel="noopener" title="Buka Beranda Publik di Tab Baru">
        <i class="bi bi-box-arrow-up-right"></i>
        <span class="d-none d-md-inline">Portal Publik</span>
    </a>

    {{-- Dropdown Informasi Akun Admin --}}
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle p-1 rounded-pill border border-soft bg-light pe-3"
           role="button" data-bs-toggle="dropdown" aria-expanded="false"
           id="topbar-user-menu" aria-haspopup="true">
            <span class="avatar avatar-sm shadow-xs" aria-hidden="true">
                {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
            </span>
            <span class="d-none d-sm-inline small fw-semibold text-dark text-truncate" style="max-width: 140px;">
                {{ auth()->user()->name ?? 'Administrator' }}
            </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border border-soft mt-1 py-2" aria-labelledby="topbar-user-menu" style="min-width: 220px;">
            <li class="px-3 py-2 border-bottom border-soft mb-1">
                <div class="fw-bold text-dark small text-truncate">{{ auth()->user()->name ?? 'Administrator' }}</div>
                <div class="text-muted text-truncate" style="font-size: 0.75rem;">{{ auth()->user()->email ?? '' }}</div>
                <span class="badge bg-primary bg-opacity-10 text-primary mt-1" style="font-size: 0.65rem;">
                    Administrator Terverifikasi
                </span>
            </li>
            <li>
                <a class="dropdown-item small py-2 d-flex align-items-center gap-2" href="{{ route('admin.integrations.index') }}">
                    <i class="bi bi-arrow-repeat text-primary"></i>
                    <span>Status Integrasi API</span>
                </a>
            </li>
            <li>
                <a class="dropdown-item small py-2 d-flex align-items-center gap-2" href="{{ route('admin.notifications.index') }}">
                    <i class="bi bi-bell text-primary"></i>
                    <span>Pengaturan Notifikasi</span>
                </a>
            </li>
            <li>
                <a class="dropdown-item small py-2 d-flex align-items-center gap-2" href="{{ route('home') }}" target="_blank">
                    <i class="bi bi-box-arrow-up-right text-primary"></i>
                    <span>Buka Portal Publik</span>
                </a>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li>
                <form method="POST" action="{{ route('admin.logout') }}" id="topbar-logout-form">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger small fw-semibold py-2 d-flex align-items-center gap-2" id="btn-logout">
                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                        <span>Keluar dari Sistem</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>

</header>
