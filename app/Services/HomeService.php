<?php

namespace App\Services;

use App\Models\AirQualityReading;
use App\Models\Guide;
use App\Models\Hotspot;
use App\Models\Province;
use App\Models\Announcement;
use App\Models\SafetyGuide;

class HomeService
{
    /**
     * Terjemahkan slug provinsi menjadi id (null bila tidak ditemukan).
     */
    public function resolveProvinceId(?string $slug): ?int
    {
        if ($slug === null || $slug === '') {
            return null;
        }

        $id = Province::query()->where('slug', $slug)->value('id');

        return $id !== null ? (int) $id : null;
    }

    /**
     * Data beranda. Tidak mengarang angka: bila tidak ada data, nilai nol/kosong.
     */
    public function getHomeData(?int $provinceId = null): array
    {
        // Tabel provinsi kecil — query langsung tanpa cache agar aman (serialisasi
        // Eloquent Collection di cache file rentan "incomplete object").
        $provinces = Province::query()
            ->withCount(['hotspots as hotspots_7d_count' => function ($q) {
                $q->where('detected_at', '>=', now()->subDays(7));
            }])
            ->orderBy('name')
            ->get();

        $activeProvince = $provinceId !== null
            ? $provinces->firstWhere('id', $provinceId)
            : null;

        $base = Hotspot::query()->where('detected_at', '>=', now()->subDays(7));
        if ($activeProvince !== null) {
            $base->where('province_id', $activeProvince->id);
        }

        return [
            'provinces' => $provinces,
            'activeProvince' => $activeProvince,
            'summary' => [
                'total_7d' => (clone $base)->count(),
                'last_24h' => (clone $base)->where('detected_at', '>=', now()->subDay())->count(),
                'high_confidence' => (clone $base)->where('confidence', '>=', 80)->count(),
                'last_detected_at' => $this->lastDetectedAt($base),
            ],
            'hotspots' => (clone $base)->with('province:id,name,slug')
                ->orderByDesc('detected_at')->limit(8)->get(),
            'mapHotspots' => (clone $base)->get(['id', 'province_id', 'latitude', 'longitude', 'confidence', 'detected_at']),
            'announcements' => Announcement::query()->published()->orderByDesc('published_at')->limit(3)->get(),
            'safetyGuides' => SafetyGuide::query()->where('is_active', true)->orderBy('sort_order')->limit(4)->get(),
            'publishedGuides' => Guide::query()->published()->orderByDesc('published_at')->limit(4)->get(),
            'latestAirQuality' => AirQualityReading::query()
                ->when($activeProvince !== null, fn ($q) => $q->whereHas('station', fn ($sq) => $sq->where('province_id', $activeProvince->id)))
                ->with(['station.province'])
                ->orderByDesc('measured_at')
                ->first(),
        ];
    }

    /**
     * Waktu deteksi terakhir sebagai instance Carbon (null bila kosong).
     * Query agregat mengembalikan string, sehingga perlu dikonversi eksplisit.
     */
    private function lastDetectedAt($query): ?\Illuminate\Support\Carbon
    {
        $value = (clone $query)->max('detected_at');

        return $value !== null ? \Illuminate\Support\Carbon::parse($value) : null;
    }
}

