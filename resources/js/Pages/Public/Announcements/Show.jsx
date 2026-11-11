import React, { useState } from 'react';
import { Link } from '@inertiajs/react';
import { motion } from 'motion/react';
import {
    ChevronRight,
    ArrowLeft,
    Calendar,
    Clock,
    MapPin,
    Shield,
    ExternalLink,
    Share2,
    Copy,
    Check,
    AlertTriangle,
    ShieldAlert,
    Info,
    Printer,
    ArrowUpRight,
} from 'lucide-react';
import { PublicLayout } from '@/Layouts/PublicLayout';

export default function AnnouncementShow({
    announcement,
    recentAnnouncements = [],
    back_label = 'Kembali ke Daftar Pengumuman',
}) {
    const [copied, setCopied] = useState(false);
    const [shareOpen, setShareOpen] = useState(false);

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
                    title: announcement.title,
                    text: announcement.summary,
                    url: window.location.href,
                });
            } catch (err) {
                // User cancelled or unsupported
            }
        } else {
            handleCopyLink();
        }
    };

    const paragraphs = announcement.paragraphs || (announcement.body ? announcement.body.split(/\n\s*\n/) : []);

    return (
        <PublicLayout title={`${announcement.title} — Informasi Kewaspadaan`}>
            <div className="min-h-screen bg-[#F8F9FA] text-[#1E242B] pt-24 pb-20 selection:bg-[#E05A2B]/20 selection:text-[#E05A2B]">
                {/* 1. TOP NAV & BREADCRUMBS */}
                <div className="bg-white border-b border-stone-200/80 sticky top-16 z-30 shadow-xs">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
                        <Link
                            href="/pengumuman"
                            className="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-stone-700 hover:text-[#E05A2B] transition-colors group"
                        >
                            <ArrowLeft className="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
                            <span>{back_label}</span>
                        </Link>

                        <div className="flex items-center gap-2">
                            <button
                                type="button"
                                onClick={handleCopyLink}
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-stone-600 bg-stone-100 hover:bg-stone-200 transition-colors"
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
                                className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-white bg-[#1E242B] hover:bg-[#E05A2B] transition-colors shadow-sm"
                                title="Bagikan Informasi"
                            >
                                <Share2 className="w-3.5 h-3.5" />
                                <span>Bagikan</span>
                            </button>
                        </div>
                    </div>
                </div>

                {/* 2. ARTICLE HEADER & EDITORIAL HERO */}
                <article className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
                    {/* Breadcrumbs */}
                    <nav aria-label="Breadcrumb" className="mb-6 flex items-center gap-2 text-xs text-stone-500 font-medium">
                        <Link href="/" className="hover:text-[#1E242B] transition-colors">
                            Beranda
                        </Link>
                        <ChevronRight className="w-3.5 h-3.5 text-stone-400" />
                        <Link href="/pengumuman" className="hover:text-[#1E242B] transition-colors">
                            Informasi Kewaspadaan
                        </Link>
                        <ChevronRight className="w-3.5 h-3.5 text-stone-400" />
                        <span className="text-stone-700 font-semibold truncate max-w-[200px] sm:max-w-xs">
                            {announcement.title}
                        </span>
                    </nav>

                    {/* Metadata Header Badges */}
                    <header className="mb-8">
                        <div className="flex flex-wrap items-center gap-2 mb-4">
                            {/* Urgency Level Badge */}
                            <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border ${announcement.level_badge} bg-white shadow-xs`}>
                                <span className={`w-2 h-2 rounded-full ${announcement.level_dot}`} />
                                Status: {announcement.level_label}
                            </span>

                            {/* Province / Regional Badge */}
                            {announcement.province ? (
                                <span className="inline-flex items-center gap-1 text-xs font-semibold text-stone-700 px-3 py-1 rounded-full bg-stone-100 border border-stone-200">
                                    <MapPin className="w-3.5 h-3.5 text-[#E05A2B]" />
                                    Wilayah: {announcement.province.name}
                                </span>
                            ) : (
                                <span className="inline-flex items-center gap-1 text-xs font-semibold text-stone-700 px-3 py-1 rounded-full bg-stone-100 border border-stone-200">
                                    <MapPin className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                    Cakupan: Seluruh Kalimantan
                                </span>
                            )}

                            {/* Verified Source Badge */}
                            <span className="inline-flex items-center gap-1 text-xs font-semibold text-[#1B4332] px-3 py-1 rounded-full bg-[#52B788]/15 border border-[#52B788]/30">
                                <Shield className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                Sumber Terverifikasi
                            </span>

                            {announcement.is_demo && (
                                <span className="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-500/20 text-amber-700 border border-amber-500/30 uppercase">
                                    Simulasi Demo
                                </span>
                            )}
                        </div>

                        {/* Title */}
                        <h1 className="font-display font-black text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-[#1E242B] tracking-tight leading-[1.2] mb-6">
                            {announcement.title}
                        </h1>

                        {/* Summary / Lead Paragraph */}
                        {announcement.summary && (
                            <p className="text-base sm:text-lg md:text-xl text-stone-600 font-normal leading-relaxed mb-6 border-l-4 border-[#E05A2B] pl-4 sm:pl-6 bg-stone-50 py-3 rounded-r-2xl">
                                {announcement.summary}
                            </p>
                        )}

                        {/* Metadata Strip */}
                        <div className="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-stone-200 text-xs sm:text-sm text-stone-600">
                            <div className="flex flex-wrap items-center gap-4 sm:gap-6">
                                <div className="flex items-center gap-2">
                                    <Calendar className="w-4 h-4 text-[#E05A2B]" />
                                    <span>{announcement.published_date_formatted}</span>
                                    <span className="text-stone-400">({announcement.published_time_formatted})</span>
                                </div>

                                <div className="flex items-center gap-2">
                                    <Clock className="w-4 h-4 text-stone-400" />
                                    <span>Estimasi {announcement.reading_time} menit baca</span>
                                </div>
                            </div>

                            <div className="flex items-center gap-2">
                                <span className="text-stone-500">Penerbit:</span>
                                <strong className="text-[#1E242B] font-bold">{announcement.source_name}</strong>
                            </div>
                        </div>
                    </header>

                    {/* 3. HERO PHOTOGRAPHY */}
                    <div className="mb-10 rounded-3xl overflow-hidden border border-stone-200/80 shadow-lg bg-stone-900 relative">
                        <img
                            src={announcement.image_url}
                            alt={announcement.title}
                            className="w-full h-[280px] sm:h-[400px] md:h-[480px] object-cover object-center"
                        />
                        <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent" />
                        <div className="absolute bottom-4 left-4 right-4 text-xs text-white/90 bg-black/50 backdrop-blur-md px-3.5 py-2 rounded-xl border border-white/10 flex items-center justify-between">
                            <span>{announcement.image_caption}</span>
                            <span className="text-white/60 text-[11px]">Dokumentasi FireAware Kalimantan</span>
                        </div>
                    </div>

                    {/* 4. MAIN CONTENT & SIDEBAR */}
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-16">
                        {/* Article Body */}
                        <div className="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-10 border border-stone-200/80 shadow-xs">
                            {/* Urgent Alert Callout */}
                            <div className={`p-4 sm:p-5 rounded-2xl mb-8 border flex items-start gap-3.5 ${
                                announcement.level === 3
                                    ? 'bg-[#E63946]/5 border-[#E63946]/30 text-[#1E242B]'
                                    : announcement.level === 2
                                    ? 'bg-[#F4A261]/10 border-[#F4A261]/30 text-[#1E242B]'
                                    : 'bg-[#2A9D8F]/10 border-[#2A9D8F]/30 text-[#1E242B]'
                            }`}>
                                {announcement.level === 3 ? (
                                    <ShieldAlert className="w-6 h-6 text-[#E63946] flex-shrink-0 mt-0.5" />
                                ) : announcement.level === 2 ? (
                                    <AlertTriangle className="w-6 h-6 text-[#F4A261] flex-shrink-0 mt-0.5" />
                                ) : (
                                    <Info className="w-6 h-6 text-[#2A9D8F] flex-shrink-0 mt-0.5" />
                                )}
                                <div className="text-xs sm:text-sm">
                                    <h2 className="font-bold mb-1">
                                        Petunjuk Otoritas Bagi Masyarakat ({announcement.level_label})
                                    </h2>
                                    <p className="text-stone-600 leading-relaxed">
                                        Informasi ini diterbitkan untuk kesiapsiagaan dan langkah pencegahan. Warga yang berada di wilayah terindikasi diimbau mematuhi arahan resmi dari Satgas Penanggulangan Karhutla dan BPBD setempat.
                                    </p>
                                </div>
                            </div>

                            {/* Rendered Paragraphs */}
                            <div className="prose prose-stone max-w-none space-y-5 text-sm sm:text-base text-stone-700 leading-relaxed font-normal">
                                {paragraphs.map((para, idx) => {
                                    // Check if paragraph looks like a list
                                    if (para.includes('\n1.') || para.startsWith('1.')) {
                                        const lines = para.split('\n');
                                        return (
                                            <div key={idx} className="my-4 bg-stone-50 p-4 sm:p-5 rounded-2xl border border-stone-200/70">
                                                {lines.map((line, lidx) => (
                                                    <p key={lidx} className="mb-2 last:mb-0 text-stone-700 font-medium">
                                                        {line}
                                                    </p>
                                                ))}
                                            </div>
                                        );
                                    }

                                    return (
                                        <p key={idx} className="leading-relaxed">
                                            {para}
                                        </p>
                                    );
                                })}
                            </div>

                            {/* Official Source Link Callout */}
                            <div className="mt-10 pt-6 border-t border-stone-200">
                                <div className="bg-stone-50 rounded-2xl p-4 sm:p-5 border border-stone-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div>
                                        <span className="text-[11px] uppercase tracking-wider font-bold text-stone-500 block mb-0.5">
                                            Rujukan Sumber Resmi
                                        </span>
                                        <p className="text-xs sm:text-sm font-bold text-[#1E242B]">
                                            {announcement.source_name}
                                        </p>
                                    </div>

                                    {announcement.source_url ? (
                                        <a
                                            href={announcement.source_url}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-stone-300 hover:border-[#E05A2B] text-xs font-bold text-[#E05A2B] hover:bg-[#E05A2B] hover:text-white transition-all shadow-xs"
                                        >
                                            <span>Buka Sumber Asli</span>
                                            <ExternalLink className="w-3.5 h-3.5" />
                                        </a>
                                    ) : (
                                        <span className="text-xs text-stone-500 italic">
                                            Rilis terverifikasi melalui kanal posko daerah
                                        </span>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Sidebar: Metadata & Actions */}
                        <div className="lg:col-span-4 space-y-6">
                            {/* Fast Action Card */}
                            <div className="bg-white rounded-3xl p-5 sm:p-6 border border-stone-200/80 shadow-xs">
                                <h2 className="text-xs font-bold uppercase tracking-wider text-stone-500 mb-4 pb-2 border-b border-stone-100">
                                    Aksi &amp; Bagikan
                                </h2>

                                <div className="space-y-3">
                                    <button
                                        type="button"
                                        onClick={handleShare}
                                        className="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#1E242B] text-white text-xs font-bold hover:bg-[#E05A2B] transition-colors shadow-sm"
                                    >
                                        <Share2 className="w-4 h-4" />
                                        <span>Bagikan Informasi Ini</span>
                                    </button>

                                    <button
                                        type="button"
                                        onClick={handleCopyLink}
                                        className="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-stone-100 text-stone-700 text-xs font-bold hover:bg-stone-200 transition-colors"
                                    >
                                        {copied ? (
                                            <>
                                                <Check className="w-4 h-4 text-[#2A9D8F]" />
                                                <span className="text-[#2A9D8F]">Tautan Tersalin!</span>
                                            </>
                                        ) : (
                                            <>
                                                <Copy className="w-4 h-4" />
                                                <span>Salin Tautan Rilis</span>
                                            </>
                                        )}
                                    </button>

                                    <button
                                        type="button"
                                        onClick={() => window.print()}
                                        className="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-stone-500 hover:text-stone-800 transition-colors"
                                    >
                                        <Printer className="w-3.5 h-3.5" />
                                        <span>Cetak Dokumen</span>
                                    </button>
                                </div>
                            </div>

                            {/* Verification Summary Card */}
                            <div className="bg-gradient-to-br from-[#1E242B] to-[#12161A] text-white rounded-3xl p-5 sm:p-6 border border-white/10 shadow-lg">
                                <div className="flex items-center gap-2 mb-3">
                                    <Shield className="w-5 h-5 text-[#52B788]" />
                                    <h2 className="text-xs font-bold uppercase tracking-wider text-[#F4A261]">
                                        Integritas Informasi
                                    </h2>
                                </div>
                                <p className="text-xs text-[#F8F9FA]/80 leading-relaxed mb-4">
                                    Platform FireAware Kalimantan menerapkan prinsip transparansi: seluruh pengumuman diterbitkan sesuai redaksi otoritas berwenang tanpa mengubah esensi atau mengarang tingkat bahaya.
                                </p>
                                <div className="pt-3 border-t border-white/10 text-[11px] text-[#F8F9FA]/60">
                                    Terbit: {announcement.published_date_formatted}
                                </div>
                            </div>

                            {/* Related Links to Hotspot Map & Air Quality */}
                            <div className="bg-stone-100/80 rounded-3xl p-5 sm:p-6 border border-stone-200">
                                <h2 className="text-xs font-bold uppercase tracking-wider text-stone-600 mb-3">
                                    Koneksi Data Lingkungan
                                </h2>
                                <div className="space-y-2">
                                    <Link
                                        href="/peta-hotspot"
                                        className="flex items-center justify-between p-2.5 rounded-xl bg-white hover:bg-stone-50 border border-stone-200 text-xs font-bold text-stone-700 hover:text-[#E05A2B] transition-colors"
                                    >
                                        <span>Peta Titik Panas Satelit</span>
                                        <ArrowUpRight className="w-4 h-4 text-stone-400" />
                                    </Link>
                                    <Link
                                        href="/kualitas-udara"
                                        className="flex items-center justify-between p-2.5 rounded-xl bg-white hover:bg-stone-50 border border-stone-200 text-xs font-bold text-stone-700 hover:text-[#E05A2B] transition-colors"
                                    >
                                        <span>Pemantauan Kualitas Udara</span>
                                        <ArrowUpRight className="w-4 h-4 text-stone-400" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* 5. RECENT / RELATED ANNOUNCEMENTS */}
                    {recentAnnouncements.length > 0 && (
                        <section className="pt-10 border-t border-stone-200 mb-12">
                            <div className="flex items-center justify-between mb-6">
                                <div>
                                    <h2 className="font-display font-extrabold text-xl sm:text-2xl text-[#1E242B] tracking-tight">
                                        Informasi Kewaspadaan Lainnya
                                    </h2>
                                    <p className="text-xs text-stone-500 mt-0.5">
                                        Rilis dan perkembangan situasi kebakaran hutan terkini
                                    </p>
                                </div>
                                <Link
                                    href="/pengumuman"
                                    className="text-xs font-bold text-[#E05A2B] hover:underline flex items-center gap-1"
                                >
                                    <span>Lihat Semua</span>
                                    <ArrowRight className="w-3.5 h-3.5" />
                                </Link>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {recentAnnouncements.map((item) => (
                                    <article
                                        key={item.id}
                                        className="group bg-white rounded-3xl overflow-hidden border border-stone-200/80 shadow-xs hover:shadow-lg transition-all flex flex-col justify-between"
                                    >
                                        <div>
                                            <div className="relative h-40 overflow-hidden bg-stone-900">
                                                <img
                                                    src={item.image_url}
                                                    alt={item.title}
                                                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                />
                                                <div className="absolute top-3 left-3">
                                                    <span className={`inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold border ${item.level_badge} bg-white shadow-xs`}>
                                                        <span className={`w-1.5 h-1.5 rounded-full ${item.level_dot}`} />
                                                        {item.level_label}
                                                    </span>
                                                </div>
                                            </div>

                                            <div className="p-5">
                                                <div className="text-[11px] text-stone-500 mb-1.5 flex items-center gap-2">
                                                    <Calendar className="w-3 h-3 text-[#E05A2B]" />
                                                    <span>{item.published_date_formatted}</span>
                                                </div>
                                                <h3 className="font-display font-bold text-sm text-[#1E242B] group-hover:text-[#E05A2B] transition-colors line-clamp-2 leading-snug mb-2">
                                                    <Link href={`/pengumuman/${item.slug}`}>
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
                                                {item.source_name}
                                            </span>
                                            <Link
                                                href={`/pengumuman/${item.slug}`}
                                                className="font-bold text-[#E05A2B] group-hover:translate-x-1 transition-transform inline-flex items-center gap-1"
                                            >
                                                <span>Baca</span>
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

