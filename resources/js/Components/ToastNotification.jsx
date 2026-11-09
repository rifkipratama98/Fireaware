import React, { useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';
import { motion, AnimatePresence } from 'motion/react';
import { CheckCircle2, AlertCircle, Info, AlertTriangle, X } from 'lucide-react';
import { cn } from '@/Lib/utils';

export function ToastNotification() {
    const { flash } = usePage().props;
    const [toasts, setToasts] = useState([]);

    useEffect(() => {
        if (!flash) return;

        const newToasts = [];
        if (flash.success) {
            newToasts.push({
                id: Date.now() + '-success',
                type: 'success',
                message: flash.success,
            });
        }
        if (flash.error) {
            newToasts.push({
                id: Date.now() + '-error',
                type: 'error',
                message: flash.error,
            });
        }
        if (flash.warning) {
            newToasts.push({
                id: Date.now() + '-warning',
                type: 'warning',
                message: flash.warning,
            });
        }
        if (flash.info || flash.status) {
            newToasts.push({
                id: Date.now() + '-info',
                type: 'info',
                message: flash.info || flash.status,
            });
        }

        if (newToasts.length > 0) {
            setToasts((prev) => [...prev, ...newToasts]);
        }
    }, [flash]);

    const removeToast = (id) => {
        setToasts((prev) => prev.filter((t) => t.id !== id));
    };

    return (
        <div className="fixed bottom-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm w-full pointer-events-none p-4">
            <AnimatePresence>
                {toasts.map((toast) => (
                    <motion.div
                        key={toast.id}
                        initial={{ opacity: 0, y: 20, scale: 0.95 }}
                        animate={{ opacity: 1, y: 0, scale: 1 }}
                        exit={{ opacity: 0, y: 10, scale: 0.95 }}
                        transition={{ duration: 0.2 }}
                        className={cn(
                            'pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-xl border backdrop-blur-md text-sm',
                            toast.type === 'success' &&
                                'bg-[#1B4332]/95 text-[#F8F9FA] border-[#52B788]/30',
                            toast.type === 'error' &&
                                'bg-[#E63946]/95 text-white border-red-300/30',
                            toast.type === 'warning' &&
                                'bg-[#F4A261]/95 text-[#1E242B] border-amber-400/40 font-medium',
                            toast.type === 'info' &&
                                'bg-[#1E242B]/95 text-[#F8F9FA] border-white/20'
                        )}
                    >
                        <div className="shrink-0 mt-0.5">
                            {toast.type === 'success' && (
                                <CheckCircle2 className="w-5 h-5 text-[#52B788]" />
                            )}
                            {toast.type === 'error' && (
                                <AlertCircle className="w-5 h-5 text-white" />
                            )}
                            {toast.type === 'warning' && (
                                <AlertTriangle className="w-5 h-5 text-[#1E242B]" />
                            )}
                            {toast.type === 'info' && (
                                <Info className="w-5 h-5 text-[#F4A261]" />
                            )}
                        </div>
                        <div className="flex-1 leading-snug">{toast.message}</div>
                        <button
                            type="button"
                            onClick={() => removeToast(toast.id)}
                            className="shrink-0 p-1 rounded-lg opacity-70 hover:opacity-100 hover:bg-white/10 transition-opacity"
                            aria-label="Tutup notifikasi"
                        >
                            <X className="w-4 h-4" />
                        </button>
                    </motion.div>
                ))}
            </AnimatePresence>
        </div>
    );
}

