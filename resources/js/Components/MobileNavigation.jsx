import React from 'react';
import { Link } from '@inertiajs/react';
import { motion, AnimatePresence } from 'motion/react';
import { X, Bell, Shield, Flame, ChevronRight, LogIn } from 'lucide-react';

export function MobileNavigation({
    isOpen,
    onClose,
    navLinks,
    currentUrl,
    onOpenAlertModal,
    user,
}) {
    return (
        <AnimatePresence>
            {isOpen && (
                <div className="fixed inset-0 z-50 lg:hidden">
                    {/* Backdrop */}
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        onClick={onClose}
                        className="fixed inset-0 bg-[#1E242B]/80 backdrop-blur-md"
                        aria-hidden="true"
                    />

                    {/* Sheet Content */}
                    <motion.div
                        initial={{ x: '100%' }}
                        animate={{ x: 0 }}
                        exit={{ x: '100%' }}
                        transition={{ type: 'spring', damping: 28, stiffness: 300 }}
                        className="fixed inset-y-0 right-0 w-full max-w-xs bg-[#1E242B]/95 text-[#F8F9FA] shadow-2xl border-l border-white/10 p-6 flex flex-col justify-between overflow-y-auto glass-reflection"
                    >
                        <div>
                            {/* Header */}
                            <div className="flex items-center justify-between pb-6 border-b border-white/10">
                                <div className="flex items-center gap-2.5">
                                    <div className="w-8 h-8 rounded-lg bg-[#E05A2B] text-white flex items-center justify-center shadow-xs">
                                        <Flame className="w-5 h-5 fill-current" />
                                    </div>
                                    <div>
                                        <div className="font-extrabold text-base leading-none tracking-tight font-display text-[#F8F9FA]">
                                            FireAware
                                        </div>
                                        <div className="text-[10px] tracking-wider text-[#F4A261] uppercase font-semibold">
                                            Kalimantan
                                        </div>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    onClick={onClose}
                                    className="p-2 rounded-xl text-white/70 hover:text-white hover:bg-white/10 transition-colors"
                                    aria-label="Tutup navigasi"
                                >
                                    <X className="w-5 h-5" />
                                </button>
                            </div>

                            {/* Nav items */}
                            <nav className="mt-6 flex flex-col gap-1">
                                {navLinks.map((item) => {
                                    const isActive =
                                        item.href === '/'
                                            ? currentUrl === '/'
                                            : currentUrl.startsWith(item.href);

                                    return (
                                        <Link
                                            key={item.href}
                                            href={item.href}
                                            onClick={onClose}
                                            className={`flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all ${
                                                isActive
                                                    ? 'bg-[#E05A2B] text-white shadow-xs font-semibold'
                                                    : 'text-[#F8F9FA]/80 hover:text-white hover:bg-white/5'
                                            }`}
                                        >
                                            <span>{item.label}</span>
                                            {isActive && (
                                                <ChevronRight className="w-4 h-4 text-white" />
                                            )}
                                        </Link>
                                    );
                                })}
                            </nav>
                        </div>

                        {/* Bottom Actions */}
                        <div className="pt-6 border-t border-white/10 flex flex-col gap-3">
                            <button
                                type="button"
                                onClick={() => {
                                    onClose();
                                    if (onOpenAlertModal) onOpenAlertModal();
                                }}
                                className="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-white/10 text-white hover:bg-white/15 border border-white/15 transition-all"
                            >
                                <Bell className="w-4 h-4 text-[#F4A261]" />
                                <span>Peringatan Bahaya</span>
                            </button>

                            {user ? (
                                <Link
                                    href="/admin"
                                    onClick={onClose}
                                    className="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-[#2B303A] text-white hover:bg-black/40 border border-white/10 transition-all shadow-sm"
                                >
                                    <Shield className="w-4 h-4 text-[#52B788]" />
                                    <span>Dashboard Admin</span>
                                </Link>
                            ) : (
                                <Link
                                    href="/admin/login"
                                    onClick={onClose}
                                    className="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-white/10 text-white hover:bg-white/15 border border-white/15 transition-all shadow-sm"
                                >
                                    <LogIn className="w-4 h-4 text-[#F8F9FA]" />
                                    <span>Login Admin</span>
                                </Link>
                            )}
                        </div>
                    </motion.div>
                </div>
            )}
        </AnimatePresence>
    );
}

