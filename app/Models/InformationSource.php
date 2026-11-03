<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Identitas sumber informasi kewaspadaan.
 *
 * Disimpan terpisah agar setiap informasi selalu dapat dilacak asalnya dan
 * informasi dari beberapa sumber tidak pernah tampil sebagai satu suara.
 */
class InformationSource extends Model
{
    use HasFactory;

    /** Otoritas resmi (mis. BMKG, KLHK, BPBD). */
    public const KIND_AUTHORITY = 'authority';

    /** Media atau lembaga lain yang bukan otoritas penanggulangan bencana. */
    public const KIND_MEDIA = 'media';

    /** Konten yang ditulis sendiri oleh tim FireAware. */
    public const KIND_EDITORIAL = 'editorial';

    /** @var array<string, string> */
    public const KINDS = [
        self::KIND_AUTHORITY => 'Otoritas resmi',
        self::KIND_MEDIA => 'Media / lembaga',
        self::KIND_EDITORIAL => 'Editorial FireAware',
    ];

    protected $fillable = [
        'name', 'slug', 'kind', 'organization', 'homepage_url',
        'description', 'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /** @return HasMany<InformationItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(InformationItem::class);
    }

    /** True bila sumber ini otoritas yang berwenang mengeluarkan peringatan. */
    public function isAuthority(): bool
    {
        return $this->kind === self::KIND_AUTHORITY;
    }

    /** True bila identitas sumber sudah diperiksa (bukan sekadar diklaim). */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function kindLabel(): string
    {
        return self::KINDS[$this->kind] ?? 'Sumber tidak diklasifikasikan';
    }
}
