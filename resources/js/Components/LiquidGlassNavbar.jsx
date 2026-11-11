import React, { useState, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { motion } from 'motion/react';
import { Trees, Bell, MapPin, Menu, LogIn, Shield } from 'lucide-react';
import { MobileNavigation } from './MobileNavigation';
import { cn } from '@/Lib/utils';

export function LiquidGlassNavbar({ onOpenAlertModal }) {
    const { url, props } = usePage();
    const user = props?.auth?.user;
    const [scrolled, setScrolled] = useState(false);
    const [mobileOpen, setMobileOpen] = useState(false);

    useEffect(() => {
        const handleScroll = () => {
            if (window.scrollY > 30) {
                setScrolled(true);
            } else {
                setScrolled(false);
            }
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    const navLinks = [
        { href: '/', label: 'Beranda' },
        { href: '/peta-hotspot', label: 'Peta Hotspot' },
        { href: '/kualitas-udara', label: 'Kualitas Udara' },
        { href: '/pengumuman', label: 'Pengumuman' },
        { href: '/edukasi', label: 'Edukasi' },
        { href: '/panduan', label: 'Panduan' },
        { href: '/tentang', label: 'Tentang' },
    ];

    return (
        <>
            <header className={cn(
                'sticky top-0 left-0 right-0 z-50 w-full px-3 sm:px-6 lg:px-8 transition-all duration-300 pointer-events-none -mb-16 sm:-mb-20',
                scrolled ? 'pt-2.5 sm:pt-3' : 'pt-3.5 sm:pt-4'
            )}>
                <nav
                    className={cn(
                        'pointer-events-auto mx-auto max-w-7xl flex items-center justify-between px-4 sm:px-6 py-2 sm:py-2.5 rounded-2xl sm:rounded-full transition-all duration-300 glass-reflection',
                        scrolled ? 'liquid-glass-scrolled' : 'liquid-glass'
                    )}
                    aria-label="Navigasi Utama"
                >
                    {/* Brand Logo */}
                    <Link
                        href="/"
                        className="group flex items-center gap-2.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#E05A2B] rounded-lg"
                    >
                        <div className="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gradient-to-br from-[#E05A2B] to-[#D94E34] text-white flex items-center justify-center shadow-[0_2px_8px_rgba(224,90,43,0.4),inset_0_1px_0_0_rgba(255,255,255,0.3)] group-hover:scale-105 transition-transform duration-200 border border-white/20">
                            <Trees className="w-4 h-4 sm:w-5 sm:h-5 stroke-[2.2]" />
                        </div>
                        <div className="flex flex-col select-none">
                            <span className="font-display font-extrabold text-sm sm:text-base tracking-tight text-[#F8F9FA] group-hover:text-[#F4A261] transition-colors leading-tight">
                                FireAware
                            </span>
                            <span className="text-[9px] sm:text-[10px] uppercase font-bold tracking-widest text-[#F4A261] leading-none">
                                Kalimantan
                            </span>
                        </div>
                    </Link>

                    {/* Desktop Menu */}
                    <div className="hidden lg:flex items-center gap-0.5">
                        {navLinks.map((item) => {
                            const isActive =
                                item.href === '/'
                                    ? url === '/'
                                    : url.startsWith(item.href);

                            return (
                                <Link
                                    key={item.href}
                                    href={item.href}
                                    className={cn(
                                        'relative px-3.5 py-1.5 rounded-full text-xs font-semibold tracking-wide transition-all duration-200 select-none',
                                        isActive
                                            ? 'text-white'
                                            : 'text-[#F8F9FA]/80 hover:text-white hover:bg-white/[0.08]'
                                    )}
                                >
                                    {isActive && (
                                        <motion.span
                                            layoutId="activeNavPill"
                                            className="absolute inset-0 rounded-full bg-gradient-to-r from-[#E05A2B] to-[#D94E34] shadow-[0_2px_10px_rgba(224,90,43,0.4),inset_0_1px_0_0_rgba(255,255,255,0.25)] border border-white/20 -z-10"
                                            transition={{ type: 'spring', stiffness: 400, damping: 32 }}
                                        />
                                    )}
                                    {item.label}
                                </Link>
                            );
                        })}
                    </div>

                    {/* Right Action CTA Buttons */}
                    <div className="hidden sm:flex items-center gap-2">
                        <button
                            type="button"
                            onClick={onOpenAlertModal}
                            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold text-[#F8F9FA] bg-white/[0.08] hover:bg-white/[0.14] border border-white/15 hover:border-white/25 transition-all shadow-[inset_0_1px_0_0_rgba(255,255,255,0.15)] cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#F4A261] active:scale-95"
                            title="Aktifkan peringatan siaga"
                        >
                            <Bell className="w-3.5 h-3.5 text-[#F4A261]" />
                            <span>Peringatan</span>
                        </button>

                        {user ? (
                            <Link
                                href="/admin"
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold text-[#F8F9FA] bg-white/[0.08] hover:bg-white/[0.14] border border-white/15 hover:border-white/25 transition-all shadow-[inset_0_1px_0_0_rgba(255,255,255,0.15)] cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#52B788] active:scale-95"
                                title={`Dashboard Admin (${user.name || 'Admin'})`}
                            >
                                <Shield className="w-3.5 h-3.5 text-[#52B788]" />
                                <span>Dashboard</span>
                            </Link>
                        ) : (
                            <Link
                                href="/admin/login"
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold text-[#F8F9FA] bg-white/[0.08] hover:bg-white/[0.14] border border-white/15 hover:border-white/25 transition-all shadow-[inset_0_1px_0_0_rgba(255,255,255,0.15)] cursor-pointer focus:outline-none focus:ring-2 focus:ring-[#E05A2B] active:scale-95"
                                title="Masuk Administrator"
                            >
                                <LogIn className="w-3.5 h-3.5 text-[#F8F9FA]/80" />
                                <span>Login</span>
                            </Link>
                        )}

                        <Link
                            href="/peta-hotspot"
                            className="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold text-white bg-gradient-to-r from-[#E05A2B] via-[#E05A2B] to-[#D94E34] hover:brightness-110 border border-white/25 shadow-[0_4px_16px_rgba(224,90,43,0.35),inset_0_1px_0_0_rgba(255,255,255,0.3)] transition-all active:scale-95"
                        >
                            <MapPin className="w-3.5 h-3.5" />
                            <span>Peta Hotspot</span>
                        </Link>
                    </div>

                    {/* Mobile Menu Toggle Button */}
                    <div className="flex lg:hidden items-center gap-1.5">
                        <Link
                            href={user ? '/admin' : '/admin/login'}
                            className="p-2 rounded-xl text-[#F8F9FA] hover:bg-white/10"
                            aria-label={user ? 'Dashboard Admin' : 'Login Admin'}
                            title={user ? 'Dashboard Admin' : 'Login Admin'}
                        >
                            {user ? (
                                <Shield className="w-4 h-4 text-[#52B788]" />
                            ) : (
                                <LogIn className="w-4 h-4 text-[#F8F9FA]/90" />
                            )}
                        </Link>
                        <button
                            type="button"
                            onClick={onOpenAlertModal}
                            className="p-2 rounded-xl text-[#F8F9FA] hover:bg-white/10"
                            aria-label="Peringatan bahaya"
                        >
                            <Bell className="w-4 h-4 text-[#F4A261]" />
                        </button>
                        <button
                            type="button"
                            onClick={() => setMobileOpen(true)}
                            className="p-2 rounded-xl text-[#F8F9FA] hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-[#E05A2B]"
                            aria-label="Buka menu navigasi"
                        >
                            <Menu className="w-5 h-5" />
                        </button>
                    </div>
                </nav>
            </header>

            {/* Mobile Navigation Drawer */}
            <MobileNavigation
                isOpen={mobileOpen}
                onClose={() => setMobileOpen(false)}
                navLinks={navLinks}
                currentUrl={url}
                onOpenAlertModal={onOpenAlertModal}
                user={user}
            />
        </>
    );
}
