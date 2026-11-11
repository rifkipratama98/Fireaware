import React, { useState } from 'react';
import { cn } from '@/Lib/utils';
import { Trees } from 'lucide-react';

export function ImageWithFallback({
    src,
    alt = '',
    className,
    fallbackText = 'FireAware Kalimantan',
    ...props
}) {
    const [hasError, setHasError] = useState(!src);

    if (hasError || !src) {
        return (
            <div
                className={cn(
                    'flex flex-col items-center justify-center bg-linear-to-br from-[#1E242B] to-[#2B303A] text-[#F8F9FA]/70 p-4 text-center select-none',
                    className
                )}
            >
                <Trees className="w-8 h-8 text-[#E05A2B] mb-2 stroke-[1.5]" />
                <span className="text-xs font-medium tracking-wide">{fallbackText}</span>
            </div>
        );
    }

    return (
        <img
            src={src}
            alt={alt}
            onError={() => setHasError(true)}
            className={cn('object-cover transition-opacity duration-300', className)}
            {...props}
        />
    );
}

