import React, { useState, useEffect } from 'react';
import { Link, router } from '@inertiajs/react';
import { motion } from 'motion/react';
import {
    ShieldAlert,
    Info,
    Search,
    Filter,
    Calendar,
    Clock,
    MapPin,
    ArrowRight,
    RotateCcw,
    ChevronRight,
    Newspaper,
    Shield,
    CheckCircle2,
    Radio,
    Flame,
    BookOpen,
} from 'lucide-react';
import { PublicLayout } from '@/Layouts/PublicLayout';

export default function AnnouncementsIndex({
    announcements,
    featured,
    provinces,
    regionalStatus = [],
    activeProvince,
    activeLevel,
    activeCategory = 'semua',
    search = '',
    sort = 'terbaru',
    stats,
    lastUpdatedFormatted,
    categories = [],
}) {
    const [searchQuery, setSearchQuery] = useState(search || '');
    const [selectedProvince, setSelectedProvince] = useState(activeProvince?.slug || '');
    const [selectedSort, setSelectedSort] = useState(sort || 'terbaru');
    const [activeTab, setActiveTab] = useState(activeCategory || 'semua');
    const [isSearching, setIsSearching] = useState(false);

    // Sinkronisasi state lokal jika props berubah
    useEffect(() => {
        setSearchQuery(search || '');
        setSelectedProvince(activeProvince?.slug || '');
        setSelectedSort(sort || 'terbaru');
        setActiveTab(activeCategory || 'semua');
    }, [search, activeProvince, sort, activeCategory]);

    // Handle submit pencarian dan filter
    const applyFilters = (overrides = {}) => {
        setIsSearching(true);
        const params = {
            q: overrides.q !== undefined ? overrides.q : searchQuery,
            provinsi: overrides.provinsi !== undefined ? overrides.provinsi : selectedProvince,
            kategori: overrides.kategori !== undefined ? overrides.kategori : activeTab,
            sort: overrides.sort !== undefined ? overrides.sort : selectedSort,
        };

        // Bersihkan parameter kosong
        Object.keys(params).forEach((key) => {
            if (!params[key] || params[key] === 'semua' || params[key] === '') {
                delete params[key];
            }
        });

        router.get('/pengumuman', params, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setIsSearching(false),
        });
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        applyFilters({ q: searchQuery });
    };

    const handleCategoryClick = (categoryKey) => {
        setActiveTab(categoryKey);
        applyFilters({ kategori: categoryKey });
    };

    const handleProvinceChange = (e) => {
        const val = e.target.value;
        setSelectedProvince(val);
        applyFilters({ provinsi: val });
    };

    const handleSortChange = (e) => {
        const val = e.target.value;
        setSelectedSort(val);
        applyFilters({ sort: val });
    };

    const handleResetFilter = () => {
        setSearchQuery('');
        setSelectedProvince('');
        setSelectedSort('terbaru');
        setActiveTab('semua');
        setIsSearching(true);
        router.get('/pengumuman', {}, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => setIsSearching(false),
        });
    };

    const hasActiveFilters = Boolean(
        searchQuery ||
        selectedProvince ||
        (activeTab && activeTab !== 'semua') ||
        (selectedSort && selectedSort !== 'terbaru')
    );

    const items = announcements?.data || [];

    return (
        <PublicLayout title="Informasi Kewaspadaan & Peringatan Dini Karhutla">
            <div className="min-h-screen bg-[#F8F9FA] text-[#1E242B] pt-24 pb-20 selection:bg-[#E05A2B]/20 selection:text-[#E05A2B]">
                {/* 1. HERO & BREADCRUMBS SECTION */}
                <section className="relative overflow-hidden bg-gradient-to-b from-[#1C2526] via-[#1E242B] to-[#12161A] text-white pt-10 pb-16 sm:pb-20 border-b border-white/10">
                    {/* Atmospheric glow effects */}
                    <div className="absolute top-0 right-1/4 w-96 h-96 bg-[#E05A2B]/10 rounded-full blur-3xl pointer-events-none" />
                    <div className="absolute bottom-0 left-10 w-80 h-80 bg-[#1B4332]/20 rounded-full blur-3xl pointer-events-none" />

                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                        {/* Breadcrumbs */}
                        <nav aria-label="Breadcrumb" className="mb-6 flex items-center gap-2 text-xs text-[#F8F9FA]/60 font-medium">
                            <Link href="/" className="hover:text-white transition-colors">
                                Beranda
                            </Link>
                            <ChevronRight className="w-3.5 h-3.5 text-white/40" />
                            <span className="text-[#F4A261] font-semibold">Informasi Kewaspadaan</span>
                        </nav>

                        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-end">
                            <div className="lg:col-span-8">
                                {/* Environmental Alert Center Badge */}
                                <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/[0.08] border border-white/15 backdrop-blur-md text-xs font-semibold text-[#F4A261] mb-4 shadow-[0_2px_10px_rgba(0,0,0,0.2)]">
                                    <span className="relative flex h-2 w-2">
                                        <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#E05A2B] opacity-75" />
                                        <span className="relative inline-flex rounded-full h-2 w-2 bg-[#E05A2B]" />
                                    </span>
                                    <span>Environmental Alert Center</span>
                                    <span className="w-1 h-1 rounded-full bg-white/30" />
                                    <span className="text-[#F8F9FA]/80">Kalimantan</span>
                                </div>

                                <h1 className="font-display font-black text-3xl sm:text-4xl md:text-5xl lg:text-5xl text-white tracking-tight leading-[1.15] mb-4">
                                    Informasi Kewaspadaan <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#E05A2B] via-[#F4A261] to-[#F8F9FA]">Karhutla</span>
                                </h1>

                                <p className="text-sm sm:text-base md:text-lg text-[#F8F9FA]/80 font-normal leading-relaxed max-w-2xl">
                                    Ikuti informasi, peringatan, dan perkembangan kondisi kebakaran hutan dan lahan di wilayah Kalimantan melalui sumber informasi yang dapat dipercaya.
                                </p>
                            </div>

                            {/* Header Status & Metadata Strip */}
                            <div className="lg:col-span-4 flex flex-col gap-3">
                                <div className="p-4 rounded-2xl bg-white/[0.05] border border-white/10 backdrop-blur-md shadow-lg">
                                    <div className="flex items-center justify-between text-xs text-[#F8F9FA]/70 mb-2 font-medium">
                                        <span>Status Pemantauan Resmi</span>
                                        <span className="inline-flex items-center gap-1 text-[#52B788] font-semibold">
                                            <CheckCircle2 className="w-3.5 h-3.5" /> Terverifikasi
                                        </span>
                                    </div>

                                    <div className="grid grid-cols-3 gap-2 text-center py-2 border-y border-white/10">
                                        <div className="px-1">
                                            <span className="text-lg font-black text-[#E63946] block leading-tight">
                                                {stats?.darurat ?? 0}
                                            </span>
                                            <span className="text-[10px] text-[#F8F9FA]/70 font-semibold uppercase tracking-wider">
                                                Siaga Darurat
                                            </span>
                                        </div>
                                        <div className="px-1 border-x border-white/10">
                                            <span className="text-lg font-black text-[#F4A261] block leading-tight">
                                                {stats?.waspada ?? 0}
                                            </span>
                                            <span className="text-[10px] text-[#F8F9FA]/70 font-semibold uppercase tracking-wider">
                                                Waspada
                                            </span>
                                        </div>
                                        <div className="px-1">
                                            <span className="text-lg font-black text-[#2A9D8F] block leading-tight">
                                                {stats?.info ?? 0}
                                            </span>
                                            <span className="text-[10px] text-[#F8F9FA]/70 font-semibold uppercase tracking-wider">
                                                Informasi
                                            </span>
                                        </div>
                                    </div>

                                    <div className="mt-2.5 flex items-center justify-between text-[11px] text-[#F8F9FA]/60">
                                        <span className="flex items-center gap-1">
                                            <Clock className="w-3 h-3 text-[#F4A261]" />
                                            {lastUpdatedFormatted ? `Pembaruan: ${lastUpdatedFormatted}` : 'Pembaruan berkala'}
                                        </span>
                                        <span className="font-semibold text-white/90">
                                            {stats?.total ?? 0} Rilis Aktif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* 2. REGIONAL ALERT STATUS PANEL */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 sm:-mt-8 relative z-20 mb-12">
                    <div className="bg-white rounded-2xl shadow-xl shadow-black/5 border border-stone-200/80 p-4 sm:p-6 backdrop-blur-sm">
                        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-3 border-b border-stone-100">
                            <div className="flex items-center gap-2">
                                <div className="w-2.5 h-2.5 rounded-full bg-[#E05A2B] animate-pulse" />
                                <h2 className="text-xs sm:text-sm font-bold uppercase tracking-wider text-[#1E242B]">
                                    Status Kewaspadaan 5 Provinsi Kalimantan
                                </h2>
                            </div>
                            <span className="text-[11px] text-stone-500 font-medium">
                                Bersumber dari ketetapan otoritas resmi BPBD, BMKG &amp; Pemda
                            </span>
                        </div>

                        {/* Grid 5 Provinsi */}
                        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                            {regionalStatus.map((region) => {
                                const isSelected = selectedProvince === region.slug;
                                return (
                                    <button
                                        key={region.id}
                                        type="button"
                                        onClick={() => {
                                            const newSlug = isSelected ? '' : region.slug;
                                            setSelectedProvince(newSlug);
                                            applyFilters({ provinsi: newSlug });
                                        }}
                                        className={`text-left p-3.5 rounded-xl border transition-all duration-200 group flex flex-col justify-between ${
                                            isSelected
                                                ? 'bg-[#1E242B] border-[#1E242B] text-white shadow-md'
                                                : 'bg-stone-50/80 hover:bg-stone-100/90 border-stone-200/70 text-[#1E242B]'
                                        }`}
                                    >
                                        <div>
                                            <div className="flex items-center justify-between mb-1.5">
                                                <span className={`text-[10px] font-bold px-1.5 py-0.5 rounded tracking-wide ${
                                                    isSelected ? 'bg-white/20 text-[#F4A261]' : 'bg-stone-200/80 text-stone-700'
                                                }`}>
                                                    {region.code || region.name.substring(0, 6)}
                                                </span>
                                                <span className={`inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-full border ${
                                                    isSelected ? 'bg-white/10 text-white border-white/20' : region.status_badge
                                                }`}>
                                                    <span className={`w-1.5 h-1.5 rounded-full ${region.status_dot}`} />
                                                    {region.status_label}
                                                </span>
                                            </div>
                                            <h3 className={`text-xs sm:text-sm font-bold tracking-tight mb-1 ${
                                                isSelected ? 'text-white' : 'text-[#1E242B] group-hover:text-[#E05A2B]'
                                            }`}>
                                                {region.name}
                                            </h3>
                                        </div>

                                        <div className="mt-2 pt-2 border-t border-stone-200/50 flex items-center justify-between text-[10px]">
                                            <span className={isSelected ? 'text-white/60' : 'text-stone-500'}>
                                                {region.latest_updated_at ? `Update: ${region.latest_updated_at}` : 'Rilis aktif terpantau'}
                                            </span>
                                            <span className={`font-semibold ${isSelected ? 'text-[#F4A261]' : 'text-[#E05A2B]'}`}>
                                                {isSelected ? 'Aktif' : 'Pilih →'}
                                            </span>
                                        </div>
                                    </button>
                                );
                            })}
                        </div>

                        {/* Regulatory Notice & Clarification */}
                        <div className="mt-4 pt-3 border-t border-stone-100 flex items-start gap-2 text-[11px] text-stone-500 leading-normal">
                            <Info className="w-4 h-4 text-[#2A9D8F] flex-shrink-0 mt-0.5" />
                            <p>
                                <strong>Transparansi Penilaian:</strong> Status kewaspadaan di atas merupakan kompilasi pengumuman dan ketetapan resmi yang dideklarasikan oleh otoritas berwenang (BPBD, BMKG, atau Pemerintah Daerah). Sistem FireAware tidak menyimpulkan status siaga darurat semata-mata berdasarkan hitungan titik panas (hotspot) sensor satelit.
                            </p>
                        </div>
                    </div>
                </section>

                {/* 3. FEATURED ALERT SECTION */}
                {featured && !hasActiveFilters && (
                    <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-14">
                        <div className="relative overflow-hidden rounded-3xl bg-[#1C2526] text-white shadow-2xl border border-white/15">
                            {/* Photographic Background with overlay */}
                            <div className="absolute inset-0 z-0">
                                <img
                                    src={featured.image_url}
                                    alt={featured.title}
                                    className="w-full h-full object-cover object-center brightness-[0.38] contrast-105 scale-105 group-hover:scale-100 transition-transform duration-700"
                                />
                                <div className="absolute inset-0 bg-gradient-to-t from-[#1C2526] via-[#1C2526]/80 to-transparent" />
                                <div className="absolute inset-0 bg-gradient-to-r from-[#1C2526] via-[#1C2526]/85 to-transparent" />
                            </div>

                            <div className="relative z-10 p-6 sm:p-8 md:p-12 lg:p-14 max-w-4xl">
                                <div className="flex flex-wrap items-center gap-2.5 mb-4">
                                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#E05A2B] text-white text-xs font-black uppercase tracking-wider shadow-lg">
                                        <Flame className="w-3.5 h-3.5" /> Peringatan Unggulan
                                    </span>

                                    <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border ${featured.level_badge} bg-[#1C2526]/80 backdrop-blur-md`}>
                                        <span className={`w-2 h-2 rounded-full ${featured.level_dot}`} />
                                        {featured.level_label}
                                    </span>

                                    {featured.province && (
                                        <span className="inline-flex items-center gap-1 text-xs text-[#F8F9FA]/80 px-2.5 py-1 rounded-full bg-white/10 backdrop-blur-md">
                                            <MapPin className="w-3 h-3 text-[#F4A261]" />
                                            {featured.province.name}
                                        </span>
                                    )}

                                    {featured.is_demo && (
                                        <span className="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30 uppercase">
                                            Simulasi Demo
                                        </span>
                                    )}
                                </div>

                                <h2 className="font-display font-extrabold text-2xl sm:text-3xl md:text-4xl text-white tracking-tight leading-[1.2] mb-4">
                                    <Link
                                        href={`/pengumuman/${featured.slug}`}
                                        className="hover:text-[#F4A261] transition-colors"
                                    >
                                        {featured.title}
                                    </Link>
                                </h2>

                                <p className="text-sm sm:text-base text-[#F8F9FA]/90 leading-relaxed mb-6 font-normal line-clamp-3">
                                    {featured.summary}
                                </p>

                                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-white/15 text-xs text-[#F8F9FA]/70">
                                    <div className="flex flex-wrap items-center gap-4">
                                        <span className="inline-flex items-center gap-1.5 font-medium text-white/90">
                                            <Shield className="w-4 h-4 text-[#52B788]" />
                                            Sumber: {featured.source_name}
                                        </span>
                                        <span className="inline-flex items-center gap-1.5">
                                            <Calendar className="w-4 h-4 text-[#F4A261]" />
                                            {featured.published_date_formatted}
                                        </span>
                                        <span className="inline-flex items-center gap-1.5">
                                            <Clock className="w-4 h-4 text-white/50" />
                                            {featured.reading_time} menit baca
                                        </span>
                                    </div>

                                    <Link
                                        href={`/pengumuman/${featured.slug}`}
                                        className="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#E05A2B] to-[#D94E34] text-white font-bold text-xs uppercase tracking-wider hover:brightness-110 transition-all shadow-[0_4px_16px_rgba(224,90,43,0.4)] active:scale-95 self-start sm:self-auto"
                                    >
                                        <span>Baca Rilis Lengkap</span>
                                        <ArrowRight className="w-4 h-4" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </section>
                )}

                {/* 4. MAIN FEED SECTION: CATEGORIES, SEARCH, AND ARTICLES */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Filter & Control Bar */}
                    <div className="bg-white rounded-2xl p-4 sm:p-5 border border-stone-200/80 shadow-sm mb-8">
                        <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            {/* Segmented Category Navigation */}
                            <div className="flex items-center gap-1.5 overflow-x-auto pb-2 lg:pb-0 scrollbar-none">
                                {categories.map((cat) => {
                                    const isActive = activeTab === cat.key;
                                    return (
                                        <button
                                            key={cat.key}
                                            type="button"
                                            onClick={() => handleCategoryClick(cat.key)}
                                            className={`relative px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all duration-200 flex items-center gap-2 ${
                                                isActive
                                                    ? 'text-white bg-[#1E242B] shadow-md'
                                                    : 'text-stone-600 hover:text-[#1E242B] hover:bg-stone-100'
                                            }`}
                                        >
                                            {isActive && (
                                                <motion.span
                                                    layoutId="activeCategoryPill"
                                                    className="absolute inset-0 rounded-xl bg-[#1E242B] -z-10 shadow-sm"
                                                    transition={{ type: 'spring', stiffness: 400, damping: 30 }}
                                                />
                                            )}
                                            <span>{cat.label}</span>
                                            <span className={`text-[10px] px-1.5 py-0.2 rounded-full font-bold ${
                                                isActive ? 'bg-white/20 text-[#F4A261]' : 'bg-stone-200/70 text-stone-600'
                                            }`}>
                                                {cat.count}
                                            </span>
                                        </button>
                                    );
                                })}
                            </div>

                            {/* Controls: Search, Province Select, and Reset */}
                            <div className="flex flex-wrap items-center gap-2.5">
                                {/* Search Input */}
                                <form onSubmit={handleSearchSubmit} className="relative flex-1 sm:w-64">
                                    <Search className="w-4 h-4 text-stone-400 absolute left-3 top-1/2 -translate-y-1/2" />
                                    <input
                                        type="text"
                                        placeholder="Cari kata kunci rilis..."
                                        value={searchQuery}
                                        onChange={(e) => setSearchQuery(e.target.value)}
                                        className="w-full pl-9 pr-3 py-2 text-xs rounded-xl bg-stone-50 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#E05A2B] focus:border-[#E05A2B] text-[#1E242B]"
                                    />
                                </form>

                                {/* Province Select */}
                                <div className="relative">
                                    <select
                                        value={selectedProvince}
                                        onChange={handleProvinceChange}
                                        aria-label="Pilih Wilayah Provinsi"
                                        className="text-xs py-2 pl-3 pr-8 rounded-xl bg-stone-50 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#E05A2B] text-stone-700 font-semibold cursor-pointer"
                                    >
                                        <option value="">Semua Wilayah</option>
                                        {provinces.map((p) => (
                                            <option key={p.id} value={p.slug}>
                                                {p.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                {/* Sort Select */}
                                <div className="relative">
                                    <select
                                        value={selectedSort}
                                        onChange={handleSortChange}
                                        aria-label="Urutkan Rilis"
                                        className="text-xs py-2 pl-3 pr-8 rounded-xl bg-stone-50 border border-stone-200 focus:outline-none focus:ring-2 focus:ring-[#E05A2B] text-stone-700 font-semibold cursor-pointer"
                                    >
                                        <option value="terbaru">Terbaru</option>
                                        <option value="urgensi">Tingkat Urgensi</option>
                                        <option value="terlama">Terlama</option>
                                    </select>
                                </div>

                                {/* Reset Filter Button */}
                                {hasActiveFilters && (
                                    <button
                                        type="button"
                                        onClick={handleResetFilter}
                                        className="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-stone-600 bg-stone-100 hover:bg-stone-200 transition-colors"
                                        title="Reset semua filter"
                                    >
                                        <RotateCcw className="w-3.5 h-3.5" />
                                        <span>Reset</span>
                                    </button>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Section Header: Active Filter Status */}
                    <div className="flex items-center justify-between mb-6">
                        <div className="flex items-center gap-2">
                            <Newspaper className="w-5 h-5 text-[#E05A2B]" />
                            <h2 className="font-display font-extrabold text-xl sm:text-2xl text-[#1E242B] tracking-tight">
                                {activeCategory === 'darurat'
                                    ? 'Peringatan Siaga Darurat'
                                    : activeCategory === 'waspada'
                                    ? 'Informasi Status Waspada'
                                    : activeCategory === 'info'
                                    ? 'Warta & Imbauan Preventif'
                                    : 'Aliran Informasi Resmi'}
                            </h2>
                            <span className="text-xs font-bold text-stone-500 bg-stone-100 px-2 py-0.5 rounded-full">
                                {announcements?.total ?? items.length} Rilis
                            </span>
                        </div>

                        {activeProvince && (
                            <div className="text-xs text-stone-600">
                                Wilayah: <span className="font-bold text-[#1E242B]">{activeProvince.name}</span>
                            </div>
                        )}
                    </div>

                    {/* Editorial Layout: Varied Article Cards */}
                    {items.length === 0 ? (
                        /* Empty State */
                        <div className="bg-white rounded-3xl p-10 sm:p-14 text-center border border-stone-200/80 shadow-sm max-w-xl mx-auto my-12">
                            <div className="w-16 h-16 rounded-2xl bg-stone-100 text-stone-400 flex items-center justify-center mx-auto mb-4">
                                <Search className="w-8 h-8" />
                            </div>
                            <h3 className="font-display font-bold text-lg text-[#1E242B] mb-2">
                                Tidak Ada Informasi yang Sesuai
                            </h3>
                            <p className="text-xs sm:text-sm text-stone-500 leading-relaxed mb-6">
                                Tidak ditemukan rilis pengumuman atau peringatan kewaspadaan dengan kriteria filter saat ini. Silakan coba kata kunci lain atau reset filter.
                            </p>
                            <button
                                type="button"
                                onClick={handleResetFilter}
                                className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#1E242B] text-white text-xs font-bold hover:bg-[#E05A2B] transition-colors"
                            >
                                <RotateCcw className="w-4 h-4" />
                                <span>Tampilkan Semua Rilis</span>
                            </button>
                        </div>
                    ) : (
                        <div className="space-y-8">
                            {/* Visual Grid: Editorial Hybrid Layout */}
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {items.map((item, index) => {
                                    // Berikan variasi tampilan untuk card pertama (Large card) jika di halaman pertama tanpa search
                                    const isLead = index === 0 && !hasActiveFilters && announcements.current_page === 1;

                                    if (isLead) {
                                        return (
                                            <article
                                                key={item.id}
                                                className="md:col-span-2 lg:col-span-2 group bg-white rounded-3xl overflow-hidden border border-stone-200/80 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col sm:flex-row"
                                            >
                                                {/* Image side */}
                                                <div className="sm:w-1/2 relative min-h-[220px] sm:min-h-[300px] overflow-hidden bg-stone-900">
                                                    <img
                                                        src={item.image_url}
                                                        alt={item.title}
                                                        className="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500"
                                                    />
                                                    <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent sm:hidden" />
                                                    <div className="absolute top-4 left-4 flex flex-wrap gap-2">
                                                        <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border ${item.level_badge} bg-white/95 backdrop-blur-md shadow-sm`}>
                                                            <span className={`w-2 h-2 rounded-full ${item.level_dot}`} />
                                                            {item.level_label}
                                                        </span>
                                                    </div>
                                                </div>

                                                {/* Content side */}
                                                <div className="sm:w-1/2 p-6 sm:p-8 flex flex-col justify-between">
                                                    <div>
                                                        <div className="flex items-center gap-2 text-xs text-stone-500 mb-2">
                                                            <Calendar className="w-3.5 h-3.5 text-[#E05A2B]" />
                                                            <span>{item.published_date_formatted}</span>
                                                            <span>•</span>
                                                            <span className="font-medium text-stone-600">{item.province?.name || 'Seluruh Kalimantan'}</span>
                                                        </div>

                                                        <h3 className="font-display font-extrabold text-xl sm:text-2xl text-[#1E242B] group-hover:text-[#E05A2B] transition-colors leading-tight mb-3">
                                                            <Link href={`/pengumuman/${item.slug}`}>
                                                                {item.title}
                                                            </Link>
                                                        </h3>

                                                        <p className="text-xs sm:text-sm text-stone-600 leading-relaxed line-clamp-3 mb-4">
                                                            {item.summary}
                                                        </p>
                                                    </div>

                                                    <div className="pt-4 border-t border-stone-100 flex items-center justify-between">
                                                        <span className="text-[11px] text-stone-500 font-medium">
                                                            Sumber: <strong className="text-stone-700">{item.source_name}</strong>
                                                        </span>
                                                        <Link
                                                            href={`/pengumuman/${item.slug}`}
                                                            className="inline-flex items-center gap-1 text-xs font-bold text-[#E05A2B] group-hover:translate-x-1 transition-transform"
                                                        >
                                                            <span>Rincian</span>
                                                            <ArrowRight className="w-3.5 h-3.5" />
                                                        </Link>
                                                    </div>
                                                </div>
                                            </article>
                                        );
                                    }

                                    // Medium Image Cards for other items
                                    return (
                                        <article
                                            key={item.id}
                                            className="group bg-white rounded-3xl overflow-hidden border border-stone-200/80 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                                        >
                                            <div>
                                                {/* Card Image */}
                                                <div className="relative h-48 overflow-hidden bg-stone-900">
                                                    <img
                                                        src={item.image_url}
                                                        alt={item.title}
                                                        className="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-500"
                                                    />
                                                    <div className="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent" />

                                                    <div className="absolute top-3 left-3 flex flex-wrap gap-2">
                                                        <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border ${item.level_badge} bg-white/95 backdrop-blur-md shadow-sm`}>
                                                            <span className={`w-1.5 h-1.5 rounded-full ${item.level_dot}`} />
                                                            {item.level_label}
                                                        </span>
                                                    </div>

                                                    {item.province && (
                                                        <div className="absolute bottom-3 left-3">
                                                            <span className="text-[10px] font-bold px-2 py-0.5 rounded-full bg-black/60 text-white backdrop-blur-md inline-flex items-center gap-1">
                                                                <MapPin className="w-2.5 h-2.5 text-[#F4A261]" />
                                                                {item.province.name}
                                                            </span>
                                                        </div>
                                                    )}
                                                </div>

                                                {/* Card Body */}
                                                <div className="p-5">
                                                    <div className="flex items-center gap-2 text-[11px] text-stone-500 mb-2">
                                                        <Calendar className="w-3 h-3 text-[#E05A2B]" />
                                                        <span>{item.published_date_formatted}</span>
                                                        <span>•</span>
                                                        <span>{item.reading_time} mnt baca</span>
                                                    </div>

                                                    <h3 className="font-display font-bold text-base text-[#1E242B] group-hover:text-[#E05A2B] transition-colors leading-snug mb-2 line-clamp-2">
                                                        <Link href={`/pengumuman/${item.slug}`}>
                                                            {item.title}
                                                        </Link>
                                                    </h3>

                                                    <p className="text-xs text-stone-600 leading-relaxed line-clamp-3 mb-4">
                                                        {item.summary}
                                                    </p>
                                                </div>
                                            </div>

                                            {/* Card Footer */}
                                            <div className="px-5 pb-5 pt-3 border-t border-stone-100 flex items-center justify-between text-[11px]">
                                                <span className="text-stone-500 font-medium truncate max-w-[65%]" title={item.source_name}>
                                                    {item.source_name}
                                                </span>
                                                <Link
                                                    href={`/pengumuman/${item.slug}`}
                                                    className="inline-flex items-center gap-1 text-xs font-bold text-[#E05A2B] group-hover:translate-x-1 transition-transform"
                                                >
                                                    <span>Selengkapnya</span>
                                                    <ArrowRight className="w-3.5 h-3.5" />
                                                </Link>
                                            </div>
                                        </article>
                                    );
                                })}
                            </div>

                            {/* 5. PAGINATION SECTION */}
                            {announcements.links && announcements.links.length > 3 && (
                                <nav aria-label="Navigasi Halaman" className="pt-8 flex items-center justify-center gap-1.5">
                                    {announcements.links.map((link, idx) => {
                                        // Clean label entities
                                        const cleanLabel = link.label
                                            .replace('&laquo; Previous', '← Sebelumnya')
                                            .replace('Next &raquo;', 'Selanjutnya →');

                                        if (!link.url) {
                                            return (
                                                <span
                                                    key={idx}
                                                    className="px-3.5 py-2 rounded-xl text-xs text-stone-400 bg-stone-100/50 cursor-not-allowed select-none"
                                                >
                                                    {cleanLabel}
                                                </span>
                                            );
                                        }

                                        return (
                                            <Link
                                                key={idx}
                                                href={link.url}
                                                preserveScroll
                                                preserveState
                                                className={`px-3.5 py-2 rounded-xl text-xs font-bold transition-all ${
                                                    link.active
                                                        ? 'bg-[#E05A2B] text-white shadow-md'
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

                {/* 6. EDUCATIONAL ACCORDION / AUTHORITY CREDIBILITY FOOTNOTE */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
                    <div className="rounded-3xl bg-gradient-to-br from-[#1E242B] to-[#12161A] text-white p-6 sm:p-10 border border-white/10 shadow-xl">
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="flex items-start gap-3.5">
                                <div className="w-10 h-10 rounded-xl bg-[#E05A2B]/20 text-[#E05A2B] flex items-center justify-center flex-shrink-0 border border-[#E05A2B]/30">
                                    <ShieldAlert className="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 className="text-sm font-bold text-white mb-1">Rantai Komando Resmi</h3>
                                    <p className="text-xs text-[#F8F9FA]/70 leading-relaxed">
                                        Seluruh data peringatan dini divalidasi silang bersama BPBD provinsi, satgas terpadu karhutla, dan data klimatologi BMKG sebelum dipublikasikan.
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-start gap-3.5">
                                <div className="w-10 h-10 rounded-xl bg-[#F4A261]/20 text-[#F4A261] flex items-center justify-center flex-shrink-0 border border-[#F4A261]/30">
                                    <Radio className="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 className="text-sm font-bold text-white mb-1">Kesiapsiagaan Warga</h3>
                                    <p className="text-xs text-[#F8F9FA]/70 leading-relaxed">
                                        Masyarakat diimbau mematuhi arahan evakuasi atau penggunaan masker N95 jika status Siaga Darurat kabut asap telah diterbitkan posko daerah.
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-start gap-3.5">
                                <div className="w-10 h-10 rounded-xl bg-[#52B788]/20 text-[#52B788] flex items-center justify-center flex-shrink-0 border border-[#52B788]/30">
                                    <BookOpen className="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 className="text-sm font-bold text-white mb-1">Panduan &amp; Edukasi</h3>
                                    <p className="text-xs text-[#F8F9FA]/70 leading-relaxed">
                                        Pelajari protokol keselamatan pencegahan kebakaran gambut dan tindakan darurat melalui laman panduan dan edukasi FireAware.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </PublicLayout>
    );
}

