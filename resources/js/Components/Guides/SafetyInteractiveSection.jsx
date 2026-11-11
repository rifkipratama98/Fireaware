import React, { useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import {
    ShieldCheck,
    HeartPulse,
    CheckCircle2,
    XCircle,
    Trees,
    PhoneCall,
    RotateCcw,
    Sparkles,
    ChevronDown,
    CheckSquare,
    Info,
    HelpCircle,
    Building2,
    AlertOctagon,
} from 'lucide-react';

export function SafetyInteractiveSection() {
    // 1. STATE PROTOKOL TAHAPAN TINDAKAN (Sebelum, Saat, Setelah)
    const [activePhase, setActivePhase] = useState('sebelum');

    // 2. STATE CHECKLIST KESELAMATAN (Disimpan di localStorage)
    const initialChecklist = [
        {
            id: 'info_resmi',
            title: 'Memantau Data ISPU & Hotspot Resmi',
            desc: 'Rutin memantau stasiun pemantau kualitas udara dan sebaran titik panas di FireAware setiap pagi.',
        },
        {
            id: 'stok_masker',
            title: 'Persediaan Masker Standar N95 / KN95',
            desc: 'Memiliki persediaan respirator N95/KN95/KF94 minimal untuk kebutuhan 7–14 hari bagi seluruh anggota keluarga.',
        },
        {
            id: 'obat_rutin',
            title: 'Kotak P3K & Obat Gangguan Pernapasan',
            desc: 'Menyiapkan inhaler darurat bagi penderita asma, obat tetes mata steril, dan cairan semprot hidung saline.',
        },
        {
            id: 'clean_room',
            title: 'Menyiapkan Ruang Bersih (Clean Room)',
            desc: 'Menentukan 1 kamar tengah yang minim bukaan ventilasi dan menyiapkan kain/lakban penutup celah pintu.',
        },
        {
            id: 'kontak_darurat',
            title: 'Menyimpan Nomor Kontak Darurat 112 & Puskesmas',
            desc: 'Menyimpan nomor bebas pulsa 112, posko BPBD daerah, serta nomor faskes darurat di ponsel seluruh keluarga.',
        },
        {
            id: 'rute_evakuasi',
            title: 'Memahami Rencana & Rute Aman Evakuasi',
            desc: 'Mengetahui jalur jalan poros utama menjauhi lahan rawa gambut jika sewaktu-waktu aparat menginstruksikan evakuasi.',
        },
    ];

    const [checkedItems, setCheckedItems] = useState({});

    useEffect(() => {
        try {
            const saved = localStorage.getItem('fireaware_safety_checklist');
            if (saved) {
                setCheckedItems(JSON.parse(saved));
            }
        } catch (e) {
            // Local storage access error fallback
        }
    }, []);

    const handleToggleCheck = (id) => {
        const updated = {
            ...checkedItems,
            [id]: !checkedItems[id],
        };
        setCheckedItems(updated);
        try {
            localStorage.setItem('fireaware_safety_checklist', JSON.stringify(updated));
        } catch (e) {
            // fallback
        }
    };

    const handleResetChecklist = () => {
        setCheckedItems({});
        try {
            localStorage.removeItem('fireaware_safety_checklist');
        } catch (e) {
            // fallback
        }
    };

    const checkedCount = Object.values(checkedItems).filter(Boolean).length;
    const progressPercent = Math.round((checkedCount / initialChecklist.length) * 100);

    // 3. STATE FAQ ACCORDION
    const [openFaq, setOpenFaq] = useState(0);

    const faqs = [
        {
            question: 'Apa tindakan pertama yang harus diambil jika melihat kobaran api karhutla di dekat permukiman?',
            answer: 'Amankan keselamatan jiwa Anda dan keluarga terlebih dahulu. Jauhi arah angin dan jilatan api. Dilarang keras mencoba memadamkan api gambut secara mandiri karena permukaan tanah gambut dapat runtuh membara di bawahnya. Segera catat patokan lokasi aman (nama jalan, desa, patokan sungai) dan hubungi Layanan Panggilan Darurat Nasional 112 atau posko BPBD setempat.',
            source: 'Prosedur Tetap BPBD & Balai PPIKHL KLHK Wilayah Kalimantan',
        },
        {
            question: 'Mengapa masker kain atau masker bedah biasa tidak efektif untuk melindungi diri dari kabut asap karhutla?',
            answer: 'Asap karhutla didominasi oleh partikel mikroskopis PM2.5 (diameter ≤ 2.5 µm), yaitu 30 kali lebih tipis dari sehelai rambut manusia. Masker kain dan masker bedah hanya menahan partikel debu kasar serta memiliki celah bocor di sisi samping. Untuk menyaring partikel mikroskopis PM2.5 agar tidak menembus alveoli paru-paru, gunakan respirator standar N95, KN95, KF94, atau FFP2 yang memiliki serat elektrostatik rapat dan segel ketat pada wajah.',
            source: 'Kementerian Kesehatan RI & World Health Organization (WHO)',
        },
        {
            question: 'Bagaimana cara membuat "Ruang Bersih" (Clean Air Room) di rumah sederhana tanpa pendingin ruangan mahal?',
            answer: 'Pilih satu ruangan tengah di rumah dengan bukaan jendela paling sedikit (misalnya kamar tidur utama). Tutup rapat jendela dan ventilasi udara menggunakan handuk basah atau lakban kain tebal. Hindari aktivitas di dalam ruangan yang menghasilkan debu atau asap (jangan merokok, jangan menyalakan obat nyamuk bakar/lilin). Bersihkan lantai dengan lap basah secara berkala dan minimalkan keluar-masuk pintu ruangan.',
            source: 'Pedoman Sanitasi Udara Darurat Bencana Asap Kemenkes RI',
        },
        {
            question: 'Siapa sajakah kelompok masyarakat yang paling rentan terhadap paparan asap dan memerlukan proteksi khusus?',
            answer: 'Kelompok rentan meliputi bayi, anak balita (karena frekuensi pernapasan mereka lebih cepat), wanita hamil (risiko hipoksia janin), lansia di atas 60 tahun, serta individu yang memiliki riwayat penyakit asma, PPOK, bronkitis kronis, maupun gangguan kardiovaskular. Kelompok ini harus sepenuhnya berada di dalam ruangan tertutup saat ISPU berada di kategori Tidak Sehat.',
            source: 'Perhimpunan Dokter Paru Indonesia (PDPI) & Klaster Kesehatan BNPB',
        },
        {
            question: 'Kapan warga harus segera mencari pertolongan medis darurat ke Puskesmas atau IGD?',
            answer: 'Segera kunjungi faskes terdekat jika muncul tanda bahaya (red flags): sesak napas berat yang tidak membaik dalam 30 menit, bibir atau kuku tampak kebiruan (sianosis), rasa nyeri dada tertekan saat bernapas, batuk berdahak kehitaman pekat, atau penderita asma yang tidak merespons inhaler darurat.',
            source: 'Protokol Triage Penanganan Korban Asap Dinkes Provinsi Kalimantan',
        },
    ];

    return (
        <div className="space-y-20">
            {/* =========================================================================
                SECTION 8: STEP-BY-STEP SAFETY GUIDE (PROTOKOL TIGA FASE KARHUTLA)
               ========================================================================= */}
            <section id="protokol-tahapan" className="scroll-mt-24">
                <div className="bg-gradient-to-br from-[#1C2526] via-[#1B4332] to-[#12161A] text-white rounded-3xl p-6 sm:p-10 lg:p-12 shadow-2xl border border-white/10 relative overflow-hidden">
                    <div className="absolute top-0 right-0 w-96 h-96 bg-[#52B788]/15 rounded-full blur-3xl pointer-events-none" />
                    <div className="absolute bottom-0 left-0 w-80 h-80 bg-[#E05A2B]/10 rounded-full blur-3xl pointer-events-none" />

                    <div className="relative z-10">
                        <div className="max-w-3xl mb-8">
                            <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 text-[#52B788] text-xs font-bold uppercase tracking-wider mb-3 border border-white/10">
                                <Sparkles className="w-3.5 h-3.5" />
                                <span>Prosedur Operasional Standar Masyarakat</span>
                            </div>
                            <h2 className="font-display font-black text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight leading-tight mb-3">
                                Protokol Keselamatan Tiga Fase Karhutla
                            </h2>
                            <p className="text-sm sm:text-base text-[#F8F9FA]/80 leading-relaxed">
                                Panduan tindakan berurutan dari kesiapsiagaan awal sebelum kemarau, respons cepat saat kabut asap melanda, hingga pemulihan sanitasi setelah bahaya mereda.
                            </p>
                        </div>

                        {/* Phase Selector Tabs */}
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-8">
                            <button
                                type="button"
                                onClick={() => setActivePhase('sebelum')}
                                className={`p-4 rounded-2xl text-left border transition-all cursor-pointer ${
                                    activePhase === 'sebelum'
                                        ? 'bg-[#1B4332] border-[#52B788] text-white shadow-lg ring-2 ring-[#52B788]/30'
                                        : 'bg-white/5 border-white/10 text-[#F8F9FA]/70 hover:bg-white/10'
                                }`}
                            >
                                <span className="text-[10px] uppercase font-bold tracking-widest text-[#52B788] block mb-1">
                                    Fase 1 • Pencegahan
                                </span>
                                <h3 className="font-bold text-sm sm:text-base text-white">
                                    Sebelum Terjadi Karhutla
                                </h3>
                            </button>

                            <button
                                type="button"
                                onClick={() => setActivePhase('saat')}
                                className={`p-4 rounded-2xl text-left border transition-all cursor-pointer ${
                                    activePhase === 'saat'
                                        ? 'bg-[#E05A2B] border-[#F4A261] text-white shadow-lg ring-2 ring-[#F4A261]/30'
                                        : 'bg-white/5 border-white/10 text-[#F8F9FA]/70 hover:bg-white/10'
                                }`}
                            >
                                <span className="text-[10px] uppercase font-bold tracking-widest text-amber-200 block mb-1">
                                    Fase 2 • Respons Darurat
                                </span>
                                <h3 className="font-bold text-sm sm:text-base text-white">
                                    Saat Terjadi Karhutla
                                </h3>
                            </button>

                            <button
                                type="button"
                                onClick={() => setActivePhase('setelah')}
                                className={`p-4 rounded-2xl text-left border transition-all cursor-pointer ${
                                    activePhase === 'setelah'
                                        ? 'bg-[#2A9D8F] border-teal-300 text-white shadow-lg ring-2 ring-teal-300/30'
                                        : 'bg-white/5 border-white/10 text-[#F8F9FA]/70 hover:bg-white/10'
                                }`}
                            >
                                <span className="text-[10px] uppercase font-bold tracking-widest text-teal-200 block mb-1">
                                    Fase 3 • Pemulihan Lingkungan
                                </span>
                                <h3 className="font-bold text-sm sm:text-base text-white">
                                    Setelah Karhutla Mereda
                                </h3>
                            </button>
                        </div>

                        {/* Phase Content Stepper Cards */}
                        <AnimatePresence mode="wait">
                            {activePhase === 'sebelum' && (
                                <motion.div
                                    key="sebelum"
                                    initial={{ opacity: 0, y: 15 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    exit={{ opacity: 0, y: -15 }}
                                    transition={{ duration: 0.25 }}
                                    className="grid grid-cols-1 md:grid-cols-2 gap-4"
                                >
                                    <div className="p-5 rounded-2xl bg-white/[0.06] border border-white/10">
                                        <div className="w-8 h-8 rounded-xl bg-[#52B788]/20 text-[#52B788] flex items-center justify-center font-bold text-xs mb-3">
                                            01
                                        </div>
                                        <h4 className="font-bold text-white text-base mb-1.5">
                                            Pantau Sumber Informasi Resmi
                                        </h4>
                                        <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed">
                                            Ketahui stasiun ISPU dan peta sebaran hotspot terdekat di platform FireAware, serta pantau rilis peringatan dini cuaca ekstrem dari BMKG Kalimantan.
                                        </p>
                                    </div>

                                    <div className="p-5 rounded-2xl bg-white/[0.06] border border-white/10">
                                        <div className="w-8 h-8 rounded-xl bg-[#52B788]/20 text-[#52B788] flex items-center justify-center font-bold text-xs mb-3">
                                            02
                                        </div>
                                        <h4 className="font-bold text-white text-base mb-1.5">
                                            Siapkan Persediaan Masker Standar N95
                                        </h4>
                                        <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed">
                                            Sediakan masker respirator partikulat (N95/KN95/KF94) minimal untuk kebutuhan 1–2 minggu seluruh anggota keluarga sebelum stok di apotek menipis.
                                        </p>
                                    </div>

                                    <div className="p-5 rounded-2xl bg-white/[0.06] border border-white/10">
                                        <div className="w-8 h-8 rounded-xl bg-[#52B788]/20 text-[#52B788] flex items-center justify-center font-bold text-xs mb-3">
                                            03
                                        </div>
                                        <h4 className="font-bold text-white text-base mb-1.5">
                                            Amankan Obat-Obatan Esensial Keluarga
                                        </h4>
                                        <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed">
                                            Siapkan inhaler cadangan bagi anggota keluarga penderita asma, obat tetes mata steril konjungtivitis, dan cairan semprot hidung pembersih jelaga.
                                        </p>
                                    </div>

                                    <div className="p-5 rounded-2xl bg-white/[0.06] border border-white/10">
                                        <div className="w-8 h-8 rounded-xl bg-[#52B788]/20 text-[#52B788] flex items-center justify-center font-bold text-xs mb-3">
                                            04
                                        </div>
                                        <h4 className="font-bold text-white text-base mb-1.5">
                                            Rencanakan Zona Bersih &amp; Rute Aman
                                        </h4>
                                        <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed">
                                            Tentukan satu ruangan tengah di rumah sebagai calon Clean Room, serta pahami akses jalur evakuasi menuju tempat pengungsian atau faskes terdekat.
                                        </p>
                                    </div>
                                </motion.div>
                            )}

                            {activePhase === 'saat' && (
                                <motion.div
                                    key="saat"
                                    initial={{ opacity: 0, y: 15 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    exit={{ opacity: 0, y: -15 }}
                                    transition={{ duration: 0.25 }}
                                    className="space-y-4"
                                >
                                    {/* Critical Warning Callout */}
                                    <div className="p-4 sm:p-5 rounded-2xl bg-[#D94E34]/20 border border-[#D94E34]/40 flex items-start gap-3.5">
                                        <AlertOctagon className="w-6 h-6 text-[#F4A261] flex-shrink-0 mt-0.5" />
                                        <div className="text-xs sm:text-sm">
                                            <h4 className="font-bold text-white mb-1">
                                                Peringatan Keselamatan Jiwa: Dilarang Mendekati Titik Api
                                            </h4>
                                            <p className="text-[#F8F9FA]/90 leading-relaxed">
                                                Masyarakat dilarang keras mencoba memadamkan kebakaran lahan secara mandiri. Api gambut membara di kedalaman tanah tanpa nyala terbuka, rapuh untuk diinjak, dan menghasilkan gas beracun mematikan. Utamakan evakuasi dan laporkan ke 112 atau posko siaga terdekat.
                                            </p>
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div className="p-5 rounded-2xl bg-white/[0.06] border border-white/10">
                                            <div className="w-8 h-8 rounded-xl bg-[#E05A2B]/20 text-[#F4A261] flex items-center justify-center font-bold text-xs mb-3">
                                                01
                                            </div>
                                            <h4 className="font-bold text-white text-base mb-1.5">
                                                Tutup Seluruh Bukaan Hunian
                                            </h4>
                                            <p className="text-xs text-[#F8F9FA]/80 leading-relaxed">
                                                Rapatkan jendela dan pintu. Sumpal celah bawah pintu dengan kain basah untuk mencegah masuknya partikel asap PM2.5.
                                            </p>
                                        </div>

                                        <div className="p-5 rounded-2xl bg-white/[0.06] border border-white/10">
                                            <div className="w-8 h-8 rounded-xl bg-[#E05A2B]/20 text-[#F4A261] flex items-center justify-center font-bold text-xs mb-3">
                                                02
                                            </div>
                                            <h4 className="font-bold text-white text-base mb-1.5">
                                                Wajib Masker N95 di Luar Ruang
                                            </h4>
                                            <p className="text-xs text-[#F8F9FA]/80 leading-relaxed">
                                                Bila terpaksa keluar rumah, kenakan respirator N95/KN95 dengan fit check rapat. Batasi waktu di luar seminimal mungkin.
                                            </p>
                                        </div>

                                        <div className="p-5 rounded-2xl bg-white/[0.06] border border-white/10">
                                            <div className="w-8 h-8 rounded-xl bg-[#E05A2B]/20 text-[#F4A261] flex items-center justify-center font-bold text-xs mb-3">
                                                03
                                            </div>
                                            <h4 className="font-bold text-white text-base mb-1.5">
                                                Ikuti Evakuasi Resmi Petugas
                                            </h4>
                                            <p className="text-xs text-[#F8F9FA]/80 leading-relaxed">
                                                Jika aparat BPBD atau Satgas menginstruksikan pengungsian, segera bawa dokumen penting, tas siaga, dan ikuti arahan petugas menuju shelter aman.
                                            </p>
                                        </div>
                                    </div>
                                </motion.div>
                            )}

                            {activePhase === 'setelah' && (
                                <motion.div
                                    key="setelah"
                                    initial={{ opacity: 0, y: 15 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    exit={{ opacity: 0, y: -15 }}
                                    transition={{ duration: 0.25 }}
                                    className="grid grid-cols-1 md:grid-cols-2 gap-4"
                                >
                                    <div className="p-5 rounded-2xl bg-white/[0.06] border border-white/10">
                                        <div className="w-8 h-8 rounded-xl bg-teal-400/20 text-teal-300 flex items-center justify-center font-bold text-xs mb-3">
                                            01
                                        </div>
                                        <h4 className="font-bold text-white text-base mb-1.5">
                                            Tunggu Konfirmasi Keamanan Wilayah
                                        </h4>
                                        <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed">
                                            Jangan terburu-buru memasuki area bekas bakaran. Pastikan BPBD dan satgas telah menyatakan zona aman dari bahaya pohon rapuh tumbang dan sisa gas beracun.
                                        </p>
                                    </div>

                                    <div className="p-5 rounded-2xl bg-white/[0.06] border border-white/10">
                                        <div className="w-8 h-8 rounded-xl bg-teal-400/20 text-teal-300 flex items-center justify-center font-bold text-xs mb-3">
                                            02
                                        </div>
                                        <h4 className="font-bold text-white text-base mb-1.5">
                                            Pembersihan Debu dengan Metode Basah
                                        </h4>
                                        <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed">
                                            Gunakan kain pel dan lap basah untuk membersihkan jelaga. Jangan menggunakan sapu kering karena akan menerbangkan kembali partikel debu mikro ke udara pernapasan.
                                        </p>
                                    </div>

                                    <div className="p-5 rounded-2xl bg-white/[0.06] border border-white/10">
                                        <div className="w-8 h-8 rounded-xl bg-teal-400/20 text-teal-300 flex items-center justify-center font-bold text-xs mb-3">
                                            03
                                        </div>
                                        <h4 className="font-bold text-white text-base mb-1.5">
                                            Pemeriksaan Kesehatan Pascabencana
                                        </h4>
                                        <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed">
                                            Periksakan anggota keluarga rentan (anak-anak dan lansia) ke Puskesmas setempat untuk skrining fungsi paru dan infeksi saluran pernapasan akut (ISPA).
                                        </p>
                                    </div>

                                    <div className="p-5 rounded-2xl bg-white/[0.06] border border-white/10">
                                        <div className="w-8 h-8 rounded-xl bg-teal-400/20 text-teal-300 flex items-center justify-center font-bold text-xs mb-3">
                                            04
                                        </div>
                                        <h4 className="font-bold text-white text-base mb-1.5">
                                            Peremajaan Filter Udara &amp; Stok Masker
                                        </h4>
                                        <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed">
                                            Ganti filter HEPA pendingin udara yang telah jenuh dengan partikel abu hitam, dan perbarui isi kotak siaga keluarga untuk kesiapsiagaan masa depan.
                                        </p>
                                    </div>
                                </motion.div>
                            )}
                        </AnimatePresence>
                    </div>
                </div>
            </section>

            {/* =========================================================================
                SECTION 9: PANDUAN PERLINDUNGAN DARI ASAP & BLUEPRINT CLEAN ROOM
               ========================================================================= */}
            <section id="mitigasi-asap" className="scroll-mt-24">
                <div className="bg-white rounded-3xl p-6 sm:p-10 border border-stone-200/80 shadow-sm">
                    <div className="max-w-3xl mb-8">
                        <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1B4332]/10 text-[#1B4332] text-xs font-bold uppercase tracking-wider mb-2">
                            <HeartPulse className="w-3.5 h-3.5 text-[#52B788]" />
                            <span>Mitigasi Kesehatan Pernapasan &amp; Sanitasi Udara</span>
                        </div>
                        <h2 className="font-display font-black text-2xl sm:text-3xl text-[#1E242B] tracking-tight">
                            Panduan Perlindungan dari Asap Karhutla &amp; Clean Room
                        </h2>
                        <p className="text-xs sm:text-sm text-stone-600 mt-2 leading-relaxed">
                            Standar ilmiah perlindungan pernapasan berbasis bukti medis terpercaya (Kemenkes RI, WHO, dan PDPI) untuk mencegah dampak jangka panjang akumulasi partikel PM2.5.
                        </p>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-8">
                        {/* Perbandingan Masker */}
                        <div className="lg:col-span-6 space-y-4">
                            <h3 className="font-display font-bold text-base text-[#1E242B] flex items-center gap-2">
                                <ShieldCheck className="w-5 h-5 text-[#1B4332]" />
                                <span>Perbandingan Efektivitas Masker terhadap Asap Karhutla</span>
                            </h3>

                            {/* Masker Bedah / Kain */}
                            <div className="p-4 rounded-2xl bg-rose-50/70 border border-rose-200">
                                <div className="flex items-center justify-between mb-2">
                                    <span className="font-bold text-xs text-rose-800 uppercase tracking-wider flex items-center gap-1">
                                        <XCircle className="w-4 h-4 text-rose-600" />
                                        Masker Kain / Masker Bedah Longgar
                                    </span>
                                    <span className="text-[10px] font-bold px-2 py-0.5 rounded bg-rose-200 text-rose-900">
                                        Tidak Direkomendasikan
                                    </span>
                                </div>
                                <p className="text-xs text-stone-600 leading-relaxed mb-2">
                                    Hanya menyaring partikel debu kasar di atas 10 mikron. Memiliki celah longgar di pipi dan hidung, sehingga partikel gas beracun dan aerosol PM2.5 tetap leluasa masuk ke alveoli paru.
                                </p>
                                <span className="text-[11px] font-semibold text-rose-700">
                                    Efisiensi PM2.5: Kurang dari 20%
                                </span>
                            </div>

                            {/* Respirator Standar N95/KN95 */}
                            <div className="p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200">
                                <div className="flex items-center justify-between mb-2">
                                    <span className="font-bold text-xs text-emerald-900 uppercase tracking-wider flex items-center gap-1">
                                        <CheckCircle2 className="w-4 h-4 text-emerald-600" />
                                        Respirator Standar N95 / KN95 / KF94
                                    </span>
                                    <span className="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-200 text-emerald-900">
                                        Rekomendasi Medis Resmi
                                    </span>
                                </div>
                                <p className="text-xs text-stone-600 leading-relaxed mb-2">
                                    Memiliki lapisan serat elektrostatik mikro dan kawat hidung fleksibel yang membentuk segel rapat pada wajah. Mampu menahan setidaknya 95% partikel mikroskopis berbahaya.
                                </p>
                                <span className="text-[11px] font-semibold text-emerald-800">
                                    Efisiensi PM2.5: Minimal 95% (Bila Fit Check Rapat)
                                </span>
                            </div>
                        </div>

                        {/* 4 Pilar Clean Room */}
                        <div className="lg:col-span-6 bg-[#F6F5F2] rounded-3xl p-5 sm:p-6 border border-stone-200/80">
                            <h3 className="font-display font-bold text-base text-[#1E242B] mb-3 flex items-center gap-2">
                                <Building2 className="w-5 h-5 text-[#E05A2B]" />
                                <span>Blueprint Ruang Bersih (Clean Air Room) di Rumah</span>
                            </h3>

                            <div className="space-y-3">
                                <div className="p-3.5 rounded-xl bg-white border border-stone-200 text-xs">
                                    <strong className="text-[#1E242B] block mb-0.5 font-bold">1. Pemilihan Lokasi Sentral</strong>
                                    <span className="text-stone-600 leading-relaxed">
                                        Gunakan kamar tidur tengah tanpa jendela luar langsung atau ruangan dengan bukaan ventilasi paling sedikit.
                                    </span>
                                </div>

                                <div className="p-3.5 rounded-xl bg-white border border-stone-200 text-xs">
                                    <strong className="text-[#1E242B] block mb-0.5 font-bold">2. Penyekatan Celah Udara</strong>
                                    <span className="text-stone-600 leading-relaxed">
                                        Tutup celah ventilasi dan bagian bawah kusen pintu menggunakan lakban tebal atau gulungan handuk lembap.
                                    </span>
                                </div>

                                <div className="p-3.5 rounded-xl bg-white border border-stone-200 text-xs">
                                    <strong className="text-[#1E242B] block mb-0.5 font-bold">3. Penyaringan Sirkulasi Internal</strong>
                                    <span className="text-stone-600 leading-relaxed">
                                        Bila memiliki alat pembersih udara (*air purifier*), gunakan filter HEPA asli dan posisikan di tengah ruangan.
                                    </span>
                                </div>

                                <div className="p-3.5 rounded-xl bg-white border border-stone-200 text-xs">
                                    <strong className="text-[#1E242B] block mb-0.5 font-bold">4. Protokol Akses Masuk</strong>
                                    <span className="text-stone-600 leading-relaxed">
                                        Lepaskan pakaian luar yang terpapar asap dan ganti pakaian bersih sebelum beristirahat di dalam ruang bersih.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Medical Red Flags Callout */}
                    <div className="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200 flex items-start gap-3.5">
                        <Info className="w-5 h-5 text-[#E05A2B] flex-shrink-0 mt-0.5" />
                        <div className="text-xs sm:text-sm text-stone-700">
                            <strong className="text-[#1E242B] block mb-1 font-bold">
                                Kapan Harus Segera Mencari Pertolongan Medis (Red Flags)?
                            </strong>
                            <p className="leading-relaxed">
                                Segera kunjungi IGD Puskesmas atau Rumah Sakit terdekat bila mengalami: sesak napas berat yang tidak membaik dalam 30 menit, nyeri dada saat bernapas, bibir atau kuku kebiruan, rasa linglung, atau anak balita yang mengalami tarikan dinding dada ke dalam (*retraksi interkostal*).
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {/* =========================================================================
                SECTION 10 & 11: PENCEGAHAN & PELAPORAN KEJADIAN TERVERIFIKASI
               ========================================================================= */}
            <section id="pelaporan-darurat" className="scroll-mt-24">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                    {/* Edukasi Pencegahan & Hukum */}
                    <div className="lg:col-span-6 bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/80 shadow-sm flex flex-col justify-between">
                        <div>
                            <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1B4332]/10 text-[#1B4332] text-xs font-bold uppercase tracking-wider mb-3">
                                <Trees className="w-3.5 h-3.5 text-[#52B788]" />
                                <span>Pencegahan &amp; Konservasi Gambut</span>
                            </div>

                            <h3 className="font-display font-black text-xl sm:text-2xl text-[#1E242B] tracking-tight mb-3">
                                Larangan Pembakaran Lahan &amp; Landasan Hukum
                            </h3>

                            <p className="text-xs sm:text-sm text-stone-600 leading-relaxed mb-6">
                                Membuka lahan dengan cara membakar di kawasan gambut tropis memicu kebakaran bawah tanah (*ground fire*) yang melanggar hukum negara dan mengancam hak hidup sehat jutaan warga.
                            </p>

                            <div className="space-y-3 mb-6">
                                <div className="p-4 rounded-2xl bg-stone-50 border border-stone-200 text-xs">
                                    <strong className="text-[#1E242B] block font-bold mb-1">
                                        UU No. 32 Tahun 2009 Pasal 69 &amp; 108
                                    </strong>
                                    <span className="text-stone-600 leading-relaxed">
                                        Setiap orang dilarang melakukan pembukaan lahan dengan cara membakar. Pelanggar diancam pidana penjara paling lama 10 hingga 15 tahun dan denda maksimal Rp 15 miliar.
                                    </span>
                                </div>

                                <div className="p-4 rounded-2xl bg-stone-50 border border-stone-200 text-xs">
                                    <strong className="text-[#1E242B] block font-bold mb-1">
                                        Dukung Restorasi &amp; Sekat Kanal BRGM
                                    </strong>
                                    <span className="text-stone-600 leading-relaxed">
                                        Dukung pemeliharaan sekat kanal (*canal blocking*) agar muka air tanah gambut tetap terjaga di atas batas kritis -40 cm sepanjang musim kemarau.
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div className="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200 text-xs text-emerald-900 font-medium flex items-center gap-2">
                            <CheckCircle2 className="w-4 h-4 text-[#2A9D8F] flex-shrink-0" />
                            <span>Hindari membuang puntung rokok di lahan gambut kering dan batasi pembakaran sampah terbuka.</span>
                        </div>
                    </div>

                    {/* Direktori Pelaporan Resmi */}
                    <div className="lg:col-span-6 bg-gradient-to-br from-[#1E242B] to-[#2B303A] text-white rounded-3xl p-6 sm:p-8 border border-white/10 shadow-lg flex flex-col justify-between">
                        <div>
                            <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#E05A2B]/20 text-[#F4A261] text-xs font-bold uppercase tracking-wider mb-3 border border-[#E05A2B]/30">
                                <PhoneCall className="w-3.5 h-3.5" />
                                <span>Kanal Pelaporan Resmi Terverifikasi</span>
                            </div>

                            <h3 className="font-display font-black text-xl sm:text-2xl text-white tracking-tight mb-2">
                                Cara Melaporkan Titik Api Tanpa Membahayakan Diri
                            </h3>

                            <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed mb-6">
                                Jangan pernah mendekati api. Catat nama desa, km jalan, patokan sungai, atau koordinat GPS dari jarak aman, lalu segera hubungi layanan darurat resmi:
                            </p>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                                <div className="p-4 rounded-2xl bg-white/5 border border-white/10 flex items-center gap-3">
                                    <div className="w-10 h-10 rounded-xl bg-[#E05A2B] text-white flex items-center justify-center font-black text-sm flex-shrink-0 shadow-md">
                                        112
                                    </div>
                                    <div>
                                        <span className="text-white font-bold text-xs block">Layanan Panggilan Darurat</span>
                                        <span className="text-[11px] text-[#F4A261]">Bebas Pulsa Nasional</span>
                                    </div>
                                </div>

                                <div className="p-4 rounded-2xl bg-white/5 border border-white/10 flex items-center gap-3">
                                    <div className="w-10 h-10 rounded-xl bg-[#D94E34] text-white flex items-center justify-center font-black text-sm flex-shrink-0 shadow-md">
                                        113
                                    </div>
                                    <div>
                                        <span className="text-white font-bold text-xs block">Pemadam Kebakaran (Damkar)</span>
                                        <span className="text-[11px] text-stone-300">Posko Kabupaten / Kota</span>
                                    </div>
                                </div>

                                <div className="p-4 rounded-2xl bg-white/5 border border-white/10 flex items-center gap-3">
                                    <div className="w-10 h-10 rounded-xl bg-[#1B4332] text-[#52B788] flex items-center justify-center font-black text-sm flex-shrink-0 border border-[#52B788]/40">
                                        SIP
                                    </div>
                                    <div>
                                        <span className="text-white font-bold text-xs block">SIPONGI KLHK RI</span>
                                        <span className="text-[11px] text-stone-300">021-5745588</span>
                                    </div>
                                </div>

                                <div className="p-4 rounded-2xl bg-white/5 border border-white/10 flex items-center gap-3">
                                    <div className="w-10 h-10 rounded-xl bg-[#2A9D8F] text-white flex items-center justify-center font-black text-sm flex-shrink-0 shadow-md">
                                        117
                                    </div>
                                    <div>
                                        <span className="text-white font-bold text-xs block">Pusdalops BNPB</span>
                                        <span className="text-[11px] text-stone-300">Penanggulangan Bencana</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="pt-4 border-t border-white/10 text-[11px] text-[#F8F9FA]/70 italic">
                            *Catatan: Seluruh nomor di atas merupakan kanal resmi Republik Indonesia. Masyarakat tidak perlu mendekati lokasi kebakaran untuk mengambil foto dokumentasi.
                        </div>
                    </div>
                </div>
            </section>

            {/* =========================================================================
                SECTION 12: INTERACTIVE SAFETY CHECKLIST (KESIAPSIAGAAN RUMAH TANGGA)
               ========================================================================= */}
            <section id="checklist-kesiapsiagaan" className="scroll-mt-24">
                <div className="bg-white rounded-3xl p-6 sm:p-10 border border-stone-200/80 shadow-md">
                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-stone-100">
                        <div>
                            <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1B4332]/10 text-[#1B4332] text-xs font-bold uppercase tracking-wider mb-2">
                                <CheckSquare className="w-3.5 h-3.5 text-[#52B788]" />
                                <span>Kesiapsiagaan Mandiri Keluarga</span>
                            </div>
                            <h2 className="font-display font-black text-2xl sm:text-3xl text-[#1E242B] tracking-tight">
                                Checklist Kesiapsiagaan Karhutla &amp; Asap
                            </h2>
                            <p className="text-xs sm:text-sm text-stone-600 mt-1">
                                Periksa kesiapan hunian dan logistik darurat keluarga Anda. Progres tersimpan otomatis di perangkat Anda.
                            </p>
                        </div>

                        <button
                            type="button"
                            onClick={handleResetChecklist}
                            className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-stone-600 bg-stone-100 hover:bg-stone-200 transition-colors self-start sm:self-auto cursor-pointer"
                            title="Reset Checklist"
                        >
                            <RotateCcw className="w-3.5 h-3.5" />
                            <span>Atur Ulang</span>
                        </button>
                    </div>

                    {/* Progress Bar Kesiapsiagaan */}
                    <div className="mb-8 p-4 sm:p-5 rounded-2xl bg-stone-50 border border-stone-200/80">
                        <div className="flex items-center justify-between text-xs sm:text-sm font-bold text-[#1E242B] mb-2">
                            <span className="flex items-center gap-2">
                                <span>Status Kesiapan:</span>
                                <span className={`px-2.5 py-0.5 rounded-full text-xs font-bold ${
                                    progressPercent === 100
                                        ? 'bg-[#52B788]/20 text-[#1B4332] border border-[#52B788]/40'
                                        : progressPercent >= 50
                                        ? 'bg-[#F4A261]/20 text-[#E05A2B] border border-[#F4A261]/40'
                                        : 'bg-stone-200 text-stone-700'
                                }`}>
                                    {progressPercent === 100
                                        ? 'Sangat Siap (100%)'
                                        : progressPercent >= 50
                                        ? `Cukup Siap (${progressPercent}%)`
                                        : `Perlu Dilengkapi (${progressPercent}%)`}
                                </span>
                            </span>
                            <span className="text-stone-500 font-medium">
                                {checkedCount} dari {initialChecklist.length} Langkah Terpenuhi
                            </span>
                        </div>

                        <div className="w-full bg-stone-200 h-2.5 rounded-full overflow-hidden">
                            <motion.div
                                className="h-full bg-gradient-to-r from-[#1B4332] via-[#52B788] to-[#2A9D8F]"
                                initial={{ width: 0 }}
                                animate={{ width: `${progressPercent}%` }}
                                transition={{ duration: 0.4, ease: 'easeOut' }}
                            />
                        </div>
                    </div>

                    {/* Checklist Items */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        {initialChecklist.map((item) => {
                            const isChecked = Boolean(checkedItems[item.id]);
                            return (
                                <div
                                    key={item.id}
                                    onClick={() => handleToggleCheck(item.id)}
                                    className={`p-4 sm:p-5 rounded-2xl border transition-all cursor-pointer flex items-start gap-3.5 select-none ${
                                        isChecked
                                            ? 'bg-emerald-50/70 border-[#52B788] shadow-xs'
                                            : 'bg-white border-stone-200 hover:border-stone-300'
                                    }`}
                                >
                                    <div className="mt-0.5 flex-shrink-0">
                                        {isChecked ? (
                                            <div className="w-5 h-5 rounded-md bg-[#1B4332] text-white flex items-center justify-center">
                                                <CheckCircle2 className="w-4 h-4 text-[#52B788]" />
                                            </div>
                                        ) : (
                                            <div className="w-5 h-5 rounded-md border-2 border-stone-300 bg-white" />
                                        )}
                                    </div>

                                    <div className="flex-1">
                                        <h4 className={`text-xs sm:text-sm font-bold mb-1 ${
                                            isChecked ? 'text-[#1B4332] line-through decoration-[#52B788]' : 'text-[#1E242B]'
                                        }`}>
                                            {item.title}
                                        </h4>
                                        <p className="text-[11px] sm:text-xs text-stone-600 leading-relaxed font-normal">
                                            {item.desc}
                                        </p>
                                    </div>
                                </div>
                            );
                        })}
                    </div>

                    <p className="text-[11px] text-stone-400 italic">
                        *Catatan Penafian: Checklist ini merupakan alat evaluasi kesiapsiagaan mandiri keluarga dan tidak menggantikan instruksi resmi evakuasi darurat dari otoritas BPBD setempat.
                    </p>
                </div>
            </section>

            {/* =========================================================================
                SECTION 13: FAQ KESELAMATAN (ACCORDION INTERAKTIF)
               ========================================================================= */}
            <section id="faq-keselamatan" className="scroll-mt-24">
                <div className="bg-white rounded-3xl p-6 sm:p-10 border border-stone-200/80 shadow-sm">
                    <div className="max-w-3xl mb-8">
                        <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1B4332]/10 text-[#1B4332] text-xs font-bold uppercase tracking-wider mb-2">
                            <HelpCircle className="w-3.5 h-3.5 text-[#52B788]" />
                            <span>Tanya Jawab Kesiapsiagaan Publik</span>
                        </div>
                        <h2 className="font-display font-black text-2xl sm:text-3xl text-[#1E242B] tracking-tight">
                            Pertanyaan Umum Seputar Keselamatan &amp; Asap Karhutla
                        </h2>
                        <p className="text-xs sm:text-sm text-stone-600 mt-2 leading-relaxed">
                            Jawaban praktis dan ringkas atas pertanyaan paling sering diajukan masyarakat terkait penanganan kondisi darurat kabut asap di Kalimantan.
                        </p>
                    </div>

                    <div className="space-y-4">
                        {faqs.map((faq, index) => {
                            const isOpen = openFaq === index;
                            return (
                                <div
                                    key={index}
                                    className={`rounded-2xl border transition-all duration-200 overflow-hidden ${
                                        isOpen
                                            ? 'bg-stone-50/90 border-[#1B4332]/30 shadow-xs'
                                            : 'bg-white border-stone-200 hover:border-stone-300'
                                    }`}
                                >
                                    <button
                                        type="button"
                                        onClick={() => setOpenFaq(isOpen ? null : index)}
                                        className="w-full text-left p-4 sm:p-5 flex items-center justify-between gap-4 cursor-pointer"
                                    >
                                        <h3 className="font-display font-bold text-sm sm:text-base text-[#1E242B] leading-snug">
                                            {faq.question}
                                        </h3>

                                        <div className={`w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 bg-stone-100 text-stone-600 transition-transform duration-300 ${
                                            isOpen ? 'rotate-180 bg-[#1B4332] text-white' : ''
                                        }`}>
                                            <ChevronDown className="w-4 h-4" />
                                        </div>
                                    </button>

                                    <AnimatePresence initial={false}>
                                        {isOpen && (
                                            <motion.div
                                                initial={{ height: 0, opacity: 0 }}
                                                animate={{ height: 'auto', opacity: 1 }}
                                                exit={{ height: 0, opacity: 0 }}
                                                transition={{ duration: 0.25, ease: 'easeInOut' }}
                                            >
                                                <div className="px-4 pb-5 sm:px-5 pt-2 border-t border-stone-200/60 bg-white/70">
                                                    <p className="text-xs sm:text-sm text-stone-700 leading-relaxed font-normal mb-3">
                                                        {faq.answer}
                                                    </p>

                                                    <div className="text-[11px] text-stone-500 pt-2 border-t border-stone-100 flex items-center gap-1.5">
                                                        <span className="font-semibold text-stone-600">Rujukan Otoritas:</span>
                                                        <span>{faq.source}</span>
                                                    </div>
                                                </div>
                                            </motion.div>
                                        )}
                                    </AnimatePresence>
                                </div>
                            );
                        })}
                    </div>
                </div>
            </section>
        </div>
    );
}

export default SafetyInteractiveSection;

