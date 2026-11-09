<?php

namespace App\Http\Controllers;

use App\Services\HomeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __construct(private HomeService $homeService)
    {
    }

    /**
     * Halaman utama publik dengan filter provinsi opsional (?provinsi=slug).
     */
    public function index(Request $request): Response
    {
        $slug = $request->query('provinsi');
        $provinceId = $this->homeService->resolveProvinceId(is_string($slug) ? $slug : null);
        $data = $this->homeService->getHomeData($provinceId);

        return Inertia::render('Public/Home', [
            'provinces' => $data['provinces'],
            'activeProvince' => $data['activeProvince'],
            'summary' => [
                'total_7d' => $data['summary']['total_7d'],
                'last_24h' => $data['summary']['last_24h'],
                'high_confidence' => $data['summary']['high_confidence'],
                'last_detected_at' => $data['summary']['last_detected_at']?->toIso8601String(),
            ],
            'hotspots' => $data['hotspots'],
            'mapHotspots' => $data['mapHotspots']->map(fn ($h) => [
                'id' => $h->id,
                'lat' => (float) $h->latitude,
                'lng' => (float) $h->longitude,
                'confidence' => $h->confidence,
                'province' => $h->province?->name,
                'detected_at' => $h->detected_at?->toIso8601String(),
            ]),
            'announcements' => $data['announcements']->map(fn ($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'slug' => $a->slug,
                'summary' => $a->summary,
                'level' => $a->level,
                'level_label' => $a->levelLabel(),
                'source_name' => $a->source_name,
                'published_at' => $a->published_at?->toIso8601String(),
                'effective_date' => $a->effectiveDate()?->isoFormat('D MMMM Y'),
                'is_demo' => (bool) $a->is_demo,
            ]),
            'safetyGuides' => $data['safetyGuides'],
            'publishedGuides' => ($data['publishedGuides'] ?? collect())->map(fn ($g) => [
                'id' => $g->id,
                'title' => $g->title,
                'slug' => $g->slug,
                'summary' => $g->summary,
                'category' => $g->category,
                'category_label' => $g->categoryLabel(),
                'category_icon' => $g->categoryIcon(),
                'reading_time_minutes' => $g->reading_time_minutes,
                'published_at' => $g->published_at?->toIso8601String(),
            ]),
            'latestAirQuality' => $data['latestAirQuality'] ? [
                'station_name' => $data['latestAirQuality']->station?->name ?? 'Stasiun Pemantau',
                'province_name' => $data['latestAirQuality']->station?->province?->name ?? 'Kalimantan',
                'value' => (float) $data['latestAirQuality']->value,
                'unit' => $data['latestAirQuality']->unit ?? 'µg/m³',
                'category' => $data['latestAirQuality']->aqi_category ?? 'ISPU',
                'measured_at' => $data['latestAirQuality']->measured_at?->diffForHumans() ?? 'Baru saja',
            ] : null,
        ]);
    }

    /**
     * Ringkasan JSON untuk pemilih wilayah (fetch JS).
     */
    public function summary(Request $request): JsonResponse
    {
        $slug = $request->query('provinsi');
        $provinceId = $this->homeService->resolveProvinceId(is_string($slug) ? $slug : null);
        $data = $this->homeService->getHomeData($provinceId);

        return response()->json([
            'activeProvince' => $data['activeProvince']?->only(['id', 'name', 'slug']),
            'summary' => [
                'total_7d' => $data['summary']['total_7d'],
                'last_24h' => $data['summary']['last_24h'],
                'high_confidence' => $data['summary']['high_confidence'],
                'last_detected_at' => $data['summary']['last_detected_at']?->toIso8601String(),
            ],
            'provinces' => $data['provinces']->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'hotspots_7d_count' => $p->hotspots_7d_count,
            ]),
            'updated_at' => now()->toIso8601String(),
        ]);
    }
}

