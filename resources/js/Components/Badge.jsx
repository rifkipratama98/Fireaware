import React from 'react';
import { cva } from 'class-variance-authority';
import { cn } from '@/Lib/utils';

const badgeVariants = cva(
    'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold tracking-wide transition-colors',
    {
        variants: {
            variant: {
                aman: 'bg-[#2A9D8F]/15 text-[#1b685f] border border-[#2A9D8F]/30',
                waspada: 'bg-[#E9C46A]/20 text-[#8f6d14] border border-[#E9C46A]/40',
                siaga: 'bg-[#F4A261]/20 text-[#a85817] border border-[#F4A261]/40',
                darurat: 'bg-[#E63946]/15 text-[#b0222f] border border-[#E63946]/30',
                pine: 'bg-[#1B4332]/15 text-[#1B4332] border border-[#1B4332]/25',
                burnt: 'bg-[#E05A2B]/15 text-[#E05A2B] border border-[#E05A2B]/30',
                glass: 'bg-white/10 text-[#F8F9FA] backdrop-blur border border-white/20',
                outline: 'border border-[#2B303A]/20 text-[#1E242B]',
                neutral: 'bg-[#1E242B]/10 text-[#1E242B] border border-[#1E242B]/10',
            },
        },
        defaultVariants: {
            variant: 'neutral',
        },
    }
);

export function Badge({ className, variant, dot = false, children, ...props }) {
    return (
        <span className={cn(badgeVariants({ variant, className }))} {...props}>
            {dot && (
                <span
                    className={cn(
                        'w-1.5 h-1.5 rounded-full animate-pulse',
                        variant === 'aman' && 'bg-[#2A9D8F]',
                        variant === 'waspada' && 'bg-[#E9C46A]',
                        variant === 'siaga' && 'bg-[#F4A261]',
                        variant === 'darurat' && 'bg-[#E63946]',
                        variant === 'burnt' && 'bg-[#E05A2B]',
                        variant === 'pine' && 'bg-[#1B4332]',
                        variant === 'glass' && 'bg-[#F4A261]',
                        variant === 'neutral' && 'bg-[#1E242B]'
                    )}
                />
            )}
            {children}
        </span>
    );
}
