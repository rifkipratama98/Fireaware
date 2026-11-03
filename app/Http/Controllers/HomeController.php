<?php

namespace App\Http\Controllers;

use App\Services\HomeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(private HomeService $homeService)
    {
    }

    /**
     * Halaman utama publik dengan filter provinsi opsional (?provinsi=slug).
     */
    public function index(Request $request): View
    {
        $slug = $request->query('provinsi');
        $provinceId = $this->homeService->resolveProvinceId(is_string($slug) ? $slug : null);
        $data = $this->homeService->getHomeData($provinceId);

        return view('home', [
            'provinces' => $data['provinces'],
            'activeProvince' => $data['activeProvince'],
            'summary' => $data['summary'],
            'hotspots' => $data['hotspots'],
            'mapHotspots' => $data['mapHotspots'],
            'announcements' => $data['announcements'],
            'safetyGuides' => $data['safetyGuides'],
            'publishedGuides' => $data['publishedGuides'] ?? collect(),
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

