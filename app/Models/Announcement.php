<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Announcement extends Model
{
    use HasFactory;

    public const STATUS_PUBLISHED = 'published';
    public const STATUS_DRAFT = 'draft';

    public const LEVEL_INFO = 1;
    public const LEVEL_WASPADA = 2;
    public const LEVEL_DARURAT = 3;

    public const LEVELS = [
        self::LEVEL_INFO => [
            'label' => 'Informasi',
            'css_badge' => 'bg-info bg-opacity-10 text-info border-info border-opacity-25',
            'css_dot' => 'level-1',
            'icon' => 'bi-info-circle',
            'description' => 'Pemberitahuan umum, update cuaca, atau imbauan preventif.',
        ],
        self::LEVEL_WASPADA => [
            'label' => 'Waspada',
            'css_badge' => 'bg-warning bg-opacity-10 text-warning border-warning border-opacity-25',
            'css_dot' => 'level-2',
            'icon' => 'bi-exclamation-triangle',
            'description' => 'Peningkatan titik panas atau potensi kebakaran yang memerlukan perhatian warga.',
        ],
        self::LEVEL_DARURAT => [
            'label' => 'Siaga Darurat',
            'css_badge' => 'bg-danger bg-opacity-10 text-danger border-danger border-opacity-25',
            'css_dot' => 'level-3',
            'icon' => 'bi-shield-exclamation',
            'description' => 'Keadaan darurat kabut asap, kebakaran aktif mendekati pemukiman, atau status tanggap darurat.',
        ],
    ];

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'body',
        'level',
        'province_id',
        'source_name',
        'source_url',
        'status',
        'is_demo',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'level' => 'integer',
        'is_demo' => 'boolean',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $item): void {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->title);
            }

            if (empty($item->status)) {
                $item->status = $item->is_active ? self::STATUS_PUBLISHED : self::STATUS_DRAFT;
            }

            if ($item->status === self::STATUS_PUBLISHED && empty($item->published_at)) {
                $item->published_at = now();
            }

            // Jaga sinkronisasi is_active
            $item->is_active = ($item->status === self::STATUS_PUBLISHED);
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsTo<Province, $this> */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    /** Scope: hanya pengumuman yang berstatus published dan tanggal terbit sudah lewat. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('is_active', true)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            });
    }

    /** Scope: filter berdasarkan wilayah provinsi */
    public function scopeProvince(Builder $query, ?int $provinceId): Builder
    {
        if ($provinceId) {
            return $query->where('province_id', $provinceId);
        }

        return $query;
    }

    /** Scope: filter berdasarkan tingkat urgensi */
    public function scopeLevel(Builder $query, ?int $level): Builder
    {
        if ($level && array_key_exists($level, self::LEVELS)) {
            return $query->where('level', $level);
        }

        return $query;
    }

    /** Scope: pencarian kata kunci */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        $clean = trim((string) $keyword);
        if ($clean === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($clean) {
            $q->where('title', 'like', "%{$clean}%")
              ->orWhere('summary', 'like', "%{$clean}%")
              ->orWhere('body', 'like', "%{$clean}%")
              ->orWhere('source_name', 'like', "%{$clean}%");
        });
    }

    public function levelLabel(): string
    {
        return self::LEVELS[$this->level]['label'] ?? 'Informasi';
    }

    public function levelBadgeClass(): string
    {
        return self::LEVELS[$this->level]['css_badge'] ?? 'bg-secondary bg-opacity-10 text-secondary';
    }

    public function levelDotClass(): string
    {
        return self::LEVELS[$this->level]['css_dot'] ?? 'level-1';
    }

    public function levelIcon(): string
    {
        return self::LEVELS[$this->level]['icon'] ?? 'bi-info-circle';
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED && $this->is_active;
    }

    public function effectiveDate(): Carbon
    {
        return $this->published_at ?? $this->created_at ?? now();
    }

    /**
     * Mengembalikan ringkasan jika ada, atau potongan isi teks.
     */
    public function excerpt(int $length = 160): string
    {
        if (filled($this->summary)) {
            return $this->summary;
        }

        return Str::limit(strip_tags((string) $this->body), $length);
    }

    /**
     * Paragraf isi pengumuman.
     *
     * @return list<string>
     */
    public function bodyParagraphs(): array
    {
        $body = trim((string) $this->body);
        if ($body === '') {
            return [];
        }

        $parts = preg_split('/\R{2,}/u', $body) ?: [];

        return array_values(array_filter(
            array_map(fn (string $part) => trim($part), $parts),
            fn (string $part) => $part !== ''
        ));
    }
}
