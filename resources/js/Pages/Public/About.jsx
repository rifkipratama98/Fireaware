import React, { useState } from 'react';
import { Link } from '@inertiajs/react';
import { motion } from 'motion/react';
import { PublicLayout } from '@/Layouts/PublicLayout';
import { ImageWithFallback } from '@/Components/ImageWithFallback';
import {
    Trees,
    Satellite,
    ShieldCheck,
    MapPin,
    Radio,
    Wind,
    Droplets,
    Flame,
    ArrowRight,
    CheckCircle2,
    Compass,
    Building2,
    Globe2,
    ShieldAlert,
    Info,
    BookOpen,
    Sparkles,
    ExternalLink,
    Layers,
    Eye,
    ChevronRight,
} from 'lucide-react';

export default function About({ provinces = [] }) {
    const [activeProvinceTab, setActiveProvinceTab] = useState(
        provinces[0]?.slug || 'kalimantan-barat'
    );

    const provinceDetails = {
        'kalimantan-barat': {
            code: 'KALBAR',
            name: 'Kalimantan Barat',
            capital: 'Pontianak',
            highlight: 'Bentang kubah gambut pesisir luas, koridor perbatasan darat, dan ekosistem rawa gambut terluas di barat Borneo.',
            characteristics: 'Kawasan rawan kebakaran lahan perkebunan dan permukiman di sepanjang garis pesisir dan dataran rendah.',
            latLng: '-0.12, 110.75',
            icon: Droplets,
        },
        'kalimantan-tengah': {
            code: 'KALTENG',
            name: 'Kalimantan Tengah',
            capital: 'Palangka Raya',
            highlight: 'Pusat kubah gambut terdalam Indonesia (hingga > 7 meter), bentang hidrologis Sungai Kahayan, dan zona prioritas restorasi sekat kanal BRGM.',
            characteristics: 'Sangat rentan terhadap kebakaran bawah tanah (ground fire) dan kabut asap tebal pekat berhari-hari saat kemarau panjang.',
            latLng: '-1.50, 113.50',
            icon: Flame,
        },
        'kalimantan-selatan': {
            code: 'KALSEL',
            name: 'Kalimantan Selatan',
            capital: 'Banjarbaru',
            highlight: 'Dataran aluvial rawa pasang surut, zona penyangga bandara Syamsudin Noor, dan jalur poros transportasi padat penduduk.',
            characteristics: 'Kerentanan tinggi terhadap kabut asap permukaan yang mengganggu jarak pandang penerbangan dan jalur logistik darat.',
            latLng: '-2.90, 115.35',
            icon: Building2,
        },
        'kalimantan-timur': {
            code: 'KALTIM',
            name: 'Kalimantan Timur',
            capital: 'Samarinda',
            highlight: 'Bentang kanopi hutan hujan tropis perbukitan karst, DAS Mahakam, dan kawasan penyangga Ibu Kota Nusantara (IKN).',
            characteristics: 'Potensi karhutla di perbatasan konsesi dan semak belukar kering yang memerlukan pemantauan intensif.',
            latLng: '0.50, 116.50',
            icon: Trees,
        },
        'kalimantan-utara': {
            code: 'KALTARA',
            name: 'Kalimantan Utara',
            capital: 'Tanjung Selor',
            highlight: 'Bentang kanopi hutan primer dataran tinggi, kawasan konservasi Kayan Mentarang, dan koridor perbatasan negara bagian Sabah.',
            characteristics: 'Kerapatan tutupan hutan lebat yang menjadi benteng pertahanan iklim dan cadangan biodiversitas penting dunia.',
            latLng: '2.90, 116.00',
            icon: Compass,
        },
    };

    const currentProvince = provinceDetails[activeProvinceTab] || provinceDetails['kalimantan-barat'];

    const coreFeatures = [
        {
            title: 'Peta Hotspot Satelit Interaktif',
            desc: 'Visualisasi geospatial titik panas NASA FIRMS berbasis sensor VIIRS & MODIS dengan marker clustering dan analisis tingkat kepercayaan.',
            href: '/peta-hotspot',
            badge: 'Geospatial Telemetry',
            icon: Satellite,
            color: 'from-[#E05A2B] to-[#D94E34]',
        },
        {
            title: 'Pemantauan Kualitas Udara PM2.5',
            desc: 'Pengukuran konsentrasi partikulat halus mikroskopis dan kategorisasi ISPU resmi berdasarkan Permen LHK No. 14 Tahun 2020.',
            href: '/kualitas-udara',
            badge: 'Air Quality & ISPU',
            icon: Wind,
            color: 'from-[#2A9D8F] to-[#1B4332]',
        },
        {
            title: 'Informasi Kewaspadaan & Peringatan',
            desc: 'Portal pengumuman siaga karhutla, rilis kondisi cuaca ekstrem BMKG, dan buletin kesiapsiagaan darurat bencana asap daerah.',
            href: '/pengumuman',
            badge: 'Early Warning Center',
            icon: Radio,
            color: 'from-[#F4A261] to-[#E05A2B]',
        },
        {
            title: 'Pusat Edukasi Sains Karhutla',
            desc: 'Infografis kedalaman kubah gambut, mekanisme perambatan bara bawah tanah, penepis mitos vs fakta, dan kuis pemahaman mandiri.',
            href: '/edukasi',
            badge: 'Science Education',
            icon: BookOpen,
            color: 'from-[#1B4332] to-[#52B788]',
        },
        {
            title: 'Panduan Keselamatan & Mitigasi',
            desc: 'Protokol tindakan tiga fase, blueprint ruang bersih (clean room), checklist kesiapsiagaan keluarga, dan direktori nomor darurat 112.',
            href: '/panduan',
            badge: 'Public Safety Protocol',
            icon: ShieldCheck,
            color: 'from-[#E05A2B] to-[#F4A261]',
        },
    ];

    const workflowSteps = [
        {
            num: '01',
            title: 'Pengumpulan Data',
            tag: 'Data Ingestion',
            desc: 'Sistem menyerap telemetri citra satelit NASA FIRMS (sensor VIIRS 375m & MODIS 1km), data kualitas udara OpenAQ, serta buletin peringatan BMKG secara berkala.',
            icon: Satellite,
        },
        {
            num: '02',
            title: 'Pengolahan & Validasi',
            tag: 'Normalization',
            desc: 'Data anomali termal diverifikasi batas koordinat WGS84 5 provinsi Kalimantan, dihitung confidence level, dan distandarisasi ke baku mutu ISPU Permen LHK No. 14/2020.',
            icon: Layers,
        },
        {
            num: '03',
            title: 'Penyajian Informasi',
            tag: 'Public Visualization',
            desc: 'Disajikan melalui peta interaktif dengan klaster geospatial, grafik tren PM2.5, serta ringkasan wilayah yang dirancang transparan dan mudah dipahami warga.',
            icon: Eye,
        },
        {
            num: '04',
            title: 'Edukasi & Mitigasi',
            tag: 'Community Empowerment',
            desc: 'Menghubungkan data teknis dengan rekomendasi perlindungan nyata: panduan penggunaan masker N95, kesiapsiagaan clean room, serta kanal pelaporan resmi tanpa mendekati api.',
            icon: ShieldCheck,
        },
    ];

    return (
        <PublicLayout title="Tentang FireAware Kalimantan — Platform Informasi & Pemantauan Lingkungan">
            {/* =========================================================================
                1. HERO SECTION — IDENTITAS FIREAWARE (Cinematic Deep Pine & Rainforest)
               ========================================================================= */}
            <section className="relative overflow-hidden bg-[#1C2526] text-white pt-28 sm:pt-36 pb-20 sm:pb-28 border-b border-white/10">
                {/* Background Photography with Controlled Multi-Layered Overlays */}
                <div className="absolute inset-0 z-0">
                    <ImageWithFallback
                        src="/images/hero-kalimantan-rainforest.jpg"
                        alt="Bentang Kanopi Hutan Hujan Tropis Kalimantan"
                        fallbackText="Hutan Hujan Tropis Kalimantan"
                        className="w-full h-full object-cover object-[center_35%] brightness-[0.35] contrast-105 select-none pointer-events-none"
                    />
                    <div className="absolute inset-0 bg-gradient-to-t from-[#1C2526] via-[#1C2526]/75 to-transparent" />
                    <div className="absolute inset-0 bg-gradient-to-r from-[#1C2526]/95 via-[#1B4332]/85 to-transparent" />
                </div>

                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    {/* Breadcrumbs */}
                    <nav aria-label="Breadcrumb" className="mb-6 flex items-center gap-2 text-xs text-[#F8F9FA]/70 font-medium">
                        <Link href="/" className="hover:text-white transition-colors">
                            Beranda
                        </Link>
                        <ChevronRight className="w-3.5 h-3.5 text-white/40" />
                        <span className="text-[#52B788] font-semibold">Tentang FireAware</span>
                    </nav>

                    <div className="max-w-3xl">
                        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/[0.08] border border-white/15 backdrop-blur-md text-xs font-semibold text-[#52B788] mb-4 shadow-sm">
                            <Trees className="w-3.5 h-3.5 text-[#52B788]" />
                            <span className="tracking-widest uppercase font-bold text-[11px]">
                                Environmental Technology &amp; Forest Conservation
                            </span>
                            <span className="w-1 h-1 rounded-full bg-white/30" />
                            <span className="text-white/80">Kalimantan</span>
                        </div>

                        <h1 className="font-display font-black text-3xl sm:text-5xl lg:text-6xl text-white tracking-tight leading-[1.12] mb-5">
                            Mengenal <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#52B788] via-[#F4A261] to-[#F8F9FA]">FireAware Kalimantan.</span>
                        </h1>

                        <p className="text-sm sm:text-base md:text-lg text-[#F8F9FA]/85 font-normal leading-relaxed mb-8 max-w-2xl">
                            Teknologi informasi dan pemantauan lingkungan untuk meningkatkan kesadaran masyarakat terhadap kebakaran hutan dan lahan gambut di seluruh bentang alam Borneo.
                        </p>

                        {/* Telemetry Highlights */}
                        <div className="flex flex-wrap gap-2.5 sm:gap-3">
                            <div className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-white/[0.08] backdrop-blur-md border border-white/15 text-[#F8F9FA]">
                                <MapPin className="w-3.5 h-3.5 text-[#F4A261]" />
                                <span>5 Provinsi Cakupan Penuh</span>
                            </div>
                            <div className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-white/[0.08] backdrop-blur-md border border-white/15 text-[#F8F9FA]">
                                <Satellite className="w-3.5 h-3.5 text-[#52B788]" />
                                <span>Telemetri Satelit NASA FIRMS</span>
                            </div>
                            <div className="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-white/[0.08] backdrop-blur-md border border-white/15 text-[#F8F9FA]">
                                <ShieldCheck className="w-3.5 h-3.5 text-[#E05A2B]" />
                                <span>Baku Mutu ISPU Permen LHK 14/2020</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* =========================================================================
                2. TENTANG FIREAWARE (Platform Identity & Honest Positioning)
               ========================================================================= */}
            <section className="py-16 sm:py-24 bg-white border-b border-stone-200/80">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                        <div className="lg:col-span-7">
                            <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1B4332]/10 text-[#1B4332] text-xs font-bold uppercase tracking-wider mb-3">
                                <Info className="w-3.5 h-3.5 text-[#52B788]" />
                                <span>Identitas &amp; Komitmen Platform</span>
                            </div>

                            <h2 className="font-display font-black text-2xl sm:text-4xl text-[#1E242B] tracking-tight leading-snug mb-5">
                                Apa Itu FireAware Kalimantan?
                            </h2>

                            <p className="text-sm sm:text-base text-stone-700 leading-relaxed font-normal mb-4">
                                <strong>FireAware Kalimantan</strong> adalah platform informasi dan pemantauan lingkungan publik yang menyatukan data deteksi anomali termal satelit, pengukuran kualitas udara partikulat, dan warta kewaspadaan karhutla dalam satu antarmuka yang bersih, mudah dipahami, serta dapat diakses bebas oleh seluruh masyarakat.
                            </p>

                            <p className="text-sm sm:text-base text-stone-700 leading-relaxed font-normal mb-6">
                                Platform ini menghubungkan sains satelit beresolusi tinggi dengan kebutuhan mitigasi sehari-hari: membantu warga mengenali risiko paparan asap, memahami perlindungan pernapasan yang tepat, dan mengetahui kanal pelaporan resmi tanpa melakukan tindakan berisiko.
                            </p>

                            {/* Honest Positioning Statement Box */}
                            <div className="p-4 sm:p-5 rounded-2xl bg-stone-50 border border-stone-200 text-xs sm:text-sm text-stone-700 leading-relaxed mb-6">
                                <strong className="text-[#1E242B] block mb-1 font-bold">
                                    Media Informasi Publik, Bukan Sistem Prediksi Cuaca
                                </strong>
                                <p className="text-stone-600">
                                    FireAware beroperasi murni sebagai media informasi dan pemantauan berbasis data faktual sensor. Sistem ini <strong>bukan sistem prediksi kebakaran masa depan</strong> dan <strong>tidak menggantikan instruksi maupun peringatan resmi instansi berwenang</strong> (seperti BPBD, KLHK Manggala Agni, atau BMKG).
                                </p>
                            </div>

                            <div className="flex flex-wrap gap-3">
                                <Link
                                    href="/peta-hotspot"
                                    className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#1B4332] text-white font-bold text-xs uppercase tracking-wider hover:bg-[#2D6A4F] transition-all shadow-md active:scale-95"
                                >
                                    <Satellite className="w-4 h-4" />
                                    <span>Buka Peta Hotspot</span>
                                </Link>
                                <Link
                                    href="/panduan"
                                    className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold text-xs tracking-wider transition-all"
                                >
                                    <ShieldCheck className="w-4 h-4 text-[#E05A2B]" />
                                    <span>Panduan Keselamatan</span>
                                </Link>
                            </div>
                        </div>

                        <div className="lg:col-span-5">
                            <div className="p-6 sm:p-8 rounded-3xl bg-[#F6F5F2] border border-stone-200/80 shadow-sm space-y-4">
                                <div className="flex items-center gap-3 pb-4 border-b border-stone-200">
                                    <div className="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#E05A2B] to-[#D94E34] text-white flex items-center justify-center shadow-md">
                                        <Compass className="w-6 h-6" />
                                    </div>
                                    <div>
                                        <h3 className="font-bold text-base text-[#1E242B]">
                                            5 Pilar Utama FireAware
                                        </h3>
                                        <span className="text-xs text-stone-500">
                                            Layanan keterbukaan data lingkungan
                                        </span>
                                    </div>
                                </div>

                                <ul className="space-y-3.5 text-xs text-stone-700">
                                    <li className="flex items-start gap-2.5">
                                        <CheckCircle2 className="w-4 h-4 text-[#2A9D8F] shrink-0 mt-0.5" />
                                        <span><strong>Akses Hotspot Faktual:</strong> Menampilkan anomali suhu sensor satelit tanpa estimasi spekulatif.</span>
                                    </li>
                                    <li className="flex items-start gap-2.5">
                                        <CheckCircle2 className="w-4 h-4 text-[#2A9D8F] shrink-0 mt-0.5" />
                                        <span><strong>Kualitas Udara Partikulat:</strong> Visualisasi konsentrasi PM2.5 aktual dan rentang ISPU resmi.</span>
                                    </li>
                                    <li className="flex items-start gap-2.5">
                                        <CheckCircle2 className="w-4 h-4 text-[#2A9D8F] shrink-0 mt-0.5" />
                                        <span><strong>Kewaspadaan Terverifikasi:</strong> Menyampaikan buletin siaga dari otoritas kebencanaan daerah.</span>
                                    </li>
                                    <li className="flex items-start gap-2.5">
                                        <CheckCircle2 className="w-4 h-4 text-[#2A9D8F] shrink-0 mt-0.5" />
                                        <span><strong>Literasi Ekologi Gambut:</strong> Mengedukasi mekanisme ground fire dan sains restorasi 3R.</span>
                                    </li>
                                    <li className="flex items-start gap-2.5">
                                        <CheckCircle2 className="w-4 h-4 text-[#2A9D8F] shrink-0 mt-0.5" />
                                        <span><strong>Protokol Keselamatan Mandiri:</strong> Langkah mitigasi praktis bagi keluarga dan kelompok rentan.</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* =========================================================================
                3. LATAR BELAKANG & PERMASALAHAN (Storytelling Karhutla Kalimantan)
               ========================================================================= */}
            <section className="py-16 sm:py-24 bg-[#F8F9FA] border-b border-stone-200/80">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="max-w-3xl mb-12">
                        <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#E05A2B]/10 text-[#E05A2B] text-xs font-bold uppercase tracking-wider mb-2">
                            <Flame className="w-3.5 h-3.5" />
                            <span>Tantangan Ekologis &amp; Latar Belakang</span>
                        </div>
                        <h2 className="font-display font-black text-2xl sm:text-4xl text-[#1E242B] tracking-tight leading-tight mb-3">
                            Mengapa Kalimantan Memerlukan FireAware?
                        </h2>
                        <p className="text-xs sm:text-sm text-stone-600 leading-relaxed">
                            Kebakaran hutan dan lahan di Pulau Borneo memiliki karakteristik tanah gambut yang unik dan menantang, membutuhkan keterbukaan informasi yang transparan dan dapat dipahami seluruh lapisan masyarakat.
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                        <div className="p-6 rounded-3xl bg-white border border-stone-200/80 shadow-xs flex flex-col justify-between">
                            <div>
                                <div className="w-12 h-12 rounded-2xl bg-[#E05A2B]/10 text-[#E05A2B] flex items-center justify-center mb-4">
                                    <Flame className="w-6 h-6 stroke-[1.8]" />
                                </div>
                                <h3 className="text-base font-bold text-[#1E242B] font-display mb-2">
                                    Dilema Bara Api Bawah Tanah (Ground Fire)
                                </h3>
                                <p className="text-xs text-stone-600 leading-relaxed">
                                    Saat kubah gambut mengering melewati batas kritis -40 cm, bara api merambat di kedalaman 50 cm hingga beberapa meter di bawah permukaan tanpa nyala api terbuka. Pembakaran bawah tanah ini sangat sulit dipadamkan dan melepaskan emisi karbon masif.
                                </p>
                            </div>
                        </div>

                        <div className="p-6 rounded-3xl bg-white border border-stone-200/80 shadow-xs flex flex-col justify-between">
                            <div>
                                <div className="w-12 h-12 rounded-2xl bg-[#2A9D8F]/10 text-[#2A9D8F] flex items-center justify-center mb-4">
                                    <Wind className="w-6 h-6 stroke-[1.8]" />
                                </div>
                                <h3 className="text-base font-bold text-[#1E242B] font-display mb-2">
                                    Ancaman Partikel Mikroskopis PM2.5
                                </h3>
                                <p className="text-xs text-stone-600 leading-relaxed">
                                    Pembakaran tidak sempurna (*incomplete combustion*) menghasilkan aerosol pekat kaya partikel PM2.5 yang dapat melayang puluhan kilometer, menembus paru-paru terdalam, dan memicu krisis kesehatan pernapasan akut bagi kelompok rentan.
                                </p>
                            </div>
                        </div>

                        <div className="p-6 rounded-3xl bg-white border border-stone-200/80 shadow-xs flex flex-col justify-between">
                            <div>
                                <div className="w-12 h-12 rounded-2xl bg-[#1B4332]/10 text-[#1B4332] flex items-center justify-center mb-4">
                                    <Radio className="w-6 h-6 stroke-[1.8]" />
                                </div>
                                <h3 className="text-base font-bold text-[#1E242B] font-display mb-2">
                                    Fragmentasi Data Lingkungan
                                </h3>
                                <p className="text-xs text-stone-600 leading-relaxed">
                                    Sebelumnya, data citra satelit, stasiun ISPU, dan rilis kebencanaan tersebar di berbagai portal terpisah dengan istilah teknis yang rumit. Masyarakat membutuhkan kanal satu pintu yang menyajikan data secara kontekstual dan komunikatif.
                                </p>
                            </div>
                        </div>
                    </div>

                    {/* Dual Documentary Photos */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div className="rounded-3xl overflow-hidden border border-stone-200/80 bg-white shadow-xs group">
                            <div className="relative h-60 sm:h-72 overflow-hidden bg-stone-900">
                                <ImageWithFallback
                                    src="/images/peatland-gambut.jpg"
                                    alt="Bentang alam lahan gambut tropis Kalimantan"
                                    fallbackText="Lahan Gambut Tropis Kalimantan"
                                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                                />
                                <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none" />
                            </div>
                            <div className="p-4 sm:p-5 text-xs text-stone-700 border-t border-stone-100 flex items-start gap-2.5">
                                <span className="font-bold text-[#1B4332] flex-shrink-0">Ekosistem Kubah Gambut:</span>
                                <span>Menyimpan cadangan air tawar raksasa dan karbon bumi yang sangat vital bila kelembapannya tetap terjaga.</span>
                            </div>
                        </div>

                        <div className="rounded-3xl overflow-hidden border border-stone-200/80 bg-white shadow-xs group">
                            <div className="relative h-60 sm:h-72 overflow-hidden bg-stone-900">
                                <ImageWithFallback
                                    src="/images/smoke-haze.jpg"
                                    alt="Kondisi kabut asap karhutla pekat di Kalimantan"
                                    fallbackText="Dampak Kabut Asap Karhutla"
                                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                                />
                                <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none" />
                            </div>
                            <div className="p-4 sm:p-5 text-xs text-stone-700 border-t border-stone-100 flex items-start gap-2.5">
                                <span className="font-bold text-[#E05A2B] flex-shrink-0">Mitigasi Asap:</span>
                                <span>Kewaspadaan dini memampukan keluarga mempersiapkan respirator standar N95 dan menyekat ruang aman sebelum asap masuk.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* =========================================================================
                4. VISI DAN MISI (Editorial Deep Pine Layout)
               ========================================================================= */}
            <section className="py-16 sm:py-24 bg-gradient-to-b from-[#1C2526] via-[#1B4332] to-[#12161A] text-white border-b border-white/10">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                        {/* Vision Column */}
                        <div className="lg:col-span-5">
                            <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-[#52B788] text-xs font-bold uppercase tracking-wider mb-3 border border-white/10">
                                <Sparkles className="w-3.5 h-3.5" />
                                <span>Arah &amp; Cita-Cita Platform</span>
                            </div>

                            <h2 className="font-display font-black text-2xl sm:text-4xl text-white tracking-tight leading-tight mb-5">
                                Visi Kami
                            </h2>

                            <div className="p-6 sm:p-8 rounded-3xl bg-white/[0.06] border border-white/15 backdrop-blur-md shadow-xl mb-6">
                                <p className="text-base sm:text-lg text-white font-medium leading-relaxed italic">
                                    &ldquo;Menjadi platform informasi lingkungan yang mendukung kesadaran masyarakat dan upaya pencegahan kebakaran hutan dan lahan di Kalimantan.&rdquo;
                                </p>
                            </div>

                            <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed font-normal">
                                Kami berkomitmen menghadirkan keterbukaan informasi yang berkeadilan, menjangkau seluruh lapisan masyarakat mulai dari petani pedesaan, relawan satgas, hingga pengambil kebijakan.
                            </p>
                        </div>

                        {/* Mission Column */}
                        <div className="lg:col-span-7 space-y-3.5">
                            <h2 className="font-display font-black text-2xl sm:text-4xl text-white tracking-tight leading-tight mb-5">
                                Misi Kami
                            </h2>

                            <div className="p-4 sm:p-5 rounded-2xl bg-white/[0.05] border border-white/10 flex items-start gap-4">
                                <div className="w-8 h-8 rounded-xl bg-[#52B788]/20 text-[#52B788] flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                                    1
                                </div>
                                <div className="text-xs sm:text-sm">
                                    <strong className="text-white block font-bold mb-1">
                                        Menyediakan Akses Informasi Hotspot Terpercaya
                                    </strong>
                                    <p className="text-[#F8F9FA]/80 leading-relaxed font-normal">
                                        Menyajikan data deteksi anomali termal langsung dari satelit NASA FIRMS (VIIRS 375m &amp; MODIS 1km) dengan visualisasi geospatial interaktif dan akurat.
                                    </p>
                                </div>
                            </div>

                            <div className="p-4 sm:p-5 rounded-2xl bg-white/[0.05] border border-white/10 flex items-start gap-4">
                                <div className="w-8 h-8 rounded-xl bg-[#52B788]/20 text-[#52B788] flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                                    2
                                </div>
                                <div className="text-xs sm:text-sm">
                                    <strong className="text-white block font-bold mb-1">
                                        Membantu Pemahaman Kualitas Udara Faktual
                                    </strong>
                                    <p className="text-[#F8F9FA]/80 leading-relaxed font-normal">
                                        Menerjemahkan konsentrasi polutan mikroskopis PM2.5 ke dalam rentang baku mutu ISPU resmi Permen LHK No. 14 Tahun 2020 tanpa rekayasa data.
                                    </p>
                                </div>
                            </div>

                            <div className="p-4 sm:p-5 rounded-2xl bg-white/[0.05] border border-white/10 flex items-start gap-4">
                                <div className="w-8 h-8 rounded-xl bg-[#52B788]/20 text-[#52B788] flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                                    3
                                </div>
                                <div className="text-xs sm:text-sm">
                                    <strong className="text-white block font-bold mb-1">
                                        Menyampaikan Informasi Kewaspadaan Resmi
                                    </strong>
                                    <p className="text-[#F8F9FA]/80 leading-relaxed font-normal">
                                        Meneruskan rilis peringatan dini cuaca ekstrem dari BMKG dan penetapan status siaga darurat BPBD daerah secara cepat dan tepat sasaran.
                                    </p>
                                </div>
                            </div>

                            <div className="p-4 sm:p-5 rounded-2xl bg-white/[0.05] border border-white/10 flex items-start gap-4">
                                <div className="w-8 h-8 rounded-xl bg-[#52B788]/20 text-[#52B788] flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                                    4
                                </div>
                                <div className="text-xs sm:text-sm">
                                    <strong className="text-white block font-bold mb-1">
                                        Meningkatkan Edukasi Pencegahan &amp; Hukum Karhutla
                                    </strong>
                                    <p className="text-[#F8F9FA]/80 leading-relaxed font-normal">
                                        Menyebarluaskan pemahaman hukum larangan pembakaran lahan UU No. 32/2009 serta protokol perlindungan kesehatan keluarga.
                                    </p>
                                </div>
                            </div>

                            <div className="p-4 sm:p-5 rounded-2xl bg-white/[0.05] border border-white/10 flex items-start gap-4">
                                <div className="w-8 h-8 rounded-xl bg-[#52B788]/20 text-[#52B788] flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                                    5
                                </div>
                                <div className="text-xs sm:text-sm">
                                    <strong className="text-white block font-bold mb-1">
                                        Mendukung Perlindungan &amp; Restorasi Lahan Gambut
                                    </strong>
                                    <p className="text-[#F8F9FA]/80 leading-relaxed font-normal">
                                        Mendorong kesadaran masyarakat dalam menjaga tata air kubah gambut (muka air &ge; -40 cm) dan mendukung program sekat kanal BRGM.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* =========================================================================
                5. CAKUPAN WILAYAH PEMANTAUAN (5 Provinsi Kalimantan)
               ========================================================================= */}
            <section className="py-16 sm:py-24 bg-white border-b border-stone-200/80">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="max-w-3xl mb-12">
                        <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1B4332]/10 text-[#1B4332] text-xs font-bold uppercase tracking-wider mb-2">
                            <MapPin className="w-3.5 h-3.5 text-[#52B788]" />
                            <span>Cakupan Wilayah Geografis</span>
                        </div>
                        <h2 className="font-display font-black text-2xl sm:text-4xl text-[#1E242B] tracking-tight leading-tight mb-3">
                            Pemantauan Terpadu 5 Provinsi Kalimantan
                        </h2>
                        <p className="text-xs sm:text-sm text-stone-600 leading-relaxed">
                            FireAware memetakan telemetri satelit di seluruh daratan Pulau Borneo seluas lebih dari 544.000 km² yang mencakup 5 provinsi pemantauan resmi.
                        </p>
                    </div>

                    {/* Province Selector Pills */}
                    <div className="flex items-center gap-2 overflow-x-auto pb-4 mb-8 scrollbar-none">
                        {provinces.map((prov) => {
                            const isActive = activeProvinceTab === prov.slug;
                            const detail = provinceDetails[prov.slug];
                            return (
                                <button
                                    key={prov.id}
                                    type="button"
                                    onClick={() => setActiveProvinceTab(prov.slug)}
                                    className={`px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 flex-shrink-0 cursor-pointer ${
                                        isActive
                                            ? 'bg-[#1B4332] text-white shadow-sm ring-1 ring-[#1B4332]'
                                            : 'bg-stone-100 hover:bg-stone-200/80 text-stone-700'
                                    }`}
                                >
                                    <span>{prov.name}</span>
                                    {detail?.code && (
                                        <span className={`text-[10px] px-2 py-0.5 rounded-full font-bold ${
                                            isActive ? 'bg-white/20 text-white' : 'bg-stone-200 text-stone-600'
                                        }`}>
                                            {detail.code}
                                        </span>
                                    )}
                                </button>
                            );
                        })}
                    </div>

                    {/* Active Province Detail Card */}
                    <div className="p-6 sm:p-10 rounded-3xl bg-[#F6F5F2] border border-stone-200/80 shadow-xs mb-8">
                        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                            <div className="lg:col-span-8">
                                <div className="flex items-center gap-2 mb-2">
                                    <span className="text-xs font-bold px-2.5 py-0.5 rounded-md bg-[#2B303A] text-white tracking-wider">
                                        {currentProvince.code}
                                    </span>
                                    <span className="text-xs text-stone-500">Ibukota: {currentProvince.capital}</span>
                                    <span className="text-xs text-stone-400">•</span>
                                    <span className="text-xs font-mono text-stone-500">Koordinat: {currentProvince.latLng}</span>
                                </div>

                                <h3 className="font-display font-extrabold text-xl sm:text-2xl text-[#1E242B] mb-3">
                                    {currentProvince.name}
                                </h3>

                                <p className="text-xs sm:text-sm text-stone-700 leading-relaxed font-normal mb-3">
                                    {currentProvince.highlight}
                                </p>

                                <p className="text-xs sm:text-sm text-stone-600 leading-relaxed font-normal mb-6">
                                    <strong className="text-stone-800">Karakteristik Kerentanan:</strong> {currentProvince.characteristics}
                                </p>

                                <Link
                                    href={`/peta-hotspot?provinsi=${activeProvinceTab}`}
                                    className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#E05A2B] hover:bg-[#D94E34] text-white font-bold text-xs uppercase tracking-wider transition-all shadow-md active:scale-95 cursor-pointer"
                                >
                                    <span>Pantau Hotspot {currentProvince.name}</span>
                                    <ArrowRight className="w-4 h-4" />
                                </Link>
                            </div>

                            <div className="lg:col-span-4 bg-white rounded-2xl p-5 border border-stone-200">
                                <div className="flex items-center gap-2 text-xs font-bold text-stone-600 mb-3 pb-2 border-b border-stone-100">
                                    <Globe2 className="w-4 h-4 text-[#1B4332]" />
                                    <span>Spesifikasi Geografis</span>
                                </div>
                                <ul className="space-y-2.5 text-xs text-stone-600">
                                    <li className="flex justify-between">
                                        <span>Sistem Koordinat:</span>
                                        <strong className="text-stone-800">WGS84 / EPSG:4326</strong>
                                    </li>
                                    <li className="flex justify-between">
                                        <span>Sumber Peta Satelit:</span>
                                        <strong className="text-stone-800">NASA FIRMS 375m</strong>
                                    </li>
                                    <li className="flex justify-between">
                                        <span>Validasi Batas:</span>
                                        <strong className="text-[#2A9D8F]">Batas Resmi Kemendagri</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* =========================================================================
                6. BAGAIMANA FIREAWARE BEKERJA (Alur 4 Tahap Visual / Workflow)
               ========================================================================= */}
            <section className="py-16 sm:py-24 bg-[#F8F9FA] border-b border-stone-200/80">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="max-w-3xl mb-12">
                        <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1B4332]/10 text-[#1B4332] text-xs font-bold uppercase tracking-wider mb-2">
                            <Layers className="w-3.5 h-3.5 text-[#52B788]" />
                            <span>Arsitektur &amp; Mekanisme Sistem</span>
                        </div>
                        <h2 className="font-display font-black text-2xl sm:text-4xl text-[#1E242B] tracking-tight leading-tight mb-3">
                            Bagaimana FireAware Bekerja?
                        </h2>
                        <p className="text-xs sm:text-sm text-stone-600 leading-relaxed">
                            Alur pemrosesan data transparan dari orbit satelit hingga menjadi rekomendasi keselamatan konkret yang dapat diaplikasikan masyarakat.
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        {workflowSteps.map((step) => {
                            const StepIcon = step.icon;
                            return (
                                <div
                                    key={step.num}
                                    className="p-6 rounded-3xl bg-white border border-stone-200/80 shadow-xs flex flex-col justify-between"
                                >
                                    <div>
                                        <div className="flex items-center justify-between mb-4">
                                            <span className="font-display font-black text-2xl text-[#1B4332]">
                                                {step.num}
                                            </span>
                                            <span className="text-[10px] font-bold uppercase px-2.5 py-0.5 rounded-full bg-[#1B4332]/10 text-[#1B4332]">
                                                {step.tag}
                                            </span>
                                        </div>

                                        <h3 className="font-display font-bold text-base text-[#1E242B] mb-2">
                                            {step.title}
                                        </h3>

                                        <p className="text-xs text-stone-600 leading-relaxed font-normal">
                                            {step.desc}
                                        </p>
                                    </div>

                                    <div className="pt-4 border-t border-stone-100 mt-5 flex items-center gap-2 text-stone-400 text-xs">
                                        <StepIcon className="w-4 h-4 text-[#52B788]" />
                                        <span>Proses Terintegrasi</span>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* =========================================================================
                7. SUMBER DATA DAN INTEGRASI RESMI
               ========================================================================= */}
            <section className="py-16 sm:py-24 bg-white border-b border-stone-200/80">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="max-w-3xl mb-12">
                        <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#E05A2B]/10 text-[#E05A2B] text-xs font-bold uppercase tracking-wider mb-2">
                            <Satellite className="w-3.5 h-3.5" />
                            <span>Transparansi Data &amp; Integritas</span>
                        </div>
                        <h2 className="font-display font-black text-2xl sm:text-4xl text-[#1E242B] tracking-tight leading-tight mb-3">
                            Sumber Data &amp; Integrasi Resmi
                        </h2>
                        <p className="text-xs sm:text-sm text-stone-600 leading-relaxed">
                            FireAware berkomitmen menyajikan data langsung dari jaringan telemetri publik resmi tanpa manipulasi angka.
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {/* NASA FIRMS */}
                        <div className="p-6 rounded-3xl bg-[#F6F5F2] border border-stone-200/80 shadow-xs flex flex-col justify-between">
                            <div>
                                <div className="flex items-center justify-between mb-4">
                                    <div className="w-10 h-10 rounded-xl bg-[#E05A2B]/15 text-[#E05A2B] flex items-center justify-center font-bold text-sm">
                                        <Satellite className="w-5 h-5" />
                                    </div>
                                    <span className="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                                        Integrasi Aktif
                                    </span>
                                </div>

                                <h3 className="font-display font-bold text-lg text-[#1E242B] mb-1">
                                    NASA FIRMS
                                </h3>
                                <span className="text-xs text-stone-500 block mb-3">
                                    Deteksi Anomali Termal Satelit Global
                                </span>

                                <p className="text-xs text-stone-600 leading-relaxed mb-4">
                                    Data titik panas diproses dari sensor VIIRS (resolusi 375m pada satelit Suomi-NPP dan NOAA-20) serta sensor MODIS (resolusi 1km pada satelit Terra &amp; Aqua).
                                </p>

                                <ul className="space-y-1.5 text-xs text-stone-600 pt-3 border-t border-stone-200">
                                    <li className="flex items-center gap-2">
                                        <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                        <span>Koordinat Lintang &amp; Bujur WGS84</span>
                                    </li>
                                    <li className="flex items-center gap-2">
                                        <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                        <span>Fire Radiative Power (FRP dalam MW)</span>
                                    </li>
                                </ul>
                            </div>

                            <a
                                href="https://firms.modaps.eosdis.nasa.gov/"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="mt-5 inline-flex items-center gap-1.5 text-xs font-bold text-[#1B4332] hover:text-[#E05A2B] transition-colors"
                            >
                                <span>Portal Resmi NASA FIRMS</span>
                                <ExternalLink className="w-3.5 h-3.5" />
                            </a>
                        </div>

                        {/* KLHK RI */}
                        <div className="p-6 rounded-3xl bg-[#F6F5F2] border border-stone-200/80 shadow-xs flex flex-col justify-between">
                            <div>
                                <div className="flex items-center justify-between mb-4">
                                    <div className="w-10 h-10 rounded-xl bg-[#1B4332]/15 text-[#1B4332] flex items-center justify-center font-bold text-sm">
                                        <ShieldCheck className="w-5 h-5" />
                                    </div>
                                    <span className="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                                        Standar Regulasi
                                    </span>
                                </div>

                                <h3 className="font-display font-bold text-lg text-[#1E242B] mb-1">
                                    Kementerian LHK RI
                                </h3>
                                <span className="text-xs text-stone-500 block mb-3">
                                    Standar ISPU &amp; Satgas SIPONGI
                                </span>

                                <p className="text-xs text-stone-600 leading-relaxed mb-4">
                                    Merujuk Peraturan Menteri LHK No. 14 Tahun 2020 tentang Indeks Standar Pencemar Udara (ISPU) serta sinergi sistem pemantauan Manggala Agni KLHK.
                                </p>

                                <ul className="space-y-1.5 text-xs text-stone-600 pt-3 border-t border-stone-200">
                                    <li className="flex items-center gap-2">
                                        <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                        <span>Baku mutu 5 kategori ISPU nasional</span>
                                    </li>
                                    <li className="flex items-center gap-2">
                                        <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                        <span>Rujukan hukum UU No. 32 Tahun 2009</span>
                                    </li>
                                </ul>
                            </div>

                            <a
                                href="https://sipongi.menlhk.go.id/"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="mt-5 inline-flex items-center gap-1.5 text-xs font-bold text-[#1B4332] hover:text-[#E05A2B] transition-colors"
                            >
                                <span>Portal SIPONGI KLHK</span>
                                <ExternalLink className="w-3.5 h-3.5" />
                            </a>
                        </div>

                        {/* BMKG & OpenAQ */}
                        <div className="p-6 rounded-3xl bg-[#F6F5F2] border border-stone-200/80 shadow-xs flex flex-col justify-between">
                            <div>
                                <div className="flex items-center justify-between mb-4">
                                    <div className="w-10 h-10 rounded-xl bg-[#F4A261]/20 text-[#a85817] flex items-center justify-center font-bold text-sm">
                                        <Wind className="w-5 h-5" />
                                    </div>
                                    <span className="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-800">
                                        Sensor Faktual
                                    </span>
                                </div>

                                <h3 className="font-display font-bold text-lg text-[#1E242B] mb-1">
                                    BMKG &amp; OpenAQ
                                </h3>
                                <span className="text-xs text-stone-500 block mb-3">
                                    Sensor Udara Terbuka &amp; Cuaca
                                </span>

                                <p className="text-xs text-stone-600 leading-relaxed mb-4">
                                    Jaringan pengukuran konsentrasi partikulat aktual PM2.5 stasiun permukaan dan informasi Hari Tanpa Hujan (HTH) BMKG Kalimantan.
                                </p>

                                <ul className="space-y-1.5 text-xs text-stone-600 pt-3 border-t border-stone-200">
                                    <li className="flex items-center gap-2">
                                        <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                        <span>Konsentrasi partikulat aktual (µg/m³)</span>
                                    </li>
                                    <li className="flex items-center gap-2">
                                        <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                        <span>Peringatan potensi kemudahan terbakar</span>
                                    </li>
                                </ul>
                            </div>

                            <a
                                href="https://www.bmkg.go.id/"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="mt-5 inline-flex items-center gap-1.5 text-xs font-bold text-[#1B4332] hover:text-[#E05A2B] transition-colors"
                            >
                                <span>Portal BMKG Indonesia</span>
                                <ExternalLink className="w-3.5 h-3.5" />
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {/* =========================================================================
                8. FITUR UTAMA PLATFORM FIREAWARE
               ========================================================================= */}
            <section className="py-16 sm:py-24 bg-[#F8F9FA] border-b border-stone-200/80">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="max-w-3xl mb-12">
                        <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1B4332]/10 text-[#1B4332] text-xs font-bold uppercase tracking-wider mb-2">
                            <Sparkles className="w-3.5 h-3.5 text-[#52B788]" />
                            <span>Navigasi Ekosistem Layanan</span>
                        </div>
                        <h2 className="font-display font-black text-2xl sm:text-4xl text-[#1E242B] tracking-tight leading-tight mb-3">
                            Jelajahi Fitur Utama FireAware
                        </h2>
                        <p className="text-xs sm:text-sm text-stone-600 leading-relaxed">
                            Seluruh modul saling terhubung untuk memberikan panduan komprehensif dari deteksi dini hingga mitigasi kesehatan.
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {coreFeatures.map((feat) => {
                            const FeatIcon = feat.icon;
                            return (
                                <div
                                    key={feat.title}
                                    className="p-6 rounded-3xl bg-white border border-stone-200/80 shadow-xs hover:shadow-lg transition-all duration-300 flex flex-col justify-between group"
                                >
                                    <div>
                                        <div className="flex items-center justify-between mb-4">
                                            <div className={`w-10 h-10 rounded-xl bg-gradient-to-br ${feat.color} text-white flex items-center justify-center shadow-md`}>
                                                <FeatIcon className="w-5 h-5" />
                                            </div>
                                            <span className="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-stone-100 text-stone-600">
                                                {feat.badge}
                                            </span>
                                        </div>

                                        <h3 className="font-display font-bold text-base text-[#1E242B] group-hover:text-[#1B4332] transition-colors mb-2">
                                            {feat.title}
                                        </h3>

                                        <p className="text-xs text-stone-600 leading-relaxed font-normal mb-5">
                                            {feat.desc}
                                        </p>
                                    </div>

                                    <Link
                                        href={feat.href}
                                        className="pt-4 border-t border-stone-100 flex items-center justify-between text-xs font-bold text-[#1B4332] group-hover:text-[#E05A2B] transition-colors"
                                    >
                                        <span>Buka Halaman</span>
                                        <ArrowRight className="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" />
                                    </Link>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>

            {/* =========================================================================
                9. KETERBATASAN SISTEM (Disclaimer Profesional)
               ========================================================================= */}
            <section className="py-16 sm:py-24 bg-white border-b border-stone-200/80">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="p-6 sm:p-10 rounded-3xl bg-[#F6F5F2] border border-stone-300/80">
                        <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider mb-4 border border-amber-200">
                            <ShieldAlert className="w-3.5 h-3.5 text-[#E05A2B]" />
                            <span>Integritas Informasi &amp; Batasan Teknis</span>
                        </div>

                        <h2 className="font-display font-black text-xl sm:text-2xl text-[#1E242B] mb-3">
                            Media Pemantauan Publik, Bukan Sistem Prediksi Kebakaran
                        </h2>

                        <p className="text-xs sm:text-sm text-stone-700 leading-relaxed mb-6 font-normal">
                            FireAware beroperasi murni sebagai <strong>media informasi dan pemantauan berbasis data sensor faktual</strong> untuk meningkatkan kesadaran masyarakat. Mohon perhatikan batasan teknis berikut:
                        </p>

                        <div className="space-y-3.5 text-xs text-stone-700">
                            <div className="flex items-start gap-3 p-3.5 rounded-2xl bg-white border border-stone-200">
                                <span className="w-5 h-5 rounded-md bg-[#E05A2B]/15 text-[#E05A2B] font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    1
                                </span>
                                <div>
                                    <strong className="text-[#1E242B] block mb-0.5">Deteksi Termal Bukan Kepastian Api Aktif di Lapangan:</strong>
                                    Hotspot adalah anomali suhu termal permukaan bumi yang terdeteksi saat satelit melintas. Verifikasi lapangan (*groundcheck*) oleh petugas tetap diperlukan untuk memastikan status kebakaran.
                                </div>
                            </div>

                            <div className="flex items-start gap-3 p-3.5 rounded-2xl bg-white border border-stone-200">
                                <span className="w-5 h-5 rounded-md bg-[#E05A2B]/15 text-[#E05A2B] font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    2
                                </span>
                                <div>
                                    <strong className="text-[#1E242B] block mb-0.5">Jumlah Hotspot Tidak Linier dengan Tingkat Asap:</strong>
                                    Jumlah titik panas tidak secara langsung menunjukkan tingkat kepekatan asap di suatu pemukiman karena dipengaruhi arah angin, kecepatan angin, kelembapan udara, dan topografi bentang alam.
                                </div>
                            </div>

                            <div className="flex items-start gap-3 p-3.5 rounded-2xl bg-white border border-stone-200">
                                <span className="w-5 h-5 rounded-md bg-[#E05A2B]/15 text-[#E05A2B] font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    3
                                </span>
                                <div>
                                    <strong className="text-[#1E242B] block mb-0.5">Ketergantungan Ketersediaan Stasiun Sensor Udara:</strong>
                                    Data pemantauan kualitas udara partikulat bergantung pada operasional sensor aktif di kota/kabupaten terkait. Wilayah tanpa sensor stasiun tetap menyajikan data satelit dan buletin BMKG.
                                </div>
                            </div>

                            <div className="flex items-start gap-3 p-3.5 rounded-2xl bg-white border border-stone-200">
                                <span className="w-5 h-5 rounded-md bg-[#E05A2B]/15 text-[#E05A2B] font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    4
                                </span>
                                <div>
                                    <strong className="text-[#1E242B] block mb-0.5">Jeda Lintas Orbit Satelit (Latency):</strong>
                                    Satelit orbit kutub melintas beberapa kali sehari. Data merefleksikan kondisi saat satelit merekam, bukan video siaran langsung (*real-time video*).
                                </div>
                            </div>

                            <div className="flex items-start gap-3 p-3.5 rounded-2xl bg-white border border-stone-200">
                                <span className="w-5 h-5 rounded-md bg-[#E05A2B]/15 text-[#E05A2B] font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    5
                                </span>
                                <div>
                                    <strong className="text-[#1E242B] block mb-0.5">Bukan Pengganti Otoritas Kedaruratan Resmi:</strong>
                                    Platform ini tidak menggantikan arahan resmi dari BPBD, KLHK Manggala Agni, atau aparat pemerintah. Dalam situasi genting, selalu prioritaskan instruksi posko darurat 112 atau BPBD setempat.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* =========================================================================
                10. CLOSING CALL TO ACTION (Deep Pine & Forest Canopy)
               ========================================================================= */}
            <section className="relative overflow-hidden bg-gradient-to-br from-[#1C2526] via-[#1B4332] to-[#12161A] text-white py-16 sm:py-24">
                <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                    <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 text-[#52B788] text-xs font-bold uppercase tracking-wider mb-4 border border-white/10">
                        <Trees className="w-3.5 h-3.5" />
                        <span>Kemitraan Menjaga Ekosistem Borneo</span>
                    </div>

                    <h2 className="font-display font-black text-2xl sm:text-4xl md:text-5xl text-white tracking-tight leading-tight mb-4 max-w-3xl mx-auto">
                        Menjaga Kalimantan Dimulai dari Kepedulian Kita.
                    </h2>

                    <p className="text-xs sm:text-sm md:text-base text-[#F8F9FA]/85 leading-relaxed font-normal max-w-2xl mx-auto mb-8">
                        Kenali informasi lingkungan, tingkatkan kewaspadaan, dan dukung upaya perlindungan hutan serta lahan gambut di seluruh penjuru Kalimantan.
                    </p>

                    <div className="flex flex-col sm:flex-row items-center justify-center gap-3">
                        <Link
                            href="/peta-hotspot"
                            className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-[#E05A2B] to-[#D94E34] text-white font-bold text-xs uppercase tracking-wider hover:brightness-110 transition-all shadow-lg active:scale-95 cursor-pointer"
                        >
                            <Satellite className="w-4 h-4" />
                            <span>Jelajahi Peta Hotspot</span>
                        </Link>

                        <Link
                            href="/panduan"
                            className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-white/10 hover:bg-white/15 text-white font-bold text-xs tracking-wider border border-white/20 transition-all backdrop-blur-md cursor-pointer"
                        >
                            <ShieldCheck className="w-4 h-4 text-[#52B788]" />
                            <span>Pelajari Panduan Keselamatan</span>
                        </Link>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
