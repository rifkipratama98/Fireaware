<?php

namespace Database\Seeders;

use App\Models\EducationArticle;
use Illuminate\Database\Seeder;

class EducationArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Mengapa Lahan Gambut Tropis Kalimantan Begitu Rentan Mengalami Kebakaran Bawah Tanah?',
                'slug' => 'mengapa-lahan-gambut-tropis-kalimantan-rentan-kebakaran-bawah-tanah',
                'category' => 'ekosistem-gambut',
                'summary' => 'Memahami anatomi lahan gambut yang tersusun dari timbunan bahan organik ribuan tahun, dan mengapa kebakaran gambut sulit dipadamkan begitu bara merayap ke bawah permukaan.',
                'content' => "Lahan gambut tropis di Pulau Kalimantan merupakan salah satu penyimpan karbon terestrial terbesar di dunia. Berbeda dengan tanah mineral biasa, tanah gambut tersusun hampir 100% dari sisa-sisa vegetasi hutan rawa purba yang terdekomposisi sebagian di bawah genangan air asam selama ribuan tahun.

### 1. Sifat Unik Lahan Gambut
Dalam kondisi alami yang utuh (*pristine*), lahan gambut bertindak layaknya spons raksasa (*peat dome*). Struktur gambut mampu mengikat air tawar hingga belasan kali lipat dari berat keringnya. Selama muka air tanah tetap tinggi, gambut mustahil terbakar karena kelembapannya yang sangat tinggi.

### 2. Bahaya Pengeringan Melalui Kanal (Drainase)
Bencana kebakaran gambut hampir selalu bermula ketika kanal-kanal drainase dibangun untuk memotong kubah gambut demi pembukaan perkebunan atau perladangan. Ketika air gambut terkuras habis melalui kanal buatan, bahan organik gambut berubah menjadi bahan bakar padat berpori yang sangat mudah tersulut bahkan oleh puntung rokok sekalipun.

### 3. Fenomena Ground Fire (Kebakaran Bawah Tanah)
Kebakaran pada lahan gambut terbagi menjadi dua fase:
- **Surface Fire:** Api membakar rumput, ranting, dan tajuk vegetasi di permukaan tanah.
- **Ground Fire:** Bara api merayap lambat di kedalaman 50 sentimeter hingga beberapa meter di bawah permukaan tanah tanpa nyala api yang terlihat (smoldering combustion).

Kebakaran bawah tanah inilah yang menghasilkan volume asap pekat luar biasa besar karena pembakarannya tidak sempurna (*incomplete combustion*), menghasilkan gas beracun CO, metana, dan partikel PM2.5 dalam konsentrasi berbahaya. Pemadaman dari udara dengan water bombing sering kali tidak mempan karena air hanya membasahi permukaan atas, sementara bara api di kedalaman tanah tetap menyala.",
                'image_path' => null,
                'image_caption' => 'Ilustrasi struktur kedalaman lahan gambut dan kanal pengeringan.',
                'author' => 'Tim Riset Ekologi FireAware',
                'reading_time_minutes' => 4,
                'status' => EducationArticle::STATUS_PUBLISHED,
                'is_demo' => false,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Anomali Iklim El Niño dan Pemicu Antropogenik Karhutla Kalimantan',
                'slug' => 'anomali-iklim-el-nino-dan-pemicu-antropogenik-karhutla-kalimantan',
                'category' => 'faktor-penyebab',
                'summary' => 'Analisis komparatif antara faktor pemicu iklim regional (El Niño & IOD positif) dengan faktor aktivitas manusia dalam siklus karhutla tahunan.',
                'content' => "Setiap kali terjadi kabut asap parah di Kalimantan, perdebatan selalu muncul: apakah ini murni bencana alam akibat kemarau panjang, ataukah ulah manusia? Fakta ilmiah membuktikan bahwa karhutla di Kalimantan merupakan hasil pertemuan mematikan antara kondisi iklim kering ekstrem dan pemicu antropogenik (ulah manusia).

### 1. Peran Fenomena El Niño & Indian Ocean Dipole (IOD)
El Niño fase hangat di Samudra Pasifik ekuator menyebabkan pergeseran sirkulasi Walker. Akibatnya, pembentukan awan hujan di atas wilayah Indonesia, khususnya Kalimantan dan Sumatra, menurun drastis. Ketika El Niño bertepatan dengan fase positif Indian Ocean Dipole di Samudra Hindia, Pulau Kalimantan dapat mengalami periode Hari Tanpa Hujan (HTH) selama berbulan-bulan.

Namun demikian, iklim kering hanyalah faktor pendukung (*enabling factor*). Di hutan hujan tropis basah, vegetasi tidak memiliki mekanisme terbakar secara spontan akibat sambaran petir seperti halnya di hutan pinus beriklim sedang (temperate forests).

