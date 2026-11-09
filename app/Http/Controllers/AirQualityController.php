<?php

namespace App\Http\Controllers;

use App\Models\AirQualityReading;
use App\Models\AirQualityStation;
use App\Models\Province;
use App\Services\AirQuality\AirQualityCategory;
use App\Services\AirQuality\AirQualityService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

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
    public function index(Request $request): Response
    {
        $provinces = Province::query()->orderBy('name')->get(['id', 'name', 'slug', 'code', 'center_lat', 'center_lng']);

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
        $historyStats = null;
        $category = null;

        if ($activeStation) {
            $result = $this->airQuality->latestForStation($activeStation);
            $latestReading = $result['reading'];
            $history = $this->airQuality->historyForStation($activeStation);

            if ($history['available'] && count($history['points']) > 0) {
                $values = array_column($history['points'], 'v');
                $historyStats = [
                    'min' => (float) min($values),
                    'max' => (float) max($values),
                    'avg' => (float) round(array_sum($values) / count($values), 1),
                    'count' => count($values),
                ];
            }

            if ($latestReading) {
                $categoryBase = AirQualityCategory::forPm25((float) $latestReading->value);
                $label = !empty($latestReading->aqi_category) ? $latestReading->aqi_category : $categoryBase['label'];
                $categoryDetails = AirQualityCategory::categoryDetails($label) ?? [];
                $category = array_merge($categoryBase, $categoryDetails, [
                    'label' => $label,
                ]);

                // Hitung nilai ISPU indikatif menggunakan formula resmi Lampiran I Permen LHK 14/2020
                $calculatedIspu = AirQualityCategory::calculateIspu((float) $latestReading->value);

                $latest = [
                    'id' => $latestReading->id,
                    'station_name' => $activeStation->name,
                    'province_name' => $activeStation->province?->name ?? 'Kalimantan',
                    'pollutant' => $latestReading->pollutant,
                    'value' => (float) $latestReading->value,
                    'unit' => $latestReading->unit ?? 'µg/m³',
                    'aqi_value' => $latestReading->aqi_value ?? $calculatedIspu,
                    'aqi_category' => $latestReading->aqi_category ?? $categoryBase['label'],
                    'standard' => $latestReading->standard ?? 'Permen LHK No. 14/2020',
                    'measured_at' => $latestReading->measured_at?->toIso8601String(),
                    'fetched_at' => $latestReading->fetched_at?->toIso8601String(),
                    'source' => $latestReading->source ?? 'openaq',
                    'averaging_period' => 'Observasi 1 Jam (Hourly Sensor)',
                    'is_official_ispu' => false, // Sensor seketika OpenAQ bukan stasiun ISPU 24-jam terakreditasi KLHK
                ];
            }
        }

        // Ringkasan 5 provinsi Kalimantan secara faktual
        $regionalSummary = $provinces->map(function ($prov) {
            $station = AirQualityStation::query()
                ->where('province_id', $prov->id)
                ->where('is_active', true)
                ->first();

            $reading = null;
            $cat = null;
            if ($station) {
                $reading = $station->latestReading('pm25');
                if ($reading) {
                    $cat = AirQualityCategory::forPm25((float) $reading->value);
                }
            }

            return [
                'province_id' => $prov->id,
                'province_name' => $prov->name,
                'province_slug' => $prov->slug,
                'province_code' => $prov->code,
                'station_name' => $station?->name,
                'station_slug' => $station?->slug,
                'has_station' => (bool) $station,
                'has_data' => (bool) $reading,
                'pm25' => $reading ? (float) $reading->value : null,
                'unit' => $reading?->unit ?? 'µg/m³',
                'category' => $cat ? $cat['label'] : null,
                'category_color' => $cat ? $cat['color'] : null,
                'measured_at' => $reading?->measured_at?->toIso8601String(),
            ];
        });

        // Waktu pembaruan data terakhir di sistem
        $lastSyncedAt = AirQualityReading::query()->max('fetched_at')
            ?? AirQualityReading::query()->max('measured_at');

        return Inertia::render('Public/AirQuality', [
            'provinces' => $provinces,
            'activeProvince' => $activeProvince,
            'stations' => $stations->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'slug' => $s->slug,
                'source' => $s->source,
                'latitude' => $s->latitude ? (float) $s->latitude : null,
                'longitude' => $s->longitude ? (float) $s->longitude : null,
                'province_id' => $s->province_id,
                'province' => $s->province ? [
                    'id' => $s->province->id,
                    'name' => $s->province->name,
                    'slug' => $s->province->slug,
                ] : null,
            ]),
            'activeStation' => $activeStation ? [
                'id' => $activeStation->id,
                'name' => $activeStation->name,
                'slug' => $activeStation->slug,
                'source' => $activeStation->source,
                'latitude' => $activeStation->latitude ? (float) $activeStation->latitude : null,
                'longitude' => $activeStation->longitude ? (float) $activeStation->longitude : null,
                'province_id' => $activeStation->province_id,
                'province' => $activeStation->province ? [
                    'id' => $activeStation->province->id,
                    'name' => $activeStation->province->name,
                    'slug' => $activeStation->province->slug,
                ] : null,
            ] : null,
            'latest' => $latest,
            'history' => $history,
            'historyStats' => $historyStats,
            'category' => $category,
            'categoryChip' => $category ? AirQualityCategory::chipClass($category['label']) : null,
            'allCategories' => AirQualityCategory::allCategories(),
            'regionalSummary' => $regionalSummary,
            'isConfigured' => $this->airQuality->isConfigured(),
            'dataSourceName' => 'OpenAQ',
            'dataSourceUrl' => 'https://openaq.org/',
            'lastSyncedAt' => $lastSyncedAt ? Carbon::parse($lastSyncedAt)->toIso8601String() : null,
        ]);
    }
}
