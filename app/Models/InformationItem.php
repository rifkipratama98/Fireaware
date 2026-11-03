<?php

namespace App\Models;

use App\Services\Information\InformationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Satu informasi kewaspadaan.
 *
 * Setiap baris WAJIB terhubung ke satu sumber (information_source_id) sehingga
 * identitas sumber tidak pernah hilang. Status hanya ada bila sumber
 * menyediakannya: `status_label` NULL berarti UI tidak menampilkan status.
 */
class InformationItem extends Model
{
    use HasFactory;

    /** Ditulis sendiri oleh tim FireAware. */
    public const ORIGIN_EDITORIAL = 'editorial';

    /** Diringkas/dikutip dari sumber eksternal. */
    public const ORIGIN_EXTERNAL = 'external';

    /** @var array<string, string> */
    public const ORIGINS = [
        self::ORIGIN_EDITORIAL => 'Konten editorial FireAware',
        self::ORIGIN_EXTERNAL => 'Ringkasan dari sumber eksternal',
    ];

    protected $fillable = [
        'information_source_id', 'slug', 'content_origin', 'title', 'summary',
        'body', 'status_label', 'published_at', 'source_updated_at',
        'source_url', 'is_demo', 'is_published',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'source_updated_at' => 'datetime',
        'is_demo' => 'boolean',
        'is_published' => 'boolean',
    ];

    /**
     * `status_key` selalu diturunkan dari `status_label` agar warna status
     * tidak pernah berbeda dari status yang benar-benar diberikan sumber.
     */
    protected static function booted(): void
    {
        static::saving(function (self $item): void {
            $item->status_key = InformationStatus::keyFor($item->status_label);
        });
    }

    /** @return BelongsTo<InformationSource, $this> */
    public function source(): BelongsTo
    {
        return $this->belongsTo(InformationSource::class, 'information_source_id');
    }

    /** @return BelongsToMany<Province, $this> */
    public function provinces(): BelongsToMany
    {
        return $this->belongsToMany(Province::class, 'information_item_province')->withTimestamps();
    }

    /** Scope: hanya informasi yang sudah dipublikasikan. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /** Scope: urut dari waktu terbit terbaru, lalu pembaruan terbaru. */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')->orderByDesc('id');
    }

    /** Scope: informasi yang mencakup provinsi tertentu. */
    public function scopeForProvince(Builder $query, int $provinceId): Builder
    {
        return $query->whereHas('provinces', fn (Builder $q) => $q->whereKey($provinceId));
    }

    /** True bila sumber mencantumkan status. */
    public function hasSourceStatus(): bool
    {
        return filled($this->status_label);
    }

    /** Kelas CSS chip status (null bila tidak ada status). */
    public function statusCssClass(): ?string
    {
        return $this->hasSourceStatus() ? InformationStatus::cssClass($this->status_key) : null;
    }

    /** Ikon chip status (null bila tidak ada status). */
    public function statusIcon(): ?string
    {
        return $this->hasSourceStatus() ? InformationStatus::icon($this->status_key) : null;
    }

    /**
     * Waktu yang paling relevan untuk ditampilkan: pembaruan di sumber bila ada,
     * jika tidak waktu terbit. Tidak pernah jatuh ke `updated_at` basis data
     * karena itu waktu penyimpanan aplikasi, bukan waktu sumber.
     */
    public function effectiveTimestamp(): ?Carbon
    {
        return $this->source_updated_at ?? $this->published_at;
    }

    /** True bila ada pembaruan setelah terbit. */
    public function isUpdatedAfterPublish(): bool
    {
        return $this->source_updated_at !== null
            && $this->published_at !== null
            && $this->source_updated_at->greaterThan($this->published_at);
    }

    /** True bila isi ditulis sendiri oleh FireAware, bukan kutipan sumber. */
    public function isEditorial(): bool
    {
        return $this->content_origin === self::ORIGIN_EDITORIAL;
    }

    public function originLabel(): string
    {
        return self::ORIGINS[$this->content_origin] ?? 'Asal konten tidak dicatat';
    }

    /**
     * Paragraf isi informasi. Baris kosong dipisahkan agar mudah dibaca di
     * layar ponsel; tidak ada HTML mentah dari sumber yang dirender.
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

    /** Nama provinsi dalam satu baris, mis. "Kalimantan Barat, Kalimantan Tengah". */
    public function provinceNames(): string
    {
        return $this->provinces->pluck('name')->implode(', ');
    }
}
