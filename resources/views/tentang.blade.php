@extends('layouts.app')

@section('title', 'Tentang FireAware Kalimantan — Media Pemantauan & Informasi Lingkungan')

@section('content')
    {{-- 1. Hero Section dengan Foto Lanskap Hutan Kalimantan --}}
    <header class="about-hero" style="background-image: linear-gradient(135deg, rgba(23, 57, 45, 0.9), rgba(33, 77, 58, 0.94)), url('{{ asset('images/hero-forest.jpg') }}');">
        <div class="container">
            <span class="aq-badge-header mb-2 text-uppercase">
                <i class="bi bi-info-circle me-1" aria-hidden="true"></i> Mengenal Inisiatif Lingkungan
            </span>
            <h1 class="about-hero-title">Mengenal FireAware Kalimantan</h1>
            <p class="about-hero-lead">
                Inisiatif pemantauan mandiri dan keterbukaan informasi lingkungan untuk mendukung kesiapsiagaan
                masyarakat dalam menghadapi ancaman kebakaran hutan dan lahan gambut di seluruh penjuru Pulau Kalimantan.
            </p>

            <div class="about-pillar-row">
                <span class="about-pillar-pill">
                    <i class="bi bi-geo-alt-fill" aria-hidden="true"></i> 5 Provinsi Cakupan Penuh
                </span>
                <span class="about-pillar-pill">
                    <i class="bi bi-hdd-network-fill" aria-hidden="true"></i> 3 Jaringan Telemetri Resmi
                </span>
                <span class="about-pillar-pill">
                    <i class="bi bi-shield-check" aria-hidden="true"></i> 100% Data Faktual Sensor
    {{-- 2. Penjelasan Singkat Mengenai Tujuan & Misi FireAware --}}
    <section class="py-5 bg-white border-bottom border-soft">
        <div class="container py-lg-3">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge bg-light text-primary border border-soft px-3 py-1.5 small fw-bold text-uppercase mb-2">
                        Tujuan &amp; Visi Platform
                    </span>
                    <h2 class="h3 fw-bold text-dark mb-3" style="letter-spacing: -.02em; line-height: 1.3;">
                        Menjembatani Sains Satelit dengan Kesiapsiagaan Komunitas
                    </h2>
                    <p class="text-secondary mb-3" style="line-height: 1.7;">
                        FireAware Kalimantan dikembangkan sebagai platform informasi publik yang menyajikan pantauan titik panas (hotspot), kualitas udara partikulat (PM2.5), dan warta kewaspadaan secara transparan, mudah dipahami, dan dapat diakses bebas oleh seluruh masyarakat.
                    </p>
                    <p class="text-secondary mb-4" style="line-height: 1.7;">
                        Kami meyakini bahwa data sains lingkungan akan optimal ketika diterjemahkan ke dalam bahasa yang sederhana dan relevan bagi kehidupan sehari-hari, sehingga masyarakat memiliki daya mitigasi yang lebih tangguh untuk melindungi keluarga dan lingkungan mereka.
                    </p>

                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('hotspot.map') }}" class="btn btn-primary btn-sm px-3.5 py-2">
                            <i class="bi bi-map me-1" aria-hidden="true"></i>Buka Peta Hotspot
                        </a>
                        <a href="{{ route('air.quality') }}" class="btn btn-outline-secondary btn-sm px-3.5 py-2">
                            <i class="bi bi-clouds me-1" aria-hidden="true"></i>Pantau Kualitas Udara
                        </a>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="p-4 rounded-4 bg-soft border border-soft shadow-sm">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-primary text-white p-3 rounded-3 fs-4 d-grid place-items-center">
                                <i class="bi bi-compass" aria-hidden="true"></i>
                            </div>
                            <div>
                                <h3 class="h5 fw-bold mb-0 text-dark">Prinsip Pokok FireAware</h3>
                                <small class="text-secondary">Pilar integritas penyajian data publik</small>
                            </div>
                        </div>

                        <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                            <li class="d-flex align-items-start gap-2.5">
                                <i class="bi bi-check-circle-fill text-success fs-5 mt-n0.5" aria-hidden="true"></i>
                                <div>
                                    <strong class="text-dark d-block small">Hanya Menyajikan Data Faktual</strong>
                                    <span class="text-secondary small">Tidak ada angka prediksi buatan atau estimasi sepihak.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2.5">
                                <i class="bi bi-check-circle-fill text-success fs-5 mt-n0.5" aria-hidden="true"></i>
                                <div>
                                    <strong class="text-dark d-block small">Bahasa Sederhana &amp; Aksi Nyata</strong>
                                    <span class="text-secondary small">Setiap indikator dilengkapi rekomendasi keselamatan praktis.</span>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-2.5">
                                <i class="bi bi-check-circle-fill text-success fs-5 mt-n0.5" aria-hidden="true"></i>
                                <div>
                                    <strong class="text-dark d-block small">Mendukung Otoritas, Bukan Menggantikan</strong>
                                    <span class="text-secondary small">Memperkuat kanal resmi BPBD, BMKG, dan KLHK di tingkat masyarakat.</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

                </span>
            </div>
        </div>
    </header>
    {{-- 3. Permasalahan Karhutla & Alasan Platform Dikembangkan --}}
    <section class="py-5 bg-soft border-bottom border-soft">
        <div class="container py-lg-3">
            <div class="about-section-header">
                <span class="about-section-eyebrow">Tantangan Ekologis &amp; Latar Belakang</span>
                <h2 class="about-section-title">Mengapa Kalimantan Memerlukan FireAware?</h2>
                <p class="about-section-desc">
                    Kebakaran hutan dan lahan di Kalimantan memiliki karakteristik ekologis yang unik, membutuhkan kewaspadaan berbasis sains yang terhubung langsung ke masyarakat.
                </p>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="about-problem-card">
                        <div class="problem-icon-wrap icon-gambut">
                            <i class="bi bi-moisture" aria-hidden="true"></i>
                        </div>
                        <h3>Dilema Api Bawah Tanah Gambut</h3>
                        <p>
                            Lahan gambut kering membakar bahan organik bermeter-meter di bawah tanah (*ground fire*). Api ini tidak tampak di permukaan namun memancarkan panas ekstrem dan sulit dipadamkan tanpa pembasahan mendalam.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="about-problem-card">
                        <div class="problem-icon-wrap icon-asap">
                            <i class="bi bi-cloud-fog2" aria-hidden="true"></i>
                        </div>
                        <h3>Ancaman Polutan Mikro PM2.5</h3>
                        <p>
                            Pembakaran gambut melepaskan asap pekat kaya partikel debu halus PM2.5. Partikel berukuran 2,5 mikron ini menembus jauh ke organ pernapasan dan menyebar melintasi batas kabupaten bahkan negara.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="about-problem-card">
                        <div class="problem-icon-wrap icon-info">
                            <i class="bi bi-broadcast" aria-hidden="true"></i>
                        </div>
                        <h3>Kesenjangan Informasi Publik</h3>
                        <p>
                            Data citra satelit dan indeks sensor sering kali tersebar di berbagai portal dengan terminologi teknis. Warga membutuhkan kanal satu pintu yang menerjemahkan data menjadi instruksi keselamatan nyata.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Dokumentasi Lingkungan Gambut & Kabut Asap --}}
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="rounded-4 overflow-hidden border border-soft shadow-sm bg-white">
                        <img src="{{ asset('images/peatland-gambut.jpg') }}"
                             alt="Bentang alam lahan gambut tropis Kalimantan"
                             class="w-100"
                             style="height: 260px; object-fit: cover;"
                             loading="lazy"
                             width="640"
                             height="260">
                        <div class="p-3 border-top border-soft small text-secondary">
                            <i class="bi bi-camera me-1" aria-hidden="true"></i>
                            <strong>Ekosistem Kubah Gambut:</strong> Menyimpan cadangan air raksasa dan karbon vital bumi bila kelembapannya tetap terjaga.
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="rounded-4 overflow-hidden border border-soft shadow-sm bg-white">
                        <img src="{{ asset('images/smoke-haze.jpg') }}"
                             alt="Kondisi kabut asap karhutla pekat di Kalimantan"
                             class="w-100"
                             style="height: 260px; object-fit: cover;"
                             loading="lazy"
                             width="640"
                             height="260">
                        <div class="p-3 border-top border-soft small text-secondary">
                            <i class="bi bi-camera me-1" aria-hidden="true"></i>
                            <strong>Dampak Kabut Asap:</strong> Partikel debu pekat menuntut respons cepat masyarakat untuk mengenakan masker respirator standar N95.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. Lima Provinsi Cakupan Sistem dalam Layout Visual Menarik --}}
    <section class="py-5 bg-white border-bottom border-soft">
        <div class="container py-lg-3">
            <div class="about-section-header">
                <span class="about-section-eyebrow">Cakupan Wilayah Kalimantan</span>
                <h2 class="about-section-title">Pemantauan Terpadu 5 Provinsi</h2>
                <p class="about-section-desc">
                    FireAware menyatukan telemetri satelit dan stasiun kualitas udara di seluruh penjuru Pulau Borneo dalam satu peta interaktif berstandar nasional.
                </p>
            </div>

            <div class="row g-4">
                @php
                    $provinceDetails = [
                        'kalimantan-barat' => [
                            'code' => 'KALBAR',
                            'highlight' => 'Kawasan gambut pesisir luas dan koridor perbatasan darat.',
                            'icon' => 'bi-water',
                        ],
                        'kalimantan-tengah' => [
                            'code' => 'KALTENG',
                            'highlight' => 'Kubah gambut terdalam Indonesia dan zona prioritas pembasahan kanal.',
                            'icon' => 'bi-moisture',
                        ],
                        'kalimantan-selatan' => [
                            'code' => 'KALSEL',
                            'highlight' => 'Zona padat pemukiman rawan kabut asap bandara dan lahan rawa.',
                            'icon' => 'bi-building',
                        ],
                        'kalimantan-timur' => [
                            'code' => 'KALTIM',
                            'highlight' => 'Kawasan hutan hujan tropis perbukitan dan zona penyangga IKN.',
                            'icon' => 'bi-tree',
                        ],
                        'kalimantan-utara' => [
                            'code' => 'KALTARA',
                            'highlight' => 'Bentang kanopi hutan primer dan perbatasan dataran tinggi utara.',
                            'icon' => 'bi-compass',
                        ],
                    ];
                @endphp

                @foreach ($provinces as $prov)
                    @php
                        $detail = $provinceDetails[$prov->slug] ?? [
                            'code' => 'BORNEO',
                            'highlight' => 'Wilayah pemantauan satelit dan sensor udara terpadu.',
                            'icon' => 'bi-geo-alt',
                        ];
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="about-province-card">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="province-code-badge">{{ $detail['code'] }}</span>
                                    <i class="bi {{ $detail['icon'] }} text-primary fs-5" aria-hidden="true"></i>
                                </div>
                                <h3>{{ $prov->name }}</h3>
                                <p>{{ $detail['highlight'] }}</p>
                            </div>

                            <div class="province-footer d-flex align-items-center justify-content-between">
                                <span class="font-monospace small">
                                    {{ number_format((float) $prov->center_lat, 2) }}, {{ number_format((float) $prov->center_lng, 2) }}
                                </span>
                                <a href="{{ route('hotspot.map', ['provinsi' => $prov->slug]) }}" class="fw-semibold text-primary text-decoration-none small">
                                    Buka Peta <i class="bi bi-arrow-right ms-0.5" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Kartu Ringkasan Pulau Kalimantan --}}
                <div class="col-md-6 col-lg-4">
                    <div class="about-province-card bg-primary text-white border-0 shadow">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge bg-white text-primary fw-bold">TOTAL WILAYAH</span>
                                <i class="bi bi-globe-asia-australia text-white-50 fs-4" aria-hidden="true"></i>
                            </div>
                            <h3 class="text-white">Seluruh Pulau Kalimantan</h3>
                            <p class="text-white-50">
                                Meliputi area daratan seluas lebih dari 544.000 km² dengan ribuan kanal buatan dan tutupan vegetasi hutan tropis terbesar ketiga di dunia.
                            </p>
                        </div>
                        <div class="province-footer border-white border-opacity-25 d-flex align-items-center justify-content-between">
                            <span class="text-white-50 small">Koordinat Sentral Borneo</span>
                            <a href="{{ route('hotspot.map') }}" class="fw-bold text-white text-decoration-none small">
                                Pantau Semua <i class="bi bi-arrow-right ms-0.5" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. Sumber Data Resmi yang Digunakan --}}
    <section class="py-5 bg-soft border-bottom border-soft">
        <div class="container py-lg-3">
            <div class="about-section-header">
                <span class="about-section-eyebrow">Transparansi &amp; Integritas Data</span>
                <h2 class="about-section-title">Sumber Data Resmi Terverifikasi</h2>
                <p class="about-section-desc">
                    FireAware Kalimantan berkomitmen tidak merekayasa atau mengarang nilai titik panas maupun partikel udara. Seluruh informasi ditarik langsung dari jaringan telemetri terpercaya.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="about-source-card">
                        <div>
                            <div class="source-logo-wrap">
                                <div class="source-icon">
                                    <i class="bi bi-satellite" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h3>NASA FIRMS</h3>
                                    <span class="source-type">Deteksi Termal Satelit</span>
                                </div>
                            </div>
                            <p>
                                Sistem informasi kebakaran global milik NASA yang memproses citra satelit polar orbit untuk memetakan anomali suhu permukaan bumi.
                            </p>
                            <ul class="source-detail-list">
                                <li><i class="bi bi-check2-circle" aria-hidden="true"></i> Sensor VIIRS (S-NPP &amp; NOAA-20) resolusi 375m</li>
                                <li><i class="bi bi-check2-circle" aria-hidden="true"></i> Sensor MODIS (Terra &amp; Aqua) resolusi 1km</li>
                                <li><i class="bi bi-check2-circle" aria-hidden="true"></i> Nilai Fire Radiative Power (FRP) dalam Megawatt</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="about-source-card">
                        <div>
                            <div class="source-logo-wrap">
                                <div class="source-icon">
                                    <i class="bi bi-patch-check" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h3>KLHK RI</h3>
                                    <span class="source-type">Baku Mutu ISPU &amp; SIPONGI</span>
                                </div>
                            </div>
                            <p>
                                Kementerian Lingkungan Hidup dan Kehutanan yang menetapkan standar baku mutu pencemaran udara nasional dan koordinasi posko karhutla.
                            </p>
                            <ul class="source-detail-list">
                                <li><i class="bi bi-check2-circle" aria-hidden="true"></i> Baku mutu ISPU Kepmen LHK No. 14 Tahun 2020</li>
                                <li><i class="bi bi-check2-circle" aria-hidden="true"></i> Klasifikasi 5 tingkat kelayakan udara untuk PM2.5</li>
                                <li><i class="bi bi-check2-circle" aria-hidden="true"></i> Koordinasi regu pemadaman darat Manggala Agni</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="about-source-card">
                        <div>
                            <div class="source-logo-wrap">
                                <div class="source-icon">
                                    <i class="bi bi-clouds" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <h3>OpenAQ &amp; BMKG</h3>
                                    <span class="source-type">Sensor Udara &amp; Cuaca</span>
                                </div>
                            </div>
                            <p>
                                Jaringan data kualitas udara terbuka global dan BMKG untuk pantauan cuaca ekstrem dan kelembapan udara wilayah Kalimantan.
                            </p>
                            <ul class="source-detail-list">
                                <li><i class="bi bi-check2-circle" aria-hidden="true"></i> Sensor telemetri udara stasiun pemantau tetap</li>
                                <li><i class="bi bi-check2-circle" aria-hidden="true"></i> Konsentrasi partikulat mikro PM2.5 aktual (µg/m³)</li>
                                <li><i class="bi bi-check2-circle" aria-hidden="true"></i> Peringatan hari tanpa hujan dan anomali El Niño</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. Penjelasan Mengenai Cara Kerja Sistem --}}
    <section class="py-5 bg-white border-bottom border-soft">
        <div class="container py-lg-3">
            <div class="about-section-header">
                <span class="about-section-eyebrow">Alur Kerja Teknologi</span>
                <h2 class="about-section-title">Bagaimana FireAware Memproses Data?</h2>
                <p class="about-section-desc">
                    Arsitektur pemrosesan data otomatis empat tahap yang menjamin data tetap akurat, bersih, dan langsung siap dibaca masyarakat.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="about-pipeline-step">
                        <div class="step-number">01</div>
                        <h3>Sinkronisasi Telemetri</h3>
                        <p>Job berkala server ditarik otomatis dari endpoint satelit NASA FIRMS dan sensor OpenAQ setiap siklus pembaruan tersedia.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="about-pipeline-step">
                        <div class="step-number">02</div>
                        <h3>Validasi &amp; Integritas</h3>
                        <p>Sistem memfilter duplikasi koordinat, memvalidasi tingkat keyakinan (confidence), dan menolak nilai anomali buatan.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="about-pipeline-step">
                        <div class="step-number">03</div>
                        <h3>Visualisasi Ramah Warga</h3>
                        <p>Data spasial dipetakan lewat Leaflet marker cluster responsif dan bar spektrum warna ISPU yang langsung dipahami awam.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="about-pipeline-step">
                        <div class="step-number">04</div>
                        <h3>Peringatan &amp; Aksi Nyata</h3>
                        <p>Rilis resmi dipublikasikan bersama notifikasi peramban opt-in serta panduan praktis perlindungan diri keluarga.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 7. Batasan Sistem & Catatan Penyangkalan (Disclaimer Resmi) --}}
    <section class="py-5 bg-soft border-bottom border-soft">
        <div class="container py-lg-3">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <div class="about-disclaimer-box">
                        <span class="disclaimer-badge">
                            <i class="bi bi-shield-exclamation me-1" aria-hidden="true"></i> Batasan Sistem &amp; Disclaimer
                        </span>

                        <h2>Media Pemantauan Publik, Bukan Sistem Prediksi Kebakaran</h2>

                        <p>
                            FireAware Kalimantan beroperasi murni sebagai <strong>media informasi dan pemantauan berbasis data sensor faktual</strong> untuk meningkatkan kesadaran serta kesiapsiagaan masyarakat. Kami menegaskan batasan operasional berikut:
                        </p>

                        <ul>
                            <li>
                                <strong>Bukan Sistem Prediksi Kebakaran:</strong> Platform ini tidak memprediksi atau meramalkan kejadian kebakaran yang belum terjadi di masa depan. Titik panas (hotspot) yang ditampilkan merupakan anomali suhu yang telah terdeteksi oleh sensor satelit saat melintas.
                            </li>
                            <li>
                                <strong>Bukan Pengganti Otoritas Darurat:</strong> FireAware tidak menggantikan komando posko darurat BPBD, satgas Manggala Agni KLHK, TNI/Polri, pemadam kebakaran, maupun instansi kedaruratan resmi pemerintah.
                            </li>
                            <li>
                                <strong>Jeda Lintas Orbit Satelit (Latency):</strong> Satelit orbit kutub melintasi wilayah Kalimantan beberapa kali dalam sehari. Titik panas merefleksikan kondisi saat satelit merekam, bukan siaran video langsung (*real-time video*).
                            </li>
                            <li>
                                <strong>Tindakan Nyata di Lapangan:</strong> Bila Anda melihat kobaran api aktif, asap pekat yang mengancam pemukiman, atau bahaya langsung, jangan mengandalkan aplikasi ponsel. Segera evakuasi keluarga Anda ke tempat aman dan hubungi nomor darurat <strong>112</strong> atau posko BPBD setempat.
                            </li>
                        </ul>

                        <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top border-warning border-opacity-25 small text-secondary">
                            <i class="bi bi-info-circle-fill text-warning fs-5" aria-hidden="true"></i>
                            <span>Seluruh konten dan rilis bersumber dari keterbukaan data publik berlisensi resmi.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 8. Navigasi Aksi Cepat / Portal Nav --}}
    <section class="py-5 bg-white">
        <div class="container py-lg-2">
            <div class="about-section-header mb-4">
                <span class="about-section-eyebrow">Akses Langsung Fitur Publik</span>
                <h2 class="about-section-title">Mulai Jelajahi Data Lingkungan</h2>
            </div>

            <div class="row g-3 justify-content-center">
                <div class="col-md-4">
                    <div class="p-4 rounded-3 border border-soft bg-soft text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <i class="bi bi-map text-primary fs-2 d-block mb-2" aria-hidden="true"></i>
                            <h3 class="h6 fw-bold text-dark">Peta Hotspot Satelit</h3>
                            <p class="small text-secondary mb-3">Lihat sebaran titik panas terkini dari satelit NASA VIIRS &amp; MODIS.</p>
                        </div>
                        <a href="{{ route('hotspot.map') }}" class="btn btn-primary btn-sm">Buka Peta Hotspot</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-4 rounded-3 border border-soft bg-soft text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <i class="bi bi-clouds text-primary fs-2 d-block mb-2" aria-hidden="true"></i>
                            <h3 class="h6 fw-bold text-dark">Indeks Kualitas Udara</h3>
                            <p class="small text-secondary mb-3">Pantau partikulat PM2.5 aktual berdasarkan baku mutu ISPU KLHK.</p>
                        </div>
                        <a href="{{ route('air.quality') }}" class="btn btn-outline-primary btn-sm">Cek Kualitas Udara</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-4 rounded-3 border border-soft bg-soft text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <i class="bi bi-shield-check text-primary fs-2 d-block mb-2" aria-hidden="true"></i>
                            <h3 class="h6 fw-bold text-dark">Panduan Keselamatan</h3>
                            <p class="small text-secondary mb-3">Pelajari langkah mitigasi kabut asap dan perlindungan diri keluarga.</p>
                        </div>
                        <a href="{{ route('guides.index') }}" class="btn btn-outline-secondary btn-sm">Baca Panduan</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection