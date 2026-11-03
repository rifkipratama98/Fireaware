<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EducationArticle extends Model
{
    use HasFactory;

    public const STATUS_PUBLISHED = 'published';
    public const STATUS_DRAFT = 'draft';

    /**
     * Kategori edukasi karhutla dan gambut.
     *
     * @var array<string, array{name: string, description: string, icon: string}>
     */
    public const CATEGORIES = [
        'faktor-penyebab' => [
            'name' => 'Faktor & Penyebab Karhutla',
            'description' => 'Memahami pemicu alami seperti El Niño dan aktivitas manusia yang memicu kebakaran hutan dan lahan.',
            'icon' => 'bi-fire',
        ],
        'ekosistem-gambut' => [
            'name' => 'Ekosistem Lahan Gambut',
            'description' => 'Mengenal karakteristik unik lahan gambut tropis Kalimantan dan dinamika kebakaran bawah tanah (ground fire).',
            'icon' => 'bi-moisture',
        ],
        'dampak-lingkungan' => [
            'name' => 'Dampak Lingkungan & Keanekaragaman',
            'description' => 'Konsekuensi emisi karbon, hilangnya keanekaragaman flora-fauna khas Kalimantan, dan dampak ekologis jangka panjang.',
            'icon' => 'bi-tree',
        ],
        'teknik-pencegahan' => [
            'name' => 'Teknik Pencegahan & Restorasi',
            'description' => 'Metode sekat kanal (canal blocking), sumur bor, pemulihan hidrologis gambut, dan pembukaan lahan tanpa bakar (PLTB).',
            'icon' => 'bi-shield-check',
        ],
        'hukum-kebijakan' => [
            'name' => 'Hukum & Kebijakan Lingkungan',
            'description' => 'Instrumen perundang-undangan, sanksi pidana pembakaran hutan/lahan, dan hak masyarakat atas udara bersih.',
            'icon' => 'bi-bank',
        ],
    ];

    protected $fillable = [
        'title',
        'slug',
        'category',
        'summary',
        'content',
        'image_path',
        'image_caption',
        'author',
        'reading_time_minutes',
        'status',
        'is_demo',
        'published_at',
    ];

    protected $casts = [
        'reading_time_minutes' => 'integer',
        'is_demo' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $article): void {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }

            if (empty($article->reading_time_minutes) || $article->reading_time_minutes < 1) {
                $wordCount = str_word_count(strip_tags((string) $article->content));
                $article->reading_time_minutes = max(1, (int) ceil($wordCount / 180));
            }

            if ($article->status === self::STATUS_PUBLISHED && empty($article->published_at)) {
                $article->published_at = now();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Scope: Hanya artikel berstatus published yang sudah melewati waktu terbit */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    /** Scope: Filter berdasarkan kategori */
    public function scopeCategory(Builder $query, ?string $categorySlug): Builder
    {
        if ($categorySlug !== null && $categorySlug !== '' && array_key_exists($categorySlug, self::CATEGORIES)) {
            return $query->where('category', $categorySlug);
        }

        return $query;
    }

    /** Scope: Pencarian berdasarkan kata kunci */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        $clean = trim((string) $keyword);
        if ($clean === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($clean) {
            $q->where('title', 'like', "%{$clean}%")
              ->orWhere('summary', 'like', "%{$clean}%")
              ->orWhere('content', 'like', "%{$clean}%");
        });
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category]['name'] ?? ucfirst(str_replace('-', ' ', (string) $this->category));
    }

    public function categoryIcon(): string
    {
        return self::CATEGORIES[$this->category]['icon'] ?? 'bi-book';
    }

    public function categoryDescription(): string
    {
        return self::CATEGORIES[$this->category]['description'] ?? '';
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function effectiveDate(): Carbon
    {
        return $this->published_at ?? $this->created_at ?? now();
    }

    /**
     * Resolusi URL gambar artikel.
     */
    public function imageUrl(): ?string
    {
        if (empty($this->image_path)) {
            return null;
        }

        if (Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        return Storage::disk('public')->url($this->image_path);
    }

    /**
     * Memecah konten menjadi paragraf.
     *
     * @return list<string>
     */
    public function contentParagraphs(): array
    {
        $text = trim((string) $this->content);
        if ($text === '') {
            return [];
        }

        $parts = preg_split('/\R{2,}/u', $text) ?: [];

        return array_values(array_filter(
            array_map(fn (string $p) => trim($p), $parts),
            fn (string $p) => $p !== ''
        ));
    }
}
