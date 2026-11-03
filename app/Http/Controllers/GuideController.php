<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    /**
     * Halaman publik: Daftar Panduan Keselamatan.
     *
     * Menampilkan hanya panduan yang berstatus published, dilengkapi filter
     * kategori, pencarian judul/ringkasan, dan layout editorial yang nyaman dibaca.
     */
    public function index(Request $request): View
    {
        $categorySlug = trim((string) $request->query('kategori', ''));
        $search = trim((string) $request->query('q', ''));

        // Validasi parameter kategori
        $activeCategory = array_key_exists($categorySlug, Guide::CATEGORIES) ? $categorySlug : null;

        $query = Guide::query()
            ->published()
            ->category($activeCategory)
            ->search($search)
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        $guides = $query->paginate(8)->withQueryString();

        // Hitung panduan aktif per kategori untuk label filter
        $categoryCounts = Guide::query()
            ->published()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $totalPublishedCount = Guide::query()->published()->count();

        return view('guides.index', [
            'guides' => $guides,
            'categories' => Guide::CATEGORIES,
            'activeCategory' => $activeCategory,
            'search' => $search,
            'categoryCounts' => $categoryCounts,
            'totalPublishedCount' => $totalPublishedCount,
        ]);
    }

    /**
     * Halaman publik: Detail Panduan Keselamatan.
     *
     * Hanya menampilkan panduan berstatus published dengan URL berbasis slug.
     */
    public function show(string $slug): View
    {
        $guide = Guide::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Panduan terkait dalam kategori yang sama
        $relatedGuides = Guide::query()
            ->published()
            ->where('id', '!=', $guide->id)
            ->where('category', $guide->category)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        // Jika panduan sejenis kurang dari 3, tambahkan artikel published lainnya
        if ($relatedGuides->count() < 3) {
            $filler = Guide::query()
                ->published()
                ->where('id', '!=', $guide->id)
                ->whereNotIn('id', $relatedGuides->pluck('id'))
                ->orderByDesc('published_at')
                ->limit(3 - $relatedGuides->count())
                ->get();

            $relatedGuides = $relatedGuides->concat($filler);
        }

        return view('guides.show', [
            'guide' => $guide,
            'relatedGuides' => $relatedGuides,
            'categoryMeta' => Guide::CATEGORIES[$guide->category] ?? null,
        ]);
    }
}
