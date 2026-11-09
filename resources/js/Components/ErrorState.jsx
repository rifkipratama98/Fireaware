import React from 'react';
import { cn } from '@/Lib/utils';
import { AlertTriangle, RefreshCw } from 'lucide-react';
import { Button } from './Button';

export function ErrorState({
    title = 'Terjadi Kendala',
    description = 'Gagal memuat data dari server atau koneksi terputus. Silakan coba kembali.',
    onRetry,
    className,
}) {
    return (
        <div
            className={cn(
                'w-full flex flex-col items-center justify-center p-8 sm:p-12 text-center rounded-2xl bg-white border border-[#E63946]/20 shadow-xs',
                className
            )}
        >
            <div className="w-14 h-14 rounded-2xl bg-[#E63946]/10 text-[#E63946] flex items-center justify-center mb-4 ring-8 ring-[#E63946]/5">
                <AlertTriangle className="w-7 h-7 stroke-[1.75]" />
            </div>
            <h3 className="text-lg font-bold text-[#1E242B] font-display">{title}</h3>
            <p className="mt-1.5 text-sm text-[#1E242B]/70 max-w-md leading-relaxed">
                {description}
            </p>
            {onRetry && (
                <div className="mt-5">
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={onRetry}
                        className="gap-2 border-[#E63946]/30 text-[#E63946] hover:bg-[#E63946]/5"
                    >
                        <RefreshCw className="w-4 h-4" />
                        Coba Lagi
                    </Button>
                </div>
            )}
        </div>
    );
}

