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
    const isCenter = align === 'center';

    return (
        <div
            className={cn(
                'flex flex-col gap-2.5 w-full',
                isCenter ? 'items-center text-center' : 'items-start text-left',
                className
            )}
        >
            {badge && (
                <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-[#E05A2B]/10 text-[#E05A2B] border border-[#E05A2B]/20">
                    {badge}
                </div>
            )}
            <div
                className={cn(
                    'w-full gap-4',
                    isCenter
                        ? 'flex flex-col items-center text-center'
                        : 'flex flex-col sm:flex-row sm:items-end sm:justify-between'
                )}
            >
                <div className={cn(isCenter && 'flex flex-col items-center text-center max-w-3xl mx-auto')}>
                    <h2 className="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-[#1E242B] font-display">
                        {title}
                    </h2>
                    {description && (
                        <p
                            className={cn(
                                'mt-2.5 text-sm sm:text-base text-[#1E242B]/70 leading-relaxed',
                                isCenter ? 'max-w-2xl mx-auto text-center' : 'max-w-2xl'
                            )}
                        >
                            {description}
                        </p>
                    )}
                </div>
                {action && <div className="shrink-0">{action}</div>}
            </div>
        </div>
    );
}