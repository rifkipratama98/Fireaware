import React from 'react';
import { Link } from '@inertiajs/react';
import { Trees, ShieldCheck, Mail, MapPin, ExternalLink, Heart } from 'lucide-react';

export function Footer() {
    return (
        <footer className="mt-auto bg-[#1E242B] text-[#F8F9FA] border-t border-white/10 relative overflow-hidden">
            {/* Subtle top glow highlight */}
            <div className="absolute top-0 left-1/2 -translate-x-1/2 w-3/4 max-w-4xl h-[1px] bg-gradient-to-r from-transparent via-[#E05A2B]/40 to-transparent" />

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-12">
                    {/* Brand Column */}
                    <div className="lg:col-span-5 flex flex-col items-start">
                        <Link href="/" className="flex items-center gap-2.5 group">
                            <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-[#E05A2B] to-[#D94E34] text-white flex items-center justify-center shadow-md">
                                <Trees className="w-5 h-5 stroke-[2.2]" />
                            </div>
                            <div className="flex flex-col">
                                <span className="font-display font-extrabold text-lg tracking-tight text-[#F8F9FA] leading-tight">
                                    FireAware
                                </span>
                                <span className="text-[10px] uppercase font-bold tracking-widest text-[#F4A261] leading-none">
                                    Kalimantan
                                </span>
                            </div>
                        </Link>

                        <p className="mt-4 text-sm text-[#F8F9FA]/70 leading-relaxed max-w-md">
                            Platform informasi lingkungan dan sistem peringatan dini kebakaran hutan dan lahan di Pulau Kalimantan. Mendukung deteksi cepat titik panas satelit, pemantauan indeks ISPU, dan edukasi mitigasi masyarakat.
                        </p>

                        <div className="mt-5 flex items-start gap-3 p-3 rounded-2xl bg-white/5 border border-white/10 max-w-md">
                            <ShieldCheck className="w-5 h-5 text-[#52B788] shrink-0 mt-0.5" />
                            <div className="text-xs leading-relaxed text-[#F8F9FA]/80">
                                <strong className="text-[#F8F9FA] block font-semibold">
                                    Data Terpadu 5 Provinsi Kalimantan
                                </strong>
                                <span>Telemetri NASA FIRMS, BMKG Karhutla, &amp; Indeks Standar Pencemar Udara (ISPU) KLHK.</span>
                            </div>
                        </div>
                    </div>

                    {/* Navigasi Utama */}
                    <div className="lg:col-span-2 col-span-1">
                        <h4 className="text-xs font-bold uppercase tracking-wider text-[#F4A261] mb-4">
                            Navigasi
                        </h4>
                        <ul className="space-y-2.5 text-sm text-[#F8F9FA]/70">
                            <li>
                                <Link href="/" className="hover:text-white transition-colors">
                                    Beranda
                                </Link>
                            </li>
                            <li>
                                <Link href="/peta-hotspot" className="hover:text-white transition-colors">
                                    Peta Hotspot
                                </Link>
                            </li>
                            <li>
                                <Link href="/kualitas-udara" className="hover:text-white transition-colors">
                                    Kualitas Udara
                                </Link>
                            </li>
                            <li>
                                <Link href="/pengumuman" className="hover:text-white transition-colors">
                                    Pengumuman
                                </Link>
                            </li>
                            <li>
                                <Link href="/edukasi" className="hover:text-white transition-colors">
                                    Edukasi Gambut
                                </Link>
                            </li>
                            <li>
                                <Link href="/panduan" className="hover:text-white transition-colors">
                                    Panduan Mitigasi
                                </Link>
                            </li>
                            <li>
                                <Link href="/tentang" className="hover:text-white transition-colors">
                                    Tentang Kami
                                </Link>
                            </li>
                        </ul>
                    </div>

                    {/* Kesiapsiagaan */}
                    <div className="lg:col-span-2 col-span-1">
                        <h4 className="text-xs font-bold uppercase tracking-wider text-[#F4A261] mb-4">
                            Kesiapsiagaan
                        </h4>
                        <ul className="space-y-2.5 text-sm text-[#F8F9FA]/70">
                            <li>
                                <Link href="/panduan" className="hover:text-white transition-colors">
                                    Protokol Napas &amp; Masker
                                </Link>
                            </li>
                            <li>
                                <Link href="/edukasi" className="hover:text-white transition-colors">
                                    Karakteristik Lahan Gambut
                                </Link>
                            </li>
                            <li>
                                <Link href="/kualitas-udara" className="hover:text-white transition-colors">
                                    Kategori Bahaya ISPU PM2.5
                                </Link>
                            </li>
                            <li>
                                <Link href="/admin" className="hover:text-white transition-colors">
                                    Portal Pengelola (Admin)
                                </Link>
                            </li>
                        </ul>
                    </div>

                    {/* Kontak & Wilayah */}
                    <div className="lg:col-span-3">
                        <h4 className="text-xs font-bold uppercase tracking-wider text-[#F4A261] mb-4">
                            Wilayah Pemantauan
                        </h4>
                        <p className="text-xs text-[#F8F9FA]/60 leading-relaxed mb-4">
                            Kalimantan Barat, Kalimantan Tengah, Kalimantan Selatan, Kalimantan Timur, dan Kalimantan Utara.
                        </p>
                        <div className="space-y-2 text-xs text-[#F8F9FA]/80">
                            <div className="flex items-center gap-2">
                                <Mail className="w-4 h-4 text-[#E05A2B]" />
                                <span>halo@fireaware.id</span>
                            </div>
                            <div className="flex items-center gap-2">
                                <MapPin className="w-4 h-4 text-[#E05A2B]" />
                                <span>Banjarmasin, Kalimantan Selatan</span>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Bottom Bar */}
                <div className="mt-12 pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-[#F8F9FA]/50">
                    <p>
                        &copy; {new Date().getFullYear()} FireAware Kalimantan. Sistem Informasi &amp; Peringatan Dini Karhutla.
                    </p>
                    <div className="flex items-center gap-4">
                        <span>Pencegahan Kebakaran Berkelanjutan</span>
                    </div>
                </div>
            </div>
        </footer>
    );
}

