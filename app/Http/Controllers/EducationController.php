<?php

namespace App\Http\Controllers;

use App\Models\EducationArticle;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    /**
     * Halaman publik: Daftar Artikel Edukasi Karhutla.
     *
     * Menampilkan materi literasi kebakaran hutan & lahan gambut,
     * filter kategori, pencarian, dan penanganan gambar cover.
     */
    public function index(Request $request): View
    {
        $categorySlug = trim((string) $request->query('kategori', ''));
        $search = trim((string) $request->query('q', ''));

        $activeCategory = array_key_exists($categorySlug, EducationArticle::CATEGORIES) ? $categorySlug : null;

        $query = EducationArticle::query()
            ->published()
            ->category($activeCategory)
            ->search($search)
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        $articles = $query->paginate(9)->withQueryString();

        $categoryCounts = EducationArticle::query()
            ->published()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $totalCount = EducationArticle::query()->published()->count();

        // Artikel unggulan (featured/lead) jika tidak ada filter/pencarian
        $featuredArticle = ($activeCategory === null && $search === '' && $articles->currentPage() === 1)
            ? $articles->first()
            : null;

        return view('edukasi.index', [
            'articles' => $articles,
            'featuredArticle' => $featuredArticle,
            'categories' => EducationArticle::CATEGORIES,
            'activeCategory' => $activeCategory,
            'search' => $search,
            'categoryCounts' => $categoryCounts,
            'totalCount' => $totalCount,
        ]);
    }

    /**
     * Halaman publik: Detail Artikel Edukasi Karhutla.
     */
    public function show(string $slug): View
    {
        $article = EducationArticle::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedArticles = EducationArticle::query()
            ->published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        if ($relatedArticles->count() < 3) {
            $filler = EducationArticle::query()
                ->published()
                ->where('id', '!=', $article->id)
                ->whereNotIn('id', $relatedArticles->pluck('id'))
                ->orderByDesc('published_at')
                ->limit(3 - $relatedArticles->count())
                ->get();

            $relatedArticles = $relatedArticles->concat($filler);
        }

        return view('edukasi.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'categoryMeta' => EducationArticle::CATEGORIES[$article->category] ?? null,
        ]);
    }
}
