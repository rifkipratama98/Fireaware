import React, { useState, useEffect, useRef, useMemo } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { motion, AnimatePresence } from 'motion/react';
import {
    Wind,
    Activity,
    ShieldCheck,
    AlertTriangle,
    AlertCircle,
    Info,
    Calendar,
    Clock,
    MapPin,
    RotateCcw,
    Search,
    ChevronRight,
    TrendingUp,
    TrendingDown,
    Layers,
    ExternalLink,
    ShieldAlert,
    CheckCircle2,
    HeartPulse,
    Trees,
    Radio,
    FileText,
    Sparkles,
    Flame,
    Droplets,
    Building2,
    Compass,
    HelpCircle,
    Filter,
} from 'lucide-react';
import { PublicLayout } from '@/Layouts/PublicLayout';
import { cn } from '@/Lib/utils';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler,
} from 'chart.js';

// Register Chart.js components
ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    Filler
);

/**
 * Helper Format Tanggal & Waktu Standar WITA (Asia/Makassar)
 */
function formatWitaDateTime(isoString) {
    if (!isoString) return '—';
    try {
        const d = new Date(isoString);
        if (Number.isNaN(d.getTime())) return '—';
        return d.toLocaleString('id-ID', {
            timeZone: 'Asia/Makassar',
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }) + ' WITA';
    } catch {
        return '—';
    }
}

/**
 * Peta Warna & Aksen Kategori ISPU Resmi (Permen LHK No. 14 Tahun 2020 Lampiran I)
 * Ambang Batas Konsentrasi PM2.5 (24 Jam):
 * - Baik: 0 - 15.5 µg/m³ (ISPU 0 - 50, Hijau)
 * - Sedang: 15.6 - 55.4 µg/m³ (ISPU 51 - 100, Biru)
 * - Tidak Sehat: 55.5 - 150.4 µg/m³ (ISPU 101 - 200, Kuning)
 * - Sangat Tidak Sehat: 150.5 - 250.4 µg/m³ (ISPU 201 - 300, Merah)
 * - Berbahaya: >= 250.5 µg/m³ (ISPU 301 - 500, Hitam)
 */
const ISPU_PALETTE = {
    Baik: {
        color: '#2A9D8F',
        bg: 'rgba(42, 157, 143, 0.12)',
        border: 'rgba(42, 157, 143, 0.35)',
        text: '#134e4a',
        badge: 'bg-[#2A9D8F]/15 text-[#2A9D8F] border-[#2A9D8F]/30',
        pm25_range: '0 - 15.5 µg/m³',
        ispu_range: '0 - 50',
        risk: 'Risiko rendah',
        official_color: 'Hijau',
    },
    Sedang: {
        color: '#2563EB',
        bg: 'rgba(37, 99, 235, 0.12)',
        border: 'rgba(37, 99, 235, 0.35)',
        text: '#1e40af',
        badge: 'bg-[#2563EB]/15 text-[#2563EB] border-[#2563EB]/30',
        pm25_range: '15.6 - 55.4 µg/m³',
        ispu_range: '51 - 100',
        risk: 'Risiko sedang',
        official_color: 'Biru',
    },
    'Tidak Sehat': {
        color: '#E05A2B',
        bg: 'rgba(224, 90, 43, 0.14)',
        border: 'rgba(224, 90, 43, 0.38)',
        text: '#9a3412',
        badge: 'bg-[#E05A2B]/15 text-[#E05A2B] border-[#E05A2B]/30',
        pm25_range: '55.5 - 150.4 µg/m³',
        ispu_range: '101 - 200',
        risk: 'Risiko meningkat',
        official_color: 'Kuning',
    },
    'Sangat Tidak Sehat': {
        color: '#E63946',
        bg: 'rgba(230, 57, 70, 0.15)',
        border: 'rgba(230, 57, 70, 0.38)',
        text: '#991b1b',
        badge: 'bg-[#E63946]/15 text-[#E63946] border-[#E63946]/30',
        pm25_range: '150.5 - 250.4 µg/m³',
        ispu_range: '201 - 300',
        risk: 'Risiko tinggi',
        official_color: 'Merah',
    },
    Berbahaya: {
        color: '#1E242B',
        bg: 'rgba(30, 36, 43, 0.15)',
        border: 'rgba(30, 36, 43, 0.4)',
        text: '#0f172a',
        badge: 'bg-[#1E242B]/15 text-[#1E242B] border-[#1E242B]/30',
        pm25_range: '≥ 250.5 µg/m³',
        ispu_range: '301 - 500',
        risk: 'Risiko sangat tinggi',
        official_color: 'Hitam',
    },
};

/**
 * Komponen Semicircular Gauge Modern
 * Dikalibrasi berdasarkan rentang konsentrasi PM2.5 Permen LHK 14/2020 (0 s.d. 250+ µg/m³)
 */
function SemiCircularGauge({ value, categoryLabel }) {
    const numericValue = typeof value === 'number' && !Number.isNaN(value) ? value : null;
    const maxScale = 250.5;
    const clamped = numericValue !== null ? Math.min(Math.max(numericValue, 0), maxScale) : 0;
    const percentage = numericValue !== null ? clamped / maxScale : 0;

    // Radius 82, perimeter setengah lingkaran: PI * 82 = ~257.6
    const radius = 82;
    const arcLength = Math.PI * radius;
    const strokeDashoffset = arcLength * (1 - percentage);

    return (
        <div className="relative flex flex-col items-center justify-center select-none">
            <svg
                viewBox="0 0 220 128"
                className="w-56 sm:w-64 h-auto overflow-visible"
                aria-label={numericValue !== null ? `Konsentrasi PM2.5: ${numericValue} µg/m³` : 'Data Belum Tersedia'}
            >
                <defs>
                    {/* Linear Gradient for Semicircular Arc sesuai warna Permen LHK 14/2020 */}
                    <linearGradient id="gaugeGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stopColor="#2A9D8F" />     {/* Hijau (Baik) */}
                        <stop offset="25%" stopColor="#2563EB" />    {/* Biru (Sedang) */}
                        <stop offset="60%" stopColor="#E05A2B" />    {/* Kuning-Oranye (Tidak Sehat) */}
                        <stop offset="85%" stopColor="#E63946" />    {/* Merah (Sangat Tidak Sehat) */}
                        <stop offset="100%" stopColor="#1E242B" />   {/* Hitam (Berbahaya) */}
                    </linearGradient>

                    <filter id="gaugeShadow" x="-20%" y="-20%" width="140%" height="140%">
                        <feDropShadow dx="0" dy="3" stdDeviation="4" floodOpacity="0.15" />
                    </filter>
                </defs>

                {/* Background Arc Track */}
                <path
                    d="M 28 118 A 82 82 0 0 1 192 118"
                    fill="none"
                    stroke="#E2E8F0"
                    strokeWidth="15"
                    strokeLinecap="round"
                />

                {/* Animated Colored Active Value Arc */}
                {numericValue !== null && (
                    <motion.path
                        d="M 28 118 A 82 82 0 0 1 192 118"
                        fill="none"
                        stroke="url(#gaugeGradient)"
                        strokeWidth="15"
                        strokeLinecap="round"
                        strokeDasharray={arcLength}
                        initial={{ strokeDashoffset: arcLength }}
                        animate={{ strokeDashoffset }}
                        transition={{ duration: 1.2, ease: [0.16, 1, 0.3, 1] }}
                        filter="url(#gaugeShadow)"
                    />
                )}
            </svg>

            {/* Central Value & Unit Typography */}
            <div className="absolute top-14 sm:top-16 flex flex-col items-center text-center">
                <span className="text-4xl sm:text-5xl font-black font-display tracking-tight text-[#1E242B]">
                    {numericValue !== null ? numericValue.toFixed(1) : '—'}
                </span>
                <span className="text-xs sm:text-sm font-semibold text-[#1E242B]/60 tracking-wider uppercase mt-0.5">
                    µg/m³ PM2.5
                </span>
            </div>

            {/* Threshold Ticks Legend at Base */}
            <div className="w-56 sm:w-64 flex justify-between px-1 text-[9.5px] font-bold text-[#1E242B]/45 mt-1 font-mono">
                <span>0</span>
                <span>15.5</span>
                <span>55.4</span>
                <span>150.4</span>
                <span>250.5+</span>
            </div>
        </div>
    );
}

/**
 * Komponen Utama Halaman Kualitas Udara
 */
