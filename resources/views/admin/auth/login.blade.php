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

<div class="admin-login-layout">

    {{-- Sisi Kiri: Visual Lingkungan & Identitas FireAware Kalimantan --}}
    <aside class="admin-login-visual" style="background-image: linear-gradient(150deg, rgba(23, 57, 45, 0.94) 0%, rgba(33, 77, 58, 0.88) 55%, rgba(16, 40, 31, 0.97) 100%), url('{{ asset('images/hero-forest.jpg') }}');" aria-label="Identitas Visual Lingkungan FireAware Kalimantan">
        
        {{-- Brand Header --}}
        <div class="admin-visual-brand">
            <span class="brand-mark" aria-hidden="true">
                <i class="bi bi-tree"></i>
            </span>
            <div class="brand-text-block">
                <span class="brand-name">FireAware</span>
                <span class="brand-sub">Kalimantan</span>
            </div>
        </div>

        {{-- Konten Utama Lingkungan (Tampil di Desktop) --}}
        <div class="admin-visual-content">
            <span class="visual-pill">
                <i class="bi bi-shield-check" aria-hidden="true"></i>
                Portal Administrator Karhutla
            </span>

            <h2 class="visual-title">
                Pusat Pemantauan & Tanggap Dini Karhutla
            </h2>

            <p class="visual-desc">
                Sistem koordinasi terpadu pemantauan titik panas satelit, indeks standar kualitas udara, dan publikasi panduan kesiapsiagaan masyarakat di seluruh penjuru Kalimantan.
            </p>

            <div class="visual-list">
                <div class="visual-list-item">
                    <i class="bi bi-broadcast" aria-hidden="true"></i>
                    <span>Agregasi hotspot satelit NASA FIRMS dan data BMKG</span>
                </div>
                <div class="visual-list-item">
                    <i class="bi bi-wind" aria-hidden="true"></i>
                    <span>Pemantauan indeks kualitas udara real-time 5 provinsi</span>
                </div>
                <div class="visual-list-item">
                    <i class="bi bi-shield-lock" aria-hidden="true"></i>
                    <span>Akses administratif terenkripsi dan terlindungi rate limiting</span>
                </div>
            </div>
        </div>

        {{-- Footer Sisi Visual --}}
        <div class="admin-visual-footer">
            <p class="mb-0">
                &copy; {{ date('Y') }} FireAware Kalimantan. Khusus pengelola dan satuan tugas berwenang.
            </p>
        </div>

    </aside>

    {{-- Sisi Kanan: Formulir Login Admin --}}
    <main class="admin-login-form-side" id="main-content" aria-label="Formulir masuk admin">

        <div class="admin-login-card">

            {{-- Baris Navigasi Atas --}}
            <div class="admin-login-top-action">
                <a href="{{ route('home') }}" class="back-link">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    <span>Kembali ke Beranda</span>
                </a>
                <span class="badge-scope">Akses Khusus</span>
            </div>

            {{-- Header Formulir --}}
            <div class="admin-login-header">
                <h1 class="login-heading">Masuk sebagai Admin</h1>
                <p class="login-subheading">Akses terbatas untuk pengelola sistem FireAware Kalimantan.</p>
            </div>

            {{-- Status dari redirect / session --}}
            @if (session('status'))
                <div class="alert alert-warning mb-4 py-2 px-3 small d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-exclamation-triangle-fill text-warning flex-shrink-0" aria-hidden="true"></i>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            {{-- Formulir login --}}
            <form method="POST" action="{{ route('admin.login') }}" novalidate>
                @csrf

                {{-- Input Email --}}
                <div class="admin-field-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="admin-input-box">
                        <span class="box-icon" aria-hidden="true">
                            <i class="bi bi-envelope"></i>
                        </span>
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
                            aria-describedby="@error('email') email-error @enderror"
                        >
                    </div>
                    @error('email')
                        <div class="admin-input-error" id="email-error" role="alert">
                            <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                {{-- Input Password --}}
                <div class="admin-field-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="admin-input-box has-toggle">
                        <span class="box-icon" aria-hidden="true">
                            <i class="bi bi-shield-lock"></i>
                        </span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            autocomplete="current-password"
                            required
                            placeholder="Masukkan password"
                            aria-describedby="@error('password') password-error @enderror"
                        >
                        <button
                            type="button"
                            class="admin-toggle-pass"
                            id="toggle-password"
                            aria-label="Tampilkan atau sembunyikan password"
                            aria-controls="password"
                            aria-pressed="false"
                        >
                            <i class="bi bi-eye" id="toggle-icon" aria-hidden="true"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="admin-input-error" id="password-error" role="alert">
                            <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                {{-- Opsi Remember Me --}}
                <div class="admin-login-options">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="remember"
                            id="remember"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="remember">
                            Ingat saya di perangkat ini
                        </label>
                    </div>
                </div>

                {{-- Tombol Masuk --}}
                <button type="submit" class="btn btn-primary w-100 admin-login-submit-btn login-submit-btn" id="btn-login">
                    <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                    <span>Masuk</span>
                </button>
            </form>

            {{-- Catatan Keamanan --}}
            <div class="admin-security-footer">
                <i class="bi bi-shield-check" aria-hidden="true"></i>
                <span>Dilindungi pembatasan frekuensi percobaan (rate limiting) dan pencatatan sesi keamanan.</span>
            </div>

            {{-- Link Kembali Khusus Tampilan Mobile --}}
            <div class="admin-mobile-back-link">
                <a href="{{ route('home') }}">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    <span>Kembali ke Beranda Publik</span>
                </a>
            </div>

        </div>

    </main>

</div>

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
