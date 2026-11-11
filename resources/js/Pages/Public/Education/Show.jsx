import React, { useState, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import { motion } from 'motion/react';
import {
    ChevronRight,
    ArrowLeft,
    Calendar,
    Clock,
    User,
    Shield,
    ExternalLink,
    Share2,
    Copy,
    Check,
    Printer,
    ArrowUpRight,
    Sparkles,
    Droplets,
    Flame,
    Trees,
    Scale,
    BookOpen,
    ListFilter,
} from 'lucide-react';
import { PublicLayout } from '@/Layouts/PublicLayout';
import { ImageWithFallback } from '@/Components/ImageWithFallback';

export default function EducationShow({
    article,
    relatedArticles = [],
    categoryMeta,
    back_label = 'Kembali ke Edukasi',
}) {
    const [copied, setCopied] = useState(false);
    const [readingProgress, setReadingProgress] = useState(0);

    // Reading Progress Indicator
    useEffect(() => {
        const handleScroll = () => {
            const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
            if (totalHeight > 0) {
                const currentProgress = (window.scrollY / totalHeight) * 100;
                setReadingProgress(Math.min(100, Math.max(0, currentProgress)));
            }
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll();
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);

    const handleCopyLink = () => {
        if (navigator?.clipboard?.writeText) {
            navigator.clipboard.writeText(window.location.href);
            setCopied(true);
            setTimeout(() => setCopied(false), 2500);
        }
    };

    const handleShare = async () => {
        if (navigator.share) {
            try {
                await navigator.share({
                    title: article.title,
                    text: article.summary,
                    url: window.location.href,
                });
            } catch (err) {
                // User cancelled
            }
        } else {
            handleCopyLink();
        }
    };

    const paragraphs = article.paragraphs || (article.content ? article.content.split(/\n\s*\n/) : []);
    const headings = article.headings || [];

    const getCategoryIcon = (key) => {
        switch (key) {
            case 'ekosistem-gambut':
                return <Droplets className="w-3.5 h-3.5 text-[#2A9D8F]" />;
            case 'faktor-penyebab':
                return <Flame className="w-3.5 h-3.5 text-[#E05A2B]" />;
            case 'dampak-lingkungan':
                return <Trees className="w-3.5 h-3.5 text-[#52B788]" />;
            case 'teknik-pencegahan':
                return <Shield className="w-3.5 h-3.5 text-[#F4A261]" />;
            case 'hukum-kebijakan':
                return <Scale className="w-3.5 h-3.5 text-stone-600" />;
            default:
                return <BookOpen className="w-3.5 h-3.5 text-[#52B788]" />;
        }
    };

    return (
        <PublicLayout title={`${article.title} — Edukasi Lingkungan FireAware`}>
            {/* Reading Progress Top Bar */}
            <div
                className="fixed top-0 left-0 h-1 bg-gradient-to-r from-[#52B788] via-[#F4A261] to-[#E05A2B] z-50 transition-all duration-150"
                style={{ width: `${readingProgress}%` }}
                role="progressbar"
                aria-valuenow={Math.round(readingProgress)}
                aria-valuemin={0}
                aria-valuemax={100}
            />

            <div className="min-h-screen bg-[#F6F5F2] text-[#1E242B] pt-24 pb-20 selection:bg-[#52B788]/20 selection:text-[#1B4332]">
                {/* 1. TOP NAV BAR */}
                <div className="bg-white border-b border-stone-200/80 sticky top-16 z-30 shadow-xs">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
                        <Link
                            href="/edukasi"
                            className="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-stone-700 hover:text-[#1B4332] transition-colors group cursor-pointer"
                        >
                            <ArrowLeft className="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
                            <span>{back_label}</span>
                        </Link>

                        <div className="flex items-center gap-2">
                            <button
                                type="button"
                                onClick={handleCopyLink}
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-stone-600 bg-stone-100 hover:bg-stone-200 transition-colors cursor-pointer"
                                title="Salin Tautan"
                            >
                                {copied ? (
                                    <>
                                        <Check className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                        <span className="text-[#2A9D8F]">Tersalin!</span>
                                    </>
                                ) : (
                                    <>
                                        <Copy className="w-3.5 h-3.5" />
                                        <span>Salin Tautan</span>
                                    </>
                                )}
                            </button>

                            <button
                                type="button"
                                onClick={handleShare}
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-white bg-[#1B4332] hover:bg-[#2D6A4F] transition-colors shadow-sm cursor-pointer"
                                title="Bagikan Materi"
                            >
                                <Share2 className="w-3.5 h-3.5" />
                                <span>Bagikan</span>
                            </button>
                        </div>
                    </div>
                </div>

                {/* 2. ARTICLE HEADER */}
                <article className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
                    {/* Breadcrumbs */}
                    <nav aria-label="Breadcrumb" className="mb-6 flex items-center gap-2 text-xs text-stone-500 font-medium">
                        <Link href="/" className="hover:text-[#1E242B] transition-colors">
                            Beranda
                        </Link>
                        <ChevronRight className="w-3.5 h-3.5 text-stone-400" />
                        <Link href="/edukasi" className="hover:text-[#1E242B] transition-colors">
                            Edukasi
                        </Link>
                        <ChevronRight className="w-3.5 h-3.5 text-stone-400" />
                        <span className="text-stone-700 font-semibold truncate max-w-[200px] sm:max-w-xs">
                            {article.title}
                        </span>
                    </nav>

                    <header className="mb-8">
                        {/* Category & Attributes */}
                        <div className="flex flex-wrap items-center gap-2 mb-4">
                            <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#1B4332]/10 text-[#1B4332] border border-[#1B4332]/25 shadow-xs">
                                {getCategoryIcon(article.category)}
                                {article.category_label}
                            </span>

                            <span className="inline-flex items-center gap-1 text-xs font-semibold text-stone-600 px-3 py-1 rounded-full bg-stone-100 border border-stone-200">
                                <Clock className="w-3.5 h-3.5 text-[#F4A261]" />
                                {article.reading_time_minutes} Menit Membaca
                            </span>

                            {article.is_demo && (
                                <span className="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-500/20 text-amber-700 border border-amber-500/30 uppercase">
                                    Simulasi / Contoh
                                </span>
                            )}
                        </div>

                        {/* Title */}
                        <h1 className="font-display font-black text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-[#1E242B] tracking-tight leading-[1.2] mb-6">
                            {article.title}
                        </h1>

                        {/* Summary Lead */}
                        {article.summary && (
                            <p className="text-base sm:text-lg md:text-xl text-stone-700 font-normal leading-relaxed mb-6 border-l-4 border-[#52B788] pl-4 sm:pl-6 bg-white/70 py-3 rounded-r-2xl border-y border-r border-stone-200/70">
                                {article.summary}
                            </p>
                        )}

                        {/* Metadata Strip */}
                        <div className="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-stone-200 text-xs sm:text-sm text-stone-600">
                            <div className="flex flex-wrap items-center gap-4 sm:gap-6">
                                <div className="flex items-center gap-2">
                                    <User className="w-4 h-4 text-[#1B4332]" />
                                    <span>Penulis: <strong className="text-stone-800">{article.author}</strong></span>
                                </div>

                                <div className="flex items-center gap-2">
                                    <Calendar className="w-4 h-4 text-[#52B788]" />
                                    <span>{article.published_date_formatted}</span>
                                </div>
                            </div>

                            {article.source_name && (
                                <div className="flex items-center gap-2">
                                    <span className="text-stone-500">Rujukan:</span>
                                    <strong className="text-stone-800">{article.source_name}</strong>
                                </div>
                            )}
                        </div>
                    </header>

                    {/* 3. HERO PHOTOGRAPHY */}
                    <div className="mb-10 rounded-3xl overflow-hidden border border-stone-200/80 shadow-lg bg-stone-900 relative">
                        <ImageWithFallback
                            src={article.image_url}
                            alt={article.title}
                            fallbackText={article.title}
                            className="w-full h-[280px] sm:h-[400px] md:h-[480px] object-cover object-center"
                        />
                        <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none" />
                        <div className="absolute bottom-4 left-4 right-4 text-xs text-white/90 bg-black/50 backdrop-blur-md px-3.5 py-2 rounded-xl border border-white/10 flex items-center justify-between">
                            <span>{article.image_caption}</span>
                            <span className="text-white/60 text-[11px]">FireAware Environmental Archive</span>
                        </div>
                    </div>

                    {/* 4. MAIN ARTICLE CONTENT & SIDEBAR */}
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-16">
                        {/* Article Main Body */}
                        <div className="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-10 border border-stone-200/80 shadow-xs">
                            {/* Key Learning Takeaway Box */}
                            <div className="p-4 sm:p-5 rounded-2xl mb-8 bg-[#1B4332]/10 border border-[#1B4332]/25 text-[#1E242B] flex items-start gap-3.5">
                                <Sparkles className="w-6 h-6 text-[#1B4332] flex-shrink-0 mt-0.5" />
                                <div className="text-xs sm:text-sm">
                                    <h2 className="font-bold mb-1 text-[#1B4332]">
                                        Tujuan Pembelajaran Modul
                                    </h2>
                                    <p className="text-stone-700 leading-relaxed">
                                        Materi ini disajikan untuk memberikan pemahaman berbasis bukti (*evidence-based*) mengenai dinamika karhutla dan ekologi Kalimantan, serta mendorong langkah pencegahan dini dari tingkat individu hingga komunitas.
                                    </p>
                                </div>
                            </div>

                            {/* Rendered Paragraphs */}
                            <div className="prose prose-stone max-w-none space-y-6 text-sm sm:text-base text-stone-700 leading-relaxed font-normal">
                                {paragraphs.map((para, idx) => {
                                    // Check if paragraph is a section heading (### Heading)
                                    if (para.startsWith('### ')) {
                                        const headingText = para.replace('### ', '');
                                        return (
                                            <h2
                                                key={idx}
                                                className="font-display font-extrabold text-xl sm:text-2xl text-[#1E242B] pt-4 pb-1 border-b border-stone-100 scroll-mt-24"
                                            >
                                                {headingText}
                                            </h2>
                                        );
                                    }

                                    // Check if paragraph contains bullet points
                                    if (para.includes('\n- ') || para.startsWith('- ')) {
                                        const lines = para.split('\n');
                                        return (
                                            <ul key={idx} className="my-3 space-y-2 pl-4 list-disc marker:text-[#52B788]">
                                                {lines.map((line, lidx) => (
                                                    <li key={lidx} className="text-stone-700 leading-relaxed">
                                                        {line.replace(/^- /, '')}
                                                    </li>
                                                ))}
                                            </ul>
                                        );
                                    }

                                    return (
                                        <p key={idx} className="leading-relaxed">
                                            {para}
                                        </p>
                                    );
                                })}
                            </div>

                            {/* Source and Reference Footnote */}
                            {(article.source_name || article.source_url) && (
                                <div className="mt-10 pt-6 border-t border-stone-200">
                                    <div className="bg-stone-50 rounded-2xl p-4 sm:p-5 border border-stone-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div>
                                            <span className="text-[11px] uppercase tracking-wider font-bold text-stone-500 block mb-0.5">
                                                Referensi &amp; Bahan Rujukan
                                            </span>
                                            <p className="text-xs sm:text-sm font-bold text-[#1E242B]">
                                                {article.source_name || 'Kompilasi Studi Lingkungan FireAware'}
                                            </p>
                                        </div>

                                        {article.source_url && (
                                            <a
                                                href={article.source_url}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-stone-300 hover:border-[#1B4332] text-xs font-bold text-[#1B4332] hover:bg-[#1B4332] hover:text-white transition-all shadow-xs cursor-pointer"
                                            >
                                                <span>Buka Tautan Rujukan</span>
                                                <ExternalLink className="w-3.5 h-3.5" />
                                            </a>
                                        )}
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Sidebar: Table of Contents & Quick Actions */}
                        <div className="lg:col-span-4 space-y-6">
                            {/* Table of Contents (Daftar Isi) */}
                            {headings.length > 0 && (
                                <div className="bg-white rounded-3xl p-5 sm:p-6 border border-stone-200/80 shadow-xs sticky top-28">
                                    <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-stone-500 mb-3 pb-2 border-b border-stone-100">
                                        <ListFilter className="w-4 h-4 text-[#1B4332]" />
                                        <span>Daftar Isi Materi</span>
                                    </div>

                                    <nav className="space-y-2 text-xs">
                                        {headings.map((h, i) => (
                                            <a
                                                key={i}
                                                href={`#${h.id}`}
                                                className="block py-1 px-2.5 rounded-lg text-stone-600 hover:text-[#1B4332] hover:bg-stone-50 transition-colors font-medium"
                                            >
                                                {h.text}
                                            </a>
                                        ))}
                                    </nav>
                                </div>
                            )}

                            {/* Share and Action Card */}
                            <div className="bg-white rounded-3xl p-5 sm:p-6 border border-stone-200/80 shadow-xs">
                                <h3 className="text-xs font-bold uppercase tracking-wider text-stone-500 mb-4 pb-2 border-b border-stone-100">
                                    Bagikan &amp; Simpan
                                </h3>

                                <div className="space-y-3">
                                    <button
                                        type="button"
                                        onClick={handleShare}
                                        className="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#1B4332] text-white text-xs font-bold hover:bg-[#2D6A4F] transition-colors shadow-sm cursor-pointer"
                                    >
                                        <Share2 className="w-4 h-4" />
                                        <span>Bagikan Artikel Ini</span>
                                    </button>

                                    <button
                                        type="button"
                                        onClick={handleCopyLink}
                                        className="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-stone-100 text-stone-700 text-xs font-bold hover:bg-stone-200 transition-colors cursor-pointer"
                                    >
                                        {copied ? (
                                            <>
                                                <Check className="w-4 h-4 text-[#2A9D8F]" />
                                                <span className="text-[#2A9D8F]">Tautan Tersalin!</span>
                                            </>
                                        ) : (
                                            <>
                                                <Copy className="w-4 h-4" />
                                                <span>Salin Tautan Artikel</span>
                                            </>
                                        )}
                                    </button>

                                    <button
                                        type="button"
                                        onClick={() => window.print()}
                                        className="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-stone-500 hover:text-stone-800 transition-colors cursor-pointer"
                                    >
                                        <Printer className="w-3.5 h-3.5" />
                                        <span>Cetak Materi Edukasi</span>
                                    </button>
                                </div>
                            </div>

                            {/* Cross-Link Card to Hotspot Map & Air Quality */}
                            <div className="bg-[#1C2526] text-white rounded-3xl p-5 sm:p-6 border border-white/10 shadow-lg">
                                <h3 className="text-xs font-bold uppercase tracking-wider text-[#F4A261] mb-2">
                                    Pemantauan Lapangan
                                </h3>
                                <p className="text-xs text-stone-300 leading-relaxed mb-4">
                                    Lihat bagaimana konsep-konsep di artikel ini berinteraksi langsung dengan data satelit dan kualitas udara Kalimantan secara real-time.
                                </p>
                                <div className="space-y-2">
                                    <Link
                                        href="/peta-hotspot"
                                        className="flex items-center justify-between p-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-xs font-bold text-white transition-colors"
                                    >
                                        <span>Peta Titik Panas FIRMS</span>
                                        <ArrowUpRight className="w-4 h-4 text-[#F4A261]" />
                                    </Link>
                                    <Link
                                        href="/kualitas-udara"
                                        className="flex items-center justify-between p-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-xs font-bold text-white transition-colors"
                                    >
                                        <span>Sensor Kualitas Udara (PM2.5)</span>
                                        <ArrowUpRight className="w-4 h-4 text-[#52B788]" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* 5. RELATED ARTICLES SECTION */}
                    {relatedArticles.length > 0 && (
                        <section className="pt-10 border-t border-stone-200 mb-12">
                            <div className="flex items-center justify-between mb-6">
                                <div>
                                    <h2 className="font-display font-extrabold text-xl sm:text-2xl text-[#1E242B] tracking-tight">
                                        Materi Edukasi Terkait
                                    </h2>
                                    <p className="text-xs text-stone-500 mt-0.5">
                                        Perdalam wawasan lingkungan dengan topik serupa
                                    </p>
                                </div>
                                <Link
                                    href="/edukasi"
                                    className="text-xs font-bold text-[#1B4332] hover:underline flex items-center gap-1"
                                >
                                    <span>Lihat Semua</span>
                                    <ArrowRight className="w-3.5 h-3.5" />
                                </Link>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {relatedArticles.map((item) => (
                                    <article
                                        key={item.id}
                                        className="group bg-white rounded-3xl overflow-hidden border border-stone-200/80 shadow-xs hover:shadow-lg transition-all flex flex-col justify-between"
                                    >
                                        <div>
                                            <div className="relative h-40 overflow-hidden bg-stone-900">
                                                <ImageWithFallback
                                                    src={item.image_url}
                                                    alt={item.title}
                                                    fallbackText={item.title}
                                                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                />
                                                <div className="absolute top-3 left-3">
                                                    <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white text-[#1B4332] shadow-xs">
                                                        {item.category_label}
                                                    </span>
                                                </div>
                                            </div>

                                            <div className="p-5">
                                                <div className="text-[11px] text-stone-500 mb-1.5 flex items-center gap-2">
                                                    <Clock className="w-3 h-3 text-[#F4A261]" />
                                                    <span>{item.reading_time_minutes} mnt baca</span>
                                                </div>
                                                <h3 className="font-display font-bold text-sm text-[#1E242B] group-hover:text-[#1B4332] transition-colors line-clamp-2 leading-snug mb-2">
                                                    <Link href={`/edukasi/${item.slug}`}>
                                                        {item.title}
                                                    </Link>
                                                </h3>
                                                <p className="text-xs text-stone-600 line-clamp-2 leading-relaxed">
                                                    {item.summary}
                                                </p>
                                            </div>
                                        </div>

                                        <div className="px-5 pb-5 pt-2 border-t border-stone-100 flex items-center justify-between text-[11px]">
                                            <span className="text-stone-500 truncate max-w-[65%]">
                                                {item.author}
                                            </span>
                                            <Link
                                                href={`/edukasi/${item.slug}`}
                                                className="font-bold text-[#1B4332] group-hover:translate-x-1 transition-transform inline-flex items-center gap-1"
                                            >
                                                <span>Pelajari</span>
                                                <ArrowRight className="w-3 h-3" />
                                            </Link>
                                        </div>
                                    </article>
                                ))}
                            </div>
                        </section>
                    )}
                </article>
            </div>
        </PublicLayout>
    );
}

