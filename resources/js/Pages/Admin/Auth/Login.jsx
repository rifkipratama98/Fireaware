import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import { motion, AnimatePresence } from 'motion/react';
import {
    ShieldCheck,
    Lock,
    Mail,
    Eye,
    EyeOff,
    ArrowLeft,
    AlertTriangle,
    TreePine,
    Flame,
    Radio,
    Compass,
    CheckCircle2,
    Loader2,
    ShieldAlert,
} from 'lucide-react';

export default function Login({ title = 'Masuk sebagai Admin', status, fields = ['Email', 'Password'] }) {
    const [showPassword, setShowPassword] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/login', {
            onFinish: () => reset('password'),
        });
    };

    return (
        <>
            <Head title={title}>
                <meta name="description" content="Portal administrasi dan pengendalian telemetri kebakaran hutan FireAware Kalimantan." />
                <meta name="robots" content="noindex, nofollow" />
            </Head>

            <div className="min-h-screen w-full flex flex-col lg:flex-row bg-[#F8F9FA] text-[#1E242B] selection:bg-[#E05A2B]/20 selection:text-[#E05A2B]">
                {/* ============================================================ */}
                {/* SISI KIRI — VISUAL ENVIRONMENT (Environmental Control Access) */}
                {/* ============================================================ */}
                <aside
                    aria-label="Identitas Visual Lingkungan FireAware Kalimantan"
                    className="relative w-full lg:w-[52%] xl:w-[56%] flex flex-col justify-between p-6 sm:p-10 lg:p-14 overflow-hidden min-h-[380px] lg:min-h-screen text-white order-2 lg:order-1"
                >
                    {/* Background Foto Hutan Tropis Kalimantan */}
                    <div
                        className="absolute inset-0 bg-cover bg-center bg-no-repeat scale-105 transition-transform duration-1000 ease-out"
                        style={{
                            backgroundImage: "url('/images/hero-kalimantan-rainforest.jpg')",
                        }}
                    />

                    {/* Multi-layered Gradients & Vignette for Cinematic Depth */}
                    <div
                        className="absolute inset-0 bg-gradient-to-br from-[#1C2526]/95 via-[#1B4332]/90 to-[#1C2526]/98"
                        style={{
                            backdropFilter: 'blur(1px)',
                        }}
                    />

                    {/* Subtle Organic Radial Accents */}
                    <div
                        className="absolute -top-32 -left-32 w-96 h-96 rounded-full bg-[#52B788]/15 blur-3xl pointer-events-none"
                        aria-hidden="true"
                    />
                    <div
                        className="absolute -bottom-32 -right-32 w-96 h-96 rounded-full bg-[#E05A2B]/15 blur-3xl pointer-events-none"
                        aria-hidden="true"
                    />

                    {/* Fine Grid Texture Overlay */}
                    <div
                        className="absolute inset-0 opacity-[0.04] pointer-events-none bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:24px_24px]"
                        aria-hidden="true"
                    />

                    {/* Top Section: Back Link & Brand Header */}
                    <header className="relative z-10 flex items-center justify-between gap-4">
                        <Link
                            href="/"
                            className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-medium text-white/85 hover:text-white bg-white/[0.08] hover:bg-white/[0.14] border border-white/15 backdrop-blur-md transition-all duration-200 active:scale-[0.98] group"
                            title="Kembali ke Halaman Beranda Publik"
                        >
                            <ArrowLeft className="w-3.5 h-3.5 transition-transform duration-200 group-hover:-translate-x-0.5" />
                            <span>Kembali ke Beranda</span>
                        </Link>

                        <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[11px] font-semibold tracking-wider uppercase bg-[#1B4332]/80 text-[#52B788] border border-[#52B788]/30 backdrop-blur-md">
                            <span className="w-1.5 h-1.5 rounded-full bg-[#52B788] animate-pulse" />
                            <span>ADMINISTRATION PORTAL</span>
                        </div>
                    </header>

                    {/* Middle Section: Cinematic Identity & Headline */}
                    <div className="relative z-10 my-auto py-8 sm:py-12 max-w-xl">
                        {/* Brand Logo & Name */}
                        <motion.div
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.5, ease: [0.16, 1, 0.3, 1] }}
                            className="flex items-center gap-3.5 mb-6 sm:mb-8"
                        >
                            <div className="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-[#E05A2B] to-[#D94E34] flex items-center justify-center shadow-[0_8px_24px_rgba(224,90,43,0.4)] border border-white/20">
                                <TreePine className="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <div className="text-xl sm:text-2xl font-extrabold tracking-tight font-display flex items-center gap-1.5 leading-none">
                                    <span>FireAware</span>
                                    <span className="text-[#F4A261] font-semibold">Kalimantan</span>
                                </div>
                                <p className="text-[11px] sm:text-xs text-[#F6F5F2]/70 tracking-wide mt-1 font-medium">
                                    Environmental Monitoring &amp; Awareness Platform
                                </p>
                            </div>
                        </motion.div>

                        {/* Main Headline */}
                        <motion.h2
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.55, delay: 0.08, ease: [0.16, 1, 0.3, 1] }}
                            className="text-2xl sm:text-4xl lg:text-[40px] font-extrabold tracking-tight text-white leading-tight font-display"
                        >
                            Melindungi Kalimantan Dimulai dari{' '}
                            <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#F4A261] via-[#E05A2B] to-[#F4A261]">
                                Informasi.
                            </span>
                        </motion.h2>

                        {/* Description */}
                        <motion.p
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.55, delay: 0.16, ease: [0.16, 1, 0.3, 1] }}
                            className="mt-4 text-sm sm:text-base text-[#F6F5F2]/80 leading-relaxed"
                        >
                            Portal pengelolaan informasi, pemantauan lingkungan, dan edukasi kebakaran hutan dan lahan di Kalimantan.
                        </motion.p>

                        {/* Telemetry Feature Badges */}
                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.55, delay: 0.24, ease: [0.16, 1, 0.3, 1] }}
                            className="mt-6 sm:mt-8 pt-6 border-t border-white/10 grid grid-cols-1 sm:grid-cols-2 gap-3"
                        >
                            <div className="flex items-center gap-3 p-3 rounded-xl bg-white/[0.04] border border-white/10 backdrop-blur-sm">
                                <div className="w-8 h-8 rounded-lg bg-[#52B788]/20 flex items-center justify-center text-[#52B788] shrink-0">
                                    <Radio className="w-4 h-4" />
                                </div>
                                <div className="text-xs">
                                    <p className="font-semibold text-white">Telemetri Satelit NASA</p>
                                    <p className="text-white/60 text-[11px]">Agregasi sensor VIIRS &amp; MODIS</p>
                                </div>
                            </div>

                            <div className="flex items-center gap-3 p-3 rounded-xl bg-white/[0.04] border border-white/10 backdrop-blur-sm">
                                <div className="w-8 h-8 rounded-lg bg-[#F4A261]/20 flex items-center justify-center text-[#F4A261] shrink-0">
                                    <Compass className="w-4 h-4" />
                                </div>
                                <div className="text-xs">
                                    <p className="font-semibold text-white">5 Wilayah Terpantau</p>
                                    <p className="text-white/60 text-[11px]">Cakupan ekologis Pulau Borneo</p>
                                </div>
                            </div>
                        </motion.div>
                    </div>

                    {/* Bottom Section: Security & System Status */}
                    <footer className="relative z-10 pt-4 border-t border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-white/60">
                        <div className="flex items-center gap-2">
                            <ShieldCheck className="w-4 h-4 text-[#52B788]" />
                            <span>Sistem Terpantau 24/7 &bull; Akses Satuan Tugas Berwenang</span>
                        </div>
                        <p className="text-[11px] text-white/50">
                            &copy; {new Date().getFullYear()} FireAware Kalimantan.
                        </p>
                    </footer>
                </aside>

                {/* ============================================================ */}
                {/* SISI KANAN — LOGIN FORM (Formulir Masuk Administrator)         */}
                {/* ============================================================ */}
                <main
                    id="main-content"
                    aria-label="Formulir masuk admin"
                    className="relative w-full lg:w-[48%] xl:w-[44%] flex flex-col justify-center items-center px-6 py-10 sm:px-10 lg:px-14 xl:px-16 bg-[#F8F9FA] order-1 lg:order-2"
                >
                    {/* Inner Form Container */}
                    <div className="w-full max-w-[420px] mx-auto">
                        {/* Mobile Brand Bar (Tampil hanya pada layar kecil) */}
                        <div className="lg:hidden flex items-center justify-between mb-8 pb-4 border-b border-[#2B303A]/10">
                            <div className="flex items-center gap-2.5">
                                <div className="w-8 h-8 rounded-xl bg-gradient-to-br from-[#E05A2B] to-[#D94E34] flex items-center justify-center text-white">
                                    <TreePine className="w-4 h-4" />
                                </div>
                                <span className="font-bold font-display text-sm text-[#1E242B]">
                                    FireAware <span className="text-[#E05A2B]">Admin</span>
                                </span>
                            </div>
                            <span className="text-[10px] font-semibold uppercase tracking-wider text-[#1B4332] bg-[#1B4332]/10 px-2.5 py-1 rounded-full border border-[#1B4332]/20">
                                Portal Khusus
                            </span>
                        </div>

                        {/* Card Header & Scope Badge */}
                        <div className="mb-7">
                            <div className="flex items-center justify-between gap-2 mb-2">
                                <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#1B4332]/10 text-[#1B4332] border border-[#1B4332]/20">
                                    <ShieldCheck className="w-3.5 h-3.5 text-[#52B788]" />
                                    <span>Masuk sebagai Admin</span>
                                </span>
                                <span className="text-[11px] font-medium text-[#2B303A]/60">
                                    Akses Terbatas
                                </span>
                            </div>

                            <h1 className="text-2xl sm:text-3xl font-bold text-[#1E242B] tracking-tight font-display">
                                Selamat Datang Kembali
                            </h1>
                            <p className="text-sm text-[#2B303A]/70 mt-1.5 leading-relaxed">
                                Masuk ke panel administrator FireAware Kalimantan.
                            </p>
                        </div>

                        {/* Status Message from Session (e.g. Redirect from Protected Route) */}
                        {status && (
                            <motion.div
                                initial={{ opacity: 0, y: -8 }}
                                animate={{ opacity: 1, y: 0 }}
                                className="mb-6 p-3.5 rounded-xl bg-[#F4A261]/15 border border-[#F4A261]/40 text-[#a85817] text-xs font-medium flex items-start gap-2.5 leading-relaxed"
                                role="status"
                            >
                                <AlertTriangle className="w-4 h-4 shrink-0 text-[#E05A2B] mt-0.5" />
                                <div>{status}</div>
                            </motion.div>
                        )}

                        {/* Form Error Banner if non-field or general email failure */}
                        {errors.email && (
                            <motion.div
                                initial={{ opacity: 0, y: -6 }}
                                animate={{ opacity: 1, y: 0 }}
                                className="mb-6 p-3.5 rounded-xl bg-[#E63946]/10 border border-[#E63946]/30 text-[#b0222f] text-xs font-medium flex items-start gap-2.5 leading-relaxed"
                                role="alert"
                            >
                                <ShieldAlert className="w-4 h-4 shrink-0 text-[#E63946] mt-0.5" />
                                <div>{errors.email}</div>
                            </motion.div>
                        )}

                        {/* FORMULIR LOGIN */}
                        <form onSubmit={handleSubmit} className="space-y-5" noValidate>
                            {/* FIELD: EMAIL */}
                            <div>
                                <label
                                    htmlFor="email"
                                    className="block text-xs font-semibold text-[#1E242B] uppercase tracking-wider mb-2"
                                >
                                    Email
                                </label>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#2B303A]/50">
                                        <Mail className="w-4 h-4" />
                                    </div>
                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        placeholder="admin@fireaware.id"
                                        autoComplete="email"
                                        autoFocus
                                        required
                                        disabled={processing}
                                        className={`w-full h-11 pl-10 pr-4 text-sm bg-white text-[#1E242B] rounded-xl border ${
                                            errors.email
                                                ? 'border-[#E63946] focus:border-[#E63946] focus:ring-2 focus:ring-[#E63946]/20'
                                                : 'border-[#2B303A]/20 hover:border-[#2B303A]/40 focus:border-[#E05A2B] focus:ring-2 focus:ring-[#E05A2B]/20'
                                        } shadow-sm transition-all duration-150 outline-none placeholder:text-[#2B303A]/40 disabled:opacity-60 disabled:cursor-not-allowed`}
                                    />
                                </div>
                            </div>

                            {/* FIELD: PASSWORD */}
                            <div>
                                <div className="flex items-center justify-between mb-2">
                                    <label
                                        htmlFor="password"
                                        className="block text-xs font-semibold text-[#1E242B] uppercase tracking-wider"
                                    >
                                        Password
                                    </label>
                                    <span className="text-[11px] text-[#2B303A]/50">
                                        Wajib diisi
                                    </span>
                                </div>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#2B303A]/50">
                                        <Lock className="w-4 h-4" />
                                    </div>
                                    <input
                                        type={showPassword ? 'text' : 'password'}
                                        id="password"
                                        name="password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        placeholder="••••••••"
                                        autoComplete="current-password"
                                        required
                                        disabled={processing}
                                        className={`w-full h-11 pl-10 pr-11 text-sm bg-white text-[#1E242B] rounded-xl border ${
                                            errors.password
                                                ? 'border-[#E63946] focus:border-[#E63946] focus:ring-2 focus:ring-[#E63946]/20'
                                                : 'border-[#2B303A]/20 hover:border-[#2B303A]/40 focus:border-[#E05A2B] focus:ring-2 focus:ring-[#E05A2B]/20'
                                        } shadow-sm transition-all duration-150 outline-none placeholder:text-[#2B303A]/40 disabled:opacity-60 disabled:cursor-not-allowed`}
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword(!showPassword)}
                                        disabled={processing}
                                        aria-label={showPassword ? 'Sembunyikan password' : 'Tampilkan password'}
                                        className="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#2B303A]/50 hover:text-[#1E242B] transition-colors focus:outline-none cursor-pointer"
                                    >
                                        {showPassword ? (
                                            <EyeOff className="w-4 h-4" />
                                        ) : (
                                            <Eye className="w-4 h-4" />
                                        )}
                                    </button>
                                </div>
                                <AnimatePresence>
                                    {errors.password && (
                                        <motion.p
                                            initial={{ opacity: 0, height: 0 }}
                                            animate={{ opacity: 1, height: 'auto' }}
                                            exit={{ opacity: 0, height: 0 }}
                                            className="text-xs text-[#E63946] mt-1.5 font-medium flex items-center gap-1"
                                        >
                                            <AlertTriangle className="w-3.5 h-3.5 shrink-0" />
                                            <span>{errors.password}</span>
                                        </motion.p>
                                    )}
                                </AnimatePresence>
                            </div>

                            {/* BARIS OPSI: INGAT SAYA */}
                            <div className="flex items-center justify-between pt-1">
                                <label
                                    htmlFor="remember"
                                    className="inline-flex items-center gap-2.5 text-xs text-[#1E242B] font-medium cursor-pointer select-none"
                                >
                                    <input
                                        type="checkbox"
                                        id="remember"
                                        name="remember"
                                        checked={data.remember}
                                        onChange={(e) => setData('remember', e.target.checked)}
                                        disabled={processing}
                                        className="w-4 h-4 rounded text-[#E05A2B] border-[#2B303A]/30 focus:ring-[#E05A2B] focus:ring-offset-0 cursor-pointer accent-[#E05A2B]"
                                    />
                                    <span>Ingat saya di perangkat ini</span>
                                </label>
                                <span className="text-[11px] text-[#52B788] font-medium hidden sm:inline-flex items-center gap-1">
                                    <ShieldCheck className="w-3 h-3" />
                                    TLS 1.3
                                </span>
                            </div>

                            {/* TOMBOL SUBMIT */}
                            <div className="pt-2">
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="w-full h-11 sm:h-12 inline-flex items-center justify-center gap-2 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-[#E05A2B] via-[#E05A2B] to-[#D94E34] shadow-[0_4px_18px_rgba(224,90,43,0.32),inset_0_1px_0_0_rgba(255,255,255,0.25)] hover:shadow-[0_6px_24px_rgba(224,90,43,0.48)] hover:brightness-105 active:scale-[0.985] transition-all duration-200 border border-white/20 disabled:pointer-events-none disabled:opacity-60 cursor-pointer"
                                >
                                    {processing ? (
                                        <>
                                            <Loader2 className="w-4 h-4 animate-spin text-white" />
                                            <span>Memverifikasi Kredensial...</span>
                                        </>
                                    ) : (
                                        <>
                                            <Lock className="w-4 h-4 text-white/90" />
                                            <span>Masuk ke Panel Admin</span>
                                        </>
                                    )}
                                </button>
                            </div>
                        </form>

                        {/* Security Notice & Footer */}
                        <div className="mt-8 pt-6 border-t border-[#2B303A]/10 text-center">
                            <p className="text-[11px] text-[#2B303A]/60 leading-relaxed flex items-center justify-center gap-1.5 mb-2">
                                <Lock className="w-3 h-3 text-[#52B788] shrink-0" />
                                <span>Akses dilindungi enkripsi sesi HTTPS dan pembatasan frekuensi (Rate Limiting).</span>
                            </p>
                            <Link
                                href="/"
                                className="text-xs font-semibold text-[#E05A2B] hover:text-[#D94E34] transition-colors inline-flex items-center gap-1 mt-1"
                            >
                                <span>Bukan administrator? Kembali ke Beranda Publik</span>
                                <span>&rarr;</span>
                            </Link>
                        </div>
                    </div>
                </main>
            </div>
        </>
    );
}

