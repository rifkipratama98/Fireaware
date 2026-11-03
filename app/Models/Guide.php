<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Model Guide — Panduan Keselamatan Masyarakat Kalimantan.
 *
 * Digunakan untuk menyajikan artikel edukasi langkah aman mitigasi asap
 * dan peringatan dini kebakaran hutan/lahan dengan rujukan terpercaya.
 */
class Guide extends Model
{
    use HasFactory;

    public const STATUS_PUBLISHED = 'published';
    public const STATUS_DRAFT = 'draft';

    /**
     * Lima kategori resmi sesuai kebutuhan mitigasi karhutla di Kalimantan.
     *
     * @var array<string, array{name: string, description: string, icon: string}>
     */
    public const CATEGORIES = [
        'menghadapi-asap' => [
            'name' => 'Menghadapi Asap',
            'description' => 'Langkah perlindungan diri, pertolongan pertama, dan mitigasi kesehatan saat asap mulai masuk ke lingkungan tinggal.',
            'icon' => 'bi-cloud-fog2',
        ],
        'mengurangi-paparan-asap' => [
            'name' => 'Mengurangi Paparan Asap',
            'description' => 'Upaya meminimalkan kontak dengan polutan udara, standar penggunaan masker, dan menjaga kualitas udara dalam ruangan.',
            'icon' => 'bi-shield-check',
        ],
        'persiapan-saat-kualitas-udara-memburuk' => [
            'name' => 'Persiapan Saat Kualitas Udara Memburuk',
            'description' => 'Kesiapsiagaan logistik, perlindungan kelompok rentan (anak, lansia, penderita asma), dan rencana darurat keluarga.',
            'icon' => 'bi-heart-pulse',
        ],
        'tindakan-ketika-melihat-indikasi-kebakaran' => [
            'name' => 'Tindakan Ketika Melihat Indikasi Kebakaran',
            'description' => 'Prosedur aman melapor ke posko siaga tanpa membahayakan keselamatan diri atau mendekati lokasi api berbahaya.',
            'icon' => 'bi-exclamation-triangle',
        ],
        'cara-memperoleh-informasi-resmi' => [
            'name' => 'Cara Memperoleh Informasi Resmi',
            'description' => 'Kanal pantauan terpercaya dari BMKG, KLHK SIPONGI, dan BNPB agar terhindar dari rumor dan disinformasi.',
            'icon' => 'bi-patch-check',
        ],
    ];

    protected $fillable = [
        'title',
        'slug',
        'category',
        'summary',
        'content',
        'source_name',
        'source_url',
        'source_notes',
        'reading_time_minutes',
        'status',
        'published_at',
    ];

    protected $casts = [
        'reading_time_minutes' => 'integer',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $guide): void {
            // Pastikan slug terisi
            if (empty($guide->slug)) {
                $guide->slug = Str::slug($guide->title);
            }

            // Hitung otomatis perkiraan waktu baca bila belum ditentukan
            if (empty($guide->reading_time_minutes) || $guide->reading_time_minutes < 1) {
                $wordCount = str_word_count(strip_tags((string) $guide->content));
                $guide->reading_time_minutes = max(1, (int) ceil($wordCount / 180));
            }

            // Set published_at jika status published dan tanggal masih kosong
            if ($guide->status === self::STATUS_PUBLISHED && empty($guide->published_at)) {
                $guide->published_at = now();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope: Hanya panduan yang berstatus published dan tanggal terbit sudah lewat.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    /**
     * Scope: Filter berdasarkan kategori (slug kategori).
     */
    public function scopeCategory(Builder $query, ?string $categorySlug): Builder
    {
        if ($categorySlug !== null && $categorySlug !== '' && array_key_exists($categorySlug, self::CATEGORIES)) {
            return $query->where('category', $categorySlug);
        }

        return $query;
    }

    /**
     * Scope: Pencarian berdasarkan kata kunci pada judul atau ringkasan.
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        $clean = trim((string) $keyword);
        if ($clean === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($clean) {
            $q->where('title', 'like', "%{$clean}%")
              ->orWhere('summary', 'like', "%{$clean}%");
        });
    }

    /**
     * Nama kategori manusiawi.
     */
    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category]['name'] ?? ucfirst(str_replace('-', ' ', (string) $this->category));
    }

    /**
     * Ikon bootstrap untuk kategori.
     */
    public function categoryIcon(): string
    {
        return self::CATEGORIES[$this->category]['icon'] ?? 'bi-journal-bookmark';
    }

    /**
     * Deskripsi singkat kategori.
     */
    public function categoryDescription(): string
    {
        return self::CATEGORIES[$this->category]['description'] ?? '';
    }

    /**
     * Status apakah artikel sudah diterbitkan.
     */
    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    /**
     * Memecah konten menjadi paragraf untuk tampilan bersih.
     * Mendukung baris baru ganda sebagai pemisah paragraf.
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

    /**
     * Tanggal efektif untuk tampilan publik (diutamakan published_at, fallback created_at).
     */
    public function effectiveDate(): Carbon
    {
        return $this->published_at ?? $this->created_at ?? now();
    }
}
