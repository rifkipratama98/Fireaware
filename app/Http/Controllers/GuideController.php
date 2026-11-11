<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class GuideController extends Controller
{
    /**
     * Halaman publik: Daftar Panduan Keselamatan Asap & Mitigasi Karhutla Kalimantan.
     *
     * Menampilkan hanya panduan yang berstatus published, dilengkapi filter
     * kategori, pencarian, sort, dan layout editorial interaktif.
     */
    public function index(Request $request): Response
    {
        $categorySlug = trim((string) $request->query('kategori', ''));
        $search = trim((string) $request->query('q', ''));
        $sort = trim((string) $request->query('sort', 'terbaru'));

        // Validasi parameter kategori
        $activeCategory = array_key_exists($categorySlug, Guide::CATEGORIES) ? $categorySlug : null;

        $query = Guide::query()
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

        $guides = $query->paginate(8)->withQueryString();

        $transformedGuides = $guides->through(function (Guide $guide) {
            return $this->formatGuide($guide);
        });

        // Hitung panduan aktif per kategori untuk label filter
        $categoryCounts = Guide::query()
            ->published()
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $totalPublishedCount = Guide::query()->published()->count();

        // Susun daftar kategori dengan metadata & jumlah
        $categoriesList = [
            [
                'key' => 'semua',
                'name' => 'Semua Topik',
                'description' => 'Seluruh protokol keselamatan, mitigasi kabut asap, dan pelaporan darurat karhutla.',
                'icon' => 'ShieldCheck',
                'count' => $totalPublishedCount,
            ],
        ];

        foreach (Guide::CATEGORIES as $key => $meta) {
            $categoriesList[] = [
                'key' => $key,
                'name' => $meta['name'],
                'description' => $meta['description'],
                'icon' => $meta['icon'],
                'count' => $categoryCounts[$key] ?? 0,
            ];
        }

        // Panduan unggulan (Featured/Lead) hanya saat tanpa filter & di halaman pertama
        $featuredGuide = null;
        if ($activeCategory === null && $search === '' && $guides->currentPage() === 1 && $guides->isNotEmpty()) {
            $featuredGuide = $transformedGuides->first();
        }

        return Inertia::render('Public/Guides/Index', [
            'guides' => $transformedGuides,
            'featuredGuide' => $featuredGuide,
            'categories' => $categoriesList,
            'rawCategories' => Guide::CATEGORIES,
            'activeCategory' => $activeCategory,
            'search' => $search,
            'sort' => $sort,
            'categoryCounts' => $categoryCounts,
            'totalPublishedCount' => $totalPublishedCount,
            'empty_message' => 'Tidak Ada Panduan yang Ditemukan',
        ]);
    }

    /**
     * Halaman publik: Detail Panduan Keselamatan.
     *
     * Hanya menampilkan panduan berstatus published dengan URL berbasis slug.
     * Draft panduan mengembalikan respons 404.
     */
    public function show(string $slug): Response
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

        $transformedRelated = $relatedGuides->map(fn (Guide $g) => $this->formatGuide($g));

        return Inertia::render('Public/Guides/Show', [
            'guide' => $this->formatGuideDetail($guide),
            'relatedGuides' => $transformedRelated,
            'categoryMeta' => Guide::CATEGORIES[$guide->category] ?? null,
            'back_label' => 'Kembali ke Panduan',
        ]);
    }

    /**
     * Format ringkas panduan untuk kartu katalog.
     */
    protected function formatGuide(Guide $guide): array
    {
        return [
            'id' => $guide->id,
            'title' => $guide->title,
            'slug' => $guide->slug,
            'category' => $guide->category,
            'category_label' => $guide->categoryLabel(),
            'category_icon' => $guide->categoryIcon(),
            'category_description' => $guide->categoryDescription(),
            'summary' => $guide->summary,
            'source_name' => $guide->source_name,
            'source_url' => $guide->source_url,
            'reading_time_minutes' => $guide->reading_time_minutes ?: 4,
            'thumbnail_image' => $guide->thumbnailImage(),
            'thumbnail_alt' => $guide->thumbnailAlt(),
            'published_date_formatted' => $this->formatDate($guide->effectiveDate()),
        ];
    }

    /**
     * Format komprehensif panduan untuk halaman detail.
     */
    protected function formatGuideDetail(Guide $guide): array
    {
        $data = $this->formatGuide($guide);
        $data['content'] = $guide->content;
        $data['source_notes'] = $guide->source_notes;
        $data['updated_date_formatted'] = $guide->updated_at ? $this->formatDate($guide->updated_at) : null;

        // Ekstraksi paragraf
        $paragraphs = $guide->contentParagraphs();
        $data['paragraphs'] = $paragraphs;

        // Ekstraksi heading untuk daftar isi (TOC)
        $headings = [];
        foreach ($paragraphs as $idx => $p) {
            if (Str::startsWith($p, '### ') || Str::startsWith($p, '## ')) {
                $hText = Str::startsWith($p, '### ') ? Str::after($p, '### ') : Str::after($p, '## ');
                $headings[] = [
                    'id' => 'bagian-' . ($idx + 1),
                    'text' => trim($hText),
                    'level' => Str::startsWith($p, '## ') ? 2 : 3,
                ];
            }
        }
        $data['headings'] = $headings;

        return $data;
    }

    /**
     * Format tanggal ke standar Indonesia yang mudah dibaca.
     */
    protected function formatDate(?Carbon $date): string
    {
        if (! $date) {
            return '—';
        }

        return $date->locale('id')->translatedFormat('d F Y');
    }
}
