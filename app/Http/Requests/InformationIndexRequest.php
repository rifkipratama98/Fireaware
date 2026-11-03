<?php

namespace App\Http\Requests;

use App\Models\Province;
use App\Services\Information\InformationStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi filter daftar Informasi Kewaspadaan.
 *
 * Semua filter bersifat opsional. Nilai yang tidak valid ditolak dengan pesan
 * berbahasa Indonesia agar URL yang dibagikan tidak menghasilkan halaman
 * kosong yang membingungkan.
 */
class InformationIndexRequest extends FormRequest
{
    /** Halaman publik: tidak butuh otorisasi. */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'wilayah' => [
                'nullable',
                'string',
                'max:100',
                Rule::exists('provinces', 'slug'),
            ],
            'status' => [
                'nullable',
                'string',
                Rule::in(array_keys(InformationStatus::filterOptions())),
            ],
            'sumber' => [
                'nullable',
                'string',
                'max:120',
                Rule::exists('information_sources', 'slug'),
            ],
            'q' => [
                'nullable',
                'string',
                'min:3',
                'max:100',
            ],
            'page' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'wilayah.exists' => 'Wilayah yang dipilih tidak dikenali.',
            'status.in' => 'Filter status tidak dikenali.',
            'sumber.exists' => 'Sumber informasi tidak dikenali.',
            'q.min' => 'Kata kunci minimal :min karakter.',
            'q.max' => 'Kata kunci maksimal :max karakter.',
            'page.integer' => 'Nomor halaman harus berupa angka.',
            'page.min' => 'Nomor halaman minimal 1.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'wilayah' => 'wilayah',
            'status' => 'status',
            'sumber' => 'sumber',
            'q' => 'kata kunci',
            'page' => 'halaman',
        ];
    }

    /** Slug provinsi yang valid (null bila filter kosong). */
    public function provinceSlug(): ?string
    {
        $value = $this->validated('wilayah');

        return is_string($value) && $value !== '' ? $value : null;
    }

    /** Key status yang valid (null bila filter kosong). */
    public function statusKey(): ?string
    {
        $value = $this->validated('status');

        return is_string($value) && $value !== '' ? $value : null;
    }

    /** Slug sumber yang valid (null bila filter kosong). */
    public function sourceSlug(): ?string
    {
        $value = $this->validated('sumber');

        return is_string($value) && $value !== '' ? $value : null;
    }

    /** Kata kunci pencarian yang sudah dirapikan. */
    public function keyword(): ?string
    {
        $value = $this->validated('q');

        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }

    /**
     * Filter yang dipakai service/UI.
     *
     * @return array{wilayah:?string,status:?string,sumber:?string,q:?string}
     */
    public function filters(): array
    {
        return [
            'wilayah' => $this->provinceSlug(),
            'status' => $this->statusKey(),
            'sumber' => $this->sourceSlug(),
            'q' => $this->keyword(),
        ];
    }

    /** True bila ada filter aktif (dipakai untuk menampilkan tombol reset). */
    public function hasActiveFilters(): bool
    {
        return collect($this->filters())->filter(fn ($value) => $value !== null)->isNotEmpty();
    }

    /** ID provinsi dari slug terpilih (null bila tidak ada filter wilayah). */
    public function provinceId(): ?int
    {
        $slug = $this->provinceSlug();

        if ($slug === null) {
            return null;
        }

        $id = Province::query()->where('slug', $slug)->value('id');

        return $id !== null ? (int) $id : null;
    }
}
