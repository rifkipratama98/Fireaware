<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AirQualityStation;
use App\Models\Announcement;
use App\Models\Hotspot;
use App\Models\Province;
use Illuminate\View\View;

class RegionController extends Controller
{
    /**
     * Tampilkan data wilayah 5 provinsi di Kalimantan beserta ringkasan metrik.
     */
    public function index(): View
    {
        $provinces = Province::query()
            ->withCount([
                'hotspots as hotspots_7d_count' => fn ($q) => $q->where('detected_at', '>=', now()->subDays(7)),
                'hotspots as hotspots_total_count',
            ])
            ->orderBy('name')
            ->get();

        // Ambil jumlah stasiun per provinsi
        $stationCounts = AirQualityStation::query()
            ->selectRaw('province_id, COUNT(*) as total')
            ->where('is_active', true)
            ->groupBy('province_id')
            ->pluck('total', 'province_id');

        // Ambil jumlah pengumuman aktif per provinsi
        $announcementCounts = Announcement::query()
            ->selectRaw('province_id, COUNT(*) as total')
            ->where('is_active', true)
            ->whereNotNull('province_id')
            ->groupBy('province_id')
            ->pluck('total', 'province_id');

        $provinces->transform(function ($prov) use ($stationCounts, $announcementCounts) {
            $prov->stations_count = $stationCounts->get($prov->id, 0);
            $prov->active_announcements_count = $announcementCounts->get($prov->id, 0);
            return $prov;
        });

        $totals = [
            'provinces'     => $provinces->count(),
            'hotspots_7d'   => $provinces->sum('hotspots_7d_count'),
            'hotspots_total'=> $provinces->sum('hotspots_total_count'),
            'stations'      => $stationCounts->sum(),
        ];

        return view('admin.regions.index', compact('provinces', 'totals'));
    }
}
