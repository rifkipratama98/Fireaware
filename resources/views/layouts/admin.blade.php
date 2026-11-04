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

            if (collapseBtn && shell) {
                // Pulihkan preferensi collapse pengguna
                if (localStorage.getItem('fireaware_admin_sidebar_collapsed') === 'true') {
                    shell.classList.add('sidebar-collapsed');
                }

                collapseBtn.addEventListener('click', function () {
                    shell.classList.toggle('sidebar-collapsed');
                    const isCollapsed = shell.classList.contains('sidebar-collapsed');
                    localStorage.setItem('fireaware_admin_sidebar_collapsed', String(isCollapsed));
                });
            }
        })();
    </script>

    @stack('scripts')
</body>
</html>
