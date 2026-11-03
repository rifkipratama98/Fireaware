<?php

namespace Database\Seeders;

use App\Models\Guide;
use Illuminate\Database\Seeder;

class GuideSeeder extends Seeder
{
    /**
     * Jalankan database seeds untuk Panduan Keselamatan.
     */
    public function run(): void
    {
        $guides = [
            [
                'title' => 'Prosedur Perlindungan Diri Saat Kabut Asap Karhutla Memasuki Pemukiman',
                'slug' => 'prosedur-perlindungan-diri-saat-kabut-asap-karhutla-memasuki-pemukiman',
                'category' => 'menghadapi-asap',
                'summary' => 'Langkah-langkah esensial saat partikel asap pekat mulai menyelimuti tempat tinggal: mulai dari penutupan ventilasi, pemantauan gejala pernapasan akut, hingga penentuan waktu mencari pertolongan medis.',
                'content' => "Kabut asap akibat kebakaran hutan dan lahan (karhutla) mengandung campuran gas berbahaya seperti karbon monoksida (CO), sulfur dioksida (SO2), nitrogen dioksida (NO2), serta partikel mikroskopis PM2.5 yang dapat menembus jauh ke dalam alveolus paru-paru. Saat asap mulai tercium pekat atau tampak kabut kelabu tebal di sekitar rumah Anda, segera ambil langkah mitigasi aktif berikut:

### 1. Amankan Udara di Dalam Rumah
- Segera tutup semua jendela, pintu, dan lubang ventilasi yang terbuka.
- Bila terdapat celah di bawah pintu atau kusen jendela yang longgar, gunakan kain basah, handuk lembap, atau lakban pelindung untuk menutup celah udara luar.
- Matikan pendingin ruangan (AC) yang menyedot udara segar dari luar rumah, atau aktifkan mode sirkulasi dalam (*recirculation mode*).
- Hindari menyapu lantai yang memicu debu berhamburan; gunakan pel basah untuk membersihkan permukaan lantai dan perabotan.

### 2. Batasi Polutan Tambahan di Dalam Rumah
Jangan menambah beban pernapasan keluarga dengan menciptakan polutan baru di dalam hunian:
- Dilarang keras merokok atau menggunakan rokok elektronik di dalam rumah.
- Hindari menyalakan lilin, obat nyamuk bakar, dupa, atau pemanas beraroma minyak.
- Batasi aktivitas memasak yang menghasilkan banyak asap atau menggoreng dengan suhu minyak sangat tinggi tanpa ventilasi berpenyaring.

### 3. Waspadai Gejala Gangguan Kesehatan Akut
Paparan asap karhutla dapat memicu berbagai gejala akut pada tubuh. Kenali tanda-tanda berikut:
- Gejala ringan: Mata merah, berair, tenggorokan kering, bersin-bersin, batuk kering sesekali.
- Gejala sedang hingga berat: Sesak napas berulang, napas berbunyi (mengi), nyeri dada saat menarik napas, rasa linglung, kelelahan ekstrem, atau bibir kebiruan.

### 4. Kapan Harus Segera ke Puskesmas atau Rumah Sakit?
Jika Anda atau anggota keluarga mengalami sesak napas berat yang tidak membaik dalam 30 menit, penderita asma yang tidak tertolong dengan inhaler darurat, atau anak balita yang tampak kesulitan bernapas (tulang rusuk cekung ke dalam saat bernapas), segera bawa ke IGD Puskesmas atau rumah sakit terdekat dengan tetap mengenakan masker perlindungan.",
                'source_name' => 'Kementerian Kesehatan Republik Indonesia (Kemenkes RI)',
                'source_url' => 'https://p2ptm.kemkes.go.id',
                'source_notes' => 'Berdasarkan Pedoman Kesiapsiagaan Dampak Kesehatan Akibat Asap Karhutla (Direktorat P2PTM Kemenkes RI).',
                'reading_time_minutes' => 4,
                'status' => Guide::STATUS_PUBLISHED,
                'published_at' => now()->subDays(4),
            ],
            [
                'title' => 'Standar Penggunaan Masker Partikulat dan Isolasi Ruang Bersih di Rumah',
                'slug' => 'standar-penggunaan-masker-partikulat-dan-isolasi-ruang-bersih-di-rumah',
                'category' => 'mengurangi-paparan-asap',
                'summary' => 'Panduan pemilihan masker yang mampu menyaring partikel berbahaya PM2.5 serta metode praktis menyekat satu ruangan menjadi "Clean Room" sederhana saat indeks kualitas udara berada di kategori berbahaya.',
                'content' => "Menghirup asap karhutla tanpa proteksi dapat menyebabkan akumulasi partikel beracun di pembuluh darah dan saluran pernapasan. Agar paparan polutan berkurang maksimal, diperlukan perlindungan mekanik yang sesuai standar ilmiah dan penciptaan zona bernapas yang aman di lingkungan rumah.

### 1. Memilih Jenis Masker yang Tepat
Tidak semua masker mampu menahan partikel halus karhutla:
- **Masker Kain & Masker Bedah Biasa:** Hanya mampu menahan partikel besar (debu kasar dan droplet air). Masker jenis ini **TIDAK EFEKTIF** menyaring partikel PM2.5 yang berukuran 30 kali lebih kecil dari diameter rambut manusia.
- **Masker Partikulat Berstandar (N95 / KN95 / KF94 / FFP2):** Direkomendasikan oleh otoritas kesehatan karena memiliki serat elektrostatik yang mampu memfilter minimal 94% hingga 95% partikel mikroskopis di udara.

### 2. Cara Mengenakan dan Memeriksa Kerapatan (Fit Check)
Manfaat masker respirator akan hilang jika udara kotor masih bocor melalui tepi masker:
- Pastikan kawat hidung ditekan rapat mengikuti lekuk hidung menggunakan kedua ujung jari.
- Tarik bagian bawah masker hingga menutup sempurna bagian bawah dagu.
- Lakukan *fit check*: Letakkan kedua telapak tangan di atas permukaan masker (jangan menekan terlalu keras), lalu embuskan napas kuat. Bila terasa ada hembusan udara hangat mengenai mata atau pipi samping, sesuaikan kembali posisi tali dan kawat hidung hingga tidak ada kebocoran udara tepi.

### 3. Membuat 'Ruang Bersih' (Clean Air Room) di Rumah
Saat asap di luar rumah bertahan selama beberapa hari, tentukan satu ruangan di dalam rumah untuk dijadikan zona aman:
- Pilih ruangan yang memiliki jendela dan pintu paling sedikit (misalnya kamar tidur tengah).
- Tutup rapat semua celah ventilasi ruangan tersebut dengan kain lembap atau isolasi tape.
- Bila memiliki alat pembersih udara (*air purifier*), gunakan yang dilengkapi filter HEPA asli dan posisikan di tengah ruangan.
- Usahakan ruangan tersebut tidak dimasuki dengan alas kaki luar rumah, dan ganti pakaian setelah bepergian sebelum masuk ke ruang bersih.",
                'source_name' => 'Organisasi Kesehatan Dunia (WHO) & Kemenkes RI',
                'source_url' => 'https://www.who.int/indonesia',
                'source_notes' => 'Diadaptasi dari WHO Guidance on Exposure to Wildfire Smoke dan Standar Pemakaian Respirator Partikulat Kemenkes RI.',
                'reading_time_minutes' => 5,
                'status' => Guide::STATUS_PUBLISHED,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Kesiapsiagaan Logistik dan Perlindungan Kelompok Rentan Menjelang Puncak Kemarau',
                'slug' => 'kesiapsiagaan-logistik-dan-perlindungan-kelompok-rentan-menjelang-puncak-kemarau',
                'category' => 'persiapan-saat-kualitas-udara-memburuk',
                'summary' => 'Daftar perlengkapan siaga udara buruk, manajemen obat-obatan esensial bagi penderita asma/alergi, serta mitigasi aktivitas harian di lingkungan keluarga dan sekolah.',
                'content' => "Musim kemarau di Pulau Kalimantan kerap diiringi penurunan drastis kualitas udara. Menyiapkan kebutuhan darurat sebelum kabut asap tebal datang adalah kunci menjaga ketahanan fisik keluarga dan mencegah lonjakan kasus Infeksi Saluran Pernapasan Akut (ISPA).

### 1. Kotak Siaga Bencana Asap Keluarga
Siapkan satu kotak penyimpanan khusus yang mudah dijangkau berisi:
- Persediaan masker berstandar N95/KN95/KF94 minimal untuk kebutuhan 7-14 hari seluruh anggota keluarga.
- Obat-obatan rutin bagi penderita penyakit kronis, terutama inhaler bronkodilator untuk penderita asma atau PPOK.
- Cairan tetes mata steril (*artificial tears*) untuk meredakan iritasi konjungtiva akibat gas asap.
- Cairan pembersih hidung (*saline nasal spray*) untuk membersihkan jelaga dari rongga hidung setelah bepergian.
- Termometer dan obat pereda demam/nyeri dasar.

### 2. Perlindungan Khusus Bagi Kelompok Rentan
Beberapa kelompok masyarakat berisiko mengalami komplikasi fatal akibat asap karhutla:
- **Bayi dan Balita:** Saluran napas anak-anak masih sempit dan frekuensi napas mereka lebih cepat dibanding orang dewasa. Jauhkan anak-anak dari luar rumah sama sekali saat indeks udara kategori 'Tidak Sehat'.
- **Ibu Hamil:** Paparan gas karbon monoksida dan partikel berbahaya dapat mempengaruhi pasokan oksigen ke janin. Hindari bepergian keluar rumah kecuali untuk pemeriksaan kehamilan mendesak.
- **Lansia dan Penderita Kardiovaskular:** Partikel PM2.5 dapat memicu peradangan vaskular dan meningkatkan risiko serangan jantung serta stroke. Pantau tekanan darah secara teratur.

### 3. Kebiasaan Hidup Sehat Pendukung
- Tingkatkan konsumsi air putih bersih minimal 2 hingga 2,5 liter per hari untuk menjaga kelembapan lendir saluran pernapasan sehingga kotoran asap lebih mudah disaring secara alami oleh tubuh.
- Konsumsi sayur-mayur dan buah-buahan yang kaya antioksidan (vitamin C dan vitamin E) guna membantu menangkal stres oksidatif pada sel-sel paru-paru.",
                'source_name' => 'Badan Nasional Penanggulangan Bencana (BNPB)',
                'source_url' => 'https://bnpb.go.id',
                'source_notes' => 'Merujuk Panduan Kesiapsiagaan Menghadapi Bencana Asap Karhutla - Klaster Kesehatan & Logistik BNPB.',
                'reading_time_minutes' => 4,
                'status' => Guide::STATUS_PUBLISHED,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Protokol Pelaporan Titik Api dan Keselamatan Warga: Jangan Mendekati Lokasi Kebakaran',
                'slug' => 'protokol-pelaporan-titik-api-dan-keselamatan-warga-jangan-mendekati-lokasi-kebakaran',
                'category' => 'tindakan-ketika-melihat-indikasi-kebakaran',
                'summary' => 'Langkah aman saat mencurigai kepulan asap atau bara api karhutla: cara mencatat patokan lokasi, evakuasi menjauhi arah angin, dan kanal resmi pelaporan tanpa melakukan pemadaman mandiri di area berbahaya.',
                'content' => "Kebakaran hutan dan lahan di Kalimantan, terutama pada lahan gambut, memiliki karakteristik yang sangat berbeda dari kebakaran biasa. Api di lahan gambut membakar material organik kering di bawah permukaan tanah (*ground fire*), menciptakan rongga bawah tanah membara yang rapuh dan dapat runtuh seketika ketika diinjak.

### PERINGATAN KESELAMATAN UTAMA:
> **JANGAN PERNAH MENDEKATI TITIK API ATAU MASUK KE AREA LAHAN TERBAKAR.**
> Masyarakat dilarang mencoba memadamkan kebakaran lahan besar secara mandiri. Lahan gambut dapat menjebak kaki ke dalam bara api bawah tanah bersuhu ratusan derajat Celsius, pohon dapat tumbang tiba-tiba akibat akarnya habis terbakar, dan kepungan asap tebal dapat menghilangkan orientasi arah dalam hitungan detik.

### 1. Langkah Pertama: Amankan Diri Anda dan Warga Sekitar
- Jika Anda melihat kobaran api atau asap tebal muncul di lahan sekitar Anda, segera bergerak menjauh.
- Perhatikan arah angin: berjalanlah tegak lurus atau berlawanan arah dengan rambatan asap. Jangan pernah berlari searah tiupan asap.
- Ingatkan tetangga dan warga terdekat untuk segera meninggalkan perimeter bahaya, terutama anak-anak dan lansia.

### 2. Informasi yang Perlu Dicatat untuk Laporan Petugas
Tanpa perlu mendekati api, amati dari jarak aman (minimal beberapa ratus meter) hal-hal berikut:
- **Lokasi atau Patokan Jelas:** Nama desa/kelurahan, kilometer jalan, nama sungai terdekat, atau koordinat GPS jika Anda mengetahui posisi aman Anda berdiri.
- **Perkiraan Luas dan Arah Pergerakan:** Apakah api mendekati pemukiman warga, perkebunan, atau fasilitas umum seperti sekolah/jembatan.
- **Warna dan Ketebalan Asap:** Asap putih keabu-abuan umumnya mengindikasikan gambut basah/baru menyala, sedangkan asap hitam pekat menandakan vegetasi kering atau karet/sawit yang terbakar kuat.

### 3. Nomor Darurat Resmi yang Harus Dihubungi
Segera hubungi kanal darurat terdekat:
- **Layanan Panggilan Darurat Nasional:** Telepon **112** (bebas pulsa, dapat dihubungi meski tanpa pulsa).
- **Badan Penanggulangan Bencana Daerah (BPBD):** Posko Siaga BPBD Kabupaten/Kota setempat.
- **Manggala Agni (KLHK):** Balai Pengendalian Perubahan Iklim dan Kebakaran Hutan dan Lahan (BPPIKHL) wilayah Kalimantan.
- **Dinas Pemadam Kebakaran dan Penyelamatan (Damkar)** di wilayah Anda.",
                'source_name' => 'Kementerian Lingkungan Hidup dan Kehutanan (KLHK / SIPONGI) & BPBD',
                'source_url' => 'https://sipongi.menlhk.go.id',
                'source_notes' => 'Berdasarkan Prosedur Operasional Standar Mitigasi Bahaya Karhutla KLHK dan Panduan Kesiapsiagaan Tanggap Bencana BPBD.',
                'reading_time_minutes' => 4,
                'status' => Guide::STATUS_PUBLISHED,
                'published_at' => now()->subDay(),
            ],
            [
                'title' => 'Kanal Pantauan Resmi Karhutla dan Cara Membaca Data Kualitas Udara yang Valid',
                'slug' => 'kanal-pantauan-resmi-karhutla-dan-cara-membaca-data-kualitas-udara-yang-valid',
                'category' => 'cara-memperoleh-informasi-resmi',
                'summary' => 'Panduan mengakses portal resmi pantauan titik panas (SIPONGI KLHK), sistem peringatan dini BMKG, pembacaan indeks ISPU/PM2.5, serta cara menangkal kabar bohong terkait karhutla.',
                'content' => "Di tengah situasi kabut asap, sering kali beredar foto lama, video dramatis dari tahun-tahun sebelumnya, atau informasi keliru mengenai status siaga di media sosial. Mengakses informasi dari instansi berwenang sangat penting agar keputusan keselamatan yang Anda ambil bersama keluarga benar-benar akurat.

### 1. Sistem Informasi Titik Panas Resmi: SIPONGI KLHK
Kementerian Lingkungan Hidup dan Kehutanan mengelola sistem SiPongi (sipongi.menlhk.go.id) yang mengintegrasikan citra satelit NASA (MODIS dan VIIRS):
- **Memahami 'Hotspot' (Titik Panas):** Titik panas adalah anomali suhu permukaan bumi yang terdeteksi oleh sensor inframerah satelit saat melintas. Hotspot adalah indikasi awal potensi kebakaran, bukan kepastian kebakaran 100%.
- **Tingkat Kepercayaan (Confidence Level):** Perhatikan persentase kepercayaan:
  - Rendah (0-29%): Kemungkinan pantulan panas dari seng bangunan atau permukaan air dangkal.
  - Sedang (30-79%): Potensi indikasi api sedang yang membutuhkan pengecekan lapangan (*groundcheck*).
  - Tinggi (80-100%): Sangat besar kemungkinan kebakaran aktif di permukaan.

### 2. Memantau Cuaca dan Sebaran Asap: BMKG
Badan Meteorologi, Klimatologi, dan Geofisika menyediakan pemantauan cuaca dan atmosfer harian:
- Peta sebaran asap (*smoke haze dispersion*) berbasis satelit Himawari.
- Informasi Hari Tanpa Hujan (HTH) dan potensi kekeringan meteorologis.
- Peringatan dini potensi kemudahan terjadinya kebakaran hutan dan lahan (Indeks FWI - Fire Weather Index).

### 3. Membaca Indeks Kualitas Udara (ISPU & PM2.5)
Indeks Standar Pencemar Udara (ISPU) diukur melalui stasiun pemantau resmi:
- **0 - 50 (Baik / Hijau):** Udara sehat, aman untuk semua aktivitas luar ruang.
- **51 - 100 (Sedang / Biru):** Masih dapat diterima, beberapa individu yang sangat sensitif disarankan membatasi paparan berlebihan.
- **101 - 200 (Tidak Sehat / Kuning):** Mulai merugikan kesehatan manusia dan hewan sensitif. Kenakan masker pelindung.
- **201 - 300 (Sangat Tidak Sehat / Merah):** Tingkat bahaya meningkat. Kurangi aktivitas luar ruangan secara signifikan.
- **> 300 (Berbahaya / Hitam):** Udara sangat berbahaya. Seluruh warga harus berada di dalam ruangan tertutup.

### 4. Sikap Cerdas Mengatasi Hoaks
- Sebelum menyebarkan informasi darurat atau peringatan evakuasi, pastikan sumber berita mencantumkan rilis resmi BPBD, BMKG, atau Dinas Kesehatan setempat.
- Periksa tanggal publikasi berita untuk memastikan bukan peristiwa musim asap tahun sebelumnya.",
                'source_name' => 'Badan Meteorologi, Klimatologi, dan Geofisika (BMKG) & KLHK',
                'source_url' => 'https://www.bmkg.go.id',
                'source_notes' => 'Dirangkum dari Panduan Layanan Informasi Meteorologi Publik BMKG dan Dokumentasi Teknis Sistem SIPONGI KLHK.',
                'reading_time_minutes' => 4,
                'status' => Guide::STATUS_PUBLISHED,
                'published_at' => now()->subHours(12),
            ],
            [
                'title' => 'Panduan Perawatan Filter Udara Mandiri dan Penyaringan Partikel Ruangan',
                'slug' => 'panduan-perawatan-filter-udara-mandiri-dan-penyaringan-partikel-ruangan',
                'category' => 'mengurangi-paparan-asap',
                'summary' => 'Teknik pembersihan awal filter pendingin ruangan dan pemanfaatan media filter darurat untuk menjaga sirkulasi udara bersih di dalam fasilitas umum skala kecil.',
                'content' => "Draf panduan teknis mengenai pemeliharaan rutin filter pendingin ruangan (AC) dan sirkulator udara berfilter kasa rapat saat intensitas kabut asap mencapai puncaknya di kawasan Kalimantan. Artikel ini masih dalam tinjauan teknis keselamatan sebelum dipublikasikan untuk publik.",
                'source_name' => 'Balai Standardisasi Lingkungan & Kemenkes RI',
                'source_url' => 'https://kemkes.go.id',
                'source_notes' => 'Draf dokumen teknis rekomendasi sanitasi udara darurat.',
                'reading_time_minutes' => 3,
                'status' => Guide::STATUS_DRAFT,
                'published_at' => null,
            ],
        ];

        foreach ($guides as $row) {
            Guide::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
}
