import React from 'react';
import { cn } from '@/Lib/utils';

export function SectionHeading({
    badge,
    title,
    description,
    align = 'left',
    action,
    className,
}) {
    return (
        <div
            className={cn(
                'flex flex-col gap-2.5',
                align === 'center' ? 'items-center text-center' : 'items-start text-left',
                className
            )}
        >
            {badge && (
                <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-[#E05A2B]/10 text-[#E05A2B] border border-[#E05A2B]/20">
                    {badge}
                </div>
            )}
            <div className="flex flex-col sm:flex-row sm:items-end sm:justify-between w-full gap-4">
                <div>
                    <h2 className="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#1E242B] font-display">
                        {title}
                    </h2>
                    {description && (
                        <p className="mt-2 text-sm sm:text-base text-[#1E242B]/70 max-w-2xl leading-relaxed">
                            {description}
                        </p>
                    )}
                </div>
                {action && <div className="shrink-0">{action}</div>}
            </div>
        </div>
    );
}

