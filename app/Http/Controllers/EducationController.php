<?php

namespace App\Http\Controllers;

use App\Models\EducationArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class EducationController extends Controller
{
    /**
     * Halaman publik: Daftar Artikel & Pusat Literasi Lingkungan Karhutla.
     *
     * Menyajikan kurasi edukasi penyebab, dampak, restorasi gambut, dan hukum,
     * dilengkapi filter kategori, pencarian, dan artikel unggulan.
     */
    public function index(Request $request): Response
    {
        $categorySlug = trim((string) $request->query('kategori', ''));
        $search = trim((string) $request->query('q', ''));
        $sort = trim((string) $request->query('sort', 'terbaru'));

        $activeCategory = array_key_exists($categorySlug, EducationArticle::CATEGORIES) ? $categorySlug : null;

        $query = EducationArticle::query()
            ->published()
            ->category($activeCategory)
            ->search($search);

        if ($sort === 'terlama') {
            $query->orderBy('published_at', 'asc')->orderBy('id', 'asc');
        } elseif ($sort === 'populer') {
            $query->orderByDesc('reading_time_minutes')->orderByDesc('published_at');
        } else {
            $query->orderByDesc('published_at')->orderByDesc('id');
        }

        $paginated = $query->paginate(9)->withQueryString();

        $transformedArticles = $paginated->through(function (EducationArticle $article) {
            return $this->formatArticle($article);
        });

        // Hitung statistik kategori untuk rilis published
        $categoryCounts = EducationArticle::query()
            ->published()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $totalCount = EducationArticle::query()->published()->count();

        // Susun daftar kategori dengan metadata & jumlah
        $categoriesList = [
            [
                'key' => 'semua',
                'name' => 'Semua Edukasi',
                'description' => 'Seluruh modul literasi kebakaran hutan, lahan gambut, dan restorasi lingkungan.',
                'icon' => 'BookOpen',
                'count' => $totalCount,
            ],
        ];

        foreach (EducationArticle::CATEGORIES as $key => $meta) {
            $categoriesList[] = [
                'key' => $key,
                'name' => $meta['name'],
                'description' => $meta['description'],
                'icon' => $meta['icon'],
                'count' => $categoryCounts[$key] ?? 0,
            ];
        }

        // Artikel unggulan (Featured/Lead) hanya saat tanpa filter & di halaman pertama
        $featuredArticle = null;
        if ($activeCategory === null && $search === '' && $paginated->currentPage() === 1 && $paginated->isNotEmpty()) {
            $featuredArticle = $transformedArticles->first();
        }

        return Inertia::render('Public/Education/Index', [
            'articles' => $transformedArticles,
            'featuredArticle' => $featuredArticle,
            'categories' => $categoriesList,
            'rawCategories' => EducationArticle::CATEGORIES,
            'activeCategory' => $activeCategory,
            'search' => $search,
            'sort' => $sort,
            'categoryCounts' => $categoryCounts,
            'totalCount' => $totalCount,
        ]);
    }

    /**
     * Halaman publik: Detail Artikel Edukasi Lingkungan.
     */
    public function show(string $slug): Response
    {
        $article = EducationArticle::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Cari artikel terkait dalam kategori yang sama
        $relatedArticles = EducationArticle::query()
            ->published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        // Jika kurang dari 3, ambil dari kategori lain
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

        $formattedRelated = $relatedArticles->map(fn (EducationArticle $item) => $this->formatArticle($item));

        return Inertia::render('Public/Education/Show', [
            'article' => $this->formatArticle($article, includeContent: true),
            'relatedArticles' => $formattedRelated,
            'categoryMeta' => EducationArticle::CATEGORIES[$article->category] ?? null,
            'back_label' => 'Kembali ke Edukasi',
        ]);
    }

    /**
     * Format data artikel untuk frontend React.
     */
    private function formatArticle(EducationArticle $article, bool $includeContent = false): array
    {
        $publishedAt = $article->published_at ?? $article->created_at ?? now();
        $carbonDate = Carbon::parse($publishedAt)->setTimezone('Asia/Makassar');

        $categoryMeta = EducationArticle::CATEGORIES[$article->category] ?? [
            'name' => ucfirst(str_replace('-', ' ', (string) $article->category)),
            'description' => '',
            'icon' => 'BookOpen',
        ];

        // Resolusi gambar artikel atau fallback tematik
        $imageUrl = $article->displayImageUrl();
        $imageCaption = $article->displayImageAlt();

        $data = [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'category' => $article->category,
            'category_label' => $categoryMeta['name'],
            'category_description' => $categoryMeta['description'] ?? '',
            'category_icon' => $categoryMeta['icon'] ?? 'BookOpen',
            'summary' => $article->summary,
            'author' => $article->author ?: 'Tim Riset Ekologi FireAware',
            'source_name' => $article->source_name,
            'source_url' => $article->source_url,
            'reading_time_minutes' => $article->reading_time_minutes ?: 4,
            'image_url' => $imageUrl,
            'image_caption' => $imageCaption,
            'is_demo' => (bool) $article->is_demo,
            'published_at' => $carbonDate->toIso8601String(),
            'published_date_formatted' => $carbonDate->translatedFormat('d F Y'),
            'published_time_formatted' => $carbonDate->translatedFormat('H:i') . ' WITA',
            'published_relative' => $carbonDate->diffForHumans(),
        ];

        if ($includeContent) {
            $data['content'] = $article->content;
            $data['paragraphs'] = $article->contentParagraphs();

            // Ekstrak heading untuk Daftar Isi (TOC)
            preg_match_all('/^###?\s+(.+)$/m', (string) $article->content, $matches);
            $headings = [];
            if (!empty($matches[1])) {
                foreach ($matches[1] as $idx => $heading) {
                    $cleanHeading = trim($heading);
                    $headings[] = [
                        'id' => 'section-' . ($idx + 1),
                        'text' => $cleanHeading,
                    ];
                }
            }
            $data['headings'] = $headings;
        }

        return $data;
    }
}
