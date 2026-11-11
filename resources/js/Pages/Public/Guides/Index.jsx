import React, { useState, useEffect } from 'react';
import { Link, router } from '@inertiajs/react';
import { motion } from 'motion/react';
import {
    Shield,
    ShieldCheck,
    Search,
    Calendar,
    Clock,
    ArrowRight,
    RotateCcw,
    Sparkles,
    ChevronRight,
    Wind,
    CloudFog,
    HeartPulse,
    AlertTriangle,
    CheckCircle2,
} from 'lucide-react';
import { PublicLayout } from '@/Layouts/PublicLayout';
import { ImageWithFallback } from '@/Components/ImageWithFallback';
import { SafetyInteractiveSection } from '@/Components/Guides/SafetyInteractiveSection';

export default function GuidesIndex({
    guides,
    featuredGuide,
    categories = [],
    activeCategory = null,
    search = '',
    sort = 'terbaru',
    categoryCounts = {},
    totalPublishedCount = 0,
    empty_message = 'Tidak Ada Panduan yang Ditemukan',
}) {
    const [searchQuery, setSearchQuery] = useState(search || '');
    const [selectedSort, setSelectedSort] = useState(sort || 'terbaru');
    const [activeTab, setActiveTab] = useState(activeCategory || 'semua');
    const [isSearching, setIsSearching] = useState(false);

    useEffect(() => {
        setSearchQuery(search || '');
        setSelectedSort(sort || 'terbaru');
        setActiveTab(activeCategory || 'semua');
    }, [search, activeCategory, sort]);

    const applyFilters = (overrides = {}) => {
        setIsSearching(true);
        const params = {
            q: overrides.q !== undefined ? overrides.q : searchQuery,
            kategori: overrides.kategori !== undefined ? overrides.kategori : activeTab,
            sort: overrides.sort !== undefined ? overrides.sort : selectedSort,
        };

        if (!params.q || params.q.trim() === '') delete params.q;
        if (!params.kategori || params.kategori === 'semua') delete params.kategori;
        if (!params.sort || params.sort === 'terbaru') delete params.sort;

        router.get('/panduan', params, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setIsSearching(false),
        });
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        applyFilters({ q: searchQuery });
    };

    const handleCategoryClick = (key) => {
        setActiveTab(key);
        applyFilters({ kategori: key });
    };

    const handleSortChange = (e) => {
        const val = e.target.value;
        setSelectedSort(val);
        applyFilters({ sort: val });
    };

    const handleResetFilter = () => {
        setSearchQuery('');
        setSelectedSort('terbaru');
        setActiveTab('semua');
        setIsSearching(true);
        router.get('/panduan', {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setIsSearching(false),
        });
    };

    const hasActiveFilters = Boolean(
        searchQuery ||
        (activeTab && activeTab !== 'semua') ||
        (selectedSort && selectedSort !== 'terbaru')
    );

    const items = guides?.data || [];

    const getCategoryIcon = (key) => {
        switch (key) {
            case 'menghadapi-asap':
                return <CloudFog className="w-3.5 h-3.5 text-[#E05A2B]" />;
            case 'mengurangi-paparan-asap':
                return <ShieldCheck className="w-3.5 h-3.5 text-[#52B788]" />;
            case 'persiapan-saat-kualitas-udara-memburuk':
                return <HeartPulse className="w-3.5 h-3.5 text-[#E63946]" />;
            case 'tindakan-ketika-melihat-indikasi-kebakaran':
                return <AlertTriangle className="w-3.5 h-3.5 text-[#F4A261]" />;
            case 'cara-memperoleh-informasi-resmi':
                return <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />;
            default:
                return <Shield className="w-3.5 h-3.5 text-[#52B788]" />;
        }
    };

    return (
        <PublicLayout title="Panduan Keselamatan Asap & Mitigasi Karhutla Kalimantan">
            {/* 1. HERO SECTION (Deep Pine & Fotografi Lingkungan, Menempel Langsung di Bawah Navbar) */}
            <section className="relative overflow-hidden bg-gradient-to-b from-[#1C2526] via-[#1B4332] to-[#121E17] text-white pt-28 sm:pt-32 pb-16 sm:pb-20 border-b border-white/10">
                <div className="absolute top-0 right-1/4 w-96 h-96 bg-[#52B788]/15 rounded-full blur-3xl pointer-events-none" />
                <div className="absolute bottom-0 left-10 w-80 h-80 bg-[#E05A2B]/10 rounded-full blur-3xl pointer-events-none" />

                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    {/* Breadcrumbs */}
                    <nav aria-label="Breadcrumb" className="mb-5 flex items-center gap-2 text-xs text-[#F8F9FA]/70 font-medium">
                        <Link href="/" className="hover:text-white transition-colors">
                            Beranda
                        </Link>
                        <ChevronRight className="w-3.5 h-3.5 text-white/40" />
                        <span className="text-[#52B788] font-semibold">Panduan Keselamatan</span>
                    </nav>

                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <div className="lg:col-span-8">
                            <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.08] border border-white/15 backdrop-blur-md text-xs font-semibold text-[#52B788] mb-3.5 shadow-sm">
                                <Shield className="w-3.5 h-3.5 text-[#52B788]" />
                                <span className="tracking-widest uppercase font-bold text-[11px]">Public Safety &amp; Preparedness</span>
                                <span className="w-1 h-1 rounded-full bg-white/30" />
                                <span className="text-white/80">Kalimantan</span>
                            </div>

                            <h1 className="font-display font-black text-2xl sm:text-4xl md:text-5xl text-white tracking-tight leading-[1.18] mb-3.5">
                                Kenali Risiko, Lindungi Diri, <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#52B788] via-[#F4A261] to-[#F8F9FA]">Jaga Kalimantan.</span>
                            </h1>

                            <p className="text-xs sm:text-sm md:text-base text-[#F8F9FA]/85 font-normal leading-relaxed max-w-2xl mb-6">
                                Pelajari langkah keselamatan, pencegahan, dan mitigasi kebakaran hutan dan lahan untuk melindungi diri, keluarga, serta lingkungan sekitar.
                            </p>

                            <div className="flex flex-wrap items-center gap-3">
                                <a
                                    href="#katalog-panduan"
                                    className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#E05A2B] to-[#D94E34] text-white font-bold text-xs uppercase tracking-wider hover:brightness-110 transition-all shadow-md active:scale-95 cursor-pointer"
                                >
                                    <span>Jelajahi Panduan</span>
                                    <ArrowRight className="w-4 h-4" />
                                </a>

                                <a
                                    href="#mitigasi-asap"
                                    className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/15 text-white border border-white/20 font-semibold text-xs tracking-wider transition-all backdrop-blur-md cursor-pointer"
                                >
                                    <Wind className="w-4 h-4 text-[#F4A261]" />
                                    <span>Langkah Menghadapi Kabut Asap</span>
                                </a>
                            </div>
                        </div>

                        <div className="lg:col-span-4">
                            <div className="p-4 sm:p-5 rounded-2xl bg-white/[0.06] border border-white/10 backdrop-blur-md shadow-lg">
                                <div className="flex items-center justify-between text-xs text-[#F8F9FA]/80 mb-2 font-medium">
                                    <span className="flex items-center gap-1.5 font-bold">
                                        <ShieldCheck className="w-4 h-4 text-[#52B788]" />
                                        Pusat Siaga Bencana Asap
                                    </span>
                                    <span className="font-bold text-white bg-white/15 px-2.5 py-0.5 rounded-full">
                                        {totalPublishedCount} Protokol
                                    </span>
                                </div>
                                <p className="text-[11px] text-[#F8F9FA]/70 leading-relaxed mb-3">
                                    Pedoman perlindungan pernapasan dan protokol kesiapsiagaan darurat bersumber resmi dari Kemenkes RI, BNPB, BMKG, dan KLHK.
                                </p>
                                <div className="pt-2.5 border-t border-white/10 flex items-center justify-between text-[10px] text-[#F8F9FA]/60">
                                    <span>Bebas Akses Masyarakat</span>
                                    <span className="text-[#52B788] font-bold">Terverifikasi Otoritas</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* Content Area dengan Latar Soft Sand */}
            <div className="min-h-screen bg-[#F6F5F2] text-[#1E242B] pb-20 selection:bg-[#52B788]/20 selection:text-[#1B4332]">
                {/* 2. FEATURED SAFETY GUIDE (Panduan Unggulan Editorial) */}
                {featuredGuide && !hasActiveFilters && (
                    <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 sm:-mt-10 relative z-20 mb-12">
                        <div className="relative overflow-hidden rounded-3xl bg-[#1C2526] text-white shadow-xl border border-white/15 group">
                            <div className="absolute inset-0 z-0">
                                <ImageWithFallback
                                    src={featuredGuide.thumbnail_image}
                                    alt={featuredGuide.thumbnail_alt}
                                    fallbackText={featuredGuide.title}
                                    className="w-full h-full object-cover object-center brightness-[0.38] contrast-105 group-hover:scale-105 transition-transform duration-700 ease-out"
                                />
                                <div className="absolute inset-0 bg-gradient-to-t from-[#1C2526] via-[#1C2526]/85 to-transparent" />
                                <div className="absolute inset-0 bg-gradient-to-r from-[#1C2526] via-[#1C2526]/90 to-transparent" />
                            </div>

                            <div className="relative z-10 p-6 sm:p-8 md:p-10 lg:p-12 max-w-4xl">
                                <div className="flex flex-wrap items-center gap-2.5 mb-3.5">
                                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#E05A2B] text-white text-xs font-black uppercase tracking-wider shadow-sm">
                                        <Sparkles className="w-3.5 h-3.5" /> Panduan Prioritas Utama
                                    </span>

                                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-[#1B4332]/85 text-[#52B788] border border-[#52B788]/40 backdrop-blur-md">
                                        {getCategoryIcon(featuredGuide.category)}
                                        {featuredGuide.category_label}
                                    </span>

                                    <span className="text-xs text-white/75 px-2.5 py-1 rounded-full bg-white/10 backdrop-blur-md flex items-center gap-1">
                                        <Clock className="w-3 h-3 text-[#F4A261]" />
                                        {featuredGuide.reading_time_minutes} menit baca
                                    </span>
                                </div>

                                <h2 className="font-display font-extrabold text-xl sm:text-2xl md:text-3xl lg:text-4xl text-white tracking-tight leading-[1.22] mb-3.5">
                                    <Link
                                        href={`/panduan/${featuredGuide.slug}`}
                                        className="hover:text-[#F4A261] transition-colors"
                                    >
                                        {featuredGuide.title}
                                    </Link>
                                </h2>

                                <p className="text-xs sm:text-sm md:text-base text-[#F8F9FA]/90 leading-relaxed mb-5 font-normal line-clamp-3">
                                    {featuredGuide.summary}
                                </p>

                                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-white/15 text-xs text-[#F8F9FA]/75">
                                    <div className="flex flex-wrap items-center gap-4">
                                        <span className="inline-flex items-center gap-1.5 font-medium text-white/95">
                                            <ShieldCheck className="w-3.5 h-3.5 text-[#52B788]" />
                                            Rujukan: {featuredGuide.source_name}
                                        </span>
                                        <span className="inline-flex items-center gap-1.5">
                                            <Calendar className="w-3.5 h-3.5 text-[#F4A261]" />
                                            {featuredGuide.published_date_formatted}
                                        </span>
                                    </div>

                                    <Link
                                        href={`/panduan/${featuredGuide.slug}`}
                                        className="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#52B788] to-[#2D6A4F] text-white font-bold text-xs uppercase tracking-wider hover:brightness-110 transition-all shadow-md active:scale-95 self-start sm:self-auto cursor-pointer"
                                    >
                                        <span>Baca Panduan Lengkap</span>
                                        <ArrowRight className="w-4 h-4" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </section>
                )}

                {/* 3. QUICK GUIDE NAVIGATION & SEARCH CONTROLS */}
                <section id="katalog-panduan" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-10 scroll-mt-24">
                    <div className="bg-white rounded-3xl p-4 sm:p-5 border border-stone-200/80 shadow-xs">
                        <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            {/* Segmented Category Buttons */}
                            <div className="flex items-center gap-1.5 overflow-x-auto pb-2 lg:pb-0 scrollbar-none">
                                {categories.map((cat) => {
                                    const isActive = activeTab === cat.key;
                                    return (
                                        <button
                                            key={cat.key}
                                            type="button"
                                            onClick={() => handleCategoryClick(cat.key)}
                                            className={`px-3.5 py-2 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 flex-shrink-0 cursor-pointer ${
                                                isActive
                                                    ? 'bg-[#1B4332] text-white shadow-sm ring-1 ring-[#1B4332]'
                                                    : 'bg-stone-100 hover:bg-stone-200/80 text-stone-700'
                                            }`}
                                        >
                                            {getCategoryIcon(cat.key)}
                                            <span>{cat.name}</span>
                                            <span
                                                className={`text-[10px] px-2 py-0.5 rounded-full font-bold ${
                                                    isActive
                                                        ? 'bg-white/20 text-white'
                                                        : 'bg-stone-200 text-stone-600'
                                                }`}
                                            >
                                                {cat.count}
                                            </span>
                                        </button>
                                    );
                                })}
                            </div>

                            {/* Search & Sort Input Controls */}
                            <div className="flex items-center gap-2 flex-shrink-0">
                                <form onSubmit={handleSearchSubmit} className="relative">
                                    <input
                                        type="text"
                                        value={searchQuery}
                                        onChange={(e) => setSearchQuery(e.target.value)}
                                        placeholder="Cari kata kunci panduan..."
                                        className="w-48 sm:w-64 pl-8 pr-3 py-2 text-xs rounded-xl bg-stone-50 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#1B4332] text-[#1E242B]"
                                    />
                                    <Search className="w-3.5 h-3.5 text-stone-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" />
                                </form>

                                <div className="relative">
                                    <select
                                        value={selectedSort}
                                        onChange={handleSortChange}
                                        className="text-xs bg-stone-50 border border-stone-200 rounded-xl px-3 py-2 text-stone-700 focus:outline-none focus:ring-2 focus:ring-[#1B4332] cursor-pointer"
                                    >
                                        <option value="terbaru">Terbaru</option>
                                        <option value="populer">Waktu Baca Terpanjang</option>
                                        <option value="terlama">Terlama</option>
                                    </select>
                                </div>

                                {hasActiveFilters && (
                                    <button
                                        type="button"
                                        onClick={handleResetFilter}
                                        className="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-stone-600 bg-stone-100 hover:bg-stone-200 transition-colors cursor-pointer"
                                        title="Reset Filter"
                                    >
                                        <RotateCcw className="w-3.5 h-3.5" />
                                        <span>Reset</span>
                                    </button>
                                )}
                            </div>
                        </div>
                    </div>
                </section>

                {/* 4. SAFETY GUIDE COLLECTION (Katalog Panduan) */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20">
                    <div className="flex items-center justify-between mb-6">
                        <div className="flex items-center gap-2">
                            <ShieldCheck className="w-5 h-5 text-[#1B4332]" />
                            <h2 className="font-display font-black text-xl sm:text-2xl text-[#1E242B] tracking-tight">
                                {activeCategory
                                    ? categories.find((c) => c.key === activeCategory)?.name || 'Katalog Panduan'
                                    : 'Katalog Protokol Keselamatan'}
                            </h2>
                            <span className="text-xs font-bold text-stone-500 bg-stone-100 px-2.5 py-0.5 rounded-full">
                                {guides?.total ?? items.length} Artikel
                            </span>
                        </div>
                    </div>

                    {/* Loading Skeleton */}
                    {isSearching ? (
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-pulse">
                            {[1, 2, 3].map((n) => (
                                <div key={n} className="bg-white rounded-3xl overflow-hidden border border-stone-200 p-5 space-y-4">
                                    <div className="bg-stone-200 aspect-[16/10] rounded-2xl w-full" />
                                    <div className="h-4 bg-stone-200 rounded w-1/3" />
                                    <div className="h-6 bg-stone-200 rounded w-4/5" />
                                    <div className="h-4 bg-stone-200 rounded w-full" />
                                </div>
                            ))}
                        </div>
                    ) : items.length === 0 ? (
                        /* Empty State */
                        <div className="bg-white rounded-3xl p-10 sm:p-14 text-center border border-stone-200/80 shadow-xs max-w-xl mx-auto my-10">
                            <div className="w-16 h-16 rounded-2xl bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-4">
                                <Shield className="w-8 h-8" />
                            </div>
                            <h3 className="font-display font-bold text-lg text-[#1E242B] mb-2">
                                {empty_message}
                            </h3>
                            <p className="text-xs sm:text-sm text-stone-500 leading-relaxed mb-6">
                                Panduan dengan kata kunci atau topik filter saat ini belum tersedia. Silakan gunakan kata kunci lain atau tampilkan seluruh panduan.
                            </p>
                            <button
                                type="button"
                                onClick={handleResetFilter}
                                className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#1B4332] text-white text-xs font-bold hover:bg-[#E05A2B] transition-colors cursor-pointer"
                            >
                                <RotateCcw className="w-4 h-4" />
                                <span>Tampilkan Semua Panduan</span>
                            </button>
                        </div>
                    ) : (
                        <div className="space-y-8">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {items.map((item) => (
                                    <article
                                        key={item.id}
                                        className="group bg-white rounded-3xl overflow-hidden border border-stone-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col justify-between h-full"
                                    >
                                        <div>
                                            {/* Thumbnail Gambar dengan Fallback */}
                                            <div className="relative aspect-[16/10] overflow-hidden bg-stone-900">
                                                <ImageWithFallback
                                                    src={item.thumbnail_image}
                                                    alt={item.thumbnail_alt}
                                                    fallbackText={item.title}
                                                    loading="lazy"
                                                    className="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500 ease-out"
                                                />
                                                <div className="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none" />

                                                {/* Posisi Kategori Konsisten di Kiri Atas */}
                                                <div className="absolute top-3.5 left-3.5">
                                                    <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/95 text-[#1B4332] shadow-sm backdrop-blur-xs">
                                                        {getCategoryIcon(item.category)}
                                                        {item.category_label}
                                                    </span>
                                                </div>

                                                {/* Waktu Baca di Kanan Bawah */}
                                                <div className="absolute bottom-3 right-3">
                                                    <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-black/60 text-white backdrop-blur-md inline-flex items-center gap-1">
                                                        <Clock className="w-2.5 h-2.5 text-[#F4A261]" />
                                                        {item.reading_time_minutes} mnt baca
                                                    </span>
                                                </div>
                                            </div>

                                            {/* Card Content dengan Line-height Nyaman */}
                                            <div className="p-5">
                                                <div className="text-[11px] text-stone-500 mb-2 flex items-center gap-2">
                                                    <Calendar className="w-3 h-3 text-[#52B788]" />
                                                    <span>{item.published_date_formatted}</span>
                                                </div>

                                                <h3 className="font-display font-extrabold text-base text-[#1E242B] group-hover:text-[#1B4332] transition-colors leading-snug line-clamp-2 min-h-[2.75rem] mb-2">
                                                    <Link href={`/panduan/${item.slug}`}>
                                                        {item.title}
                                                    </Link>
                                                </h3>

                                                <p className="text-xs sm:text-sm text-stone-600 leading-relaxed line-clamp-2 mb-4">
                                                    {item.summary}
                                                </p>
                                            </div>
                                        </div>

                                        {/* Card Footer Pinned di Bawah */}
                                        <div className="px-5 pb-5 pt-3 border-t border-stone-100 flex items-center justify-between text-xs mt-auto">
                                            <span className="text-stone-500 font-medium truncate max-w-[65%]" title={item.source_name}>
                                                Rujukan: {item.source_name}
                                            </span>
                                            <Link
                                                href={`/panduan/${item.slug}`}
                                                className="inline-flex items-center gap-1 font-bold text-[#1B4332] group-hover:text-[#E05A2B] group-hover:translate-x-1 transition-all"
                                            >
                                                <span>Baca</span>
                                                <ArrowRight className="w-3.5 h-3.5" />
                                            </Link>
                                        </div>
                                    </article>
                                ))}
                            </div>

                            {/* Pagination Links */}
                            {guides.links && guides.links.length > 3 && (
                                <nav aria-label="Navigasi Halaman Panduan" className="pt-6 flex items-center justify-center gap-1.5">
                                    {guides.links.map((link, idx) => {
                                        const cleanLabel = link.label
                                            .replace('&laquo; Previous', '← Sebelumnya')
                                            .replace('Next &raquo;', 'Selanjutnya →');

                                        return (
                                            <Link
                                                key={idx}
                                                href={link.url || '#'}
                                                preserveScroll
                                                preserveState
                                                className={`px-3 py-1.5 rounded-xl text-xs font-bold transition-colors ${
                                                    !link.url
                                                        ? 'opacity-40 cursor-not-allowed bg-stone-100 text-stone-400'
                                                        : link.active
                                                        ? 'bg-[#1B4332] text-white shadow-sm'
                                                        : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200'
                                                }`}
                                            >
                                                {cleanLabel}
                                            </Link>
                                        );
                                    })}
                                </nav>
                            )}
                        </div>
                    )}
                </section>

                {/* 5. INTERACTIVE SAFETY SECTIONS (Tahapan Tindakan, Clean Room, Pencegahan, Checklist, FAQ) */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
                    <SafetyInteractiveSection />
                </section>
            </div>
        </PublicLayout>
    );
}

