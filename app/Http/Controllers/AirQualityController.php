<?php

namespace App\Http\Controllers;

use App\Models\AirQualityStation;
use App\Models\Province;
use App\Services\AirQuality\AirQualityCategory;
use App\Services\AirQuality\AirQualityService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AirQualityController extends Controller
{
    public function __construct(private AirQualityService $airQuality)
    {
    }

    /**
     * Halaman Kualitas Udara.
     *
     * Tidak pernah menampilkan nilai yang tidak berasal dari sumber data.
     * Bila belum ada data, halaman menampilkan empty state yang jujur.
     */
    public function index(Request $request): View
    {
        $provinces = Province::query()->orderBy('name')->get(['id', 'name', 'slug']);

        // Pilihan provinsi (opsional) — divalidasi terhadap daftar yang ada
        $provinceSlug = trim((string) $request->query('provinsi', ''));
        $activeProvince = $provinceSlug !== ''
            ? $provinces->firstWhere('slug', $provinceSlug)
            : null;

        $stations = AirQualityStation::query()
            ->when($activeProvince, fn ($q) => $q->where('province_id', $activeProvince->id))
            ->where('is_active', true)
            ->with('province:id,name,slug')
            ->orderBy('name')
            ->get();

        // Stasiun terpilih: dari query (?stasiun=slug) atau yang pertama tersedia
        $stationSlug = trim((string) $request->query('stasiun', ''));
        $activeStation = $stationSlug !== ''
            ? $stations->firstWhere('slug', $stationSlug)
            : $stations->first();

        $latest = null;
        $history = ['available' => false, 'points' => [], 'message' => null];
        $category = null;

        if ($activeStation) {
            $result = $this->airQuality->latestForStation($activeStation);
            $latest = $result['reading'];
            $history = $this->airQuality->historyForStation($activeStation);

            if ($latest) {
                $category = AirQualityCategory::forPm25((float) $latest->value);
            }
        }

        return view('kualitas-udara', [
            'provinces' => $provinces,
            'activeProvince' => $activeProvince,
            'stations' => $stations,
            'activeStation' => $activeStation,
            'latest' => $latest,
            'history' => $history,
            'category' => $category,
            'categoryChip' => $category ? AirQualityCategory::chipClass($category['label']) : null,
            'isConfigured' => $this->airQuality->isConfigured(),
            'dataSourceName' => 'OpenAQ',
            'dataSourceUrl' => 'https://openaq.org/',
        ]);
    }
}
