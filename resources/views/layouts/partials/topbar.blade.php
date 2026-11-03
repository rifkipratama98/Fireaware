<header class="topbar d-flex align-items-center gap-3">

    <button class="btn btn-light d-lg-none p-2" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#adminSidebar" aria-controls="adminSidebar">
        <i class="bi bi-list fs-5 lh-1" aria-hidden="true"></i>
        <span class="visually-hidden">Buka menu</span>
    </button>

    <div class="me-auto">
        <div class="small text-secondary-soft d-none d-md-block">FireAware Kalimantan</div>
        <h1 class="h5 mb-0">@yield('page_title', 'Dashboard')</h1>
    </div>

    <div class="dropdown">
        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle"
           role="button" data-bs-toggle="dropdown" aria-expanded="false"
           id="topbar-user-menu" aria-haspopup="true">
            <span class="avatar avatar-sm" aria-hidden="true">
                {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
            </span>
            <span class="d-none d-md-inline small fw-semibold">
                {{ auth()->user()->name ?? 'Administrator' }}
            </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="topbar-user-menu">
            <li>
                <span class="dropdown-item-text small text-secondary-soft">
                    {{ auth()->user()->email ?? '' }}
                </span>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('admin.logout') }}" id="topbar-logout-form">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger" id="btn-logout">
                        <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Keluar
                    </button>
                </form>
            </li>
        </ul>
    </div>

</header>
