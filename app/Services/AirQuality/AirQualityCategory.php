<?php

namespace App\Services\AirQuality;

/**
 * Klasifikasi kategori PM2.5 berdasarkan Peraturan Menteri Lingkungan Hidup dan Kehutanan
 * (Permen LHK) Republik Indonesia Nomor 14 Tahun 2020 tentang Indeks Standar Pencemar Udara (ISPU).
 *
 * Lampiran I Permen LHK No. 14 Tahun 2020 menetapkan ambang batas konsentrasi PM2.5 (rata-rata 24 jam):
 * - Baik               : 0.0 - 15.5 µg/m³   (Nilai Indeks ISPU: 0 - 50, Warna: Hijau)
 * - Sedang             : 15.6 - 55.4 µg/m³  (Nilai Indeks ISPU: 51 - 100, Warna: Biru)
 * - Tidak Sehat        : 55.5 - 150.4 µg/m³ (Nilai Indeks ISPU: 101 - 200, Warna: Kuning)
 * - Sangat Tidak Sehat : 150.5 - 250.4 µg/m³(Nilai Indeks ISPU: 201 - 300, Warna: Merah)
 * - Berbahaya          : >= 250.5 µg/m³     (Nilai Indeks ISPU: 301 - 500, Warna: Hitam)
 */
class AirQualityCategory
{
    /**
     * Hitung kategori mutu udara berdasarkan konsentrasi fisik PM2.5 (µg/m³).
     *
     * @return array{label:string,risk:string,color:string,advice:string,pm25_range:string,ispu_range:string}
     */
    public static function forPm25(float $pm25): array
    {
        $bands = [
            [
                15.5,
                'Baik',
                'Risiko rendah',
                'success',
                'Kualitas udara sangat baik, tingkat polusi minimal. Aman untuk seluruh aktivitas luar ruangan.',
                '0 - 15.5 µg/m³',
                '0 - 50',
            ],
            [
                55.4,
                'Sedang',
                'Risiko sedang',
                'primary',
                'Kualitas udara masih dapat diterima bagi masyarakat umum. Kelompok sensitif sebaiknya membatasi aktivitas fisik berat di luar ruang.',
                '15.6 - 55.4 µg/m³',
                '51 - 100',
            ],
            [
                150.4,
                'Tidak Sehat',
                'Risiko meningkat',
                'warning',
                'Tingkat partikel polutan dapat merugikan kesehatan kelompok rentan (anak-anak, lansia, penderita asma). Gunakan masker saat keluar rumah.',
                '55.5 - 150.4 µg/m³',
                '101 - 200',
            ],
            [
                250.4,
                'Sangat Tidak Sehat',
                'Risiko tinggi',
                'danger',
                'Kualitas udara berbahaya bagi seluruh populasi. Hindari aktivitas di luar rumah dan gunakan masker respirator N95/KN95 bila harus bepergian.',
                '150.5 - 250.4 µg/m³',
                '201 - 300',
            ],
            [
                500.0,
                'Berbahaya',
                'Risiko sangat tinggi',
                'dark',
                'Kondisi darurat polusi akut akibat kabut asap tebal. Tetap di dalam rumah, tutup ventilasi rapat, dan ikuti arahan posko kebencanaan.',
                '>= 250.5 µg/m³',
                '301 - 500',
            ],
        ];

        foreach ($bands as [$max, $label, $risk, $color, $advice, $pm25_range, $ispu_range]) {
            if ($pm25 <= $max) {
                return compact('label', 'risk', 'color', 'advice', 'pm25_range', 'ispu_range');
            }
        }

        return [
            'label' => 'Berbahaya',
            'risk' => 'Risiko sangat tinggi',
            'color' => 'dark',
            'advice' => 'Kondisi darurat polusi akut akibat kabut asap tebal. Tetap di dalam rumah, tutup ventilasi rapat, dan ikuti arahan posko kebencanaan.',
            'pm25_range' => '>= 250.5 µg/m³',
            'ispu_range' => '301 - 500',
        ];
    }

    /**
     * Hitung nilai ISPU indikatif menggunakan rumus interpolasi resmi Permen LHK 14/2020:
     * I = ((Ia - Ib) / (Xa - Xb)) * (Xx - Xb) + Ib
     */
    public static function calculateIspu(float $pm25): int
    {
        if ($pm25 <= 0) {
            return 0;
        }

        if ($pm25 <= 15.5) {
            // 0 - 15.5 -> ISPU 0 - 50
            return (int) round((50.0 / 15.5) * $pm25);
        }

        if ($pm25 <= 55.4) {
            // 15.6 - 55.4 -> ISPU 51 - 100
            $ispu = ((100.0 - 51.0) / (55.4 - 15.6)) * ($pm25 - 15.6) + 51.0;
            return (int) round($ispu);
        }

        if ($pm25 <= 150.4) {
            // 55.5 - 150.4 -> ISPU 101 - 200
            $ispu = ((200.0 - 101.0) / (150.4 - 55.5)) * ($pm25 - 55.5) + 101.0;
            return (int) round($ispu);
        }

        if ($pm25 <= 250.4) {
            // 150.5 - 250.4 -> ISPU 201 - 300
            $ispu = ((300.0 - 201.0) / (250.4 - 150.5)) * ($pm25 - 150.5) + 201.0;
            return (int) round($ispu);
        }

        // >= 250.5 -> ISPU 301 - 500 (dibatasi 500)
        $ispu = ((500.0 - 301.0) / (500.0 - 250.5)) * (min(500.0, $pm25) - 250.5) + 301.0;
        return (int) min(500, round($ispu));
    }

