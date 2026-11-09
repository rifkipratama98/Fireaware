import React, { useState, useEffect } from 'react';
import { Modal } from './Modal';
import { Button } from './Button';
import { Bell, BellOff, Info, CheckCircle2, AlertTriangle } from 'lucide-react';

export function BrowserNotificationModal({ isOpen, onClose }) {
    const [permission, setPermission] = useState('default');
    const [enabledPref, setEnabledPref] = useState(false);

    useEffect(() => {
        if (typeof window !== 'undefined' && 'Notification' in window) {
            setPermission(Notification.permission);
            const savedPref = localStorage.getItem('fireaware_browser_notify_pref');
            setEnabledPref(savedPref === 'granted');
        }
    }, [isOpen]);

    const requestPermission = async () => {
        if (!('Notification' in window)) {
            alert('Peramban Anda tidak mendukung Web Notification API.');
            return;
        }

        try {
            const result = await Notification.requestPermission();
            setPermission(result);
            if (result === 'granted') {
                localStorage.setItem('fireaware_browser_notify_pref', 'granted');
                setEnabledPref(true);
                new Notification('FireAware Kalimantan', {
                    body: 'Peringatan dini peramban aktif. Anda akan menerima notifikasi saat ada lonjakan titik panas.',
                    icon: '/favicon.ico',
                });
            } else {
                localStorage.setItem('fireaware_browser_notify_pref', 'denied');
                setEnabledPref(false);
            }
        } catch (err) {
            console.error('Gagal meminta izin notifikasi:', err);
        }
    };

    const disableNotifications = () => {
        localStorage.setItem('fireaware_browser_notify_pref', 'denied');
        setEnabledPref(false);
    };

    return (
        <Modal
            isOpen={isOpen}
            onClose={onClose}
            title="Peringatan Dini Peramban Web"
            description="Dapatkan peringatan langsung di peramban saat terjadi lonjakan titik panas atau bahaya kualitas udara di Kalimantan."
            footer={
                <>
                    {enabledPref && (
                        <Button
                            variant="danger"
                            size="sm"
                            onClick={disableNotifications}
                            className="mr-auto"
                        >
                            <BellOff className="w-4 h-4 mr-1.5" />
                            Nonaktifkan
                        </Button>
                    )}
                    <Button variant="outline" size="sm" onClick={onClose}>
                        Tutup
                    </Button>
                    {permission !== 'granted' && (
                        <Button variant="primary" size="sm" onClick={requestPermission}>
                            <Bell className="w-4 h-4 mr-1.5" />
                            Aktifkan Notifikasi
                        </Button>
                    )}
                </>
            }
        >
            <div className="space-y-4">
                {/* Status Box */}
                <div className="p-4 rounded-xl bg-[#F8F9FA] border border-black/5 space-y-2.5">
                    <div className="flex items-center justify-between text-xs">
                        <span className="text-[#1E242B]/70">Status Izin Peramban:</span>
                        <span
                            className={`font-semibold px-2 py-0.5 rounded-full ${
                                permission === 'granted'
                                    ? 'bg-[#2A9D8F]/15 text-[#2A9D8F]'
                                    : permission === 'denied'
                                    ? 'bg-[#E63946]/15 text-[#E63946]'
                                    : 'bg-[#F4A261]/20 text-[#a85817]'
                            }`}
                        >
                            {permission === 'granted'
                                ? 'Diizinkan'
                                : permission === 'denied'
                                ? 'Diblokir'
                                : 'Perlu Konfirmasi'}
                        </span>
                    </div>

                    <div className="flex items-center justify-between text-xs">
                        <span className="text-[#1E242B]/70">Preferensi:</span>
                        <span className="font-semibold text-[#1E242B]">
                            {enabledPref ? 'Aktif' : 'Belum Diaktifkan'}
                        </span>
                    </div>
                </div>

                {/* Info Note */}
                <div className="p-3.5 rounded-xl bg-[#F6F5F2] border border-[#2B303A]/10 text-xs text-[#1E242B]/80 space-y-1.5">
                    <div className="flex items-center gap-1.5 font-bold text-[#E05A2B]">
                        <Info className="w-4 h-4" />
                        <span>Informasi Privasi &amp; Teknis:</span>
                    </div>
                    <div className="space-y-1.5 pt-1 text-[#1E242B]/75">
                        <div className="flex items-start gap-2">
                            <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F] shrink-0 mt-0.5" />
                            <span>Tidak memerlukan akun atau pengisian data pribadi warga.</span>
                        </div>
                        <div className="flex items-start gap-2">
                            <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F] shrink-0 mt-0.5" />
                            <span>Menggunakan <em>Web Notification API</em> bawaan peramban Anda.</span>
                        </div>
                        <div className="flex items-start gap-2">
                            <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F] shrink-0 mt-0.5" />
                            <span>Notifikasi aktif saat tab FireAware terbuka di peramban web.</span>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>
    );
}

