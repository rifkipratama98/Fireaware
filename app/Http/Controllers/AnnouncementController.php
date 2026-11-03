<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Province;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Halaman publik: Daftar Pengumuman & Peringatan Dini.
     *
     * Berisi aliran pengumuman resmi berkredibilitas tinggi,
     * filter wilayah provinsi, level kewaspadaan, dan pencarian.
     */
    public function index(Request $request): View
    {
        $provinceSlug = trim((string) $request->query('provinsi', ''));
        $level = $request->query('level');
        $search = trim((string) $request->query('q', ''));

        $provinces = Province::query()->orderBy('name')->get();
        $activeProvince = $provinceSlug !== '' ? $provinces->firstWhere('slug', $provinceSlug) : null;

        $levelInt = is_numeric($level) && array_key_exists((int) $level, Announcement::LEVELS)
            ? (int) $level
            : null;

        $query = Announcement::query()
            ->published()
            ->with('province')
            ->when($activeProvince, fn ($q) => $q->where('province_id', $activeProvince->id))
            ->level($levelInt)
            ->search($search)
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        $announcements = $query->paginate(8)->withQueryString();

        $stats = [
            'total' => Announcement::published()->count(),
            'info' => Announcement::published()->where('level', Announcement::LEVEL_INFO)->count(),
            'waspada' => Announcement::published()->where('level', Announcement::LEVEL_WASPADA)->count(),
            'darurat' => Announcement::published()->where('level', Announcement::LEVEL_DARURAT)->count(),
        ];

        return view('announcements.index', [
            'announcements' => $announcements,
            'provinces' => $provinces,
            'activeProvince' => $activeProvince,
            'activeLevel' => $levelInt,
            'search' => $search,
            'levels' => Announcement::LEVELS,
            'stats' => $stats,
        ]);
    }

    /**
     * Halaman publik: Detail Pengumuman Resmi.
     */
    public function show(string $slug): View
    {
        $announcement = Announcement::query()
            ->published()
            ->with('province')
            ->where('slug', $slug)
            ->firstOrFail();

        $recentAnnouncements = Announcement::query()
            ->published()
            ->with('province')
            ->where('id', '!=', $announcement->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('announcements.show', [
            'announcement' => $announcement,
            'recentAnnouncements' => $recentAnnouncements,
        ]);
    }
}
