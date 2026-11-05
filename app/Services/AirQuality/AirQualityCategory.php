<?php

namespace App\Services\AirQuality;

/**
 * Klasifikasi kategori PM2.5 berdasarkan ISPU (Indeks Standar Pencemar Udara).
 *
 * Ambang batas mengikuti rentang PM2.5 (µg/m³) yang dipublikasikan Kementerian
 * Lingkungan Hidup RI untuk ISPU. Kategori TIDAK dihitung ulang di sini bila
 * sumber data sudah menyediakan kategorinya sendiri — nilai dari sumber selalu
 * diutamakan.
 */
class AirQualityCategory
{
    /**
     * @return array{label:string,risk:string,color:string,advice:string}
     */
    public static function forPm25(float $pm25): array
    {
        // Rentang batas atas PM2.5 (µg/m³) per kategori ISPU
        $bands = [
            [50, 'Baik', 'Risiko rendah', 'success',
                'Udara bersih. Aman untuk aktivitas luar ruangan seperti biasa.'],
            [100, 'Sedang', 'Risiko sedang', 'primary',
                'Masih dapat beraktivitas di luar, namun kurangi aktivitas berat berkelanjutan.'],
            [200, 'Tidak Sehat', 'Risiko meningkat', 'warning',
                'Kurangi aktivitas luar ruang. Kelompok sensitif sebaiknya memakai masker.'],
            [300, 'Sangat Tidak Sehat', 'Risiko tinggi', 'danger',
                'Hindari aktivitas luar ruang. Gunakan masker N95 bila harus keluar.'],
            [500, 'Berbahaya', 'Risiko sangat tinggi', 'dark',
                'Tetap di dalam ruangan, tutup ventilasi, dan ikuti arahan otoritas setempat.'],
        ];

        foreach ($bands as [$max, $label, $risk, $color, $advice]) {
            if ($pm25 <= $max) {
                return compact('label', 'risk', 'color', 'advice');
            }
        }

        return [
            'label' => 'Berbahaya',
            'risk' => 'Risiko sangat tinggi',
            'color' => 'dark',
            'advice' => 'Tetap di dalam ruangan, tutup ventilasi, dan ikuti arahan otoritas setempat.',
        ];
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
     * Daftar seluruh kategori ISPU untuk PM2.5 dengan bahasa sederhana.
     *
     * @return array<int, array{key:string,label:string,range:string,min:int,max:int,color:string,chip:string,risk:string,simple_desc:string,advice:string,icon:string}>
     */
    public static function allCategories(): array
    {
        return [
            [
                'key' => 'good',
                'label' => 'Baik',
                'range' => '0 - 50',
                'min' => 0,
                'max' => 50,
                'color' => 'success',
                'chip' => 'aq-good',
                'risk' => 'Risiko rendah',
                'simple_desc' => 'Kualitas udara sangat bersih dan segar, tidak memberikan dampak negatif bagi manusia, hewan, maupun vegetasi.',
                'advice' => 'Sangat aman untuk seluruh kegiatan luar ruangan, olahraga, dan anak-anak bermain bebas di alam terbuka.',
                'icon' => 'bi-shield-check',
            ],
            [
                'key' => 'moderate',
                'label' => 'Sedang',
                'range' => '51 - 100',
                'min' => 51,
                'max' => 100,
                'color' => 'primary',
                'chip' => 'aq-moderate',
                'risk' => 'Risiko sedang',
                'simple_desc' => 'Kualitas udara masih tergolong wajar dan aman bagi masyarakat umum, meski konsentrasi partikel mikro mulai terdeteksi.',
                'advice' => 'Aktivitas luar ruang masih aman dilakukan. Individu yang memiliki sensitivitas tinggi terhadap debu disarankan membatasi olahraga berat.',
                'icon' => 'bi-info-circle',
            ],
            [
                'key' => 'unhealthy',
                'label' => 'Tidak Sehat',
                'range' => '101 - 200',
                'min' => 101,
                'max' => 200,
                'color' => 'warning',
                'chip' => 'aq-unhealthy',
                'risk' => 'Risiko meningkat',
                'simple_desc' => 'Tingkat partikel polutan mulai merugikan kesehatan kelompok rentan dan berpotensi menimbulkan iritasi mata atau saluran napas.',
                'advice' => 'Kurangi aktivitas di luar ruangan. Kenakan masker pelindung bila bepergian, terutama bagi anak-anak, lansia, dan penderita asma.',
                'icon' => 'bi-exclamation-triangle',
            ],
            [
                'key' => 'very_unhealthy',
                'label' => 'Sangat Tidak Sehat',
                'range' => '201 - 300',
                'min' => 201,
                'max' => 300,
                'color' => 'danger',
                'chip' => 'aq-very-unhealthy',
                'risk' => 'Risiko tinggi',
                'simple_desc' => 'Kondisi udara pekat berbahaya yang dapat memicu batuk, mata perih, sesak, dan penurunan fungsi paru pada populasi luas.',
                'advice' => 'Hindari aktivitas di luar rumah. Tutup ventilasi ruangan dan wajib gunakan masker respirator standar N95/KN95 bila harus bepergian.',
                'icon' => 'bi-slash-circle',
            ],
            [
                'key' => 'hazardous',
                'label' => 'Berbahaya',
                'range' => '> 300',
                'min' => 301,
                'max' => 500,
                'color' => 'dark',
                'chip' => 'aq-hazardous',
                'risk' => 'Risiko sangat tinggi',
                'simple_desc' => 'Tingkat darurat polusi udara akut akibat kabut asap tebal yang membahayakan kesehatan seluruh lapisan masyarakat tanpa terkecuali.',
                'advice' => 'Tetap berada di dalam rumah, nyalakan pembersih udara (air purifier), tutup rapat semua ventilasi, dan ikuti arahan posko tanggap darurat.',
                'icon' => 'bi-radioactive',
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
