<?php

namespace App\Services\Information;

/**
 * Normalisasi status informasi yang DISEDIAKAN SUMBER.
 *
 * Aturan penting:
 * - Kelas ini tidak pernah membuat status. Bila sumber tidak mencantumkan
 *   status, `keyFor()` mengembalikan null dan UI tidak menampilkan apa pun.
 * - Label yang ditampilkan selalu teks asli dari sumber; kelas ini hanya
 *   menghasilkan "key" untuk keperluan warna, agar warna tidak ditentukan
 *   dari tebakan kata kunci di dalam view.
 * - Warna sengaja beragam: darurat/waspada memakai warna hangat, sedangkan
 *   status aman/berakhir memakai warna netral-hijau — bukan merah untuk semua.
 */
class InformationStatus
{
    public const KEY_EMERGENCY = 'emergency'; // darurat / awas
    public const KEY_ALERT = 'alert';         // siaga / waspada
    public const KEY_WATCH = 'watch';         // pemantauan / peningkatan
    public const KEY_NORMAL = 'normal';       // normal / terkendali
    public const KEY_ENDED = 'ended';         // berakhir / dicabut
    public const KEY_UNKNOWN = 'unknown';     // ada status, kosakatanya tidak dikenali

    /**
     * Kata kunci per key. Dicocokkan pada label yang sudah dinormalisasi
     * (huruf kecil, tanpa tanda baca).
     *
     * @var array<string, list<string>>
     */
    private const KEYWORDS = [
        self::KEY_EMERGENCY => ['darurat', 'awas', 'emergency', 'merah'],
        self::KEY_ALERT => ['siaga', 'waspada', 'alert', 'tinggi'],
        self::KEY_WATCH => ['pemantauan', 'pantau', 'menengah', 'sedang', 'watch', 'peningkatan'],
        self::KEY_NORMAL => ['normal', 'terkendali', 'aman', 'hijau', 'rendah'],
        self::KEY_ENDED => ['berakhir', 'dicabut', 'selesai', 'ended', 'dibatalkan'],
    ];

    /**
     * Key normalisasi dari label status sumber.
     * Null bila sumber tidak menyediakan status (string kosong / null).
     */
    public static function keyFor(?string $label): ?string
    {
        $normalized = self::normalize($label);

        if ($normalized === '') {
            return null;
        }

        foreach (self::KEYWORDS as $key => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($normalized, $keyword)) {
                    return $key;
                }
            }
        }

        return self::KEY_UNKNOWN;
    }

    /**
     * Kelas CSS chip status berdasarkan key normalisasi.
     * Key yang tidak dikenali tetap mendapat warna netral, bukan merah.
     */
    public static function cssClass(?string $key): string
    {
        return match ($key) {
            self::KEY_EMERGENCY => 'status-emergency',
            self::KEY_ALERT => 'status-alert',
            self::KEY_WATCH => 'status-watch',
            self::KEY_NORMAL => 'status-normal',
            self::KEY_ENDED => 'status-ended',
            default => 'status-neutral',
        };
    }

    /** Ikon Bootstrap Icons pendamping chip status. */
    public static function icon(?string $key): string
    {
        return match ($key) {
            self::KEY_EMERGENCY => 'bi-exclamation-octagon',
            self::KEY_ALERT => 'bi-exclamation-triangle',
            self::KEY_WATCH => 'bi-eye',
            self::KEY_NORMAL => 'bi-check2-circle',
            self::KEY_ENDED => 'bi-slash-circle',
            default => 'bi-info-circle',
        };
    }

    /**
     * Pilihan filter status untuk UI.
     *
     * @return array<string, string>
     */
    public static function filterOptions(): array
    {
        return [
            self::KEY_EMERGENCY => 'Darurat / awas',
            self::KEY_ALERT => 'Siaga / waspada',
            self::KEY_WATCH => 'Pemantauan',
            self::KEY_NORMAL => 'Normal / terkendali',
            self::KEY_ENDED => 'Berakhir / dicabut',
            'with_status' => 'Semua yang memiliki status',
        ];
    }

    /** True bila nilai filter status dikenal (key atau 'with_status'). */
    public static function isFilterable(?string $value): bool
    {
        return $value !== null && array_key_exists($value, self::filterOptions());
    }

    /** Label manusiawi untuk nilai filter status. */
    public static function filterLabel(?string $value): ?string
    {
        return self::isFilterable($value) ? self::filterOptions()[$value] : null;
    }

    /** Huruf kecil, tanpa tanda baca, spasi dirapikan. */
    private static function normalize(?string $label): string
    {
        $value = trim((string) $label);

        if ($value === '') {
            return '';
        }

        $value = mb_strtolower($value);
        $value = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $value) ?? $value;

        return trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
    }
}
