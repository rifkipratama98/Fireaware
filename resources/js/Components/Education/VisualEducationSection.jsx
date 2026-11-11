import React, { useState } from 'react';
import { motion, AnimatePresence } from 'motion/react';
import {
    Flame,
    Trees,
    Layers,
    Sparkles,
    Sun,
    AlertCircle,
    CheckCircle2,
    XCircle,
    ArrowRight,
    ArrowLeft,
    HelpCircle,
    RotateCcw,
    Award,
    ChevronDown,
    Check,
    TrendingDown,
} from 'lucide-react';

export function VisualEducationSection() {
    const [activeTab, setActiveTab] = useState('gambut');
    const [openFaq, setOpenFaq] = useState(0);

    // STATE KUIS INTERAKTIF (Step-by-step Learning Experience)
    const [currentQuestionIndex, setCurrentQuestionIndex] = useState(0);
    const [selectedOption, setSelectedOption] = useState(null); // Option yang dipilih sebelum konfirmasi
    const [userAnswers, setUserAnswers] = useState({}); // { [questionId]: { chosen: number, isConfirmed: boolean, isCorrect: boolean } }
    const [quizFinished, setQuizFinished] = useState(false);

    // Soal Kuis Ilmiah Berbasis Riset Lingkungan (5 Pertanyaan Solid)
    const quizQuestions = [
        {
            id: 1,
            question: 'Mengapa kebakaran pada lahan gambut (ground fire) jauh lebih sulit dipadamkan dibanding kebakaran hutan tanah mineral?',
            options: [
                'Karena api membakar tajuk pohon yang terlalu tinggi untuk dijangkau regu darat.',
                'Karena bara api merambat lambat di kedalaman tanah tanpa nyala terbuka (smoldering) di bawah permukaan.',
                'Karena air di hutan tropis tidak dapat membasahi daun-daunan secara merata.',
                'Karena suhu udara siang hari di Kalimantan selalu di atas 45°C sepanjang tahun.',
            ],
            correct: 1,
            explanation: 'Lahan gambut tersusun dari timbunan bahan organik ribuan tahun. Saat mengering, bara api merambat di kedalaman 50 cm hingga beberapa meter di bawah permukaan (ground fire). Siraman water bombing dari helikopter hanya membasahi 5-10 cm permukaan atas, sehingga bara di kedalaman tanah tetap menyala.',
            reference: 'Badan Restorasi Gambut dan Mangrove (BRGM) & Puslitbang KLHK',
        },
        {
            id: 2,
            question: 'Berapakah batas ambang kedalaman muka air tanah gambut yang aman untuk mencegah kerentanan karhutla menurut regulasi?',
            options: [
                'Maksimal 40 cm di bawah permukaan tanah (-0,4 meter).',
                'Minimal 2 meter di bawah permukaan tanah.',
                'Harus kering total tanpa genangan air agar akar pohon leluasa bernapas.',
                'Maksimal 150 cm di bawah permukaan tanah.',
            ],
            correct: 0,
            explanation: 'Berdasarkan PP No. 71/2014 jo. PP No. 57/2016, parameter hidrologis ekosistem gambut dinyatakan rusak apabila muka air tanah lebih dalam dari 0,4 meter (40 cm) di bawah permukaan tanah. Jika air turun melewati batas kritis ini, kubah gambut mengering dan sangat rentan tersulut api.',
            reference: 'Peraturan Pemerintah No. 71 Tahun 2014 & Standar Teknis BRGM RI',
        },
        {
            id: 3,
            question: 'Bagaimana peran sekat kanal (canal blocking) dalam mencegah kebakaran di kawasan rawa gambut?',
            options: [
                'Untuk mengalirkan air gambut secepat-cepatnya menuju muara laut.',
                'Membendung aliran air drainase agar kubah gambut tetap jenuh air dan basah sepanjang tahun.',
                'Sebagai jembatan penyeberangan hewan liar saat terjadi kebakaran hutan.',
                'Sebagai tempat penampungan abu sisa kebakaran lahan.',
            ],
            correct: 1,
            explanation: 'Kanal drainase buatan menyebabkan air kubah gambut terkuras habis. Sekat kanal dibangun untuk membendung aliran keluar, menaikkan kembali tinggi muka air tanah mendekati permukaan, dan mempertahankan kelembapan gambut di atas titik jenuh api.',
            reference: 'Pedoman Restorasi Hidrologis BRGM & Pusat Studi Bencana IPB',
        },
        {
            id: 4,
            question: 'Masker jenis apa yang secara klinis efektif menyaring partikel mikroskopis PM2.5 dari kabut asap karhutla?',
            options: [
                'Masker kain katun tipis 1 lapis.',
                'Masker bedah (surgical mask) longgar.',
                'Respirator standar N95 / KN95 dengan segel rapat pada kontur wajah.',
                'Syal atau bandana kain basah.',
            ],
            correct: 2,
            explanation: 'Partikel utama asap karhutla berukuran PM2.5 (≤ 2,5 mikrometer), 30 kali lebih kecil dari diameter rambut manusia. Hanya respirator standar N95/KN95 dengan efisiensi filtrasi elektrostatik minimal 95% yang mampu menahan partikel mikroskopis menembus alveolus paru-paru.',
            reference: 'World Health Organization (WHO) & Perhimpunan Dokter Paru Indonesia (PDPI)',
        },
        {
            id: 5,
            question: 'Bagaimana ketentuan hukum dan sanksi pidana bagi pelaku pembakaran hutan dan lahan di Indonesia?',
            options: [
                'Tidak ada sanksi hukum selama pembakaran dilakukan di lahan milik pribadi.',
                'Diancam pidana penjara hingga 15 tahun dan denda Rp 5 miliar, serta korporasi konsesi dikenakan tanggung jawab mutlak (strict liability).',
                'Hanya dikenakan teguran lisan administratif oleh aparat desa.',
                'Hanya diwajibkan menanam 1 bibit pohon pengganti di lokasi bekas bakaran.',
            ],
            correct: 1,
            explanation: 'Berdasarkan Pasal 69 dan Pasal 108 UU No. 32 Tahun 2009 (PPLH) serta UU Kehutanan No. 41/1999, pembakaran hutan dan lahan diancam pidana penjara paling lama 15 tahun dan denda hingga Rp 5 miliar. Bagi korporasi pemegang konsesi berlaku doktrin strict liability (tanggung jawab mutlak) atas kebakaran di wilayah izinnya.',
            reference: 'UU No. 32 Tahun 2009 (PPLH) & Ditjen Penegakan Hukum (Gakkum) KLHK',
        },
    ];

    const currentQ = quizQuestions[currentQuestionIndex];
    const currentAnswerState = userAnswers[currentQ?.id];
    const isCurrentConfirmed = currentAnswerState?.isConfirmed;

    // Pemilihan opsi (sebelum konfirmasi)
    const handleSelectOption = (index) => {
        if (isCurrentConfirmed) return;
        setSelectedOption(index);
    };

    // Konfirmasi jawaban
    const handleConfirmAnswer = () => {
        if (selectedOption === null || isCurrentConfirmed) return;

        setUserAnswers((prev) => ({
            ...prev,
            [currentQ.id]: {
                chosen: selectedOption,
                isConfirmed: true,
                isCorrect: selectedOption === currentQ.correct,
            },
        }));
    };

    // Navigasi ke pertanyaan berikutnya atau tampilkan hasil
    const handleNextQuestion = () => {
        if (currentQuestionIndex < quizQuestions.length - 1) {
            const nextIdx = currentQuestionIndex + 1;
            setCurrentQuestionIndex(nextIdx);
            setSelectedOption(userAnswers[quizQuestions[nextIdx].id]?.chosen ?? null);
        } else {
            setQuizFinished(true);
        }
    };

    // Navigasi ke pertanyaan sebelumnya
    const handlePrevQuestion = () => {
        if (currentQuestionIndex > 0) {
            const prevIdx = currentQuestionIndex - 1;
            setCurrentQuestionIndex(prevIdx);
            setSelectedOption(userAnswers[quizQuestions[prevIdx].id]?.chosen ?? null);
        }
    };

    // Reset kuis total
    const handleResetQuiz = () => {
        setCurrentQuestionIndex(0);
        setSelectedOption(null);
        setUserAnswers({});
        setQuizFinished(false);
    };

    // Kalkulasi Skor
    const totalQuestions = quizQuestions.length;
    const answeredCount = Object.keys(userAnswers).filter((k) => userAnswers[k].isConfirmed).length;
    const correctCount = quizQuestions.filter((q) => userAnswers[q.id]?.isCorrect).length;
    const wrongCount = answeredCount - correctCount;
    const scorePercentage = Math.round((correctCount / totalQuestions) * 100);

    // Data Mitos vs Fakta Lengkap dengan Referensi Ilmiah
    const faqs = [
        {
            mitos: 'Hutan hujan tropis alami di Kalimantan bisa terbakar dengan sendirinya karena terik panas matahari.',
            fakta: 'Hutan hujan tropis primer memiliki iklim mikro yang sangat lembap dengan tutupan kanopi rapat. Secara ilmiah, vegetasi tropis basah tidak memiliki sifat menyala spontan (spontaneous combustion). Lebih dari 99% kebakaran di Kalimantan disulut oleh aktivitas manusia: pembersihan lahan murah, puntung rokok sembarangan, perapian liar, atau konflik batas lahan.',
            referensi: 'Pusat Riset Kebencanaan Geologis BRIN & Puslitbang KLHK',
        },
        {
            mitos: 'Jika kobaran api di permukaan tanah sudah padam, berarti kebakaran gambut sudah aman terkendali.',
            fakta: 'Sering kali permukaan tanah tampak dingin dan padam, padahal bara api (smoldering ground fire) tetap merayap aktif di kedalaman 50 cm hingga beberapa meter di bawah tanah. Angin kencang atau penurunan kelembapan udara dapat memicu api bawah tanah mencuat kembali ke permukaan secara tak terduga.',
            referensi: 'Badan Restorasi Gambut dan Mangrove (BRGM) & Manggala Agni',
        },
        {
            mitos: 'Membakar lahan gambut sebelum ditanami adalah cara terbaik untuk menyuburkan tanah pertanian.',
            fakta: 'Abu sisa bakaran memang meningkatkan pH tanah sesaat, namun membunuh bakteri dan fungi pengurai bermanfaat di dalam gambut. Pembakaran juga melenyapkan kubah gambut yang butuh ribuan tahun terbentuk, memicu amblesan tanah permanen (subsidence) dan banjir menahun.',
            referensi: 'Balai Penelitian Tanah Kementan & Pusat Studi Gambut IPB',
        },
        {
            mitos: 'Helikopter water bombing dari udara adalah solusi paling tuntas untuk memadamkan karhutla gambut.',
            fakta: 'Water bombing dari udara berfungsi menahan jilatan api permukaan (surface fire) agar tidak merembet ke pemukiman warga. Namun, kunci pemadaman total bara tanah gambut adalah pembasahan intensif dari bawah tanah melalui sumur bor, penyuntikan nosel gambut, dan pemulihan sekat kanal.',
            referensi: 'Satgas Penanggulangan Karhutla BNPB & BPBD Kalimantan Tengah',
        },
    ];

    return (
        <div className="space-y-16">
            {/* 1. VISUAL STORYTELLING: INFOGRAFIS PROSES KARHUTLA DENGAN KONEKTOR PROSES */}
            <motion.div
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true, margin: '-50px' }}
                transition={{ duration: 0.5 }}
                className="bg-gradient-to-br from-[#1C2526] via-[#1B4332] to-[#12161A] text-white rounded-3xl p-6 sm:p-10 lg:p-12 shadow-2xl border border-white/10 relative overflow-hidden"
            >
                <div className="absolute top-0 right-0 w-96 h-96 bg-[#52B788]/15 rounded-full blur-3xl pointer-events-none" />
                <div className="absolute bottom-0 left-0 w-80 h-80 bg-[#E05A2B]/10 rounded-full blur-3xl pointer-events-none" />

                <div className="relative z-10">
                    <div className="max-w-3xl mb-8">
                        <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 text-[#52B788] text-xs font-bold uppercase tracking-wider mb-3 border border-white/10">
                            <Sparkles className="w-3.5 h-3.5" />
                            <span>Infografis Sains Lingkungan</span>
                        </div>
                        <h2 className="font-display font-black text-2xl sm:text-3xl lg:text-4xl text-white tracking-tight leading-tight mb-3">
                            Anatomi &amp; Proses Ilmiah Karhutla Kalimantan
                        </h2>
                        <p className="text-sm sm:text-base text-[#F8F9FA]/80 leading-relaxed">
                            Pahami struktur kedalaman kubah gambut, mekanisme perambatan bara bawah tanah, serta solusi restorasi hidrologis berbasis bukti ilmiah resmi.
                        </p>
                    </div>

                    {/* Segmented Navigation Tabs */}
                    <div className="flex flex-wrap gap-2 mb-8 pb-3 border-b border-white/10">
                        <button
                            type="button"
                            onClick={() => setActiveTab('gambut')}
                            className={`px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 cursor-pointer ${
                                activeTab === 'gambut'
                                    ? 'bg-[#E05A2B] text-white shadow-lg'
                                    : 'bg-white/5 text-[#F8F9FA]/70 hover:bg-white/10 hover:text-white'
                            }`}
                        >
                            <Layers className="w-4 h-4" />
                            <span>Profil Kedalaman Lahan Gambut</span>
                        </button>

                        <button
                            type="button"
                            onClick={() => setActiveTab('3r')}
                            className={`px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 cursor-pointer ${
                                activeTab === '3r'
                                    ? 'bg-[#52B788] text-[#12161A] shadow-lg'
                                    : 'bg-white/5 text-[#F8F9FA]/70 hover:bg-white/10 hover:text-white'
                            }`}
                        >
                            <Trees className="w-4 h-4" />
                            <span>Prinsip 3R Restorasi Gambut (BRGM)</span>
                        </button>

                        <button
                            type="button"
                            onClick={() => setActiveTab('segitiga')}
                            className={`px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold transition-all flex items-center gap-2 cursor-pointer ${
                                activeTab === 'segitiga'
                                    ? 'bg-[#F4A261] text-[#12161A] shadow-lg'
                                    : 'bg-white/5 text-[#F8F9FA]/70 hover:bg-white/10 hover:text-white'
                            }`}
                        >
                            <Flame className="w-4 h-4" />
                            <span>Segitiga Api Karhutla &amp; Anomali Iklim</span>
                        </button>
                    </div>

                    {/* Tab 1: Anatomi Kedalaman Lahan Gambut dengan Visual Connector */}
                    {activeTab === 'gambut' && (
                        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                            {/* Keterangan Tahapan Berhirarki dengan Konektor Visual */}
                            <div className="lg:col-span-6 space-y-3 relative">
                                {/* Tahap 1: Pengeringan */}
                                <div className="p-4 rounded-2xl bg-[#E05A2B]/15 border border-[#E05A2B]/30 relative z-10">
                                    <div className="flex items-center justify-between mb-1">
                                        <span className="text-[#F4A261] text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                                            <TrendingDown className="w-4 h-4" /> Tahap 1: Penurunan Muka Air (&lt; -40 cm)
                                        </span>
                                        <span className="text-[10px] bg-[#E05A2B]/30 px-2 py-0.5 rounded text-white font-bold">Ambang Kritis</span>
                                    </div>
                                    <p className="text-xs sm:text-sm text-[#F8F9FA]/90 leading-relaxed">
                                        Kanal drainase buatan menguras cadangan air kubah gambut. Saat muka air tanah turun melewati batas kritis minus 40 cm, struktur gambut mengering dan berubah menjadi bahan bakar padat berpori yang sangat mudah tersulut.
                                    </p>
                                </div>

                                {/* Visual Connector 1 */}
                                <div className="flex items-center justify-center my-1 text-[#F4A261]/80">
                                    <span className="text-xs font-bold flex items-center gap-1 bg-white/10 px-3 py-0.5 rounded-full border border-white/10">
                                        <span>Menyebabkan Kerentanan</span>
                                        <ChevronDown className="w-3.5 h-3.5" />
                                    </span>
                                </div>

                                {/* Tahap 2: Api Permukaan */}
                                <div className="p-4 rounded-2xl bg-white/5 border border-white/10 relative z-10">
                                    <div className="flex items-center justify-between mb-1">
                                        <span className="text-[#F4A261] text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                                            <Flame className="w-4 h-4" /> Tahap 2: Api Permukaan (Surface Fire)
                                        </span>
                                        <span className="text-[10px] bg-white/10 px-2 py-0.5 rounded text-white/70">Kedalaman: 0 cm</span>
                                    </div>
                                    <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed">
                                        Sulutan api (biasanya dari pembakaran lahan atau puntung rokok) menjalar cepat membakar tajuk pohon, semak belukar, dan serasah kering dengan suhu mencapai 500°C–800°C.
                                    </p>
                                </div>

                                {/* Visual Connector 2 */}
                                <div className="flex items-center justify-center my-1 text-[#E63946]/90">
                                    <span className="text-xs font-bold flex items-center gap-1 bg-white/10 px-3 py-0.5 rounded-full border border-white/10">
                                        <span>Merambat ke Bawah Permukaan</span>
                                        <ChevronDown className="w-3.5 h-3.5" />
                                    </span>
                                </div>

                                {/* Tahap 3: Bara Merambat */}
                                <div className="p-4 rounded-2xl bg-white/5 border border-white/10 relative z-10">
                                    <div className="flex items-center justify-between mb-1">
                                        <span className="text-[#E63946] text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                                            <Layers className="w-4 h-4" /> Tahap 3: Bara Bawah Tanah (Smoldering Ground Fire)
                                        </span>
                                        <span className="text-[10px] bg-white/10 px-2 py-0.5 rounded text-white/70">Kedalaman: 50 cm – 4 m</span>
                                    </div>
                                    <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed">
                                        Bara merambat perlahan di bawah tanah tanpa nyala terbuka. Pembakaran tidak sempurna (*incomplete combustion*) menghasilkan emisi CO dan aerosol PM2.5 yang sangat pekat dan berbahaya bagi pernapasan.
                                    </p>
                                </div>
                            </div>

                            {/* Diagram SVG Interaktif */}
                            <div className="lg:col-span-6 bg-black/40 rounded-3xl p-6 border border-white/15">
                                <h3 className="text-xs font-bold uppercase tracking-wider text-[#F4A261] mb-3 text-center">
                                    Profil Penampang Kedalaman Lahan Gambut
                                </h3>
                                <svg
                                    viewBox="0 0 500 320"
                                    className="w-full h-auto rounded-2xl overflow-hidden shadow-inner bg-[#1A1F24]"
                                    aria-label="Diagram Penampang Kebakaran Gambut"
                                >
                                    {/* Vegetasi Hutan Atas */}
                                    <rect x="0" y="0" width="500" height="70" fill="#1C2E24" />
                                    <path d="M 40 70 L 60 20 L 80 70 Z" fill="#2D5A3C" />
                                    <path d="M 120 70 L 140 30 L 160 70 Z" fill="#3E7D53" />
                                    <path d="M 220 70 L 250 15 L 280 70 Z" fill="#2D5A3C" />
                                    <path d="M 340 70 L 360 35 L 380 70 Z" fill="#3E7D53" />
                                    <path d="M 420 70 L 440 25 L 460 70 Z" fill="#2D5A3C" />

                                    {/* Api Permukaan */}
                                    <path d="M 200 68 Q 210 40 220 68 Q 230 45 240 68" stroke="#E05A2B" strokeWidth="4" fill="none" />
                                    <text x="250" y="55" fill="#F4A261" fontSize="11" fontWeight="bold">Surface Fire (Api Permukaan)</text>

                                    {/* Lapisan Gambut Kering (0 - 40 cm) */}
                                    <rect x="0" y="70" width="500" height="70" fill="#3D2B1F" />
                                    <text x="20" y="95" fill="#E9C46A" fontSize="12" fontWeight="bold">Zona Gambut Kering (Rawan Terbakar)</text>
                                    <text x="20" y="115" fill="#D5C5B5" fontSize="10">Kedalaman 0 s/d 40 cm • Sangat mudah terbakar</text>

                                    {/* Titik Bara Api Merambat */}
                                    <circle cx="210" cy="105" r="8" fill="#E63946" className="animate-pulse" />
                                    <circle cx="235" cy="115" r="6" fill="#E05A2B" />
                                    <circle cx="270" cy="110" r="10" fill="#D94E34" className="animate-pulse" />
                                    <circle cx="305" cy="120" r="7" fill="#E63946" />
                                    <text x="325" y="115" fill="#FF8A7A" fontSize="10" fontWeight="bold">Bara Bawah Tanah (Smoldering)</text>

                                    {/* Garis Batas Muka Air Tanah (-40 cm) */}
                                    <line x1="0" y1="140" x2="500" y2="140" stroke="#52B788" strokeWidth="2.5" strokeDasharray="6,4" />
                                    <text x="315" y="136" fill="#52B788" fontSize="10" fontWeight="bold">Batas Muka Air Tanah Aman (-40 cm)</text>

                                    {/* Lapisan Gambut Basah / Jenuh Air */}
                                    <rect x="0" y="140" width="500" height="110" fill="#241E19" />
                                    <text x="20" y="170" fill="#52B788" fontSize="12" fontWeight="bold">Zona Gambut Basah / Jenuh Air</text>
                                    <text x="20" y="190" fill="#A8B2A9" fontSize="10">Kadar air tinggi &gt; 500% • Kebal dari rambatan bara api</text>

                                    {/* Lapisan Liat / Substrat Dasar */}
                                    <rect x="0" y="250" width="500" height="70" fill="#151719" />
                                    <text x="20" y="280" fill="#889299" fontSize="11" fontWeight="bold">Substrat Tanah Mineral Dasar Kubah Gambut</text>
                                </svg>
                                <p className="text-[11px] text-[#F8F9FA]/60 mt-3 text-center">
                                    Sumber rujukan: Standar inventarisasi ekosistem gambut Badan Restorasi Gambut &amp; Mangrove (BRGM).
                                </p>
                            </div>
                        </div>
                    )}

                    {/* Tab 2: Prinsip 3R Restorasi Gambut dengan Konektor Horizontal */}
                    {activeTab === '3r' && (
                        <div>
                            {/* Horizontal Process Steps */}
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 relative">
                                <div className="p-6 rounded-3xl bg-white/5 border border-white/10 hover:border-[#52B788]/50 transition-all flex flex-col justify-between">
                                    <div>
                                        <div className="flex items-center justify-between mb-3">
                                            <span className="w-10 h-10 rounded-2xl bg-[#52B788]/20 text-[#52B788] flex items-center justify-center font-black text-lg border border-[#52B788]/30">
                                                1R
                                            </span>
                                            <span className="text-[11px] font-bold text-[#52B788] uppercase tracking-wider">Langkah Utama</span>
                                        </div>
                                        <h3 className="font-display font-extrabold text-lg text-white mb-2">
                                            Rewetting (Pembasahan)
                                        </h3>
                                        <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed mb-4">
                                            Membendung kanal-kanal drainase liar menggunakan sekat kanal (*canal blocking*) dan membangun sumur bor cadangan untuk menjaga muka air tanah tetap $\ge -40\text{ cm}$.
                                        </p>
                                    </div>
                                    <div className="pt-3 border-t border-white/10 text-xs text-[#52B788] font-bold">
                                        Target: Mengembalikan hidrologi kubah
                                    </div>
                                </div>

                                <div className="p-6 rounded-3xl bg-white/5 border border-white/10 hover:border-[#52B788]/50 transition-all flex flex-col justify-between">
                                    <div>
                                        <div className="flex items-center justify-between mb-3">
                                            <span className="w-10 h-10 rounded-2xl bg-[#52B788]/20 text-[#52B788] flex items-center justify-center font-black text-lg border border-[#52B788]/30">
                                                2R
                                            </span>
                                            <span className="text-[11px] font-bold text-[#52B788] uppercase tracking-wider">Langkah Lanjutan</span>
                                        </div>
                                        <h3 className="font-display font-extrabold text-lg text-white mb-2">
                                            Revegetation (Penanaman)
                                        </h3>
                                        <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed mb-4">
                                            Menanam kembali spesies pohon asli rawa gambut yang adaptif terhadap genangan air asam: Belangeran (*Shorea balangeran*), Jelutung Rawa (*Dyera polyphylla*), dan Ramin.
                                        </p>
                                    </div>
                                    <div className="pt-3 border-t border-white/10 text-xs text-[#52B788] font-bold">
                                        Target: Restorasi tutupan tajuk kanopi
                                    </div>
                                </div>

                                <div className="p-6 rounded-3xl bg-white/5 border border-white/10 hover:border-[#52B788]/50 transition-all flex flex-col justify-between">
                                    <div>
                                        <div className="flex items-center justify-between mb-3">
                                            <span className="w-10 h-10 rounded-2xl bg-[#52B788]/20 text-[#52B788] flex items-center justify-center font-black text-lg border border-[#52B788]/30">
                                                3R
                                            </span>
                                            <span className="text-[11px] font-bold text-[#52B788] uppercase tracking-wider">Keberlanjutan</span>
                                        </div>
                                        <h3 className="font-display font-extrabold text-lg text-white mb-2">
                                            Revitalization (Ekonomi)
                                        </h3>
                                        <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed mb-4">
                                            Memberdayakan masyarakat dengan mata pencaharian ramah gambut (*paludikultur*): budidaya madu kelulut, perikanan air asam, dan anyaman purun tanpa membakar lahan.
                                        </p>
                                    </div>
                                    <div className="pt-3 border-t border-white/10 text-xs text-[#52B788] font-bold">
                                        Target: Kesejahteraan tanpa tebas-bakar
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Tab 3: Segitiga Api Karhutla */}
                    {activeTab === 'segitiga' && (
                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div className="p-6 rounded-3xl bg-white/5 border border-white/10">
                                <div className="w-10 h-10 rounded-xl bg-[#E05A2B]/20 text-[#E05A2B] flex items-center justify-center mb-3">
                                    <Flame className="w-5 h-5" />
                                </div>
                                <h3 className="font-bold text-white text-base mb-2">1. Bahan Bakar (Fuel)</h3>
                                <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed">
                                    Timbunan bahan organik gambut dan serasah kering yang terbentuk selama ribuan tahun. Tanpa pengeringan melalui kanal drainase, biomassa ini tidak akan mudah terbakar.
                                </p>
                            </div>

                            <div className="p-6 rounded-3xl bg-white/5 border border-white/10">
                                <div className="w-10 h-10 rounded-xl bg-[#F4A261]/20 text-[#F4A261] flex items-center justify-center mb-3">
                                    <Sun className="w-5 h-5" />
                                </div>
                                <h3 className="font-bold text-white text-base mb-2">2. Cuaca &amp; Oksigen (Climate)</h3>
                                <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed">
                                    Fase kering anomali El Niño dan Indian Ocean Dipole (IOD) positif memicu Hari Tanpa Hujan (HTH) panjang, menurunkan kelembapan udara dan menyuplai oksigen pemicu rambatan api.
                                </p>
                            </div>

                            <div className="p-6 rounded-3xl bg-white/5 border border-white/10">
                                <div className="w-10 h-10 rounded-xl bg-[#E63946]/20 text-[#E63946] flex items-center justify-center mb-3">
                                    <AlertCircle className="w-5 h-5" />
                                </div>
                                <h3 className="font-bold text-white text-base mb-2">3. Sulutan Panas (Ignition)</h3>
                                <p className="text-xs sm:text-sm text-[#F8F9FA]/80 leading-relaxed">
                                    99% berasal dari ulah manusia: pembukaan lahan murah (*slash-and-burn*), puntung rokok sembarangan, serta perapian pencari ikan di sepanjang sungai yang ditinggalkan menyala.
                                </p>
                            </div>
                        </div>
                    )}
                </div>
            </motion.div>

            {/* 2. MITOS VS FAKTA (ACCORDION HALUS DENGAN RUJUKAN OTORITAS) */}
            <div className="bg-white rounded-3xl p-6 sm:p-10 border border-stone-200/80 shadow-sm">
                <div className="max-w-3xl mb-8">
                    <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1B4332]/10 text-[#1B4332] text-xs font-bold uppercase tracking-wider mb-2">
                        <HelpCircle className="w-3.5 h-3.5" />
                        <span>Klarifikasi Sains &amp; Fakta Lapangan</span>
                    </div>
                    <h2 className="font-display font-black text-2xl sm:text-3xl text-[#1E242B] tracking-tight">
                        Mitos vs. Fakta Seputar Karhutla Kalimantan
                    </h2>
                    <p className="text-xs sm:text-sm text-stone-600 mt-2 leading-relaxed">
                        Koreksi miskonsepsi umum mengenai kebakaran hutan tropis, lahan gambut, dan dampak kabut asap berdasarkan rujukan ilmiah resmi.
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
                                    <div className="flex items-start gap-3">
                                        <span className="inline-flex items-center gap-1 text-[11px] font-black px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 mt-0.5 uppercase tracking-wider flex-shrink-0 border border-rose-200">
                                            <XCircle className="w-3.5 h-3.5 text-rose-600" />
                                            Mitos
                                        </span>
                                        <h3 className="font-display font-bold text-sm sm:text-base text-[#1E242B] leading-snug">
                                            &ldquo;{faq.mitos}&rdquo;
                                        </h3>
                                    </div>

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
                                                <div className="flex items-start gap-3 mb-3">
                                                    <span className="inline-flex items-center gap-1 text-[11px] font-black px-2.5 py-0.5 rounded-full bg-[#52B788]/20 text-[#1B4332] mt-0.5 uppercase tracking-wider flex-shrink-0 border border-[#52B788]/30">
                                                        <CheckCircle2 className="w-3.5 h-3.5 text-[#2A9D8F]" />
                                                        Fakta Ilmiah
                                                    </span>
                                                    <p className="text-xs sm:text-sm text-stone-700 leading-relaxed font-normal">
                                                        {faq.fakta}
                                                    </p>
                                                </div>

                                                <div className="text-[11px] text-stone-500 pt-2 border-t border-stone-100 flex items-center gap-1.5 pl-1">
                                                    <span className="font-semibold text-stone-600">Rujukan Otoritas:</span>
                                                    <span>{faq.referensi}</span>
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

            {/* 3. KUIS INTERAKTIF — PRIORITAS UTAMA (Step-by-Step Learning Experience) */}
            <div className="bg-[#F8F9FA] rounded-3xl p-6 sm:p-10 border border-stone-300/80 shadow-md">
                {!quizFinished ? (
                    <div>
                        {/* A. Header & Animated Progress Indicator */}
                        <div className="mb-6 pb-4 border-b border-stone-200">
                            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                                <div className="flex items-center gap-2">
                                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1B4332]/10 text-[#1B4332] text-xs font-bold uppercase tracking-wider">
                                        <Award className="w-3.5 h-3.5 text-[#52B788]" />
                                        <span>Uji Pemahaman Mandiri</span>
                                    </span>
                                    <span className="text-xs text-stone-600 font-bold">
                                        Pertanyaan {currentQuestionIndex + 1} dari {totalQuestions}
                                    </span>
                                </div>

                                <button
                                    type="button"
                                    onClick={handleResetQuiz}
                                    className="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-semibold text-stone-600 bg-white hover:bg-stone-200 border border-stone-300 transition-colors self-start sm:self-auto cursor-pointer"
                                >
                                    <RotateCcw className="w-3.5 h-3.5" />
                                    <span>Mulai Dari Awal</span>
                                </button>
                            </div>

                            {/* Animated Progress Bar */}
                            <div className="w-full bg-stone-200 h-2.5 rounded-full overflow-hidden">
                                <motion.div
                                    className="h-full bg-gradient-to-r from-[#1B4332] to-[#52B788]"
                                    initial={{ width: 0 }}
                                    animate={{ width: `${((currentQuestionIndex + 1) / totalQuestions) * 100}%` }}
                                    transition={{ duration: 0.35, ease: 'easeOut' }}
                                />
                            </div>
                        </div>

                        {/* B. Pertanyaan dengan Animasi Slide Halus */}
                        <AnimatePresence mode="wait">
                            <motion.div
                                key={currentQ.id}
                                initial={{ opacity: 0, x: 15 }}
                                animate={{ opacity: 1, x: 0 }}
                                exit={{ opacity: 0, x: -15 }}
                                transition={{ duration: 0.2 }}
                                className="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200/90 shadow-sm mb-6"
                            >
                                <span className="text-[11px] font-bold text-[#E05A2B] uppercase tracking-wider block mb-1">
                                    Modul Pertanyaan #{currentQuestionIndex + 1}
                                </span>
                                <h3 className="font-display font-extrabold text-base sm:text-lg md:text-xl text-[#1E242B] mb-6 leading-snug">
                                    {currentQ.question}
                                </h3>

                                {/* Opsi Pilihan */}
                                <div className="space-y-3 mb-6">
                                    {currentQ.options.map((opt, optIndex) => {
                                        const isSelected = selectedOption === optIndex;
                                        const isCorrect = optIndex === currentQ.correct;

                                        let containerStyle = 'bg-stone-50 hover:bg-stone-100 border-stone-200 text-stone-700';

                                        if (isCurrentConfirmed) {
                                            if (isCorrect) {
                                                // Warna Sage Leaf untuk jawaban benar
                                                containerStyle = 'bg-[#52B788]/20 border-[#52B788] text-[#1B4332] font-semibold';
                                            } else if (currentAnswerState.chosen === optIndex) {
                                                // Warna Flame Terracotta untuk jawaban salah
                                                containerStyle = 'bg-[#D94E34]/15 border-[#D94E34] text-[#D94E34] font-semibold';
                                            } else {
                                                containerStyle = 'bg-stone-50/50 border-stone-200 text-stone-400 opacity-60';
                                            }
                                        } else if (isSelected) {
                                            // Selected state sebelum dikonfirmasi
                                            containerStyle = 'bg-[#1B4332]/10 border-[#1B4332] text-[#1B4332] font-semibold ring-2 ring-[#1B4332]/20';
                                        }

                                        return (
                                            <button
                                                key={optIndex}
                                                type="button"
                                                onClick={() => handleSelectOption(optIndex)}
                                                disabled={isCurrentConfirmed}
                                                className={`w-full text-left p-4 rounded-2xl border transition-all flex items-start gap-3 cursor-pointer min-h-[48px] ${containerStyle}`}
                                            >
                                                <span className={`w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5 border ${
                                                    isCurrentConfirmed && isCorrect
                                                        ? 'bg-[#52B788] text-white border-[#52B788]'
                                                    : isCurrentConfirmed && currentAnswerState.chosen === optIndex
                                                        ? 'bg-[#D94E34] text-white border-[#D94E34]'
                                                    : isSelected
                                                        ? 'bg-[#1B4332] text-white border-[#1B4332]'
                                                        : 'bg-white text-stone-600 border-stone-300'
                                                }`}>
                                                    {String.fromCharCode(65 + optIndex)}
                                                </span>

                                                <span className="flex-1 text-xs sm:text-sm leading-relaxed">
                                                    {opt}
                                                </span>

                                                {isCurrentConfirmed && isCorrect && (
                                                    <CheckCircle2 className="w-5 h-5 text-[#2A9D8F] flex-shrink-0" />
                                                )}
                                                {isCurrentConfirmed && currentAnswerState.chosen === optIndex && !isCorrect && (
                                                    <XCircle className="w-5 h-5 text-[#D94E34] flex-shrink-0" />
                                                )}
                                            </button>
                                        );
                                    })}
                                </div>

                                {/* Feedback Benar / Salah & Penjelasan Ilmiah */}
                                <AnimatePresence>
                                    {isCurrentConfirmed && (
                                        <motion.div
                                            initial={{ opacity: 0, y: 10 }}
                                            animate={{ opacity: 1, y: 0 }}
                                            exit={{ opacity: 0, y: -10 }}
                                            className={`p-4 sm:p-5 rounded-2xl border text-xs sm:text-sm leading-relaxed mb-6 ${
                                                currentAnswerState.isCorrect
                                                    ? 'bg-[#52B788]/15 border-[#52B788]/40 text-[#1B4332]'
                                                    : 'bg-[#D94E34]/10 border-[#D94E34]/30 text-[#9B2C1C]'
                                            }`}
                                        >
                                            <div className="flex items-center gap-2 font-bold mb-2 text-sm">
                                                {currentAnswerState.isCorrect ? (
                                                    <>
                                                        <CheckCircle2 className="w-5 h-5 text-[#2A9D8F]" />
                                                        <span>Jawaban Kamu Tepat!</span>
                                                    </>
                                                ) : (
                                                    <>
                                                        <XCircle className="w-5 h-5 text-[#D94E34]" />
                                                        <span>Jawaban Kurang Tepat</span>
                                                    </>
                                                )}
                                            </div>

                                            <p className="mb-3 leading-relaxed text-stone-700">
                                                {currentQ.explanation}
                                            </p>

                                            <div className="pt-2 border-t border-stone-200/60 text-[11px] text-stone-500 font-medium">
                                                Rujukan Sains: {currentQ.reference}
                                            </div>
                                        </motion.div>
                                    )}
                                </AnimatePresence>

                                {/* Tombol Aksi Konfirmasi & Navigasi */}
                                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4 border-t border-stone-100">
                                    <button
                                        type="button"
                                        onClick={handlePrevQuestion}
                                        disabled={currentQuestionIndex === 0}
                                        className={`inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer ${
                                            currentQuestionIndex === 0
                                                ? 'opacity-40 text-stone-400 cursor-not-allowed'
                                                : 'text-stone-700 bg-stone-100 hover:bg-stone-200'
                                        }`}
                                    >
                                        <ArrowLeft className="w-4 h-4" />
                                        <span>Soal Sebelumnya</span>
                                    </button>

                                    {!isCurrentConfirmed ? (
                                        <button
                                            type="button"
                                            onClick={handleConfirmAnswer}
                                            disabled={selectedOption === null}
                                            className={`inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold transition-all cursor-pointer ${
                                                selectedOption === null
                                                    ? 'bg-stone-200 text-stone-400 cursor-not-allowed'
                                                    : 'bg-[#1B4332] hover:bg-[#2D6A4F] text-white shadow-md active:scale-95'
                                            }`}
                                        >
                                            <Check className="w-4 h-4" />
                                            <span>Konfirmasi Jawaban</span>
                                        </button>
                                    ) : (
                                        <button
                                            type="button"
                                            onClick={handleNextQuestion}
                                            className="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold bg-[#E05A2B] hover:bg-[#D94E34] text-white shadow-md active:scale-95 transition-all cursor-pointer"
                                        >
                                            <span>
                                                {currentQuestionIndex < totalQuestions - 1
                                                    ? 'Pertanyaan Berikutnya'
                                                    : 'Lihat Hasil Akhir'}
                                            </span>
                                            <ArrowRight className="w-4 h-4" />
                                        </button>
                                    )}
                                </div>
                            </motion.div>
                        </AnimatePresence>
                    </div>
                ) : (
                    /* D. RESULT SCREEN (Layar Hasil Akhir Menarik & Informatif) */
                    <motion.div
                        initial={{ opacity: 0, scale: 0.95 }}
                        animate={{ opacity: 1, scale: 1 }}
                        transition={{ duration: 0.4 }}
                        className="bg-white rounded-3xl p-6 sm:p-10 border border-stone-200/90 shadow-lg text-center max-w-2xl mx-auto"
                    >
                        <div className="w-20 h-20 rounded-3xl bg-gradient-to-br from-[#1B4332] to-[#52B788] text-white flex items-center justify-center mx-auto mb-5 shadow-xl">
                            <Award className="w-10 h-10" />
                        </div>

                        <span className="text-xs font-bold uppercase tracking-widest text-[#52B788] block mb-1">
                            Hasil Evaluasi Pemahaman
                        </span>

                        <h3 className="font-display font-black text-2xl sm:text-3xl text-[#1E242B] mb-2">
                            {scorePercentage >= 80
                                ? 'Luar Biasa! Pemahaman Sangat Baik'
                                : scorePercentage >= 50
                                ? 'Bagus! Wawasan Cukup Mendalam'
                                : 'Perlu Memperdalam Materi Literasi'}
                        </h3>

                        <p className="text-xs sm:text-sm text-stone-600 max-w-md mx-auto leading-relaxed mb-6">
                            {scorePercentage >= 80
                                ? 'Kamu telah menguasai prinsip ilmiah seputar lahan gambut, ground fire, teknik pencegahan karhutla, dan dasar hukum perlindungan lingkungan dengan sangat baik.'
                                : scorePercentage >= 50
                                ? 'Pemahamanmu mengenai karhutla sudah cukup solid. Silakan tinjau kembali modul edukasi untuk melengkapi poin-poin penting lainnya.'
                                : 'Jangan berkecil hati. Pelajari kembali materi infografis dan artikel edukasi di halaman ini untuk memperkuat pemahaman ekologi.'}
                        </p>

                        {/* Statistik Skor */}
                        <div className="grid grid-cols-3 gap-3 p-4 rounded-2xl bg-stone-50 border border-stone-200/80 mb-6 max-w-md mx-auto">
                            <div>
                                <span className="text-2xl font-black text-[#1E242B] block leading-tight">
                                    {scorePercentage}%
                                </span>
                                <span className="text-[10px] text-stone-500 font-bold uppercase">Nilai Akhir</span>
                            </div>
                            <div className="border-x border-stone-200">
                                <span className="text-2xl font-black text-[#52B788] block leading-tight">
                                    {correctCount}
                                </span>
                                <span className="text-[10px] text-stone-500 font-bold uppercase">Benar</span>
                            </div>
                            <div>
                                <span className="text-2xl font-black text-[#D94E34] block leading-tight">
                                    {wrongCount}
                                </span>
                                <span className="text-[10px] text-stone-500 font-bold uppercase">Salah</span>
                            </div>
                        </div>

                        {/* Tombol Ulangi Kuis */}
                        <div className="flex flex-col sm:flex-row items-center justify-center gap-3">
                            <button
                                type="button"
                                onClick={handleResetQuiz}
                                className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-[#1B4332] text-white text-xs font-bold hover:bg-[#2D6A4F] transition-all shadow-md active:scale-95 cursor-pointer"
                            >
                                <RotateCcw className="w-4 h-4" />
                                <span>Ulangi Kuis Edukasi</span>
                            </button>
                        </div>

                        <p className="text-[11px] text-stone-400 mt-6 italic">
                            *Pemberitahuan: Kuis ini merupakan sarana belajar mandiri masyarakat dan tidak diterbitkan sebagai sertifikat kompetensi resmi.
                        </p>
                    </motion.div>
                )}
            </div>
        </div>
    );
}

export default VisualEducationSection;
