<header class="topbar d-flex align-items-center gap-3">

    <button class="btn btn-light d-lg-none p-2 border border-soft" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#adminSidebar" aria-controls="adminSidebar">
        <i class="bi bi-list fs-5 lh-1" aria-hidden="true"></i>
        <span class="visually-hidden">Buka menu</span>
    </button>

    <button class="btn btn-light d-none d-lg-inline-flex p-2 align-items-center justify-content-center border border-soft"
            type="button" id="sidebarCollapseBtn" aria-label="Ciutkan atau rentangkan sidebar"
            title="Ciutkan / Buka Sidebar">
        <i class="bi bi-layout-sidebar-inset fs-5 lh-1" aria-hidden="true"></i>
    </button>

    <div class="me-auto">
        <div class="small text-secondary-soft d-none d-md-block">Portal Pengelola FireAware Kalimantan</div>
        <h1 class="h5 mb-0 fw-bold text-dark">@yield('page_title', 'Dashboard')</h1>
    </div>

    <div class="d-none d-xl-flex align-items-center gap-2 px-3 py-1-5 rounded-pill bg-mist border border-soft text-primary small">
        <span class="pulse-indicator" aria-hidden="true"></span>
        <span class="fw-semibold">Status Sistem Aktif: 5 Provinsi</span>
    </div>

    <div class="dropdown">
        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle p-1 rounded-pill border border-soft bg-white pe-3"
           role="button" data-bs-toggle="dropdown" aria-expanded="false"
           id="topbar-user-menu" aria-haspopup="true">
            <span class="avatar avatar-sm" aria-hidden="true">
                {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
            </span>
            <span class="d-none d-md-inline small fw-semibold text-dark">
                {{ auth()->user()->name ?? 'Administrator' }}
            </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border border-soft" aria-labelledby="topbar-user-menu">
            <li>
                <div class="px-3 py-2">
                    <div class="fw-bold text-dark small">{{ auth()->user()->name ?? 'Administrator' }}</div>
                    <div class="small text-secondary-soft">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li>
                <a class="dropdown-item small" href="{{ route('home') }}" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-2 text-primary"></i>Lihat Portal Publik
                </a>
            </li>
            <li><hr class="dropdown-divider my-1"></li>
            <li>
                <form method="POST" action="{{ route('admin.logout') }}" id="topbar-logout-form">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger small fw-semibold" id="btn-logout">
                        <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Keluar
                    </button>
                </form>
            </li>
        </ul>
    </div>

</header>
