import React from 'react';
import { Link } from '@inertiajs/react';
import { cva } from 'class-variance-authority';
import { cn } from '@/Lib/utils';
import { Loader2 } from 'lucide-react';

const buttonVariants = cva(
    'inline-flex items-center justify-center gap-2 rounded-xl text-sm font-semibold transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#E05A2B] focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 select-none cursor-pointer',
    {
        variants: {
            variant: {
                primary:
                    'bg-gradient-to-r from-[#E05A2B] via-[#E05A2B] to-[#D94E34] text-white shadow-[0_4px_16px_rgba(224,90,43,0.32),inset_0_1px_0_0_rgba(255,255,255,0.25)] hover:shadow-[0_6px_22px_rgba(224,90,43,0.48)] hover:brightness-105 active:scale-[0.98] border border-white/20',
                secondary:
                    'bg-[#2B303A] text-[#F8F9FA] hover:bg-[#1E242B] border border-white/10 active:scale-[0.98]',
                outline:
                    'border border-[#2B303A]/20 text-[#1E242B] hover:bg-black/5 active:scale-[0.98]',
                glass:
                    'bg-white/[0.08] hover:bg-white/[0.16] text-[#F8F9FA] hover:text-white border border-white/18 backdrop-blur-md shadow-[inset_0_1px_0_0_rgba(255,255,255,0.18),0_4px_16px_rgba(0,0,0,0.2)] active:scale-[0.98]',
                ghost:
                    'text-[#1E242B] hover:bg-black/5 active:scale-[0.98]',
                danger:
                    'bg-[#E63946] text-white hover:bg-[#cf2f3c] active:scale-[0.98]',
                pine:
                    'bg-[#1B4332] text-[#F8F9FA] hover:bg-[#143326] active:scale-[0.98]',
            },
            size: {
                sm: 'h-8 px-3 text-xs rounded-lg',
                md: 'h-10 px-4 text-sm',
                lg: 'h-12 px-6 text-base rounded-2xl',
                icon: 'h-9 w-9 rounded-lg p-0',
            },
        },
        defaultVariants: {
            variant: 'primary',
            size: 'md',
        },
    }
);

export function Button({
    className,
    variant,
    size,
    loading = false,
    disabled,
    href,
    children,
    type = 'button',
    ...props
}) {
    const content = (
        <>
            {loading && <Loader2 className="w-4 h-4 animate-spin" />}
            {children}
        </>
    );

    if (href) {
        return (
            <Link
                href={href}
                className={cn(buttonVariants({ variant, size, className }))}
                {...props}
            >
                {content}
            </Link>
        );
    }

    return (
        <button
            type={type}
            disabled={disabled || loading}
            className={cn(buttonVariants({ variant, size, className }))}
            {...props}
        >
            {content}
        </button>
    );
}
