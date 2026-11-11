import React, { useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { motion } from 'motion/react';
import { PublicLayout } from '@/Layouts/PublicLayout';
import { SectionHeading } from '@/Components/SectionHeading';
import { Badge } from '@/Components/Badge';
import { Button } from '@/Components/Button';
import { HotspotMapPreview } from '@/Components/HotspotMapPreview';
import { ImageWithFallback } from '@/Components/ImageWithFallback';
import {
    Flame,
    MapPin,
    BookOpen,
    Wind,
    Shield,
    Satellite,
    ArrowRight,
    Clock,
    Activity,
    Compass,
    CheckCircle2,
    Trees,
    Radio,
    ChevronRight,
    Globe2,
    Building2,
    Droplets,
} from 'lucide-react';

export default function Home({
    provinces = [],
    activeProvince = null,
    summary = {},
    hotspots = [],
    mapHotspots = [],
    announcements = [],
    safetyGuides = [],
    publishedGuides = [],
    latestAirQuality = null,
}) {
    const [selectedProvince, setSelectedProvince] = useState(
        activeProvince ? activeProvince.slug : ''
    );

    const handleProvinceChange = (e) => {
        const slug = e.target.value;
        setSelectedProvince(slug);
        router.get(
            '/',
            slug ? { provinsi: slug } : {},
            { preserveState: true, preserveScroll: true }
        );
    };

    const provinceMetadata = {
        'kalimantan-barat': {
            code: 'KALBAR',
            desc: 'Kawasan gambut pesisir luas dan koridor perbatasan darat.',
            image: '/images/peatland-gambut.jpg',
            icon: Droplets,
        },
        'kalimantan-tengah': {
            code: 'KALTENG',
            desc: 'Kubah gambut terdalam Indonesia dan prioritas sekat kanal.',
            image: '/images/smoke-haze.jpg',
            icon: Flame,
        },
        'kalimantan-selatan': {
            code: 'KALSEL',
            desc: 'Kawasan rawa lebak padat penduduk rawan kabut asap.',
            image: '/images/hero-kalimantan-rainforest.jpg',
            icon: Building2,
        },
        'kalimantan-timur': {
            code: 'KALTIM',
            desc: 'Hutan hujan tropis perbukitan dan zona penyangga IKN.',
            image: '/images/forest-canopy.jpg',
            icon: Trees,
        },
        'kalimantan-utara': {
            code: 'KALTARA',
            desc: 'Bentang kanopi hutan primer dan perbatasan dataran tinggi.',
            image: '/images/conservation-action.jpg',
            icon: Compass,
        },
    };

    const containerVariants = {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: { staggerChildren: 0.1, delayChildren: 0.15 },
        },
    };

    const itemVariants = {
        hidden: { opacity: 0, y: 16 },
        visible: {
            opacity: 1,
            y: 0,
            transition: { duration: 0.6, ease: [0.16, 1, 0.3, 1] },
        },
    };

    const formatTimestamp = (isoString) => {
        if (!isoString) return '—';
        try {
            const date = new Date(isoString);
            return date.toLocaleString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        } catch {
            return '—';
        }
    };

    return (
        <PublicLayout title="Beranda Sistem Pemantauan Karhutla Kalimantan">
            {/* 1. HERO SECTION (Cinematic, From Ember to Forest Recovery) */}
            <section className="relative min-h-[85vh] lg:min-h-[92vh] flex items-center justify-center overflow-hidden bg-[#1E242B] text-white pt-28 sm:pt-36 pb-28 sm:pb-36 lg:pb-44">
                {/* Background Image with Controlled Multi-Layered Overlays */}
                <div className="absolute inset-0 z-0 overflow-hidden">
                    <motion.img
                        initial={{ scale: 1.06, opacity: 0 }}
                        animate={{ scale: 1, opacity: 1 }}
                        transition={{ duration: 1.2, ease: [0.16, 1, 0.3, 1] }}
                        src="/images/hero-kalimantan-rainforest.jpg"
                        alt="Bentang Kanopi Hutan Hujan Tropis Kalimantan"
                        className="w-full h-full object-cover object-[center_32%] sm:object-[center_20%] select-none pointer-events-none"
                        loading="eager"
                    />
                    {/* Left-to-Right Contrast Gradient: Darker on left for text legibility, transparent on right to reveal the sunlight & river */}
                    <div className="absolute inset-0 bg-gradient-to-r from-[#1E242B]/95 via-[#1E242B]/85 via-45% to-[#1B4332]/40 sm:to-transparent" />
                    {/* Top-to-Bottom Subtle Ambient Vignette: Charcoal Ash at top for navbar contrast, Deep Pine & Charcoal Ash at bottom */}
                    <div className="absolute inset-0 bg-gradient-to-b from-[#1E242B]/60 via-transparent via-50% to-[#1E242B]" />
                    {/* Subtle Warm Amber Glow accent at top-right to harmonize with sunrise */}
                    <div className="absolute -top-24 right-0 w-[500px] h-[500px] bg-[radial-gradient(circle,rgba(244,162,97,0.15)_0%,transparent_70%)] pointer-events-none" />
                </div>

                {/* Content Container */}
                <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    <motion.div
                        variants={containerVariants}
                        initial="hidden"
                        animate="visible"
                        className="max-w-3xl"
                    >
                        {/* Main Headline */}
                        <motion.h1
                            variants={itemVariants}
                            className="text-3xl sm:text-5xl lg:text-[54px] font-extrabold tracking-[-0.025em] font-display text-[#F8F9FA] leading-[1.14]"
                        >
                            Kenali Kondisi Hutan, Lindungi Masa Depan{' '}
                            <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#F4A261] via-[#E05A2B] to-[#D94E34]">
                                Kalimantan.
                            </span>
                        </motion.h1>

                        {/* Description */}
                        <motion.p
                            variants={itemVariants}
                            className="mt-6 text-sm sm:text-base lg:text-[17px] text-[#F8F9FA]/85 leading-[1.65] font-normal max-w-2xl"
                        >
                            Platform informasi dan pemantauan kebakaran hutan dan lahan untuk membantu masyarakat mengenali kondisi lingkungan serta meningkatkan kewaspadaan berbasis telemetri satelit NASA dan BMKG.
                        </motion.p>

                        {/* Call To Action Buttons */}
                        <motion.div
                            variants={itemVariants}
                            className="mt-8 sm:mt-10 flex flex-wrap items-center gap-3.5"
                        >
                            <Button
                                href="/peta-hotspot"
                                variant="primary"
                                size="lg"
                                className="shadow-[0_4px_20px_rgba(224,90,43,0.35)] hover:shadow-[0_8px_28px_rgba(224,90,43,0.5)]"
                            >
                                <MapPin className="w-4 h-4" />
                                <span>Jelajahi Peta Hotspot</span>
                            </Button>

                            <Button
                                href="/panduan"
                                variant="glass"
                                size="lg"
                            >
                                <BookOpen className="w-4 h-4 text-[#52B788]" />
                                <span>Pelajari Panduan Keselamatan</span>
                            </Button>
                        </motion.div>

                        {/* Live Telemetry Metadata Bar (Clean Separators, No Bullet Dots) */}
                        <motion.div
                            variants={itemVariants}
                            className="mt-10 sm:mt-12 pt-6 border-t border-white/15 flex flex-wrap items-center gap-y-2.5 gap-x-3 text-xs"
                        >
                            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-black/30 backdrop-blur-md border border-white/10 text-[#F8F9FA]/90 font-medium">
                                <Satellite className="w-3.5 h-3.5 text-[#52B788]" />
                                <span>Telemetri Satelit NASA VIIRS &amp; MODIS</span>
                            </div>
                            <span className="hidden sm:inline w-px h-4 bg-white/20 self-center" aria-hidden="true" />
                            <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-black/30 backdrop-blur-md border border-white/10 text-[#F8F9FA]/90 font-medium">
                                <Clock className="w-3.5 h-3.5 text-[#F4A261]" />
                                <span>Pembaruan: {formatTimestamp(summary?.last_detected_at)} WITA</span>
                            </div>
                        </motion.div>
                    </motion.div>
                </div>
            </section>

            {/* 2. MONITORING OVERVIEW (Controlled Overlap Transition) */}
            <div className="relative bg-gradient-to-b from-[#1E242B] via-[#1E242B] to-[#F6F5F2] pt-0 pb-10">
                <section className="relative z-20 -mt-16 sm:-mt-20 lg:-mt-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <motion.div
                        initial={{ opacity: 0, y: 24 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true, margin: '-50px' }}
                        transition={{ duration: 0.6, ease: [0.16, 1, 0.3, 1] }}
                        className="rounded-2xl sm:rounded-3xl bg-white shadow-[0_20px_50px_rgba(0,0,0,0.12),0_1px_3px_rgba(0,0,0,0.05)] border border-black/5 p-6 sm:p-8 backdrop-blur-sm"
                    >
                        <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-black/5">
                            <div>
                                <div className="flex items-center gap-2">
                                    <Activity className="w-5 h-5 text-[#E05A2B]" />
                                    <h2 className="text-lg font-bold text-[#1E242B] font-display">
                                        Ringkasan Deteksi Titik Panas
                                    </h2>
                                </div>
                                <p className="mt-1 text-xs text-[#1E242B]/60">
                                    Data spasial 7 hari terakhir dari sensor termal satelit di Pulau Borneo
                                </p>
                            </div>

                            {/* Province Quick Filter */}
                            <div className="flex items-center gap-2 self-start lg:self-auto">
                                <span className="text-xs font-semibold text-[#1E242B]/70 shrink-0">
                                    Filter Wilayah:
                                </span>
                                <select
                                    value={selectedProvince}
                                    onChange={handleProvinceChange}
                                    className="text-xs font-semibold text-[#1E242B] bg-[#F6F5F2] border border-[#2B303A]/15 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#E05A2B] cursor-pointer"
                                    aria-label="Pilih wilayah pemantauan"
                                >
                                    <option value="">Seluruh Kalimantan</option>
                                    {provinces.map((p) => (
                                        <option key={p.id} value={p.slug}>
                                            {p.name} ({p.hotspots_7d_count || 0})
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        {/* Stats Grid */}
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-6 pt-6">
                            <div className="p-4 rounded-2xl bg-[#F6F5F2] border border-black/5 flex flex-col justify-between">
                                <span className="text-xs font-medium text-[#1E242B]/70 mb-2">
                                    Total 7 Hari
                                </span>
                                <div className="flex items-baseline gap-2">
                                    <span className="text-3xl sm:text-4xl font-extrabold text-[#1E242B] font-display">
                                        {summary?.total_7d ?? 0}
                                    </span>
                                    <span className="text-xs text-[#1E242B]/50 font-medium">titik</span>
                                </div>
                            </div>

                            <div className="p-4 rounded-2xl bg-[#F6F5F2] border border-black/5 flex flex-col justify-between">
                                <span className="text-xs font-medium text-[#1E242B]/70 mb-2">
                                    24 Jam Terakhir
                                </span>
                                <div className="flex items-baseline gap-2">
                                    <span className="text-3xl sm:text-4xl font-extrabold text-[#E05A2B] font-display">
                                        {summary?.last_24h ?? 0}
                                    </span>
                                    <span className="text-xs text-[#E05A2B]/70 font-medium">titik</span>
                                </div>
                            </div>

                            <div className="p-4 rounded-2xl bg-[#F6F5F2] border border-black/5 flex flex-col justify-between">
                                <span className="text-xs font-medium text-[#1E242B]/70 mb-2">
                                    Kepercayaan Tinggi (≥80%)
                                </span>
                                <div className="flex items-baseline gap-2">
                                    <span className="text-3xl sm:text-4xl font-extrabold text-[#E63946] font-display">
                                        {summary?.high_confidence ?? 0}
                                    </span>
                                    <span className="text-xs text-[#E63946]/70 font-medium">titik</span>
                                </div>
                            </div>

                            <div className="p-4 rounded-2xl bg-[#1B4332] text-white border border-black/5 flex flex-col justify-between">
                                <span className="text-xs font-medium text-white/70 mb-2">
                                    Wilayah Terpilih
                                </span>
                                <div>
                                    <span className="text-lg sm:text-xl font-bold font-display block truncate">
                                        {activeProvince ? activeProvince.name : 'Seluruh Kalimantan'}
                                    </span>
                                    <span className="text-[11px] text-[#52B788] font-medium">
                                        5 Provinsi Terpantau
                                    </span>
                                </div>
                            </div>
                        </div>
                    </motion.div>
                </section>
            </div>

            {/* 3. HOTSPOT MAP PREVIEW (Two Columns: Editorial Context + Interactive Leaflet) */}
            <section className="py-20 lg:py-28 bg-[#F6F5F2]">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                        {/* Left Column: Heading & Data Context */}
                        <div className="lg:col-span-5 flex flex-col items-start">
                            <Badge variant="burnt" className="mb-3">
                                <Satellite className="w-3.5 h-3.5 mr-1" />
                                Telemetri Spasial Satelit
                            </Badge>

                            <h2 className="text-2xl sm:text-4xl font-extrabold tracking-tight text-[#1E242B] font-display leading-tight">
                                Persebaran Titik Panas Terkini di Kalimantan
                            </h2>

                            <p className="mt-4 text-sm sm:text-base text-[#1E242B]/75 leading-relaxed">
                                Peta ini memvisualisasikan deteksi anomali suhu permukaan bumi yang terekam oleh instrumen satelit polar NASA VIIRS (375m) dan MODIS (1km) saat melintas di atas kubah gambut dan kanopi hutan Kalimantan.
                            </p>

                            {/* Key Highlights */}
                            <div className="mt-6 space-y-3 w-full">
                                <div className="flex items-start gap-3 p-3 rounded-xl bg-white border border-black/5">
                                    <CheckCircle2 className="w-5 h-5 text-[#2A9D8F] shrink-0 mt-0.5" />
                                    <div className="text-xs text-[#1E242B]/80 leading-relaxed">
                                        <strong className="block text-[#1E242B] font-semibold">Sensor Resolusi 375 Meter:</strong>
                                        Mendeteksi api bawah tanah gambut bahkan sebelum asap tebal membubung.
                                    </div>
                                </div>

                                <div className="flex items-start gap-3 p-3 rounded-xl bg-white border border-black/5">
                                    <Shield className="w-5 h-5 text-[#52B788] shrink-0 mt-0.5" />
                                    <div className="text-xs text-[#1E242B]/80 leading-relaxed">
                                        <strong className="block text-[#1E242B] font-semibold">Integrasi Resmi NASA FIRMS:</strong>
                                        Sinkronisasi telemetri berkala tanpa pengubahan nilai sensor.
                                    </div>
                                </div>
                            </div>

                            <div className="mt-8 flex flex-wrap gap-3">
                                <Button href="/peta-hotspot" variant="primary" size="md">
                                    <MapPin className="w-4 h-4" />
                                    <span>Buka Peta Interaktif Penuh</span>
                                </Button>
                            </div>
                        </div>

                        {/* Right Column: Leaflet Map Preview */}
                        <div className="lg:col-span-7">
                            <HotspotMapPreview
                                hotspots={mapHotspots}
                                activeProvince={activeProvince}
                            />
                        </div>
                    </div>
                </div>
            </section>

            {/* 4. WILAYAH PEMANTAUAN (5 PROVINSI KALIMANTAN - EDITORIAL LAYOUT) */}
            <section className="py-20 lg:py-28 bg-white border-t border-b border-black/5">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <SectionHeading
                        align="center"
                        badge="Cakupan Ekologis Borneo"
                        title="Pemantauan 5 Provinsi Kalimantan"
                        description="Setiap provinsi memiliki karakteristik hidrologis dan risiko kebakaran yang berbeda, mulai dari kubah gambut pesisir hingga perbukitan kanopi hutan primer."
                        className="mb-14"
                    />

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {provinces.map((province, idx) => {
                            const meta = provinceMetadata[province.slug] || {
                                code: 'BORNEO',
                                desc: 'Wilayah pemantauan terpadu telemetri satelit.',
                                image: '/images/hero-kalimantan-rainforest.jpg',
                                icon: Trees,
                            };
                            const IconComponent = meta.icon;

                            return (
                                <div
                                    key={province.id}
                                    className="group relative rounded-2xl overflow-hidden border border-black/5 bg-[#F6F5F2] flex flex-col justify-between transition-all duration-300 hover:shadow-xl hover:-translate-y-1"
                                >
                                    {/* Image Top */}
                                    <div className="relative h-44 sm:h-48 overflow-hidden">
                                        <ImageWithFallback
                                            src={meta.image}
                                            alt={province.name}
                                            className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        />
                                        <div className="absolute inset-0 bg-gradient-to-t from-[#1E242B] via-transparent to-transparent opacity-80" />

                                        {/* Badges on image */}
                                        <div className="absolute top-3 left-3">
                                            <span className="px-2.5 py-1 rounded-md text-[10px] font-bold bg-[#1E242B]/90 text-white backdrop-blur-md uppercase tracking-wider">
                                                {meta.code}
                                            </span>
                                        </div>

                                        <div className="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white">
                                            <h3 className="font-display font-bold text-lg leading-tight drop-shadow-sm">
                                                {province.name}
                                            </h3>
                                            <IconComponent className="w-5 h-5 text-[#F4A261]" />
                                        </div>
                                    </div>

                                    {/* Body Description */}
                                    <div className="p-5 flex-1 flex flex-col justify-between">
                                        <p className="text-xs text-[#1E242B]/75 leading-relaxed mb-4">
                                            {meta.desc}
                                        </p>

                                        <div className="pt-4 border-t border-black/5 flex items-center justify-between text-xs">
                                            <div className="flex items-center gap-1.5 text-[#1E242B]/60">
                                                <Flame className="w-3.5 h-3.5 text-[#E05A2B]" />
                                                <span>
                                                    <strong>{province.hotspots_7d_count || 0}</strong> Titik 7 Hari
                                                </span>
                                            </div>

                                            <Link
                                                href={`/peta-hotspot?provinsi=${province.slug}`}
                                                className="inline-flex items-center gap-1 font-bold text-[#E05A2B] group-hover:text-[#c94d23] transition-colors"
                                            >
                                                <span>Peta</span>
                                                <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" />
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}

                        {/* Borneo Overview Card */}
                        <div className="rounded-2xl p-6 bg-gradient-to-br from-[#1B4332] to-[#1E242B] text-white flex flex-col justify-between shadow-lg">
                            <div>
                                <div className="flex items-center justify-between mb-4">
                                    <span className="px-2.5 py-1 rounded-md text-[10px] font-bold bg-white text-[#1B4332] uppercase tracking-wider">
                                        TOTAL BORNEO
                                    </span>
                                    <Globe2 className="w-5 h-5 text-[#52B788]" />
                                </div>
                                <h3 className="text-xl font-bold font-display mb-2">
                                    Seluruh Pulau Kalimantan
                                </h3>
                                <p className="text-xs text-white/80 leading-relaxed">
                                    Area daratan seluas 544.000 km² dengan tutupan hutan hujan tropis terbesar ketiga di dunia serta ribuan kilometer jaringan kanal gambut.
                                </p>
                            </div>

                            <div className="mt-6 pt-4 border-t border-white/10 flex items-center justify-between text-xs">
                                <span className="text-white/60">5 Provinsi Terpadu</span>
                                <Link
                                    href="/peta-hotspot"
                                    className="font-bold text-[#F4A261] hover:text-white inline-flex items-center gap-1 transition-colors"
                                >
                                    <span>Jelajahi Semua</span>
                                    <ArrowRight className="w-3.5 h-3.5" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* 5. INFORMASI KEWASPADAAN & KUALITAS UDARA (Dual Section) */}
            <section className="py-20 lg:py-28 bg-[#F6F5F2]">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                        {/* Left: Latest Air Quality Telemetry */}
                        <div className="lg:col-span-5 flex flex-col justify-between">
                            <div>
                                <div className="flex items-center justify-between mb-4">
                                    <div>
                                        <Badge variant="pine" className="mb-2">
                                            <Wind className="w-3.5 h-3.5 mr-1" />
                                            Sensor ISPU PM2.5
                                        </Badge>
                                        <h2 className="text-xl sm:text-2xl font-extrabold text-[#1E242B] font-display">
                                            Kualitas Udara Partikulat
                                        </h2>
                                    </div>
                                    <Link
                                        href="/kualitas-udara"
                                        className="text-xs font-semibold text-[#E05A2B] hover:underline"
                                    >
                                        Lihat Semua
                                    </Link>
                                </div>

                                {latestAirQuality ? (
                                    <div className="p-6 rounded-3xl bg-white border border-black/5 shadow-sm space-y-4">
                                        <div className="flex items-start justify-between">
                                            <div>
                                                <h3 className="font-bold text-base text-[#1E242B]">
                                                    {latestAirQuality.station_name || 'Stasiun Pemantau'}
                                                </h3>
                                                <span className="text-xs text-[#1E242B]/60 flex items-center gap-1 mt-0.5">
                                                    <MapPin className="w-3 h-3" />
                                                    {latestAirQuality.province_name || 'Kalimantan'}
                                                </span>
                                            </div>
                                            <Badge
                                                variant={
                                                    latestAirQuality.category === 'BAIK'
                                                        ? 'aman'
                                                        : latestAirQuality.category === 'SEDANG'
                                                            ? 'waspada'
                                                            : 'darurat'
                                                }
                                            >
                                                {latestAirQuality.category || 'ISPU'}
                                            </Badge>
                                        </div>

                                        <div className="p-4 rounded-2xl bg-[#F6F5F2] border border-black/5 flex items-baseline gap-2">
                                            <span className="text-4xl font-extrabold text-[#1E242B] font-display">
                                                {latestAirQuality.value}
                                            </span>
                                            <span className="text-xs font-medium text-[#1E242B]/60">
                                                {latestAirQuality.unit || 'µg/m³'} (PM2.5)
                                            </span>
                                        </div>

                                        <p className="text-xs text-[#1E242B]/70 leading-relaxed">
                                            Partikel debu mikro berukuran 2.5 mikron dapat menembus sistem pernapasan bawah saat kabut asap gambut terjadi.
                                        </p>

                                        <div className="pt-2 text-[11px] text-[#1E242B]/50 flex items-center gap-1.5">
                                            <Clock className="w-3.5 h-3.5" />
                                            <span>Diperbarui {latestAirQuality.measured_at}</span>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="p-8 rounded-3xl bg-white border border-black/5 text-center flex flex-col items-center justify-center shadow-xs">
                                        <div className="w-12 h-12 rounded-2xl bg-[#F6F5F2] text-[#2B303A]/50 flex items-center justify-center mb-3.5">
                                            <Wind className="w-6 h-6 stroke-[1.8]" />
                                        </div>
                                        <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-black/5 text-[#1E242B]/60 mb-2.5">
                                            Menunggu Telemetri
                                        </span>
                                        <h3 className="font-bold text-sm text-[#1E242B] font-display">
                                            Data Kualitas Udara Belum Tersedia
                                        </h3>
                                        <p className="text-xs text-[#1E242B]/65 mt-1.5 max-w-xs leading-relaxed">
                                            Stasiun pemantauan ISPU di wilayah ini belum menyalurkan data telemetri partikulat (PM2.5) terbaru. Indeks akan diperbarui secara otomatis ketika sensor stasiun aktif menyuplai data.
                                        </p>
                                        <div className="mt-4 pt-3 border-t border-black/5 w-full flex items-center justify-center gap-1.5 text-[11px] text-[#1E242B]/50">
                                            <Clock className="w-3.5 h-3.5" />
                                            <span>Pembaruan: Mengikuti jadwal sinkronisasi berkala</span>
                                        </div>
                                    </div>
                                )}
                            </div>

                            <div className="mt-6">
                                <Button href="/kualitas-udara" variant="outline" size="md" className="w-full">
                                    <Wind className="w-4 h-4 mr-2" />
                                    <span>Buka Indeks Kualitas Udara</span>
                                </Button>
                            </div>
                        </div>

                        {/* Right: Rilis Kewaspadaan & Pengumuman */}
                        <div className="lg:col-span-7">
                            <div className="flex items-center justify-between mb-4">
                                <div>
                                    <Badge variant="burnt" className="mb-2">
                                        <Radio className="w-3.5 h-3.5 mr-1" />
                                        Warta &amp; Peringatan Dini
                                    </Badge>
                                    <h2 className="text-xl sm:text-2xl font-extrabold text-[#1E242B] font-display">
                                        Pengumuman Status Karhutla
                                    </h2>
                                </div>
                                <Link
                                    href="/pengumuman"
                                    className="text-xs font-semibold text-[#E05A2B] hover:underline"
                                >
                                    Lihat Semua
                                </Link>
                            </div>

                            {announcements.length > 0 ? (
                                <div className="space-y-4">
                                    {announcements.map((item, idx) => (
                                        <article
                                            key={item.id}
                                            className="p-5 rounded-2xl bg-white border border-black/5 shadow-xs transition-shadow hover:shadow-md flex flex-col justify-between"
                                        >
                                            <div>
                                                <div className="flex items-center justify-between gap-2 mb-2">
                                                    <span
                                                        className={`px-2.5 py-0.5 rounded-full text-[10px] font-bold ${item.level === 3
                                                                ? 'bg-[#E63946]/15 text-[#E63946]'
                                                                : item.level === 2
                                                                    ? 'bg-[#F4A261]/20 text-[#a85817]'
                                                                    : 'bg-[#2A9D8F]/15 text-[#2A9D8F]'
                                                            }`}
                                                    >
                                                        {item.level_label || 'Peringatan'}
                                                    </span>
                                                    <span className="text-[11px] text-[#1E242B]/50">
                                                        {item.effective_date}
                                                    </span>
                                                </div>

                                                <h3 className="font-bold text-base text-[#1E242B] font-display hover:text-[#E05A2B] transition-colors">
                                                    <Link href={`/pengumuman/${item.slug}`}>
                                                        {item.title}
                                                    </Link>
                                                </h3>

                                                <p className="mt-1.5 text-xs text-[#1E242B]/70 line-clamp-2 leading-relaxed">
                                                    {item.summary}
                                                </p>
                                            </div>

                                            <div className="mt-3 pt-3 border-t border-black/5 flex items-center justify-between text-[11px]">
                                                <span className="text-[#1E242B]/50 font-medium">
                                                    Sumber: {item.source_name || 'BPBD / BMKG'}
                                                </span>
                                                <Link
                                                    href={`/pengumuman/${item.slug}`}
                                                    className="font-semibold text-[#E05A2B] inline-flex items-center gap-1 hover:underline"
                                                >
                                                    <span>Baca Rilis</span>
                                                    <ChevronRight className="w-3.5 h-3.5" />
                                                </Link>
                                            </div>
                                        </article>
                                    ))}
                                </div>
                            ) : (
                                <div className="p-8 rounded-3xl bg-white border border-black/5 text-center text-xs text-[#1E242B]/60">
                                    Belum ada rilis resmi baru yang diterbitkan.
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </section>

            {/* 6. PANDUAN KESELAMATAN & EDUKASI (Visual Storytelling) */}
            <section className="py-20 lg:py-28 bg-white border-t border-black/5">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-14">
                        <div>
                            <Badge variant="pine" className="mb-3">
                                <BookOpen className="w-3.5 h-3.5 mr-1" />
                                Mitigasi &amp; Edukasi Komunitas
                            </Badge>
                            <h2 className="text-2xl sm:text-4xl font-extrabold tracking-tight text-[#1E242B] font-display leading-tight">
                                Panduan Perlindungan Diri &amp; Konservasi
                            </h2>
                        </div>
                        <Button href="/panduan" variant="outline" size="sm">
                            <span>Lihat Semua Panduan</span>
                            <ArrowRight className="w-3.5 h-3.5" />
                        </Button>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {/* Story Card 1 */}
                        <div className="rounded-3xl overflow-hidden border border-black/5 bg-[#F6F5F2] flex flex-col justify-between group hover:shadow-lg transition-all duration-300">
                            <div>
                                <div className="h-52 overflow-hidden">
                                    <ImageWithFallback
                                        src="/images/smoke-haze.jpg"
                                        alt="Protokol Penggunaan Masker Respirator"
                                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    />
                                </div>
                                <div className="p-6">
                                    <span className="text-[10px] font-bold uppercase tracking-wider text-[#E05A2B] block mb-2">
                                        Kesehatan &amp; Perlindungan Napas
                                    </span>
                                    <h3 className="font-bold text-lg text-[#1E242B] font-display mb-2">
                                        Protokol Respirator N95 saat Paparan Asap Gambut
                                    </h3>
                                    <p className="text-xs text-[#1E242B]/75 leading-relaxed">
                                        Masker kain standar tidak mampu menahan partikel halus PM2.5. Pelajari cara memilih dan menggunakan respirator yang tepat bagi lansia dan anak-anak.
                                    </p>
                                </div>
                            </div>
                            <div className="p-6 pt-0">
                                <Link
                                    href="/panduan"
                                    className="text-xs font-bold text-[#E05A2B] inline-flex items-center gap-1.5 hover:underline"
                                >
                                    <span>Pelajari Protokol</span>
                                    <ArrowRight className="w-3.5 h-3.5" />
                                </Link>
                            </div>
                        </div>

                        {/* Story Card 2 */}
                        <div className="rounded-3xl overflow-hidden border border-black/5 bg-[#F6F5F2] flex flex-col justify-between group hover:shadow-lg transition-all duration-300">
                            <div>
                                <div className="h-52 overflow-hidden">
                                    <ImageWithFallback
                                        src="/images/wildfire-firefighter.jpg"
                                        alt="Aksi Petugas Manggala Agni Memadamkan Api Bawah Tanah"
                                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    />
                                </div>
                                <div className="p-6">
                                    <span className="text-[10px] font-bold uppercase tracking-wider text-[#52B788] block mb-2">
                                        Ekologi &amp; Restorasi Gambut
                                    </span>
                                    <h3 className="font-bold text-lg text-[#1E242B] font-display mb-2">
                                        Mengenal Kebakaran Bawah Tanah (Ground Fire)
                                    </h3>
                                    <p className="text-xs text-[#1E242B]/75 leading-relaxed">
                                        Mengapa lahan gambut yang mengering dapat terbakar hingga kedalaman 3 meter di bawah permukaan dan memicu kabut asap berminggu-minggu.
                                    </p>
                                </div>
                            </div>
                            <div className="p-6 pt-0">
                                <Link
                                    href="/edukasi"
                                    className="text-xs font-bold text-[#1B4332] inline-flex items-center gap-1.5 hover:underline"
                                >
                                    <span>Pelajari Restorasi</span>
                                    <ArrowRight className="w-3.5 h-3.5" />
                                </Link>
                            </div>
                        </div>

                        {/* Story Card 3 */}
                        <div className="rounded-3xl overflow-hidden border border-black/5 bg-[#F6F5F2] flex flex-col justify-between group hover:shadow-lg transition-all duration-300">
                            <div>
                                <div className="h-52 overflow-hidden">
                                    <ImageWithFallback
                                        src="/images/conservation-action.jpg"
                                        alt="Aksi Kesiapsiagaan Masyarakat"
                                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    />
                                </div>
                                <div className="p-6">
                                    <span className="text-[10px] font-bold uppercase tracking-wider text-[#F4A261] block mb-2">
                                        Komunitas &amp; Aksi Posko
                                    </span>
                                    <h3 className="font-bold text-lg text-[#1E242B] font-display mb-2">
                                        Jalur Evakuasi &amp; Posko Aman Kabupaten
                                    </h3>
                                    <p className="text-xs text-[#1E242B]/75 leading-relaxed">
                                        Nomor kontak darurat 112, ruang pemulihan oksigen, dan langkah koordinasi dengan tim Manggala Agni serta BPBD terdekat.
                                    </p>
                                </div>
                            </div>
                            <div className="p-6 pt-0">
                                <Link
                                    href="/panduan"
                                    className="text-xs font-bold text-[#E05A2B] inline-flex items-center gap-1.5 hover:underline"
                                >
                                    <span>Lihat Direktori</span>
                                    <ArrowRight className="w-3.5 h-3.5" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* 7. CALL TO ACTION (Deep Pine / Forest Recovery Closure) */}
            <section className="py-20 lg:py-28 bg-gradient-to-br from-[#1B4332] via-[#1E242B] to-[#1C2526] text-white relative overflow-hidden">
                <div className="absolute inset-0 bg-[radial-gradient(circle_at_70%_50%,rgba(82,183,136,0.15)_0%,transparent_50%)] pointer-events-none" />
                <div className="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <Badge variant="glass" className="mb-6 text-[#52B788] border-white/20">
                        <Trees className="w-3.5 h-3.5 mr-1" />
                        Gerakan Bersama Lindungi Borneo
                    </Badge>

                    <h2 className="text-3xl sm:text-5xl font-extrabold tracking-tight font-display text-white leading-tight">
                        Menjaga Kalimantan Dimulai dari Kepedulian Kita.
                    </h2>

                    <p className="mt-6 text-sm sm:text-base text-[#F8F9FA]/80 max-w-2xl mx-auto leading-relaxed">
                        Kesiapsiagaan menghadapi karhutla dimulai dari pemahaman data yang akurat. Pantau sebaran titik panas satelit, periksa kualitas udara di sekitar Anda, dan bagikan panduan keselamatan kepada keluarga tercinta.
                    </p>

                    <div className="mt-10 flex flex-wrap items-center justify-center gap-4">
                        <Button
                            href="/peta-hotspot"
                            variant="primary"
                            size="lg"
                            className="shadow-xl shadow-[#E05A2B]/20"
                        >
                            <MapPin className="w-4 h-4" />
                            <span>Buka Peta Hotspot</span>
                        </Button>
                        <Button
                            href="/panduan"
                            variant="glass"
                            size="lg"
                            className="text-white hover:text-white"
                        >
                            <BookOpen className="w-4 h-4 text-[#52B788]" />
                            <span>Baca Panduan Keselamatan</span>
                        </Button>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

