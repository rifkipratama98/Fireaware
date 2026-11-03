<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\Hotspot;
use App\Models\Announcement;
use App\Models\SafetyGuide;
use Illuminate\Database\Seeder;

class FireAwareDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedProvinces();
        $this->seedHotspots();
        $this->seedAnnouncements();
        $this->seedSafetyGuides();
    }

    private function seedProvinces(): void
    {
        $data = [
            ['name' => 'Kalimantan Barat', 'slug' => 'kalimantan-barat', 'code' => 'KALBAR', 'center_lat' => 0.123, 'center_lng' => 110.750],
            ['name' => 'Kalimantan Tengah', 'slug' => 'kalimantan-tengah', 'code' => 'KALTENG', 'center_lat' => -1.500, 'center_lng' => 113.000],
            ['name' => 'Kalimantan Selatan', 'slug' => 'kalimantan-selatan', 'code' => 'KALSEL', 'center_lat' => -3.100, 'center_lng' => 115.400],
            ['name' => 'Kalimantan Timur', 'slug' => 'kalimantan-timur', 'code' => 'KALTIM', 'center_lat' => 0.500, 'center_lng' => 116.500],
            ['name' => 'Kalimantan Utara', 'slug' => 'kalimantan-utara', 'code' => 'KALTARA', 'center_lat' => 3.100, 'center_lng' => 116.200],
        ];

        foreach ($data as $row) {
            Province::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
    private function seedHotspots(): void
    {
        // Idempoten: bersihkan data demo lama sebelum memasukkan ulang
        Hotspot::query()->where('source', 'demo')->delete();

        $map = Province::query()->pluck('id', 'slug');
        $demo = [
            'kalimantan-barat'    => [[-0.05, 109.30], [0.60, 109.90], [1.10, 110.40], [-0.85, 110.10], [0.35, 111.00], [-1.30, 109.70]],
            'kalimantan-tengah'   => [[-2.20, 111.80], [-1.10, 112.60], [-0.40, 113.50], [-1.90, 113.80], [-2.90, 112.40], [-0.85, 114.10]],
            'kalimantan-selatan'  => [[-3.40, 114.80], [-2.80, 115.10], [-3.05, 115.70], [-3.90, 115.20], [-2.55, 115.90], [-3.60, 116.10]],
            'kalimantan-timur'    => [[0.30, 117.30], [1.10, 116.90], [-0.60, 116.20], [0.90, 117.90], [-1.20, 116.60], [1.60, 118.10]],
            'kalimantan-utara'    => [[3.30, 116.10], [3.60, 116.90], [2.90, 117.50], [4.10, 117.20], [3.20, 117.70], [4.40, 116.40]],
        ];
        $now = now();
        $rows = [];
        $sensors = ['VIIRS S-NPP', 'VIIRS NOAA-20', 'MODIS Aqua', 'MODIS Terra'];
        foreach ($demo as $slug => $points) {
            $provinceId = $map[$slug] ?? null;
            if (! $provinceId) {
                continue;
            }
            foreach ($points as $i => [$lat, $lng]) {
                $rows[] = [
                    'source' => 'demo',
                    'sensor' => $sensors[$i % count($sensors)],
                    'province_id' => $provinceId,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'confidence' => random_int(55, 95),
                    'frp' => round(random_int(5, 180) + random_int(0, 99) / 100, 2),
                    'city' => null,
                    'detected_at' => $now->copy()->subHours(($i + 1) * 3),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        if ($rows !== []) {
            Hotspot::query()->insert($rows);
        }
    }

    private function seedAnnouncements(): void
    {
        Announcement::query()->truncate();

        $kalselId = Province::where('slug', 'kalimantan-selatan')->value('id');
        $kaltengId = Province::where('slug', 'kalimantan-tengah')->value('id');
        $kalbarId = Province::where('slug', 'kalimantan-barat')->value('id');

        $rows = [
            [
                'title' => 'Waspada Peningkatan Titik Panas di Kawasan Gambut Pulang Pisau dan Kapuas',
                'slug' => 'waspada-peningkatan-titik-panas-pulang-pisau-kapuas',
                'summary' => 'Hasil pantauan sensor satelit mendeteksi kenaikan anomali suhu di lahan gambut terbuka. Posko siaga darurat BPBD Kalteng mengimbau warga waspada kabut asap.',
                'body' => "Berdasarkan hasil analisis harian citra satelit dan data ground check lapangan, terdeteksi kenaikan intensitas titik panas (hotspot) di beberapa titik kawasan gambut terdegradasi wilayah Kabupaten Pulang Pisau dan Kabupaten Kapuas.\n\nRegu Manggala Agni Daops Kalimantan Tengah bersama personel BPBD telah disiagakan di posko lapangan untuk melakukan patroli terpadu dan pembasahan (water bombing) pada titik rawan.\n\nMasyarakat diimbau:\n1. Tidak melakukan pembersihan kebun atau lahan dengan cara membakar.\n2. Segera melapor ke posko BPBD bila melihat indikasi asap pekat di sekitar perladangan.\n3. Membatasi kegiatan fisik anak-anak di luar ruangan saat pagi hari jika tercium bau asap menyengat.",
                'level' => Announcement::LEVEL_WASPADA,
                'province_id' => $kaltengId,
                'source_name' => 'BPBD Provinsi Kalimantan Tengah',
                'source_url' => 'https://bpbd.kalteng.go.id',
                'status' => Announcement::STATUS_PUBLISHED,
                'is_demo' => true,
                'published_at' => now()->subHours(5),
            ],
            [
                'title' => 'Peringatan Hari Tanpa Hujan Ekstrem di Wilayah Banjarbaru dan Tanah Laut',
                'slug' => 'peringatan-hari-tanpa-hujan-ekstrem-banjarbaru-tanah-laut',
                'summary' => 'BMKG Stasiun Klimatologi Kalsel merilis status Hari Tanpa Hujan (HTH) berturut-turut lebih dari 30 hari yang memicu kerentanan karhutla tinggi.',
                'body' => "Stasiun Klimatologi BMKG Kalimantan Selatan menyampaikan peringatan dini kekeringan meteorologis. Sebagian wilayah Kota Banjarbaru, Kabupaten Banjar, dan Kabupaten Tanah Laut telah mengalami Hari Tanpa Hujan (HTH) lebih dari 30 hari berturut-turut.\n\nKondisi ini mengakibatkan vegetasi semak belukar dan gambut dangkal berada dalam tingkat kemudahan terbakar yang sangat tinggi (Indeks FWI kategori Ekstrem).\n\nInstansi terkait dan masyarakat diharapkan waspada terhadap potensi kebakaran di sepanjang jalur akses Bandara Syamsudin Noor dan kawasan perkebunan rakyat.",
                'level' => Announcement::LEVEL_DARURAT,
                'province_id' => $kalselId,
                'source_name' => 'BMKG Stasiun Klimatologi Kalimantan Selatan',
                'source_url' => 'https://bmkg.go.id',
                'status' => Announcement::STATUS_PUBLISHED,
                'is_demo' => true,
                'published_at' => now()->subDay(),
            ],
            [
                'title' => 'Sosialisasi Pembuatan Sumur Bor dan Penataan Sekat Kanal Gambut',
                'slug' => 'sosialisasi-pembuatan-sumur-bor-dan-penataan-sekat-kanal',
                'summary' => 'Kelompok Masyarakat Peduli Api (MPA) bersama dinas kehutanan menyelenggarakan pelatihan teknik pembasahan gambut mandiri di Kubu Raya.',
                'body' => "Dinas Lingkungan Hidup dan Kehutanan bersama Manggala Agni menggelar sosialisasi pemeliharaan sekat kanal (canal blocking) dan operasional sumur bor di Kabupaten Kubu Raya.\n\nProgram ini bertujuan mempertahankan tingkat kebasahan muka air tanah gambut di atas minus 40 sentimeter dari permukaan guna mencegah timbulnya titik api bawah tanah selama puncak musim kemarau.",
                'level' => Announcement::LEVEL_INFO,
                'province_id' => $kalbarId,
                'source_name' => 'Dinas LHK Provinsi Kalimantan Barat',
                'source_url' => 'https://dislhk.kalbarprov.go.id',
                'status' => Announcement::STATUS_PUBLISHED,
                'is_demo' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Seluruh Data Pengumuman dan Titik Panas Pada Versi Ini Adalah Data DEMO',
                'slug' => 'seluruh-data-pada-versi-ini-adalah-data-demo',
                'summary' => 'Pemberitahuan resmi pengembang: sistem saat ini menggunakan dataset percontohan untuk keperluan simulasi tata kelola informasi kebencanaan.',
                'body' => "Data titik panas, stasiun kualitas udara, dan pengumuman yang ditampilkan pada sistem ini masih berupa data contoh (simulasi DEMO) yang dirancang untuk pengujian antarmuka dan alur kerja aplikasi FireAware Kalimantan.\n\nSistem belum terintegrasi secara langsung dengan data telemetri real-time satelit LAPAN/BRIN maupun instansi operasional kebencanaan resmi.",
                'level' => Announcement::LEVEL_INFO,
                'province_id' => null,
                'source_name' => 'Tim Pengembang FireAware Kalimantan',
                'source_url' => null,
                'status' => Announcement::STATUS_PUBLISHED,
                'is_demo' => true,
                'published_at' => now()->subDays(4),
            ],
        ];

        foreach ($rows as $row) {
            Announcement::create($row);
        }
    }

    private function seedSafetyGuides(): void
    {
        $rows = [
            ['title' => 'Kenali jenis kebakaran lahan', 'slug' => 'kenali-jenis-kebakaran', 'audience' => 1, 'summary' => 'Beda lahan gambut, perkebunan, dan hutan menentukan cara melapor serta tingkat risikonya.', 'icon' => 'bi-fire', 'sort_order' => 1],
            ['title' => 'Lindungi diri dari asap', 'slug' => 'lindungi-diri-dari-asap', 'audience' => 1, 'summary' => 'Gunakan masker N95, kurangi aktivitas luar ruang, kenali gejala iritasi pernapasan.', 'icon' => 'bi-emoji-dizzy', 'sort_order' => 2],
            ['title' => 'Lapor tanpa membakar', 'slug' => 'lapor-tanpa-membakar', 'audience' => 3, 'summary' => 'Cara melaporkan titik api ke Manggala Agni atau BPBD tanpa mendekati lokasi.', 'icon' => 'bi-telephone-outbound', 'sort_order' => 3],
            ['title' => 'Bersiap menghadapi asap tebal', 'slug' => 'bersiap-asap-tebal', 'audience' => 2, 'summary' => 'Panduan singkat bagi pelajar saat aktivitas luar ruangan dibatasi.', 'icon' => 'bi-backpack2', 'sort_order' => 4],
        ];
        foreach ($rows as $row) {
            SafetyGuide::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
}