    /** Nama CSS class sesuai token desain untuk kategori. */
    public static function chipClass(string $label): string
    {
        return match ($label) {
            'Baik' => 'aq-good',
            'Sedang' => 'aq-moderate',
            'Tidak Sehat' => 'aq-unhealthy',
            'Sangat Tidak Sehat' => 'aq-very-unhealthy',
            default => 'aq-hazardous',
        };
    }

    /**
     * Daftar seluruh kategori ISPU resmi untuk PM2.5 berdasarkan Permen LHK 14/2020.
     *
     * @return array<int, array{key:string,label:string,pm25_range:string,ispu_range:string,min_pm25:float,max_pm25:float,min_ispu:int,max_ispu:int,color:string,chip:string,risk:string,simple_desc:string,advice:string,official_color:string,averaging_period:string}>
     */
    public static function allCategories(): array
    {
        return [
            [
                'key' => 'good',
                'label' => 'Baik',
                'pm25_range' => '0 - 15.5 µg/m³',
                'ispu_range' => '0 - 50',
                'min_pm25' => 0.0,
                'max_pm25' => 15.5,
                'min_ispu' => 0,
                'max_ispu' => 50,
                'color' => 'success',
                'official_color' => 'Hijau',
                'chip' => 'aq-good',
                'risk' => 'Risiko rendah',
                'simple_desc' => 'Tingkat mutu udara yang tidak memberikan dampak negatif terhadap kesehatan manusia ataupun hewan dan tidak mengganggu tumbuhan, bangunan ataupun nilai estetika.',
                'advice' => 'Sangat aman untuk seluruh kegiatan luar ruangan, olahraga, dan anak-anak bermain bebas di alam terbuka.',
                'averaging_period' => 'Rata-rata 24 Jam (Standar Acuan ISPU)',
            ],
            [
                'key' => 'moderate',
                'label' => 'Sedang',
                'pm25_range' => '15.6 - 55.4 µg/m³',
                'ispu_range' => '51 - 100',
                'min_pm25' => 15.6,
                'max_pm25' => 55.4,
                'min_ispu' => 51,
                'max_ispu' => 100,
                'color' => 'primary',
                'official_color' => 'Biru',
                'chip' => 'aq-moderate',
                'risk' => 'Risiko sedang',
                'simple_desc' => 'Tingkat mutu udara yang masih dapat diterima pada kesehatan manusia, hewan dan tumbuhan.',
                'advice' => 'Aktivitas luar ruang masih aman dilakukan. Individu yang memiliki sensitivitas tinggi terhadap debu disarankan membatasi aktivitas fisik berat.',
                'averaging_period' => 'Rata-rata 24 Jam (Standar Acuan ISPU)',
            ],
            [
                'key' => 'unhealthy',
                'label' => 'Tidak Sehat',
                'pm25_range' => '55.5 - 150.4 µg/m³',
                'ispu_range' => '101 - 200',
                'min_pm25' => 55.5,
                'max_pm25' => 150.4,
                'min_ispu' => 101,
                'max_ispu' => 200,
                'color' => 'warning',
                'official_color' => 'Kuning',
                'chip' => 'aq-unhealthy',
                'risk' => 'Risiko meningkat',
                'simple_desc' => 'Tingkat mutu udara yang bersifat merugikan pada manusia ataupun kelompok hewan yang sensitif atau dapat menimbulkan kerusakan pada tumbuhan ataupun nilai estetika.',
                'advice' => 'Kurangi aktivitas di luar ruangan. Kenakan masker pelindung bila bepergian, terutama bagi anak-anak, lansia, dan penderita asma.',
                'averaging_period' => 'Rata-rata 24 Jam (Standar Acuan ISPU)',
            ],
            [
                'key' => 'very_unhealthy',
                'label' => 'Sangat Tidak Sehat',
                'pm25_range' => '150.5 - 250.4 µg/m³',
                'ispu_range' => '201 - 300',
                'min_pm25' => 150.5,
                'max_pm25' => 250.4,
                'min_ispu' => 201,
                'max_ispu' => 300,
                'color' => 'danger',
                'official_color' => 'Merah',
                'chip' => 'aq-very-unhealthy',
                'risk' => 'Risiko tinggi',
                'simple_desc' => 'Tingkat mutu udara yang dapat meningkatkan risiko kesehatan pada sejumlah segmen populasi yang terpapar.',
                'advice' => 'Hindari aktivitas di luar rumah. Tutup ventilasi ruangan dan wajib gunakan masker respirator standar N95/KN95 bila harus bepergian.',
                'averaging_period' => 'Rata-rata 24 Jam (Standar Acuan ISPU)',
            ],
            [
                'key' => 'hazardous',
                'label' => 'Berbahaya',
                'pm25_range' => '>= 250.5 µg/m³',
                'ispu_range' => '301 - 500',
                'min_pm25' => 250.5,
                'max_pm25' => 500.0,
                'min_ispu' => 301,
                'max_ispu' => 500,
                'color' => 'dark',
                'official_color' => 'Hitam',
                'chip' => 'aq-hazardous',
                'risk' => 'Risiko sangat tinggi',
                'simple_desc' => 'Tingkat mutu udara yang dapat menimbulkan dampak kesehatan serius pada populasi dan memerlukan penanganan segera.',
                'advice' => 'Tetap berada di dalam rumah, nyalakan pembersih udara (air purifier), tutup rapat semua ventilasi, dan ikuti arahan posko tanggap darurat.',
                'averaging_period' => 'Rata-rata 24 Jam (Standar Acuan ISPU)',
            ],
        ];
    }

    /**
     * Cari detail kategori berdasarkan label.
     */
    public static function categoryDetails(string $label): ?array
    {
        foreach (self::allCategories() as $item) {
            if (strcasecmp($item['label'], $label) === 0) {
                return $item;
            }
        }

        return null;
    }
}
