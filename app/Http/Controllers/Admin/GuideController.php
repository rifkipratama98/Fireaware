<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuideRequest;
use App\Models\Guide;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuideController extends Controller
{
    /**
     * Tampilkan daftar seluruh panduan keselamatan untuk dikelola admin.
     */
    public function index(Request $request): View
    {
        $category = trim((string) $request->query('kategori', ''));
        $status = trim((string) $request->query('status', ''));
        $search = trim((string) $request->query('q', ''));

        $query = Guide::query();

        if ($category !== '' && array_key_exists($category, Guide::CATEGORIES)) {
            $query->where('category', $category);
        }

        if ($status !== '' && in_array($status, [Guide::STATUS_PUBLISHED, Guide::STATUS_DRAFT], true)) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('source_name', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        $guides = $query->orderByDesc('id')->paginate(12)->withQueryString();

        $stats = [
            'total' => Guide::count(),
            'published' => Guide::where('status', Guide::STATUS_PUBLISHED)->count(),
            'draft' => Guide::where('status', Guide::STATUS_DRAFT)->count(),
        ];

        return view('admin.guides.index', [
            'guides' => $guides,
            'categories' => Guide::CATEGORIES,
            'stats' => $stats,
            'filterCategory' => $category,
            'filterStatus' => $status,
            'search' => $search,
        ]);
    }

    /**
     * Tampilkan formulir tambah panduan keselamatan baru.
     */
    public function create(): View
    {
        $guide = new Guide([
            'status' => Guide::STATUS_PUBLISHED,
            'reading_time_minutes' => 3,
            'published_at' => now(),
        ]);

        return view('admin.guides.create', [
            'guide' => $guide,
            'categories' => Guide::CATEGORIES,
            'isEdit' => false,
        ]);
    }

    /**
     * Simpan panduan keselamatan baru ke basis data.
     */
    public function store(GuideRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Pastikan slug unik
        $baseSlug = $validated['slug'];
        $counter = 1;
        while (Guide::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = "{$baseSlug}-{$counter}";
            $counter++;
        }

        if (empty($validated['reading_time_minutes'])) {
            $wordCount = str_word_count(strip_tags((string) $validated['content']));
            $validated['reading_time_minutes'] = max(1, (int) ceil($wordCount / 180));
        }

        $guide = Guide::create($validated);

        return redirect()->route('admin.guides.index')
            ->with('success', 'Panduan keselamatan <strong>"' . e($guide->title) . '"</strong> berhasil ditambahkan.');
    }

    /**
     * Tampilkan rincian panduan keselamatan untuk ditinjau admin.
     */
    public function show(Guide $guide): View
    {
        return view('admin.guides.show', [
            'guide' => $guide,
            'categories' => Guide::CATEGORIES,
        ]);
    }

    /**
     * Tampilkan formulir edit panduan keselamatan.
     */
    public function edit(Guide $guide): View
    {
        return view('admin.guides.edit', [
            'guide' => $guide,
            'categories' => Guide::CATEGORIES,
            'isEdit' => true,
        ]);
    }

    /**
     * Perbarui data panduan keselamatan.
     */
    public function update(GuideRequest $request, Guide $guide): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Pastikan slug unik kecuali untuk dirinya sendiri
        $baseSlug = $validated['slug'];
        $counter = 1;
        while (Guide::where('slug', $validated['slug'])->where('id', '!=', $guide->id)->exists()) {
            $validated['slug'] = "{$baseSlug}-{$counter}";
            $counter++;
        }

        if (empty($validated['reading_time_minutes'])) {
            $wordCount = str_word_count(strip_tags((string) $validated['content']));
            $validated['reading_time_minutes'] = max(1, (int) ceil($wordCount / 180));
        }

        $guide->update($validated);

        return redirect()->route('admin.guides.index')
            ->with('success', 'Panduan keselamatan <strong>"' . e($guide->title) . '"</strong> berhasil diperbarui.');
    }

    /**
     * Hapus panduan keselamatan dari basis data.
     */
    public function destroy(Guide $guide): RedirectResponse
    {
        $title = $guide->title;
        $guide->delete();

        return redirect()->route('admin.guides.index')
            ->with('success', 'Panduan keselamatan <strong>"' . e($title) . '"</strong> telah berhasil dihapus.');
    }
}
