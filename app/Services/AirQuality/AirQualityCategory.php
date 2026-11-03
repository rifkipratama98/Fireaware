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
}
