<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementRequest;
use App\Models\Announcement;
use App\Models\Province;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    /**
     * Tampilkan daftar pengumuman di panel admin.
     */
    public function index(Request $request): View
    {
        $provinceId = $request->query('provinsi_id');
        $level = $request->query('level');
        $status = trim((string) $request->query('status', ''));
        $search = trim((string) $request->query('q', ''));

        $query = Announcement::query()->with('province');

        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        if ($level && array_key_exists((int) $level, Announcement::LEVELS)) {
            $query->where('level', (int) $level);
        }

        if ($status !== '' && in_array($status, [Announcement::STATUS_PUBLISHED, Announcement::STATUS_DRAFT], true)) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('source_name', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $announcements = $query->orderByDesc('id')->paginate(12)->withQueryString();
        $provinces = Province::query()->orderBy('name')->get();

        $stats = [
            'total' => Announcement::count(),
            'published' => Announcement::where('status', Announcement::STATUS_PUBLISHED)->count(),
            'draft' => Announcement::where('status', Announcement::STATUS_DRAFT)->count(),
        ];

        return view('admin.announcements.index', [
            'announcements' => $announcements,
            'provinces' => $provinces,
            'levels' => Announcement::LEVELS,
            'stats' => $stats,
            'filterProvince' => $provinceId,
            'filterLevel' => $level,
            'filterStatus' => $status,
            'search' => $search,
        ]);
    }

    /**
     * Formulir tambah pengumuman baru.
     */
    public function create(): View
    {
        $announcement = new Announcement([
            'status' => Announcement::STATUS_PUBLISHED,
            'level' => Announcement::LEVEL_INFO,
            'is_demo' => false,
            'published_at' => now(),
        ]);

        return view('admin.announcements.create', [
            'announcement' => $announcement,
            'provinces' => Province::query()->orderBy('name')->get(),
            'levels' => Announcement::LEVELS,
            'isEdit' => false,
        ]);
    }

    /**
     * Simpan pengumuman baru ke basis data.
     */
    public function store(AnnouncementRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $baseSlug = $validated['slug'];
        $counter = 1;
        while (Announcement::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $validated['is_demo'] = $request->boolean('is_demo');
        $validated['is_active'] = ($validated['status'] === Announcement::STATUS_PUBLISHED);

        $announcement = Announcement::create($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman <strong>"' . e($announcement->title) . '"</strong> berhasil ditambahkan.');
    }

    /**
     * Formulir edit pengumuman.
     */
    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.edit', [
            'announcement' => $announcement,
            'provinces' => Province::query()->orderBy('name')->get(),
            'levels' => Announcement::LEVELS,
            'isEdit' => true,
        ]);
    }

    /**
     * Perbarui data pengumuman.
     */
    public function update(AnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $baseSlug = $validated['slug'];
        $counter = 1;
        while (Announcement::where('slug', $validated['slug'])->where('id', '!=', $announcement->id)->exists()) {
            $validated['slug'] = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $validated['is_demo'] = $request->boolean('is_demo');
        $validated['is_active'] = ($validated['status'] === Announcement::STATUS_PUBLISHED);

        $announcement->update($validated);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman <strong>"' . e($announcement->title) . '"</strong> berhasil diperbarui.');
    }

    /**
     * Hapus pengumuman dari basis data.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        $title = $announcement->title;
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Pengumuman <strong>"' . e($title) . '"</strong> telah berhasil dihapus.');
    }
}
