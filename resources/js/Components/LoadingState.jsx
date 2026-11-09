import React from 'react';
import { cn } from '@/Lib/utils';
import { Loader2 } from 'lucide-react';

export function LoadingState({
    message = 'Memuat data...',
    className,
    size = 'md',
}) {
    const sizeMap = {
        sm: 'h-32',
        md: 'h-64',
        lg: 'h-96',
    };

    return (
        <div
            className={cn(
                'w-full flex flex-col items-center justify-center p-8 text-center rounded-2xl bg-white/60 border border-black/5 backdrop-blur-sm',
                sizeMap[size] || sizeMap.md,
                className
            )}
        >
            <div className="relative flex items-center justify-center mb-4">
                <div className="w-12 h-12 rounded-full border-2 border-[#E05A2B]/20 border-t-[#E05A2B] animate-spin" />
                <Loader2 className="w-5 h-5 text-[#E05A2B] absolute animate-pulse" />
            </div>
            <p className="text-sm font-medium text-[#1E242B]/80">{message}</p>
            <span className="text-xs text-[#1E242B]/50 mt-1">Sinkronisasi telemetri Kalimantan</span>
        </div>
    );
}

