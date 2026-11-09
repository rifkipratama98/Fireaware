<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AirQualityStation;
use App\Models\Announcement;
use App\Models\Hotspot;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            'announcements' => $announcementCounts->sum(),
        ];

        return view('admin.regions.index', compact('provinces', 'totals'));
    }

    /**
     * Simpan data wilayah baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100', 'unique:provinces,name'],
            'slug'       => ['nullable', 'string', 'max:100', 'unique:provinces,slug'],
            'code'       => ['nullable', 'string', 'max:10'],
            'center_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'center_lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Province::create($validated);

        return redirect()->route('admin.regions.index')
            ->with('success', "Wilayah <strong>{$validated['name']}</strong> berhasil ditambahkan ke sistem.");
    }

    /**
     * Perbarui koordinat dan metadata wilayah.
     */
    public function update(Request $request, Province $province): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100', 'unique:provinces,name,' . $province->id],
            'code'       => ['nullable', 'string', 'max:10'],
            'center_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'center_lng' => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $province->update($validated);

        return redirect()->route('admin.regions.index')
            ->with('success', "Data koordinat dan informasi wilayah <strong>{$province->name}</strong> berhasil diperbarui.");
    }
}

