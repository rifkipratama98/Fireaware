import React from 'react';
import { Head, Link } from '@inertiajs/react';
import { PublicLayout } from '@/Layouts/PublicLayout';
import { SectionHeading } from '@/Components/SectionHeading';
import { Badge } from '@/Components/Badge';
import { Button } from '@/Components/Button';
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
} from 'lucide-react';

export default function About({ provinces = [] }) {
    const provinceDetails = {
        'kalimantan-barat': {
            code: 'KALBAR',
            highlight: 'Kawasan gambut pesisir luas dan koridor perbatasan darat.',
            icon: Droplets,
        },
        'kalimantan-tengah': {
            code: 'KALTENG',
            highlight: 'Kubah gambut terdalam Indonesia dan zona prioritas pembasahan kanal.',
            icon: Flame,
        },
        'kalimantan-selatan': {
            code: 'KALSEL',
            highlight: 'Zona padat pemukiman rawan kabut asap bandara dan lahan rawa.',
            icon: Building2,
        },
        'kalimantan-timur': {
            code: 'KALTIM',
            highlight: 'Kawasan hutan hujan tropis perbukitan dan zona penyangga IKN.',
            icon: Trees,
        },
        'kalimantan-utara': {
            code: 'KALTARA',
            highlight: 'Bentang kanopi hutan primer dan perbatasan dataran tinggi utara.',
            icon: Compass,
        },
    };

    return (
        <PublicLayout title="Tentang FireAware Kalimantan — Media Pemantauan & Informasi Lingkungan">
            {/* 1. Hero Section */}
            <section className="relative overflow-hidden bg-gradient-to-br from-[#1B4332] via-[#1E242B] to-[#2B303A] text-[#F8F9FA] pt-28 sm:pt-36 pb-16 sm:pb-24">
                <div className="absolute inset-0 opacity-15 pointer-events-none bg-[radial-gradient(#52B788_1px,transparent_1px)] [background-size:24px_24px]" />
                <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="max-w-3xl">
                        <Badge variant="glass" className="mb-4 text-[#F4A261] border-white/20">
                            <Info className="w-3.5 h-3.5 mr-1" />
                            Mengenal Inisiatif Lingkungan
                        </Badge>
                        <h1 className="text-3xl sm:text-5xl font-extrabold tracking-tight font-display text-white leading-tight">
                            Mengenal FireAware Kalimantan
                        </h1>
                        <p className="mt-5 text-base sm:text-lg text-[#F8F9FA]/80 leading-relaxed font-normal">
                            Inisiatif pemantauan mandiri dan keterbukaan informasi lingkungan untuk mendukung kesiapsiagaan masyarakat dalam menghadapi ancaman kebakaran hutan dan lahan gambut di seluruh penjuru Pulau Kalimantan.
                        </p>

                        <div className="mt-8 flex flex-wrap gap-2.5 sm:gap-3">
                            <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/10 backdrop-blur border border-white/15 text-[#F8F9FA]">
                                <MapPin className="w-3.5 h-3.5 text-[#F4A261]" />
                                <span>5 Provinsi Cakupan Penuh</span>
                            </div>
                            <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/10 backdrop-blur border border-white/15 text-[#F8F9FA]">
                                <Satellite className="w-3.5 h-3.5 text-[#52B788]" />
                                <span>3 Jaringan Telemetri Resmi</span>
                            </div>
                            <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/10 backdrop-blur border border-white/15 text-[#F8F9FA]">
                                <ShieldCheck className="w-3.5 h-3.5 text-[#E05A2B]" />
                                <span>100% Data Faktual Sensor</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* 2. Visi & Misi */}
            <section className="py-16 sm:py-20 bg-white border-b border-black/5">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                        <div className="lg:col-span-7">
                            <SectionHeading
                                badge="Tujuan &amp; Visi Platform"
                                title="Menjembatani Sains Satelit dengan Kesiapsiagaan Komunitas"
                                description="FireAware Kalimantan dikembangkan sebagai platform informasi publik yang menyajikan pantauan titik panas (hotspot), kualitas udara partikulat (PM2.5), dan warta kewaspadaan secara transparan, mudah dipahami, dan dapat diakses bebas oleh seluruh masyarakat."
                            />
                            <p className="mt-4 text-sm sm:text-base text-[#1E242B]/75 leading-relaxed">
                                Kami meyakini bahwa data sains lingkungan akan optimal ketika diterjemahkan ke dalam bahasa yang sederhana dan relevan bagi kehidupan sehari-hari, sehingga masyarakat memiliki daya mitigasi yang lebih tangguh untuk melindungi keluarga dan lingkungan mereka.
                            </p>

                            <div className="mt-8 flex flex-wrap gap-3">
                                <Button href="/peta-hotspot" variant="primary" size="md">
                                    <MapPin className="w-4 h-4" />
                                    <span>Buka Peta Hotspot</span>
                                </Button>
                                <Button href="/kualitas-udara" variant="outline" size="md">
                                    <Wind className="w-4 h-4" />
                                    <span>Pantau Kualitas Udara</span>
                                </Button>
                            </div>
                        </div>

                        <div className="lg:col-span-5">
                            <div className="p-6 sm:p-8 rounded-3xl bg-[#F6F5F2] border border-[#2B303A]/10 shadow-sm">
                                <div className="flex items-center gap-3 mb-6">
                                    <div className="w-12 h-12 rounded-2xl bg-[#E05A2B] text-white flex items-center justify-center shadow-md">
                                        <Compass className="w-6 h-6 stroke-[2]" />
                                    </div>
                                    <div>
                                        <h3 className="font-bold text-lg text-[#1E242B] font-display">
                                            Prinsip Pokok FireAware
                                        </h3>
                                        <span className="text-xs text-[#1E242B]/60">
                                            Pilar integritas penyajian data publik
                                        </span>
                                    </div>
                                </div>

                                <ul className="space-y-4">
                                    <li className="flex items-start gap-3">
                                        <CheckCircle2 className="w-5 h-5 text-[#2A9D8F] shrink-0 mt-0.5" />
                                        <div>
                                            <strong className="block text-sm font-bold text-[#1E242B]">
                                                Hanya Menyajikan Data Faktual
                                            </strong>
                                            <span className="text-xs text-[#1E242B]/70">
                                                Tidak ada angka prediksi buatan atau estimasi sepihak.
                                            </span>
                                        </div>
                                    </li>
                                    <li className="flex items-start gap-3">
                                        <CheckCircle2 className="w-5 h-5 text-[#2A9D8F] shrink-0 mt-0.5" />
                                        <div>
                                            <strong className="block text-sm font-bold text-[#1E242B]">
                                                Bahasa Sederhana &amp; Aksi Nyata
                                            </strong>
                                            <span className="text-xs text-[#1E242B]/70">
                                                Setiap indikator dilengkapi rekomendasi keselamatan praktis.
                                            </span>
                                        </div>
                                    </li>
                                    <li className="flex items-start gap-3">
                                        <CheckCircle2 className="w-5 h-5 text-[#2A9D8F] shrink-0 mt-0.5" />
                                        <div>
                                            <strong className="block text-sm font-bold text-[#1E242B]">
                                                Mendukung Otoritas, Bukan Menggantikan
                                            </strong>
                                            <span className="text-xs text-[#1E242B]/70">
                                                Memperkuat kanal resmi BPBD, BMKG, dan KLHK di tingkat warga.
                                            </span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* 3. Tantangan Ekologis */}
            <section className="py-16 sm:py-20 bg-[#F8F9FA] border-b border-black/5">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <SectionHeading
                        align="center"
                        badge="Tantangan Ekologis &amp; Latar Belakang"
                        title="Mengapa Kalimantan Memerlukan FireAware?"
                        description="Kebakaran hutan dan lahan di Kalimantan memiliki karakteristik ekologis yang unik, membutuhkan kewaspadaan berbasis sains yang terhubung langsung ke masyarakat."
                        className="mb-12"
                    />

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                        <div className="p-6 rounded-2xl bg-white border border-black/5 shadow-xs flex flex-col justify-between">
                            <div>
                                <div className="w-12 h-12 rounded-xl bg-[#F4A261]/15 text-[#a85817] flex items-center justify-center mb-4">
                                    <Flame className="w-6 h-6 stroke-[1.8]" />
                                </div>
                                <h3 className="text-lg font-bold text-[#1E242B] font-display mb-2">
                                    Dilema Api Bawah Tanah Gambut
                                </h3>
                                <p className="text-xs sm:text-sm text-[#1E242B]/70 leading-relaxed">
                                    Lahan gambut kering membakar bahan organik bermeter-meter di bawah tanah (<em>ground fire</em>). Api ini tidak tampak di permukaan namun memancarkan panas ekstrem dan sulit dipadamkan tanpa pembasahan mendalam.
                                </p>
                            </div>
                        </div>

                        <div className="p-6 rounded-2xl bg-white border border-black/5 shadow-xs flex flex-col justify-between">
                            <div>
                                <div className="w-12 h-12 rounded-xl bg-[#2B303A]/10 text-[#2B303A] flex items-center justify-center mb-4">
                                    <Wind className="w-6 h-6 stroke-[1.8]" />
                                </div>
                                <h3 className="text-lg font-bold text-[#1E242B] font-display mb-2">
                                    Ancaman Polutan Mikro PM2.5
                                </h3>
                                <p className="text-xs sm:text-sm text-[#1E242B]/70 leading-relaxed">
                                    Pembakaran gambut melepaskan asap pekat kaya partikel debu halus PM2.5. Partikel berukuran 2,5 mikron ini menembus jauh ke organ pernapasan dan menyebar melintasi batas kabupaten bahkan negara.
                                </p>
                            </div>
                        </div>

                        <div className="p-6 rounded-2xl bg-white border border-black/5 shadow-xs flex flex-col justify-between">
                            <div>
                                <div className="w-12 h-12 rounded-xl bg-[#E05A2B]/15 text-[#E05A2B] flex items-center justify-center mb-4">
                                    <Radio className="w-6 h-6 stroke-[1.8]" />
                                </div>
                                <h3 className="text-lg font-bold text-[#1E242B] font-display mb-2">
                                    Kesenjangan Informasi Publik
                                </h3>
                                <p className="text-xs sm:text-sm text-[#1E242B]/70 leading-relaxed">
                                    Data citra satelit dan indeks sensor sering kali tersebar di berbagai portal dengan terminologi teknis. Warga membutuhkan kanal satu pintu yang menerjemahkan data menjadi instruksi keselamatan nyata.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div className="rounded-2xl overflow-hidden border border-black/5 bg-white shadow-xs">
                            <ImageWithFallback
                                src="/images/peatland-gambut.jpg"
                                alt="Bentang alam lahan gambut tropis Kalimantan"
                                className="w-full h-56 sm:h-64 object-cover"
                            />
                            <div className="p-4 text-xs text-[#1E242B]/75 border-t border-black/5 flex items-center gap-2">
                                <span className="font-bold text-[#1B4332]">Ekosistem Kubah Gambut:</span>
                                <span>Menyimpan cadangan air raksasa dan karbon vital bila kelembapannya tetap terjaga.</span>
                            </div>
                        </div>

                        <div className="rounded-2xl overflow-hidden border border-black/5 bg-white shadow-xs">
                            <ImageWithFallback
                                src="/images/smoke-haze.jpg"
                                alt="Kondisi kabut asap karhutla pekat di Kalimantan"
                                className="w-full h-56 sm:h-64 object-cover"
                            />
                            <div className="p-4 text-xs text-[#1E242B]/75 border-t border-black/5 flex items-center gap-2">
                                <span className="font-bold text-[#E05A2B]">Dampak Kabut Asap:</span>
                                <span>Partikel debu pekat menuntut respons cepat untuk mengenakan masker respirator N95.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* 4. Cakupan 5 Provinsi Kalimantan */}
            <section className="py-16 sm:py-20 bg-white border-b border-black/5">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <SectionHeading
                        align="center"
                        badge="Cakupan Wilayah Kalimantan"
                        title="Pemantauan Terpadu 5 Provinsi"
                        description="FireAware menyatukan telemetri satelit dan stasiun kualitas udara di seluruh penjuru Pulau Borneo dalam satu peta interaktif berstandar nasional."
                        className="mb-12"
                    />

                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {provinces.map((prov) => {
                            const detail = provinceDetails[prov.slug] || {
                                code: 'BORNEO',
                                highlight: 'Wilayah pemantauan satelit dan sensor udara terpadu.',
                                icon: MapPin,
                            };
                            const IconComponent = detail.icon;

                            return (
                                <div
                                    key={prov.id}
                                    className="p-6 rounded-2xl bg-[#F6F5F2] border border-black/5 flex flex-col justify-between hover:shadow-md transition-shadow duration-200"
                                >
                                    <div>
                                        <div className="flex items-center justify-between mb-3">
                                            <span className="text-xs font-bold px-2.5 py-1 rounded-md bg-[#2B303A] text-white tracking-wider">
                                                {detail.code}
                                            </span>
                                            <IconComponent className="w-5 h-5 text-[#E05A2B]" />
                                        </div>
                                        <h3 className="text-lg font-bold text-[#1E242B] font-display mb-1.5">
                                            {prov.name}
                                        </h3>
                                        <p className="text-xs text-[#1E242B]/70 leading-relaxed">
                                            {detail.highlight}
                                        </p>
                                    </div>

                                    <div className="mt-6 pt-4 border-t border-black/5 flex items-center justify-between text-xs">
                                        <span className="font-mono text-[#1E242B]/50">
                                            {Number(prov.center_lat || 0).toFixed(2)}, {Number(prov.center_lng || 0).toFixed(2)}
                                        </span>
                                        <Link
                                            href={`/peta-hotspot?provinsi=${prov.slug}`}
                                            className="font-semibold text-[#E05A2B] hover:text-[#c94d23] inline-flex items-center gap-1 transition-colors"
                                        >
                                            <span>Buka Peta</span>
                                            <ArrowRight className="w-3.5 h-3.5" />
                                        </Link>
                                    </div>
                                </div>
                            );
                        })}

                        {/* Borneo Wide Card */}
                        <div className="p-6 rounded-2xl bg-gradient-to-br from-[#1B4332] to-[#1E242B] text-white flex flex-col justify-between shadow-lg">
                            <div>
                                <div className="flex items-center justify-between mb-3">
                                    <span className="text-xs font-bold px-2.5 py-1 rounded-md bg-white text-[#1B4332] tracking-wider">
                                        TOTAL WILAYAH
                                    </span>
                                    <Globe2 className="w-5 h-5 text-[#52B788]" />
                                </div>
                                <h3 className="text-lg font-bold text-white font-display mb-1.5">
                                    Seluruh Pulau Kalimantan
                                </h3>
                                <p className="text-xs text-white/80 leading-relaxed">
                                    Meliputi area daratan seluas lebih dari 544.000 km² dengan tutupan vegetasi hutan tropis terbesar ketiga di dunia.
                                </p>
                            </div>

                            <div className="mt-6 pt-4 border-t border-white/10 flex items-center justify-between text-xs">
                                <span className="text-white/60">Sentral Borneo</span>
                                <Link
                                    href="/peta-hotspot"
                                    className="font-bold text-[#F4A261] hover:text-white inline-flex items-center gap-1 transition-colors"
                                >
                                    <span>Pantau Semua</span>
                                    <ArrowRight className="w-3.5 h-3.5" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {/* 5. Sumber Data Resmi */}
            <section className="py-16 sm:py-20 bg-[#F8F9FA] border-b border-black/5">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <SectionHeading
                        align="center"
                        badge="Transparansi &amp; Integritas Data"
                        title="Sumber Data Resmi Terverifikasi"
                        description="FireAware Kalimantan berkomitmen tidak merekayasa nilai titik panas maupun partikel udara. Seluruh data ditarik langsung dari jaringan telemetri terpercaya."
                        className="mb-12"
                    />

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div className="p-6 rounded-2xl bg-white border border-black/5 shadow-xs">
                            <div className="flex items-center gap-3 mb-4">
                                <div className="w-10 h-10 rounded-xl bg-[#E05A2B]/10 text-[#E05A2B] flex items-center justify-center">
                                    <Satellite className="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-base text-[#1E242B] font-display">NASA FIRMS</h3>
                                    <span className="text-xs text-[#1E242B]/60">Deteksi Termal Satelit</span>
                                </div>
                            </div>
                            <p className="text-xs sm:text-sm text-[#1E242B]/70 leading-relaxed mb-4">
                                Sistem informasi kebakaran global NASA memproses citra satelit polar orbit untuk memetakan anomali suhu bumi.
                            </p>
                            <ul className="space-y-2 text-xs text-[#1E242B]/75 border-t border-black/5 pt-4">
                                <li className="flex items-center gap-2">
                                    <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                    <span>Sensor VIIRS resolusi 375m</span>
                                </li>
                                <li className="flex items-center gap-2">
                                    <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                    <span>Sensor MODIS resolusi 1km</span>
                                </li>
                                <li className="flex items-center gap-2">
                                    <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                    <span>Nilai Fire Radiative Power (MW)</span>
                                </li>
                            </ul>
                        </div>

                        <div className="p-6 rounded-2xl bg-white border border-black/5 shadow-xs">
                            <div className="flex items-center gap-3 mb-4">
                                <div className="w-10 h-10 rounded-xl bg-[#1B4332]/10 text-[#1B4332] flex items-center justify-center">
                                    <ShieldCheck className="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-base text-[#1E242B] font-display">KLHK RI</h3>
                                    <span className="text-xs text-[#1E242B]/60">Baku Mutu ISPU</span>
                                </div>
                            </div>
                            <p className="text-xs sm:text-sm text-[#1E242B]/70 leading-relaxed mb-4">
                                Kementerian LHK yang menetapkan standar baku mutu pencemaran udara nasional dan koordinasi posko karhutla.
                            </p>
                            <ul className="space-y-2 text-xs text-[#1E242B]/75 border-t border-black/5 pt-4">
                                <li className="flex items-center gap-2">
                                    <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                    <span>Baku mutu ISPU Kepmen LHK No. 14/2020</span>
                                </li>
                                <li className="flex items-center gap-2">
                                    <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                    <span>5 kategori tingkat kelayakan PM2.5</span>
                                </li>
                                <li className="flex items-center gap-2">
                                    <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                    <span>Manggala Agni regu darat</span>
                                </li>
                            </ul>
                        </div>

                        <div className="p-6 rounded-2xl bg-white border border-black/5 shadow-xs">
                            <div className="flex items-center gap-3 mb-4">
                                <div className="w-10 h-10 rounded-xl bg-[#F4A261]/20 text-[#a85817] flex items-center justify-center">
                                    <Wind className="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-base text-[#1E242B] font-display">OpenAQ &amp; BMKG</h3>
                                    <span className="text-xs text-[#1E242B]/60">Sensor Udara &amp; Cuaca</span>
                                </div>
                            </div>
                            <p className="text-xs sm:text-sm text-[#1E242B]/70 leading-relaxed mb-4">
                                Jaringan data kualitas udara terbuka global dan BMKG untuk pantauan cuaca ekstrem di Pulau Kalimantan.
                            </p>
                            <ul className="space-y-2 text-xs text-[#1E242B]/75 border-t border-black/5 pt-4">
                                <li className="flex items-center gap-2">
                                    <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                    <span>Sensor telemetri udara stasiun tetap</span>
                                </li>
                                <li className="flex items-center gap-2">
                                    <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                    <span>Konsentrasi partikulat aktual PM2.5</span>
                                </li>
                                <li className="flex items-center gap-2">
                                    <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                    <span>Peringatan hari tanpa hujan</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            {/* 6. Batasan Sistem & Disclaimer */}
            <section className="py-16 sm:py-20 bg-white">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="p-6 sm:p-10 rounded-3xl bg-[#F6F5F2] border border-[#2B303A]/10">
                        <Badge variant="waspada" className="mb-4">
                            <ShieldAlert className="w-3.5 h-3.5 mr-1 text-[#8f6d14]" />
                            Batasan Sistem &amp; Disclaimer Resmi
                        </Badge>

                        <h2 className="text-xl sm:text-2xl font-extrabold text-[#1E242B] font-display mb-4">
                            Media Pemantauan Publik, Bukan Sistem Prediksi Kebakaran
                        </h2>

                        <p className="text-sm text-[#1E242B]/80 leading-relaxed mb-6">
                            FireAware Kalimantan beroperasi murni sebagai <strong>media informasi dan pemantauan berbasis data sensor faktual</strong> untuk meningkatkan kesadaran serta kesiapsiagaan masyarakat. Kami menegaskan batasan operasional berikut:
                        </p>

                        <div className="space-y-4 text-xs sm:text-sm text-[#1E242B]/75">
                            <div className="flex items-start gap-3 p-3 rounded-xl bg-white border border-black/5">
                                <span className="w-5 h-5 rounded-md bg-[#E05A2B]/15 text-[#E05A2B] font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    1
                                </span>
                                <div>
                                    <strong className="text-[#1E242B]">Bukan Sistem Prediksi Kebakaran:</strong> Platform ini tidak memprediksi kejadian kebakaran yang belum terjadi. Titik panas (hotspot) merupakan anomali suhu yang telah terdeteksi saat satelit melintas.
                                </div>
                            </div>
                            <div className="flex items-start gap-3 p-3 rounded-xl bg-white border border-black/5">
                                <span className="w-5 h-5 rounded-md bg-[#E05A2B]/15 text-[#E05A2B] font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    2
                                </span>
                                <div>
                                    <strong className="text-[#1E242B]">Bukan Pengganti Otoritas Darurat:</strong> FireAware tidak menggantikan komando posko darurat BPBD, satgas Manggala Agni KLHK, TNI/Polri, maupun instansi darurat pemerintah.
                                </div>
                            </div>
                            <div className="flex items-start gap-3 p-3 rounded-xl bg-white border border-black/5">
                                <span className="w-5 h-5 rounded-md bg-[#E05A2B]/15 text-[#E05A2B] font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    3
                                </span>
                                <div>
                                    <strong className="text-[#1E242B]">Jeda Lintas Orbit Satelit (Latency):</strong> Satelit orbit kutub melintas beberapa kali sehari. Data merefleksikan kondisi saat satelit merekam, bukan video langsung (<em>real-time video</em>).
                                </div>
                            </div>
                            <div className="flex items-start gap-3 p-3 rounded-xl bg-white border border-black/5">
                                <span className="w-5 h-5 rounded-md bg-[#E05A2B]/15 text-[#E05A2B] font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    4
                                </span>
                                <div>
                                    <strong className="text-[#1E242B]">Tindakan Nyata di Lapangan:</strong> Bila melihat kobaran api aktif atau asap pekat mengancam pemukiman, segera evakuasi keluarga Anda dan hubungi posko darurat <strong>112</strong> atau BPBD setempat.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}