### 2. Sumber Sulutan Api (Antropogenik)
Lebih dari 99% kejadian kebakaran di Kalimantan diinisiasi oleh aktivitas manusia:
- **Metode Tebas-Bakar Murah:** Pembakaran lahan dianggap sebagai cara tercepat dan termurah untuk membersihkan vegetasi liar sebelum penanaman bibit baru.
- **Konflik Tenurial dan Akses Lahan:** Pembakaran kadang digunakan sebagai klaim fisik atas penguasaan lahan di kawasan hutan terbuka.
- **Kelalaian Aktivitas Harian:** Puntung rokok yang dibuang ke serasah gambut kering, pembakaran sampah tak terjaga, atau perapian pencari ikan di sepanjang sungai pedalaman.

Mengendalikan pemicu antropogenik melalui penegakan hukum dan insentif ekonomi pembukaan lahan tanpa bakar (PLTB) adalah kunci utama pencegahan.",
                'image_path' => null,
                'image_caption' => 'Peta anomali suhu muka laut Pasifik dan hubungannya dengan curah hujan Kalimantan.',
                'author' => 'Dr. H. Sulaiman (Pusat Studi Iklim)',
                'reading_time_minutes' => 4,
                'status' => EducationArticle::STATUS_PUBLISHED,
                'is_demo' => false,
                'published_at' => now()->subDays(4),
            ],
            [
                'title' => 'Dampak Karhutla Terhadap Keanekaragaman Hayati dan Emisi Karbon Global',
                'slug' => 'dampak-karhutla-terhadap-keanekaragaman-hayati-dan-emisi-karbon',
                'category' => 'dampak-lingkungan',
                'summary' => 'Meninjau ancaman kepunahan satwa endemik Kalimantan seperti Orangutan dan Bekantan, serta kontribusi pelepasan emisi karbon ke atmosfer global.',
                'content' => "Hutan dataran rendah dan rawa gambut Kalimantan merupakan rumah bagi keanekaragaman hayati terkaya di muka bumi. Ketika api merambat tak terkendali, kehancuran yang ditimbulkan melampaui batas batas wilayah administratif.

### 1. Ancaman Nyata Spesies Kunci Kalimantan
- **Orangutan Kalimantan (*Pongo pygmaeus*):** Satwa arboreal ini kehilangan tajuk pohon tempat bersarang dan mencari buah. Orangutan yang terkepung asap kerap menderita infeksi pernapasan akut, dehidrasi parah, hingga terpaksa masuk ke perkebunan warga di mana risiko konflik meningkat drastis.
- **Bekantan (*Nasalis larvatus*):** Monyet berhidung panjang endemik hutan rawa dan mangrove ini kehilangan koridor habitat riparian akibat kebakaran tepi sungai.

### 2. Pelepasan Emisi Karbon Raksasa
Gambut Kalimantan mampu menyimpan karbon hingga 10 hingga 20 kali lipat dibanding hutan mineral biasa per satuan luas. Pada peristiwa kebakaran hebat tahun 2015 dan 2019, pelepasan karbon harian dari kebakaran gambut di Indonesia sempat melampaui emisi bahan bakar fosil seluruh perekonomian Uni Eropa.

### 3. Degradasi Kualitas Air dan Perikanan Sungai
Jelaga dan abu kebakaran yang terbawa aliran hujan ke Sungai Kapuas, Barito, dan Kahayan menyebabkan deoksigenasi air dan penurunan pH secara drastis, membunuh biota air tawar dan memutus mata pencaharian nelayan sungai tradisional.",
                'image_path' => null,
                'image_caption' => 'Penyelamatan satwa liar di kawasan hutan rawa yang terdampak asap.',
                'author' => 'Divisi Konservasi Sumber Daya Alam',
                'reading_time_minutes' => 5,
                'status' => EducationArticle::STATUS_PUBLISHED,
                'is_demo' => false,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Teknik Restorasi Gambut: Prinsip 3R (Rewetting, Revegetation, Revitalization)',
                'slug' => 'teknik-restorasi-gambut-prinsip-3r-rewetting-revegetation-revitalization',
                'category' => 'teknik-pencegahan',
                'summary' => 'Panduan terapan pemulihan ekosistem gambut melalui pembasahan ulang (sekat kanal), penanaman kembali vegetasi ramah gambut, dan revitalisasi ekonomi desa.',
                'content' => "Pencegahan karhutla yang berkelanjutan tidak dapat mengandalkan pemadaman darurat semata saat musim kemarau tiba. Upaya paling efektif adalah memulihkan kondisi hidrologis gambut yang telah rusak dengan pendekatan **3R**:

### 1. Rewetting (Pembasahan Kembali)
Langkah fondasi untuk mencegah kebakaran gambut adalah mengembalikan air ke dalam kubah gambut:
- **Pembangunan Sekat Kanal (Canal Blocking):** Membendung kanal drainase menggunakan kayu gelondongan atau material komposit untuk menahan laju aliran air keluar, sehingga tinggi muka air tanah bertahan minimal 40 cm di bawah permukaan tanah.
- **Pembangunan Sumur Bor:** Menyediakan cadangan air tanah dangkal di sekitar areal perladangan masyarakat untuk pembasahan cepat saat cuaca ekstrem.
- **Penimbunan Kanal (Canal Backfilling):** Menutup total kanal-kanal ilegal di dalam kawasan konservasi.

### 2. Revegetation (Penanaman Kembali)
Menanam kembali jenis-jenis pohon asli hutan rawa gambut yang memiliki toleransi tinggi terhadap genangan air, seperti Belangeran (*Shorea balangeran*), Jelutung Rawa (*Dyera polyphylla*), dan Ramin (*Gonystylus bancanus*).

### 3. Revitalization (Revitalisasi Sumber Mata Pencaharian)
Masyarakat di sekitar kawasan gambut memerlukan alternatif ekonomi yang tidak membutuhkan pembakaran lahan (*paludikultur*), seperti budidaya madu kelulut, perikanan air tawar ramah gambut, dan pemanfaatan purun untuk kerajinan anyaman bernilai tinggi.",
                'image_path' => null,
                'image_caption' => 'Konstruksi sekat kanal kayu untuk menahan kebasahan kubah gambut.',
                'author' => 'Badan Restorasi Gambut & Mangrove',
                'reading_time_minutes' => 4,
                'status' => EducationArticle::STATUS_PUBLISHED,
                'is_demo' => false,
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Aspek Yuridis dan Sanksi Hukum Pidana Bagi Pelaku Pembakaran Hutan dan Lahan',
                'slug' => 'aspek-yuridis-dan-sanksi-hukum-pidana-pelaku-pembakaran-karhutla',
                'category' => 'hukum-kebijakan',
                'summary' => 'Tinjauan regulasi UU PPLH dan UU Kehutanan terkait larangan mutlak pembakaran lahan serta pertanggungjawaban mutlak (strict liability) bagi korporasi.',
                'content' => "Hukum positif di Indonesia menempatkan pembakaran hutan dan lahan sebagai tindak pidana serius yang mengancam keselamatan umum, lingkungan hidup, dan hak asasi warga negara untuk menikmati lingkungan hidup yang baik dan sehat.

### 1. Landasan Hukum Larangan Membakar
Beberapa undang-undang pokok yang mengatur larangan pembakaran:
- **UU No. 32 Tahun 2009 tentang Perlindungan dan Pengelolaan Lingkungan Hidup (PPLH):** Pasal 69 ayat (1) huruf h secara tegas menyatakan: *Setiap orang dilarang melakukan pembukaan lahan dengan cara membakar.*
- **UU No. 41 Tahun 1999 tentang Kehutanan:** Pasal 50 ayat (3) huruf d melarang pembakaran hutan di dalam kawasan hutan negara maupun hutan hak.

### 2. Sanksi Pidana dan Denda
- Pelaku perorangan yang terbukti dengan sengaja membakar hutan dapat diancam pidana penjara paling lama **15 (lima belas) tahun** dan denda paling banyak **Rp 5.000.000.000 (lima miliar rupiah)**.
- Bila dilakukan akibat kelalaian (*culpa*), ancaman pidana penjara hingga 5 tahun dan denda hingga Rp 1,5 miliar.

### 3. Doktrin Strict Liability (Tanggung Jawab Mutlak) Korporasi
Bagi perusahaan pemegang konsesi (perkebunan atau kehutanan), hukum Indonesia menerapkan prinsip pertanggungjawaban mutlak (*strict liability*). Bila terjadi kebakaran di dalam wilayah konsesi berizin, perusahaan wajib membayar ganti rugi pemulihan lingkungan tanpa perlu pembuktian unsur kesalahan sengaja dari pihak korporasi.

Masyarakat memiliki hak hukum untuk mengajukan gugatan perwakilan kelompok (*class action*) terhadap pihak-pihak yang lalai menjaga konsesinya sehingga menimbulkan kabut asap berkepanjangan.",
                'image_path' => null,
                'image_caption' => 'Penegakan hukum dan pemasangan garis penyidikan di lahan yang terbakar.',
                'author' => 'Lembaga Bantuan Hukum Lingkungan',
                'reading_time_minutes' => 4,
                'status' => EducationArticle::STATUS_PUBLISHED,
                'is_demo' => false,
                'published_at' => now()->subDay(),
            ],
            [
                'title' => '[DRAFT] Panduan Pengoperasian Drone Sensor Termal untuk Deteksi Titik Panas',
                'slug' => 'panduan-pengoperasian-drone-sensor-termal-deteksi-titik-panas',
                'category' => 'teknik-pencegahan',
                'summary' => 'Draf panduan teknis operasional patroli udara jarak dekat menggunakan drone berkamera FLIR untuk tim relawan posko.',
                'content' => "Draf panduan teknis yang masih dalam tahap pengujian lapangan bersama tim Masyarakat Peduli Api mengenai kalibrasi sensor inframerah kamera drone saat melintasi tutupan asap pekat.",
                'image_path' => null,
                'image_caption' => null,
                'author' => 'Tim Teknologi Posko',
                'reading_time_minutes' => 3,
                'status' => EducationArticle::STATUS_DRAFT,
                'is_demo' => false,
                'published_at' => null,
            ],
        ];

        foreach ($articles as $row) {
            EducationArticle::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
}