export default function AirQuality({
    provinces = [],
    activeProvince = null,
    stations = [],
    activeStation = null,
    latest = null,
    history = { available: false, points: [], message: null },
    historyStats = null,
    category = null,
    categoryChip = null,
    allCategories = [],
    regionalSummary = [],
    isConfigured = false,
    dataSourceName = 'OpenAQ',
    dataSourceUrl = 'https://openaq.org/',
    lastSyncedAt = null,
}) {
    // Filter State
    const [selectedProvSlug, setSelectedProvSlug] = useState(activeProvince?.slug || '');
    const [selectedStationSlug, setSelectedStationSlug] = useState(activeStation?.slug || '');

    // Chart Canvas Ref
    const chartRef = useRef(null);
    const chartInstanceRef = useRef(null);

    // Sync state when props change
    useEffect(() => {
        setSelectedProvSlug(activeProvince?.slug || '');
        setSelectedStationSlug(activeStation?.slug || '');
    }, [activeProvince, activeStation]);

    // Handle Province Filter Change
    const handleProvinceChange = (e) => {
        const slug = e.target.value;
        setSelectedProvSlug(slug);
        setSelectedStationSlug(''); // reset station selection
        router.get(
            '/kualitas-udara',
            slug ? { provinsi: slug } : {},
            { preserveState: true, preserveScroll: true }
        );
    };

    // Handle Station Filter Change
    const handleStationChange = (e) => {
        const slug = e.target.value;
        setSelectedStationSlug(slug);
        const params = {};
        if (selectedProvSlug) params.provinsi = selectedProvSlug;
        if (slug) params.stasiun = slug;

        router.get('/kualitas-udara', params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    // Reset Filters
    const handleResetFilter = () => {
        setSelectedProvSlug('');
        setSelectedStationSlug('');
        router.get('/kualitas-udara', {}, { preserveState: true, preserveScroll: true });
    };

    // Quick filter click from Regional cards
    const handleSelectProvinceCard = (provSlug) => {
        setSelectedProvSlug(provSlug);
        setSelectedStationSlug('');
        router.get('/kualitas-udara', { provinsi: provSlug }, { preserveState: true, preserveScroll: true });
    };

    // Render Historical Chart with Chart.js
    useEffect(() => {
        if (!chartRef.current) return;

        // Cleanup existing instance
        if (chartInstanceRef.current) {
            chartInstanceRef.current.destroy();
            chartInstanceRef.current = null;
        }

        if (!history?.available || !Array.isArray(history.points) || history.points.length < 3) {
            return;
        }

        const ctx = chartRef.current.getContext('2d');
        if (!ctx) return;

        // Create gradient fill
        const gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(224, 90, 43, 0.28)');
        gradient.addColorStop(0.65, 'rgba(244, 162, 97, 0.08)');
        gradient.addColorStop(1, 'rgba(244, 162, 97, 0.0)');

        const labels = history.points.map((pt) => {
            if (!pt.t) return '—';
            try {
                const d = new Date(pt.t);
                return d.toLocaleDateString('id-ID', {
                    timeZone: 'Asia/Makassar',
                    day: 'numeric',
                    month: 'short',
                    hour: '2-digit',
                    minute: '2-digit',
                });
            } catch {
                return '—';
            }
        });

        const dataValues = history.points.map((pt) => Number(pt.v) || 0);

        const newChart = new ChartJS(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Konsentrasi PM2.5 (µg/m³)',
                        data: dataValues,
                        borderColor: '#E05A2B',
                        backgroundColor: gradient,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#FFFFFF',
                        pointBorderColor: '#E05A2B',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#E05A2B',
                        pointHoverBorderColor: '#FFFFFF',
                        pointHoverBorderWidth: 2,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 900,
                    easing: 'easeOutQuart',
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 36, 43, 0.94)',
                        titleColor: '#F8F9FA',
                        bodyColor: '#e2e8f0',
                        borderColor: 'rgba(255, 255, 255, 0.15)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: {
                            family: 'Inter, sans-serif',
                            size: 12,
                            weight: 'bold',
                        },
                        bodyFont: {
                            family: 'Inter, sans-serif',
                            size: 12,
                        },
                        displayColors: false,
                        callbacks: {
                            title: (items) => `${items[0].label} WITA`,
                            label: (item) => {
                                const val = Number(item.raw) || 0;
                                let cat = 'Baik';
                                if (val >= 250.5) cat = 'Berbahaya';
                                else if (val >= 150.5) cat = 'Sangat Tidak Sehat';
                                else if (val >= 55.5) cat = 'Tidak Sehat';
                                else if (val >= 15.6) cat = 'Sedang';
                                return [
                                    `Konsentrasi: ${val.toFixed(1)} µg/m³`,
                                    `Kategori Permen LHK: ${cat}`,
                                ];
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.04)',
                            drawBorder: false,
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 10.5,
                            },
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 7,
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false,
                        },
                        ticks: {
                            color: '#64748b',
                            font: {
                                size: 11,
                            },
                            callback: (v) => `${v} µg/m³`,
                        },
                    },
                },
            },
        });

        chartInstanceRef.current = newChart;

        return () => {
            if (chartInstanceRef.current) {
                chartInstanceRef.current.destroy();
                chartInstanceRef.current = null;
            }
        };
    }, [history]);

    // Active Category Theme Info
    const activePalette = useMemo(() => {
        if (!category?.label) return null;
        return ISPU_PALETTE[category.label] || null;
    }, [category]);

    return (
        <PublicLayout title="Kualitas Udara Kalimantan — FireAware">
            <div className="bg-[#F6F5F2] text-[#1E242B] min-h-screen pt-28 sm:pt-32 pb-20">
                {/* 1. BREADCRUMB & PAGE HEADER */}
                <header className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
                    {/* Breadcrumb Navigation */}
                    <nav className="flex items-center gap-1.5 text-xs text-[#1E242B]/60 mb-4 font-medium" aria-label="Breadcrumb">
                        <Link href="/" className="hover:text-[#E05A2B] transition-colors flex items-center gap-1">
                            <span>Beranda</span>
                        </Link>
                        <ChevronRight className="w-3.5 h-3.5 text-[#1E242B]/35" />
                        <span className="text-[#E05A2B] font-bold" aria-current="page">Kualitas Udara</span>
                    </nav>

                    <div className="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-6 border-b border-black/8">
                        <div className="max-w-3xl">
                            {/* Super-title Badge */}
                            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#1B4332]/10 text-[#1B4332] text-[11px] font-bold uppercase tracking-wider mb-3">
                                <Wind className="w-3.5 h-3.5 text-[#1B4332]" />
                                <span>Air Quality Monitoring & Telemetry</span>
                            </div>

                            <h1 className="text-3xl sm:text-4xl lg:text-5xl font-black font-display tracking-tight text-[#1E242B] mb-3">
                                Kualitas Udara Kalimantan
                            </h1>

                            <p className="text-sm sm:text-base text-[#1E242B]/75 leading-relaxed">
                                Pantau kondisi kualitas udara dan informasi partikulat untuk membantu masyarakat memahami kondisi lingkungan di wilayah Kalimantan.
                            </p>
                        </div>

                        {/* Status Pills Row */}
                        <div className="flex flex-wrap items-center gap-2 text-xs font-semibold shrink-0">
                            <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/80 border border-black/8 text-[#1E242B]/80 shadow-xs">
                                <Compass className="w-3.5 h-3.5 text-[#E05A2B]" />
                                <span>5 Provinsi Kalimantan</span>
                            </span>
                            <span className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/80 border border-black/8 text-[#1E242B]/80 shadow-xs">
                                <ShieldCheck className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                <span>Ambang Batas Permen LHK 14/2020</span>
                            </span>
                            <span className={cn(
                                'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border shadow-xs',
                                isConfigured
                                    ? 'bg-[#2A9D8F]/10 border-[#2A9D8F]/30 text-[#2A9D8F]'
                                    : 'bg-[#F4A261]/10 border-[#F4A261]/30 text-[#D97706]'
                            )}>
                                <span className={cn(
                                    'w-2 h-2 rounded-full',
                                    isConfigured ? 'bg-[#2A9D8F] animate-pulse' : 'bg-[#F4A261]'
                                )} />
                                <span>{isConfigured ? 'Sensor Terverifikasi' : 'Integrasi Terbatas'}</span>
                            </span>
                        </div>
                    </div>
                </header>

                {/* 2. FILTER & STATION SELECTOR PANEL */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8" aria-label="Filter Wilayah Pemantauan">
                    <div className="bg-white/90 backdrop-blur-md p-4 sm:p-5 rounded-2xl border border-black/8 shadow-sm">
                        <div className="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                            <div className="flex items-center gap-2.5">
                                <div className="w-9 h-9 rounded-xl bg-[#E05A2B]/10 text-[#E05A2B] flex items-center justify-center shrink-0">
                                    <MapPin className="w-4 h-4 stroke-[2.2]" />
                                </div>
                                <div>
                                    <h2 className="text-sm font-bold text-[#1E242B]">Pilih Wilayah & Stasiun Sensor</h2>
                                    <p className="text-xs text-[#1E242B]/55">
                                        Data hanya ditampilkan dari stasiun fisik yang terdaftar di basis data
                                    </p>
                                </div>
                            </div>

                            <div className="flex flex-wrap items-center gap-3">
                                {/* Dropdown Provinsi */}
                                <div className="flex-1 sm:w-56">
                                    <label htmlFor="aq-province" className="sr-only">Provinsi</label>
                                    <select
                                        id="aq-province"
                                        value={selectedProvSlug}
                                        onChange={handleProvinceChange}
                                        className="w-full text-xs font-semibold px-3 py-2.5 rounded-xl border border-black/15 bg-[#F8F9FA] text-[#1E242B] focus:ring-2 focus:ring-[#E05A2B]/30 focus:border-[#E05A2B] transition-all cursor-pointer"
                                    >
                                        <option value="">Seluruh Kalimantan</option>
                                        {provinces.map((p) => (
                                            <option key={p.id} value={p.slug}>
                                                {p.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                {/* Dropdown Stasiun */}
                                <div className="flex-1 sm:w-64">
                                    <label htmlFor="aq-station" className="sr-only">Stasiun Pemantau</label>
                                    <select
                                        id="aq-station"
                                        value={selectedStationSlug}
                                        onChange={handleStationChange}
                                        disabled={stations.length === 0}
                                        className="w-full text-xs font-semibold px-3 py-2.5 rounded-xl border border-black/15 bg-[#F8F9FA] text-[#1E242B] focus:ring-2 focus:ring-[#E05A2B]/30 focus:border-[#E05A2B] transition-all disabled:opacity-50 cursor-pointer disabled:cursor-not-allowed"
                                    >
                                        {stations.length > 0 ? (
                                            stations.map((s) => (
                                                <option key={s.id} value={s.slug}>
                                                    {s.name} ({s.province?.name || 'Kalimantan'})
                                                </option>
                                            ))
                                        ) : (
                                            <option value="">Belum ada stasiun aktif</option>
                                        )}
                                    </select>
                                </div>

                                {/* Reset Filter Button */}
                                {(selectedProvSlug || selectedStationSlug) && (
                                    <button
                                        type="button"
                                        onClick={handleResetFilter}
                                        className="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-bold text-[#1E242B]/70 bg-black/5 hover:bg-black/10 active:scale-95 transition-all cursor-pointer"
                                        title="Kembalikan ke seluruh Kalimantan"
                                    >
                                        <RotateCcw className="w-3.5 h-3.5" />
                                        <span>Reset</span>
                                    </button>
                                )}
                            </div>
                        </div>
                    </div>
                </section>

                {/* 3. MAIN AIR QUALITY OVERVIEW (Gauge & Core Telemetry Grid) */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12" aria-label="Ringkasan Kualitas Udara Utama">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                        {/* Gauge & Main Measurement Card (7 Cols) */}
                        <div className="lg:col-span-7 flex flex-col">
                            <div className="flex-1 bg-white p-6 sm:p-8 rounded-3xl border border-black/8 shadow-sm flex flex-col justify-between">
                                <div>
                                    <div className="flex items-start justify-between gap-4 mb-4">
                                        <div>
                                            <span className="text-[11px] font-bold text-[#E05A2B] uppercase tracking-wider block mb-1">
                                                Konsentrasi Partikulat Sensor Lapangan
                                            </span>
                                            <h2 className="text-xl sm:text-2xl font-bold font-display text-[#1E242B]">
                                                {activeStation ? activeStation.name : 'Wilayah Kalimantan'}
                                            </h2>
                                            <div className="flex flex-wrap items-center gap-1.5 text-xs text-[#1E242B]/60 mt-1">
                                                <MapPin className="w-3.5 h-3.5 text-[#E05A2B]" />
                                                <span>{activeStation?.province?.name || 'Kalimantan'}</span>
                                                <span className="text-[#1E242B]/30">•</span>
                                                <span>Sumber: {dataSourceName}</span>
                                                <span className="text-[#1E242B]/30">•</span>
                                                <span className="font-semibold text-[#1B4332]">
                                                    {latest?.averaging_period || 'Observasi 1 Jam'}
                                                </span>
                                            </div>
                                        </div>

                                        {/* Category Badge Pill */}
                                        {latest && category ? (
                                            <div className="flex flex-col items-end gap-1 shrink-0">
                                                <span className={cn(
                                                    'px-3.5 py-1.5 rounded-full text-xs font-extrabold border',
                                                    activePalette?.badge || 'bg-black/10 text-[#1E242B]'
                                                )}>
                                                    {category.label}
                                                </span>
                                                <span className="text-[9.5px] text-[#1E242B]/55 font-medium">
                                                    Ambang Permen LHK 14/2020
                                                </span>
                                            </div>
                                        ) : (
                                            <span className="px-3 py-1.5 rounded-full text-xs font-bold bg-black/5 text-[#1E242B]/50 border border-black/10 shrink-0">
                                                Data Belum Ada
                                            </span>
                                        )}
                                    </div>
                                </div>

                                {/* Center Visualization Area */}
                                {latest ? (
                                    <div className="my-6 flex flex-col items-center">
                                        <SemiCircularGauge
                                            value={Number(latest.value)}
                                            categoryLabel={category?.label}
                                        />

                                        {/* Status Statement & Health Advice */}
                                        <div className="mt-5 text-center max-w-md">
                                            <div className="inline-flex items-center gap-1.5 text-xs font-bold text-[#1E242B] mb-1">
                                                <span>Kategori Mutu Udara:</span>
                                                <strong style={{ color: activePalette?.color || '#E05A2B' }}>
                                                    {category?.label} ({category?.risk || 'Status Indikatif'})
                                                </strong>
                                            </div>
                                            <p className="text-xs text-[#1E242B]/75 leading-relaxed">
                                                {category?.advice || 'Pantau perkembangan partikulat udara dan ikuti panduan keselamatan.'}
                                            </p>
                                        </div>
                                    </div>
                                ) : activeStation ? (
                                    /* Empty State: Station exists but has no reading */
                                    <div className="my-10 p-8 rounded-2xl bg-[#F8F9FA] border border-black/8 text-center flex flex-col items-center">
                                        <div className="w-12 h-12 rounded-2xl bg-[#E05A2B]/10 text-[#E05A2B] flex items-center justify-center mb-3">
                                            <Wind className="w-6 h-6 stroke-[1.8]" />
                                        </div>
                                        <h3 className="text-base font-bold text-[#1E242B] mb-1">
                                            Data Pembacaan Belum Tersedia
                                        </h3>
                                        <p className="text-xs text-[#1E242B]/60 max-w-md leading-relaxed mb-4">
                                            Stasiun <strong>{activeStation.name}</strong> belum memiliki rekaman pengukuran partikulat PM2.5 aktif pada jaringan penyedia data.
                                        </p>
                                        <div className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white text-[11px] font-semibold text-[#1E242B]/70 border border-black/8">
                                            <Info className="w-3.5 h-3.5 text-[#E05A2B]" />
                                            <span>FireAware tidak menampilkan angka rekaan atau estimasi buatan</span>
                                        </div>
                                    </div>
                                ) : (
                                    /* Empty State: No stations in selected province */
                                    <div className="my-10 p-8 rounded-2xl bg-[#F8F9FA] border border-black/8 text-center flex flex-col items-center">
                                        <div className="w-12 h-12 rounded-2xl bg-[#1B4332]/10 text-[#1B4332] flex items-center justify-center mb-3">
                                            <Compass className="w-6 h-6 stroke-[1.8]" />
                                        </div>
                                        <h3 className="text-base font-bold text-[#1E242B] mb-1">
                                            Stasiun Pemantau Belum Tersedia
                                        </h3>
                                        <p className="text-xs text-[#1E242B]/60 max-w-md leading-relaxed mb-4">
                                            Belum ada stasiun pemantau resmi yang terhubung di wilayah {activeProvince?.name || 'ini'}.
                                        </p>
                                        <button
                                            type="button"
                                            onClick={handleResetFilter}
                                            className="px-4 py-2 rounded-xl text-xs font-bold bg-[#1E242B] text-white hover:bg-[#E05A2B] transition-colors cursor-pointer"
                                        >
                                            Lihat Seluruh Kalimantan
                                        </button>
                                    </div>
                                )}

                                {/* Bottom Quick Facts Strip - Strict Separation of Metrics */}
                                <div className="pt-4 border-t border-black/8 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                                    <div className="flex flex-col">
                                        <span className="text-[10px] text-[#1E242B]/50 font-medium uppercase">Konsentrasi PM2.5</span>
                                        <span className="font-bold text-[#1E242B]">
                                            {latest ? `${Number(latest.value).toFixed(1)} µg/m³` : '—'}
                                        </span>
                                    </div>
                                    <div className="flex flex-col">
                                        <span className="text-[10px] text-[#1E242B]/50 font-medium uppercase">ISPU Indikatif</span>
                                        <span className="font-bold text-[#1E242B]">
                                            {latest?.aqi_value != null ? `${latest.aqi_value} (Interpolasi)` : '—'}
                                        </span>
                                    </div>
                                    <div className="flex flex-col">
                                        <span className="text-[10px] text-[#1E242B]/50 font-medium uppercase">Waktu Pengukuran</span>
                                        <span className="font-bold text-[#1E242B]">
                                            {latest?.measured_at ? formatWitaDateTime(latest.measured_at) : '—'}
                                        </span>
                                    </div>
                                    <div className="flex flex-col">
                                        <span className="text-[10px] text-[#1E242B]/50 font-medium uppercase">Status Evaluasi</span>
                                        <span className="font-bold text-[#1B4332]">
                                            Permen LHK 14/2020
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Right Column: Historical Trend & Sensor Metadata (5 Cols) */}
                        <div className="lg:col-span-5 flex flex-col gap-6">
                            {/* Historical Trend Card */}
                            <div className="bg-white p-6 rounded-3xl border border-black/8 shadow-sm flex flex-col">
                                <div className="flex items-center justify-between mb-4">
                                    <div className="flex items-center gap-2">
                                        <Activity className="w-4 h-4 text-[#E05A2B]" />
                                        <h3 className="text-base font-bold font-display text-[#1E242B]">
                                            Riwayat & Tren Partikulat
                                        </h3>
                                    </div>
                                    {history?.available && (
                                        <span className="text-[10px] font-bold px-2 py-0.5 rounded-md bg-[#E05A2B]/10 text-[#E05A2B]">
                                            7 Hari Terakhir
                                        </span>
                                    )}
                                </div>

                                {/* Historical Stats Strip if available */}
                                {historyStats && (
                                    <div className="grid grid-cols-3 gap-2 p-2.5 mb-4 rounded-xl bg-[#F8F9FA] border border-black/6 text-center text-xs">
                                        <div>
                                            <span className="text-[10px] text-[#1E242B]/50 block">Terendah:</span>
                                            <span className="font-bold text-[#2A9D8F]">{historyStats.min.toFixed(1)}</span>
                                            <span className="text-[9px] text-[#1E242B]/40 ml-0.5">µg/m³</span>
                                        </div>
                                        <div>
                                            <span className="text-[10px] text-[#1E242B]/50 block">Rata-rata:</span>
                                            <span className="font-bold text-[#1E242B]">{historyStats.avg.toFixed(1)}</span>
                                            <span className="text-[9px] text-[#1E242B]/40 ml-0.5">µg/m³</span>
                                        </div>
                                        <div>
                                            <span className="text-[10px] text-[#1E242B]/50 block">Tertinggi:</span>
                                            <span className="font-bold text-[#E05A2B]">{historyStats.max.toFixed(1)}</span>
                                            <span className="text-[9px] text-[#1E242B]/40 ml-0.5">µg/m³</span>
                                        </div>
                                    </div>
                                )}

                                {/* Chart or Empty State Container */}
                                {history?.available && history.points?.length >= 3 ? (
                                    <div className="h-56 w-full relative">
                                        <canvas ref={chartRef} id="aqTrendChart" />
                                    </div>
                                ) : (
                                    <div className="h-56 flex flex-col items-center justify-center p-6 text-center bg-[#F8F9FA] rounded-2xl border border-black/6">
                                        <Clock className="w-8 h-8 text-[#1E242B]/30 mb-2" />
                                        <h4 className="text-xs font-bold text-[#1E242B]">Riwayat Belum Tersedia</h4>
                                        <p className="text-[11px] text-[#1E242B]/55 max-w-xs mt-1">
                                            Dibutuhkan minimal 3 titik pembacaan berkala untuk menghasilkan grafik tren yang akurat dan dapat dipertanggungjawabkan.
                                        </p>
                                    </div>
                                )}
                            </div>

                            {/* Sensor Station Detail Card */}
                            <div className="bg-white p-6 rounded-3xl border border-black/8 shadow-sm flex flex-col justify-between">
                                <div>
                                    <div className="flex items-center gap-2 mb-3">
                                        <Radio className="w-4 h-4 text-[#1B4332]" />
                                        <h3 className="text-sm font-bold text-[#1E242B]">
                                            Informasi Sensor & Kredibilitas Data
                                        </h3>
                                    </div>

                                    <div className="space-y-2 text-xs">
                                        <div className="flex items-center justify-between py-1.5 border-b border-black/6">
                                            <span className="text-[#1E242B]/60">Penyedia Jaringan:</span>
                                            <a
                                                href={dataSourceUrl}
                                                target="_blank"
                                                rel="noreferrer"
                                                className="font-bold text-[#E05A2B] hover:underline flex items-center gap-1"
                                            >
                                                <span>{dataSourceName} Community</span>
                                                <ExternalLink className="w-3 h-3" />
                                            </a>
                                        </div>
                                        <div className="flex items-center justify-between py-1.5 border-b border-black/6">
                                            <span className="text-[#1E242B]/60">Periode Pengamatan:</span>
                                            <span className="font-semibold text-[#1E242B]">
                                                Observasi 1 Jam (Hourly Sensor)
                                            </span>
                                        </div>
                                        <div className="flex items-center justify-between py-1.5 border-b border-black/6">
                                            <span className="text-[#1E242B]/60">Status API Sinkronisasi:</span>
                                            <span className={cn(
                                                'font-bold px-2 py-0.5 rounded text-[10.5px]',
                                                isConfigured ? 'bg-[#2A9D8F]/15 text-[#2A9D8F]' : 'bg-[#F4A261]/15 text-[#D97706]'
                                            )}>
                                                {isConfigured ? 'Terhubung (API Active)' : 'Menunggu Konfigurasi API'}
                                            </span>
                                        </div>
                                        <div className="flex items-center justify-between py-1.5 border-b border-black/6">
                                            <span className="text-[#1E242B]/60">Pembaruan Sistem:</span>
                                            <span className="font-semibold text-[#1E242B]">
                                                {lastSyncedAt ? formatWitaDateTime(lastSyncedAt) : 'Menunggu sinkronisasi'}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div className="mt-4 p-3 rounded-xl bg-[#1B4332]/5 border border-[#1B4332]/10 text-[11px] text-[#1B4332]/85 leading-relaxed">
                                    <strong>Ketentuan Standar:</strong> Pengukuran seketika sensor OpenAQ diklasifikasikan menggunakan ambang batas Permen LHK 14/2020 untuk tujuan kewaspadaan publik. Klasifikasi ini berstatus indikatif dan bukan pengganti rilis ISPU resmi 24-jam terakreditasi KLHK.
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* 4. REGIONAL AIR QUALITY COMPARISON (5 Kalimantan Provinces) */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-14" aria-label="Kualitas Udara Berdasarkan Wilayah">
                    <div className="mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div>
                            <span className="text-xs font-bold text-[#E05A2B] uppercase tracking-wider block mb-1">
                                Komparasi Wilayah
                            </span>
                            <h2 className="text-2xl sm:text-3xl font-bold font-display text-[#1E242B]">
                                Kualitas Udara 5 Provinsi Kalimantan
                            </h2>
                            <p className="text-xs sm:text-sm text-[#1E242B]/60 mt-1">
                                Klik kartu wilayah untuk memfokuskan data pengukuran dan riwayat stasiun
                            </p>
                        </div>

                        <div className="text-xs text-[#1E242B]/50 italic">
                            * Ketiadaan data pada suatu provinsi tidak mengindikasikan bahwa udara otomatis aman
                        </div>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        {regionalSummary.map((item) => {
                            const isSelected = selectedProvSlug === item.province_slug;
                            const palette = item.category ? ISPU_PALETTE[item.category] : null;

                            return (
                                <div
                                    key={item.province_id}
                                    onClick={() => handleSelectProvinceCard(item.province_slug)}
                                    className={cn(
                                        'bg-white p-5 rounded-2xl border transition-all cursor-pointer flex flex-col justify-between group hover:shadow-md',
                                        isSelected
                                            ? 'border-[#E05A2B] ring-2 ring-[#E05A2B]/20 shadow-sm'
                                            : 'border-black/8 hover:border-black/20'
                                    )}
                                >
                                    <div>
                                        <div className="flex items-center justify-between mb-3">
                                            <span className="text-xs font-bold text-[#1E242B]/50 group-hover:text-[#E05A2B] transition-colors">
                                                {item.province_code || 'BORNEO'}
                                            </span>
                                            <span className={cn(
                                                'w-2 h-2 rounded-full',
                                                item.has_data ? 'bg-[#2A9D8F]' : 'bg-black/20'
                                            )} />
                                        </div>

                                        <h3 className="text-sm font-bold text-[#1E242B] group-hover:text-[#E05A2B] transition-colors mb-2">
                                            {item.province_name}
                                        </h3>

                                        {item.has_data && item.pm25 !== null ? (
                                            <div className="mb-3">
                                                <div className="flex items-baseline gap-1">
                                                    <span className="text-2xl font-black font-display text-[#1E242B]">
                                                        {item.pm25.toFixed(1)}
                                                    </span>
                                                    <span className="text-[10px] text-[#1E242B]/50">µg/m³</span>
                                                </div>
                                                <span className={cn(
                                                    'inline-block px-2 py-0.5 rounded text-[10px] font-extrabold mt-1 border',
                                                    palette?.badge || 'bg-black/5 text-[#1E242B]'
                                                )}>
                                                    {item.category}
                                                </span>
                                            </div>
                                        ) : (
                                            <div className="my-3 py-2 px-2.5 rounded-xl bg-[#F8F9FA] border border-black/6 text-center">
                                                <span className="text-[11px] font-bold text-[#1E242B]/45 block">
                                                    Data belum tersedia
                                                </span>
                                                <span className="text-[9.5px] text-[#1E242B]/40 block mt-0.5">
                                                    {item.has_station ? 'Stasiun offline' : 'Belum ada stasiun'}
                                                </span>
                                            </div>
                                        )}
                                    </div>

                                    <div className="pt-3 border-t border-black/6 flex items-center justify-between text-[10px] text-[#1E242B]/50">
                                        <span className="truncate max-w-[100px]">
                                            {item.station_name || 'Tidak ada sensor'}
                                        </span>
                                        <ChevronRight className="w-3.5 h-3.5 text-[#1E242B]/30 group-hover:text-[#E05A2B] group-hover:translate-x-0.5 transition-all" />
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </section>

                {/* 5. AIR QUALITY STATUS INDICATOR (ISPU Scale Guide) */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-14" aria-label="Spektrum Tingkat Pencemaran ISPU">
                    <div className="bg-white p-6 sm:p-8 rounded-3xl border border-black/8 shadow-sm">
                        <div className="max-w-3xl mb-8">
                            <span className="text-xs font-bold text-[#2A9D8F] uppercase tracking-wider block mb-1">
                                Spektrum Tingkat Pencemaran ISPU
                            </span>
                            <h2 className="text-2xl sm:text-3xl font-bold font-display text-[#1E242B] mb-2">
                                Panduan Kategori Kualitas Udara (ISPU)
                            </h2>
                            <p className="text-xs sm:text-sm text-[#1E242B]/75 leading-relaxed">
                                Ambang batas dan kategori resmi mengacu pada <strong>Peraturan Menteri Lingkungan Hidup dan Kehutanan No. 14 Tahun 2020 Lampiran I</strong> untuk parameter partikulat PM2.5 (rata-rata 24 jam).
                            </p>
                        </div>

                        {/* 5 ISPU Cards Grid with Distinct PM2.5 Concentration and ISPU Index */}
                        <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                            {allCategories.map((cat) => {
                                const palette = ISPU_PALETTE[cat.label] || { color: '#E05A2B', bg: '#f8f9fa' };
                                const isCurrent = category?.label === cat.label;

                                return (
                                    <div
                                        key={cat.key}
                                        className={cn(
                                            'p-4 rounded-2xl border transition-all flex flex-col justify-between',
                                            isCurrent
                                                ? 'bg-white shadow-md ring-2'
                                                : 'bg-[#F8F9FA]/70 border-black/8'
                                        )}
                                        style={{
                                            borderColor: isCurrent ? palette.color : undefined,
                                            boxShadow: isCurrent ? `0 8px 24px -4px ${palette.color}25` : undefined,
                                        }}
                                    >
                                        <div>
                                            <div className="flex items-center justify-between mb-2">
                                                <div className="flex items-center gap-1.5">
                                                    <span
                                                        className="w-3 h-3 rounded-full shrink-0"
                                                        style={{ backgroundColor: palette.color }}
                                                    />
                                                    <span className="text-[11px] font-bold text-[#1E242B]/70">
                                                        {cat.official_color || 'Standar'}
                                                    </span>
                                                </div>
                                                <span className="text-[10px] font-mono font-bold px-1.5 py-0.5 rounded bg-black/5 text-[#1E242B]/70">
                                                    ISPU: {cat.ispu_range}
                                                </span>
                                            </div>

                                            <h3 className="text-base font-bold text-[#1E242B] mb-0.5">
                                                {cat.label}
                                            </h3>

                                            <div className="text-xs font-bold text-[#E05A2B] mb-2 font-mono">
                                                {cat.pm25_range}
                                            </div>

                                            <span
                                                className="inline-block text-[10.5px] font-extrabold px-2 py-0.5 rounded-md mb-2.5"
                                                style={{
                                                    backgroundColor: palette.bg,
                                                    color: palette.color,
                                                }}
                                            >
                                                {cat.risk}
                                            </span>

                                            <p className="text-xs text-[#1E242B]/80 leading-relaxed mb-3">
                                                {cat.simple_desc}
                                            </p>
                                        </div>

                                        <div className="pt-3 border-t border-black/6">
                                            <span className="text-[10.5px] font-bold text-[#1E242B]/50 uppercase tracking-wider block mb-1">
                                                Anjuran Tindakan:
                                            </span>
                                            <p className="text-xs text-[#1E242B]/75 leading-relaxed font-medium">
                                                {cat.advice}
                                            </p>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>

                        {/* Distinction Note: ISPU vs AQI vs PM2.5 */}
                        <div className="p-4 sm:p-5 rounded-2xl bg-[#F6F5F2] border border-black/8 text-xs sm:text-sm text-[#1E242B]/80 flex flex-col sm:flex-row items-start sm:items-center gap-3.5">
                            <div className="w-9 h-9 rounded-xl bg-[#E05A2B]/10 text-[#E05A2B] flex items-center justify-center shrink-0">
                                <HelpCircle className="w-5 h-5 stroke-[2.2]" />
                            </div>
                            <div className="flex-1 leading-relaxed">
                                <strong className="text-[#1E242B] block mb-1 font-bold">
                                    Pemisahan Baku Mutu: Konsentrasi PM2.5, Nilai Indeks ISPU, dan AQI US-EPA
                                </strong>
                                <span className="text-xs sm:text-sm text-[#1E242B]/75 leading-relaxed">
                                    1. <strong>Konsentrasi PM2.5 (µg/m³):</strong> Besaran massa fisik partikulat di udara hasil pembacaan sensor.<br />
                                    2. <strong>Nilai ISPU (Indeks Standar Pencemar Udara):</strong> Angka indeks tanpa satuan (0–500) yang diatur dalam Permen LHK No. 14/2020 dengan acuan agregasi 24 jam.<br />
                                    3. <strong>Nilai AQI (Air Quality Index):</strong> Indeks standar US-EPA yang menggunakan skala dan ambang batas matematis berbeda. FireAware secara ketat memisahkan ketiganya dan tidak mencampuradukkan skala AQI dengan ISPU Indonesia.
                                </span>
                            </div>
                        </div>
                    </div>
                </section>

                {/* 6. HEALTH & SAFETY GUIDANCE */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-14" aria-label="Panduan Perlindungan Diri dan Keluarga">
                    <div className="mb-6">
                        <span className="text-xs font-bold text-[#E05A2B] uppercase tracking-wider block mb-1">
                            Pedoman Keselamatan Masyarakat
                        </span>
                        <h2 className="text-2xl sm:text-3xl font-bold font-display text-[#1E242B]">
                            Panduan Perlindungan Diri &amp; Keluarga
                        </h2>
                        <p className="text-xs sm:text-sm text-[#1E242B]/60 mt-1">
                            Langkah preventif kesehatan saat kabut asap karhutla menyelimuti lingkungan tempat tinggal Anda
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {/* 1. Masker Standar */}
                        <div className="bg-white p-6 rounded-3xl border border-black/8 shadow-sm flex flex-col justify-between">
                            <div>
                                <div className="w-10 h-10 rounded-2xl bg-[#E05A2B]/10 text-[#E05A2B] flex items-center justify-center mb-4">
                                    <ShieldAlert className="w-5 h-5 stroke-[2.2]" />
                                </div>
                                <h3 className="text-base font-bold text-[#1E242B] mb-2">
                                    Gunakan Masker Standar Partikulat
                                </h3>
                                <p className="text-xs sm:text-sm text-[#1E242B]/75 leading-relaxed">
                                    Partikel PM2.5 berdiameter sangat halus (2.5 mikron) dan dapat menembus masker kain biasa. Gunakan respirator bersertifikasi N95, KN95, atau KF94 saat keluar ruangan di tengah kabut asap tebal.
                                </p>
                            </div>
                            <div className="mt-4 pt-3 border-t border-black/6 text-xs font-bold text-[#E05A2B]">
                                Masker kain tidak menyaring partikel PM2.5
                            </div>
                        </div>

                        {/* 2. Kelompok Rentan */}
                        <div className="bg-white p-6 rounded-3xl border border-black/8 shadow-sm flex flex-col justify-between">
                            <div>
                                <div className="w-10 h-10 rounded-2xl bg-[#1B4332]/10 text-[#1B4332] flex items-center justify-center mb-4">
                                    <HeartPulse className="w-5 h-5 stroke-[2.2]" />
                                </div>
                                <h3 className="text-base font-bold text-[#1E242B] mb-2">
                                    Prioritas Proteksi Kelompok Rentan
                                </h3>
                                <p className="text-xs sm:text-sm text-[#1E242B]/75 leading-relaxed">
                                    Anak-anak balita, lansia, ibu hamil, serta individu dengan riwayat asma atau penyakit paru obstruktif kronis (PPOK) wajib membatasi aktivitas di luar rumah ketika kualitas udara mencapai status Tidak Sehat.
                                </p>
                            </div>
                            <div className="mt-4 pt-3 border-t border-black/6 text-xs font-bold text-[#1B4332]">
                                Sediakan obat pelega pernapasan darurat
                            </div>
                        </div>

                        {/* 3. Ruangan Bersih */}
                        <div className="bg-white p-6 rounded-3xl border border-black/8 shadow-sm flex flex-col justify-between">
                            <div>
                                <div className="w-10 h-10 rounded-2xl bg-[#2A9D8F]/10 text-[#2A9D8F] flex items-center justify-center mb-4">
                                    <Building2 className="w-5 h-5 stroke-[2.2]" />
                                </div>
                                <h3 className="text-base font-bold text-[#1E242B] mb-2">
                                    Manajemen Udara Dalam Rumah
                                </h3>
                                <p className="text-xs sm:text-sm text-[#1E242B]/75 leading-relaxed">
                                    Tutup rapat jendela dan celah ventilasi saat asap pekat berhembus. Nyalakan pembersih udara (*air purifier*) berfilter HEPA, dan hindari menyapu kering — gunakan pel basah untuk mengikat partikel debu halus di lantai.
                                </p>
                            </div>
                            <div className="mt-4 pt-3 border-t border-black/6 text-xs font-bold text-[#2A9D8F]">
                                Bersihkan filter AC & purifier berkala
                            </div>
                        </div>

                        {/* 4. Ikuti Posko Resmi */}
                        <div className="bg-white p-6 rounded-3xl border border-black/8 shadow-sm flex flex-col justify-between">
                            <div>
                                <div className="w-10 h-10 rounded-2xl bg-[#F4A261]/15 text-[#D97706] flex items-center justify-center mb-4">
                                    <Radio className="w-5 h-5 stroke-[2.2]" />
                                </div>
                                <h3 className="text-base font-bold text-[#1E242B] mb-2">
                                    Patuhi Peringatan Posko Resmi
                                </h3>
                                <p className="text-xs sm:text-sm text-[#1E242B]/75 leading-relaxed">
                                    Pantau terus pengumuman darurat dari Badan Penanggulangan Bencana Daerah (BPBD), Dinas Kesehatan, dan BMKG terkait status sekolah, pembagian masker gratis, dan evakuasi bila kualitas udara menembus ambang Berbahaya.
                                </p>
                            </div>
                            <div className="mt-4 pt-3 border-t border-black/6 text-xs font-bold text-[#D97706]">
                                Kunjungi puskesmas bila sesak napas akut
                            </div>
                        </div>
                    </div>
                </section>

                {/* 7. AIR QUALITY INFORMATION & ENVIRONMENTAL DYNAMICS */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-14" aria-label="Edukasi Karhutla dan Kualitas Udara">
                    <div className="bg-white rounded-3xl border border-black/8 shadow-sm overflow-hidden grid grid-cols-1 lg:grid-cols-12">
                        {/* Image Column */}
                        <div className="lg:col-span-5 relative min-h-[300px] lg:min-h-full">
                            <img
                                src="/images/smoke-haze.jpg"
                                alt="Kabut asap kebakaran hutan dan lahan gambut di Kalimantan"
                                className="absolute inset-0 w-full h-full object-cover"
                                loading="lazy"
                            />
                            <div className="absolute inset-0 bg-gradient-to-t from-[#1E242B]/80 via-transparent to-transparent lg:hidden" />
                            <div className="absolute bottom-4 left-4 right-4 text-white text-xs lg:hidden font-medium">
                                Asap karhutla gambut membawa partikel PM2.5 melintasi batas wilayah
                            </div>
                        </div>

                        {/* Narrative Content Column */}
                        <div className="lg:col-span-7 p-6 sm:p-10 flex flex-col justify-center">
                            <span className="text-xs font-bold text-[#E05A2B] uppercase tracking-wider block mb-1">
                                Sains Lingkungan Kalimantan
                            </span>
                            <h2 className="text-2xl sm:text-3xl font-bold font-display text-[#1E242B] mb-4">
                                Dinamika Partikel PM2.5 di Kalimantan
                            </h2>

                            <div className="space-y-4 text-xs sm:text-sm text-[#1E242B]/80 leading-relaxed">
                                <p>
                                    Kebakaran di ekosistem lahan gambut Kalimantan terjadi secara pembakaran tanpa lidah api (*smoldering*) di bawah permukaan tanah dengan pasokan oksigen terbatas. Proses pembakaran tidak sempurna ini menghasilkan konsentrasi asap tebal yang sangat kaya akan partikel debu halus <strong>PM2.5</strong> (partikulat dengan diameter 2.5 mikrometer atau lebih kecil).
                                </p>
                                <p>
                                    Karena massanya yang luar biasa ringan, partikel PM2.5 dapat bertahan berminggu-minggu di atmosfer rendah dan terbawa angin muson melintasi batas kabupaten maupun provinsi. Ketika terhirup, partikel ini mampu melewati filter hidung dan tenggorokan, menembus dinding alveolus paru-paru, hingga memasuki peredaran darah.
                                </p>
                            </div>

                            <div className="mt-6 pt-6 border-t border-black/8 flex flex-wrap items-center gap-4 text-xs text-[#1E242B]/60">
                                <span className="inline-flex items-center gap-1.5 font-bold text-[#1B4332]">
                                    <Trees className="w-4 h-4 text-[#1B4332]" />
                                    <span>Konservasi Hutan Gambut</span>
                                </span>
                                <span>•</span>
                                <span className="inline-flex items-center gap-1.5 font-bold text-[#E05A2B]">
                                    <Flame className="w-4 h-4 text-[#E05A2B]" />
                                    <span>Mitigasi Kebakaran Bawah Tanah</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </section>

                {/* 8. DATA SOURCE & TRANSPARENCY DISCLAIMER */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Sumber Data dan Batasan Informasi">
                    <div className="p-6 rounded-2xl bg-[#1E242B] text-white/80 text-xs sm:text-sm leading-relaxed">
                        <div className="flex items-center gap-2 mb-2 text-white font-bold text-sm sm:text-base">
                            <ShieldCheck className="w-4 h-4 text-[#2A9D8F]" />
                            <span>Integritas Sumber Data &amp; Batasan Pemantauan</span>
                        </div>
                        <p className="mb-3 text-white/75 leading-relaxed text-xs sm:text-sm">
                            Data kualitas udara pada halaman ini bersumber dari jaringan stasiun sensor publik <strong>{dataSourceName}</strong> dan diklasifikasikan menggunakan ambang batas <strong>Indeks Standar Pencemar Udara (ISPU)</strong> berdasarkan Peraturan Menteri LHK No. 14 Tahun 2020.
                        </p>
                        <p className="text-white/60 text-xs leading-relaxed">
                            <strong>Pemberitahuan Metodologi:</strong> Kualitas udara di darat sangat dipengaruhi oleh arah dan kecepatan angin, ketinggian lapisan inversi atmosferik, serta jarak sensor dari titik api. Oleh karena itu, kondisi kelayakan udara tidak dapat disimpulkan secara langsung hanya dari hitungan titik panas (*hotspot*) satelit. FireAware menyajikan data apa adanya dari sensor aktif dan tidak pernah melakukan fabrikasi angka pembacaan.
                        </p>
                    </div>
                </section>
            </div>
        </PublicLayout>
    );
}
