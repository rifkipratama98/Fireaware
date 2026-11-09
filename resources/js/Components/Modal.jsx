import React, { useEffect } from 'react';
import { createPortal } from 'react-dom';
import { motion, AnimatePresence } from 'motion/react';
import { X } from 'lucide-react';
import { cn } from '@/Lib/utils';

export function Modal({
    isOpen,
    onClose,
    title,
    description,
    children,
    footer,
    maxWidth = 'max-w-lg',
    className,
}) {
    useEffect(() => {
        const handleKeyDown = (e) => {
            if (e.key === 'Escape' && isOpen) {
                onClose();
            }
        };

        if (isOpen) {
            document.body.style.overflow = 'hidden';
            window.addEventListener('keydown', handleKeyDown);
        } else {
            document.body.style.overflow = '';
        }

        return () => {
            document.body.style.overflow = '';
            window.removeEventListener('keydown', handleKeyDown);
        };
    }, [isOpen, onClose]);

    if (typeof document === 'undefined') return null;

    return createPortal(
        <AnimatePresence>
            {isOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
                    {/* Backdrop */}
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        transition={{ duration: 0.2 }}
                        onClick={onClose}
                        className="fixed inset-0 bg-[#1E242B]/70 backdrop-blur-sm"
                        aria-hidden="true"
                    />

                    {/* Modal Content */}
                    <motion.div
                        role="dialog"
                        aria-modal="true"
                        aria-labelledby={title ? 'modal-title' : undefined}
                        aria-describedby={description ? 'modal-description' : undefined}
                        initial={{ opacity: 0, scale: 0.96, y: 12 }}
                        animate={{ opacity: 1, scale: 1, y: 0 }}
                        exit={{ opacity: 0, scale: 0.96, y: 12 }}
                        transition={{ duration: 0.2, ease: [0.16, 1, 0.3, 1] }}
                        className={cn(
                            'relative w-full rounded-2xl bg-white shadow-2xl border border-black/10 overflow-hidden z-10 my-8',
                            maxWidth,
                            className
                        )}
                    >
                        {/* Header */}
                        {(title || onClose) && (
                            <div className="flex items-start justify-between p-5 sm:p-6 border-b border-black/5 bg-[#F8F9FA]/50">
                                <div>
                                    {title && (
                                        <h3
                                            id="modal-title"
                                            className="text-lg font-bold text-[#1E242B] font-display"
                                        >
                                            {title}
                                        </h3>
                                    )}
                                    {description && (
                                        <p
                                            id="modal-description"
                                            className="mt-1 text-xs sm:text-sm text-[#1E242B]/70"
                                        >
                                            {description}
                                        </p>
                                    )}
                                </div>
                                <button
                                    type="button"
                                    onClick={onClose}
                                    className="p-1.5 rounded-xl text-[#1E242B]/50 hover:text-[#1E242B] hover:bg-black/5 transition-colors focus:outline-none focus:ring-2 focus:ring-[#E05A2B]"
                                    aria-label="Tutup dialog"
                                >
                                    <X className="w-5 h-5" />
                                </button>
                            </div>
                        )}

                        {/* Body */}
                        <div className="p-5 sm:p-6">{children}</div>

                        {/* Footer */}
                        {footer && (
                            <div className="flex items-center justify-end gap-3 px-5 py-4 sm:px-6 bg-[#F8F9FA] border-t border-black/5">
                                {footer}
                            </div>
                        )}
                    </motion.div>
                </div>
            )}
        </AnimatePresence>,
        document.body
    );
}

