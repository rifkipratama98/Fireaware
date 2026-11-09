import React, { useState } from 'react';
import { Link, usePage, Head, router } from '@inertiajs/react';
import {
    LayoutDashboard,
    BookOpen,
    GraduationCap,
    Megaphone,
    MapPin,
    RefreshCw,
    Bell,
    ExternalLink,
    LogOut,
    Menu,
    X,
    Trees,
    ChevronRight,
} from 'lucide-react';
import { ToastNotification } from '@/Components/ToastNotification';
import { cn } from '@/Lib/utils';

export function AdminLayout({ children, title, subtitle, headerAction }) {
    const { url, props } = usePage();
    const user = props.auth?.user;
    const [sidebarOpen, setSidebarOpen] = useState(false);

    const adminNavLinks = [
        { href: '/admin', label: 'Dashboard', icon: LayoutDashboard },
        { href: '/admin/guides', label: 'Panduan Mitigasi', icon: BookOpen },
        { href: '/admin/education', label: 'Edukasi Karhutla', icon: GraduationCap },
        { href: '/admin/announcements', label: 'Pengumuman Siaga', icon: Megaphone },
        { href: '/admin/wilayah', label: 'Wilayah & Posko', icon: MapPin },
        { href: '/admin/integrasi', label: 'Integrasi Satelit', icon: RefreshCw },
        { href: '/admin/notifikasi', label: 'Pengaturan Notifikasi', icon: Bell },
    ];

    const handleLogout = (e) => {
        e.preventDefault();
        router.post('/admin/logout');
    };

    return (
        <div className="min-h-screen bg-[#F6F5F2] text-[#1E242B] flex flex-col lg:flex-row font-sans">
            {title && <Head title={`Admin — ${title}`} />}

            {/* Mobile Top Header */}
            <div className="lg:hidden flex items-center justify-between px-4 py-3 bg-[#1E242B] text-[#F8F9FA] border-b border-white/10 sticky top-0 z-40">
                <div className="flex items-center gap-2">
                    <div className="w-8 h-8 rounded-lg bg-[#E05A2B] text-white flex items-center justify-center">
                        <Trees className="w-4 h-4" />
                    </div>
                    <span className="font-display font-extrabold text-sm tracking-tight">
                        FireAware Admin
                    </span>
                </div>
                <button
                    type="button"
                    onClick={() => setSidebarOpen(!sidebarOpen)}
                    className="p-1.5 rounded-lg text-white hover:bg-white/10"
                    aria-label="Toggle menu"
                >
                    {sidebarOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
                </button>
            </div>

            {/* Sidebar */}
            <aside
                className={cn(
                    'fixed inset-y-0 left-0 z-50 w-64 bg-[#1E242B] text-[#F8F9FA] flex flex-col justify-between transition-transform duration-300 lg:static lg:translate-x-0 border-r border-white/10 shadow-xl lg:shadow-none',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                )}
            >
                <div className="flex flex-col flex-1 overflow-y-auto">
                    {/* Brand header */}
                    <div className="p-6 border-b border-white/10 flex items-center justify-between">
                        <Link href="/admin" className="flex items-center gap-3">
                            <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-[#E05A2B] to-[#D94E34] text-white flex items-center justify-center shadow-md">
                                <Trees className="w-5 h-5" />
                            </div>
                            <div>
                                <h1 className="font-display font-extrabold text-base tracking-tight leading-none text-[#F8F9FA]">
                                    FireAware
                                </h1>
                                <span className="text-[10px] uppercase font-bold tracking-widest text-[#F4A261]">
                                    Admin Panel
                                </span>
                            </div>
                        </Link>
                        <button
                            type="button"
                            onClick={() => setSidebarOpen(false)}
                            className="lg:hidden text-white/70 hover:text-white"
                        >
                            <X className="w-5 h-5" />
                        </button>
                    </div>

                    {/* Navigation */}
                    <nav className="p-4 space-y-1.5">
                        <div className="px-3 py-2 text-[11px] font-bold uppercase tracking-wider text-white/40">
                            Kelola Konten &amp; Data
                        </div>
                        {adminNavLinks.map((item) => {
                            const Icon = item.icon;
                            const isActive =
                                item.href === '/admin'
                                    ? url === '/admin'
                                    : url.startsWith(item.href);

                            return (
                                <Link
                                    key={item.href}
                                    href={item.href}
                                    onClick={() => setSidebarOpen(false)}
                                    className={cn(
                                        'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold tracking-wide transition-all',
                                        isActive
                                            ? 'bg-[#E05A2B] text-white shadow-sm'
                                            : 'text-[#F8F9FA]/75 hover:text-white hover:bg-white/5'
                                    )}
                                >
                                    <Icon className="w-4 h-4 shrink-0" />
                                    <span>{item.label}</span>
                                </Link>
                            );
                        })}
                    </nav>
                </div>

                {/* Bottom area */}
                <div className="p-4 border-t border-white/10 space-y-2 bg-[#1C2526]/50">
                    <Link
                        href="/"
                        target="_blank"
                        className="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-medium text-[#F8F9FA]/80 hover:text-white hover:bg-white/5 transition-all"
                    >
                        <span className="flex items-center gap-2">
                            <ExternalLink className="w-3.5 h-3.5 text-[#52B788]" />
                            <span>Lihat Portal Publik</span>
                        </span>
                        <ChevronRight className="w-3.5 h-3.5 opacity-50" />
                    </Link>

                    <div className="pt-2 border-t border-white/10 flex items-center justify-between">
                        <div className="flex flex-col text-xs leading-tight">
                            <span className="font-semibold text-white truncate max-w-[120px]">
                                {user?.name || 'Administrator'}
                            </span>
                            <span className="text-[10px] text-white/50">{user?.role || 'Admin'}</span>
                        </div>
                        <button
                            type="button"
                            onClick={handleLogout}
                            className="p-2 rounded-lg text-white/70 hover:text-[#E63946] hover:bg-white/5 transition-colors"
                            title="Keluar dari sistem"
                        >
                            <LogOut className="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </aside>

            {/* Sidebar Backdrop on Mobile */}
            {sidebarOpen && (
                <div
                    onClick={() => setSidebarOpen(false)}
                    className="fixed inset-0 bg-black/60 z-40 lg:hidden"
                />
            )}

            {/* Main Content View */}
            <div className="flex-1 flex flex-col min-w-0">
                {/* Header bar */}
                {(title || subtitle || headerAction) && (
                    <header className="bg-white border-b border-black/5 px-6 py-5 sm:px-8">
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                {title && (
                                    <h1 className="text-xl sm:text-2xl font-extrabold text-[#1E242B] font-display">
                                        {title}
                                    </h1>
                                )}
                                {subtitle && (
                                    <p className="mt-1 text-xs sm:text-sm text-[#1E242B]/60">
                                        {subtitle}
                                    </p>
                                )}
                            </div>
                            {headerAction && <div className="shrink-0">{headerAction}</div>}
                        </div>
                    </header>
                )}

                {/* Content body */}
                <main className="p-6 sm:p-8 flex-1 overflow-x-hidden">{children}</main>
            </div>

            <ToastNotification />
        </div>
    );
}

export default AdminLayout;

