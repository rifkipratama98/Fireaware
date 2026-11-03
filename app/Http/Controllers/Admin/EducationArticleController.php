<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EducationArticleRequest;
use App\Models\EducationArticle;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EducationArticleController extends Controller
{
    /**
     * Tampilkan daftar artikel edukasi di panel admin.
     */
    public function index(Request $request): View
    {
        $category = trim((string) $request->query('kategori', ''));
        $status = trim((string) $request->query('status', ''));
        $search = trim((string) $request->query('q', ''));

        $query = EducationArticle::query();

        if ($category !== '' && array_key_exists($category, EducationArticle::CATEGORIES)) {
            $query->where('category', $category);
        }

        if ($status !== '' && in_array($status, [EducationArticle::STATUS_PUBLISHED, EducationArticle::STATUS_DRAFT], true)) {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        $articles = $query->orderByDesc('id')->paginate(12)->withQueryString();

        $stats = [
            'total' => EducationArticle::count(),
            'published' => EducationArticle::where('status', EducationArticle::STATUS_PUBLISHED)->count(),
            'draft' => EducationArticle::where('status', EducationArticle::STATUS_DRAFT)->count(),
        ];

        return view('admin.education.index', [
            'articles' => $articles,
            'categories' => EducationArticle::CATEGORIES,
            'stats' => $stats,
            'filterCategory' => $category,
            'filterStatus' => $status,
            'search' => $search,
        ]);
    }

    /**
     * Formulir tambah artikel edukasi baru.
     */
    public function create(): View
    {
        $article = new EducationArticle([
            'status' => EducationArticle::STATUS_PUBLISHED,
            'reading_time_minutes' => 4,
            'is_demo' => false,
            'published_at' => now(),
        ]);

        return view('admin.education.create', [
            'article' => $article,
            'categories' => EducationArticle::CATEGORIES,
            'isEdit' => false,
        ]);
    }

    /**
     * Simpan artikel edukasi baru beserta upload gambar.
     */
    public function store(EducationArticleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $baseSlug = $validated['slug'];
        $counter = 1;
        while (EducationArticle::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = "{$baseSlug}-{$counter}";
            $counter++;
        }

        // Upload gambar jika ada
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('education', 'public');
            $validated['image_path'] = $path;
        }

        $validated['is_demo'] = $request->boolean('is_demo');

        if (empty($validated['reading_time_minutes'])) {
            $wordCount = str_word_count(strip_tags((string) $validated['content']));
            $validated['reading_time_minutes'] = max(1, (int) ceil($wordCount / 180));
        }

        $article = EducationArticle::create($validated);

        return redirect()->route('admin.education.index')
            ->with('success', 'Artikel edukasi <strong>"' . e($article->title) . '"</strong> berhasil ditambahkan.');
    }

    /**
     * Formulir edit artikel edukasi.
     */
    public function edit(EducationArticle $education): View
    {
        return view('admin.education.edit', [
            'article' => $education,
            'categories' => EducationArticle::CATEGORIES,
            'isEdit' => true,
        ]);
    }

    /**
     * Perbarui artikel edukasi beserta penggantian gambar.
     */
    public function update(EducationArticleRequest $request, EducationArticle $education): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $baseSlug = $validated['slug'];
        $counter = 1;
        while (EducationArticle::where('slug', $validated['slug'])->where('id', '!=', $education->id)->exists()) {
            $validated['slug'] = "{$baseSlug}-{$counter}";
            $counter++;
        }

        // Hapus gambar jika dicentang
        if ($request->boolean('remove_image')) {
            if ($education->image_path && Storage::disk('public')->exists($education->image_path)) {
                Storage::disk('public')->delete($education->image_path);
            }
            $validated['image_path'] = null;
        }

        // Upload gambar baru
        if ($request->hasFile('image')) {
            if ($education->image_path && Storage::disk('public')->exists($education->image_path)) {
                Storage::disk('public')->delete($education->image_path);
            }
            $path = $request->file('image')->store('education', 'public');
            $validated['image_path'] = $path;
        }

        $validated['is_demo'] = $request->boolean('is_demo');

        if (empty($validated['reading_time_minutes'])) {
            $wordCount = str_word_count(strip_tags((string) $validated['content']));
            $validated['reading_time_minutes'] = max(1, (int) ceil($wordCount / 180));
        }

        $education->update($validated);

        return redirect()->route('admin.education.index')
            ->with('success', 'Artikel edukasi <strong>"' . e($education->title) . '"</strong> berhasil diperbarui.');
    }

    /**
     * Hapus artikel edukasi beserta file gambarnya.
     */
    public function destroy(EducationArticle $education): RedirectResponse
    {
        $title = $education->title;

        if ($education->image_path && Storage::disk('public')->exists($education->image_path)) {
            Storage::disk('public')->delete($education->image_path);
        }

        $education->delete();

        return redirect()->route('admin.education.index')
            ->with('success', 'Artikel edukasi <strong>"' . e($title) . '"</strong> telah berhasil dihapus.');
    }
}
