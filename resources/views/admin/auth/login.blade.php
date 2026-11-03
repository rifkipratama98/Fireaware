<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk Admin — FireAware Kalimantan</title>
    <meta name="description" content="Halaman masuk administrator sistem FireAware Kalimantan.">
    <meta name="robots" content="noindex, nofollow">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="login-body">

<main class="login-shell" id="main-content" aria-label="Formulir masuk admin">

    <div class="login-card">

        {{-- Brand --}}
        <div class="login-brand">
            <span class="brand-mark" aria-hidden="true">
                <i class="bi bi-tree"></i>
            </span>
            <div class="login-brand-text">
                <span class="login-brand-name">FireAware</span>
                <span class="login-brand-sub">Kalimantan</span>
            </div>
        </div>

        {{-- Heading --}}
        <h1 class="login-heading">Masuk sebagai Admin</h1>
        <p class="login-subheading">Akses terbatas untuk pengelola sistem.</p>

        {{-- Status dari redirect (misalnya: "silakan masuk dulu") --}}
        @if (session('status'))
            <div class="alert alert-warning mb-4 py-2 px-3 small" role="alert">
                <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
                {{ session('status') }}
            </div>
        @endif

        {{-- Formulir login --}}
        <form method="POST" action="{{ route('admin.login') }}" novalidate>
            @csrf

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    autocomplete="username"
                    autofocus
                    required
                    placeholder="admin@fireaware.id"
                >
                @error('email')
                    <div class="invalid-feedback" role="alert">{{ $message }}</div>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        autocomplete="current-password"
                        required
                    >
                    <button
                        type="button"
                        class="btn btn-outline-secondary login-toggle-pass"
                        id="toggle-password"
                        aria-label="Tampilkan atau sembunyikan password"
                        aria-controls="password"
                        aria-pressed="false"
                    >
                        <i class="bi bi-eye" id="toggle-icon" aria-hidden="true"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback" role="alert">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Tombol masuk --}}
            <button type="submit" class="btn btn-primary w-100 login-submit-btn" id="btn-login">
                Masuk
            </button>
        </form>

        {{-- Tautan kembali ke situs publik --}}
        <div class="login-footer-link">
            <a href="{{ route('home') }}">
                <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Kembali ke situs
            </a>
        </div>

    </div>

</main>

<script>
    (function () {
        const toggle = document.getElementById('toggle-password');
        const input  = document.getElementById('password');
        const icon   = document.getElementById('toggle-icon');

        if (!toggle || !input || !icon) return;

        toggle.addEventListener('click', function () {
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
            toggle.setAttribute('aria-pressed', String(isHidden));
            toggle.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
        });
    })();
</script>

</body>
</html>
