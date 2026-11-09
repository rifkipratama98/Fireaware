<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — FireAware Kalimantan')</title>

    <!-- Font Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="admin-body">

    <div class="admin-shell">

        <!-- Sidebar (offcanvas di mobile, statis di desktop) -->
        <aside class="admin-sidebar offcanvas offcanvas-lg offcanvas-start"
               tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel">
            @include('layouts.partials.sidebar')
        </aside>

        <!-- Panel utama -->
        <div class="admin-main d-flex flex-column">

            @include('layouts.partials.topbar')

            <main class="admin-content flex-fill">
                @include('layouts.partials.alert')
                @yield('content')
            </main>

        </div>
    </div>

    <script>
        (function () {
            const collapseBtn = document.getElementById('sidebarCollapseBtn');
            const shell = document.querySelector('.admin-shell');

            function updateBtnState(isCollapsed) {
                if (!collapseBtn) return;
                const icon = collapseBtn.querySelector('i');
                if (isCollapsed) {
                    collapseBtn.setAttribute('title', 'Tampilkan Sidebar');
                    collapseBtn.setAttribute('aria-label', 'Tampilkan sidebar');
                    if (icon) icon.className = 'bi bi-layout-sidebar fs-5 lh-1';
                } else {
                    collapseBtn.setAttribute('title', 'Sembunyikan Sidebar');
                    collapseBtn.setAttribute('aria-label', 'Sembunyikan sidebar');
                    if (icon) icon.className = 'bi bi-layout-sidebar-inset fs-5 lh-1';
                }
            }

            if (collapseBtn && shell) {
                if (localStorage.getItem('fireaware_admin_sidebar_init_v3') !== '1') {
                    localStorage.removeItem('fireaware_admin_sidebar_collapsed');
                    localStorage.setItem('fireaware_admin_sidebar_init_v3', '1');
                }

                const isCollapsed = localStorage.getItem('fireaware_admin_sidebar_collapsed') === 'true';
                if (isCollapsed) {
                    shell.classList.add('sidebar-collapsed');
                }
                updateBtnState(isCollapsed);

                collapseBtn.addEventListener('click', function () {
                    shell.classList.toggle('sidebar-collapsed');
                    const nowCollapsed = shell.classList.contains('sidebar-collapsed');
                    localStorage.setItem('fireaware_admin_sidebar_collapsed', String(nowCollapsed));
                    updateBtnState(nowCollapsed);
                });
            }
        })();
    </script>

    @stack('scripts')
</body>
</html>
