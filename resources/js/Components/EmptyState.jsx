import React from 'react';
import { cn } from '@/Lib/utils';
import { Inbox } from 'lucide-react';

export function EmptyState({
    icon: Icon = Inbox,
    title = 'Belum ada data',
    description = 'Data untuk kategori atau filter yang Anda pilih saat ini belum tersedia.',
    action,
    className,
}) {
    return (
        <div
            className={cn(
                'w-full flex flex-col items-center justify-center p-8 sm:p-12 text-center rounded-2xl bg-white/70 border border-black/5 shadow-xs',
                className
            )}
        >
            <div className="w-14 h-14 rounded-2xl bg-[#E05A2B]/10 text-[#E05A2B] flex items-center justify-center mb-4 ring-8 ring-[#E05A2B]/5">
                <Icon className="w-7 h-7 stroke-[1.75]" />
            </div>
            <h3 className="text-lg font-bold text-[#1E242B] font-display">{title}</h3>
            <p className="mt-1.5 text-sm text-[#1E242B]/70 max-w-md leading-relaxed">
                {description}
            </p>
            {action && <div className="mt-5">{action}</div>}
        </div>
    );
}

