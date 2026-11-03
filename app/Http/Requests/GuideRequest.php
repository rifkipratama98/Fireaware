<?php

namespace App\Http\Requests;

use App\Models\Guide;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class GuideRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna berwenang membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk panduan keselamatan.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $guideId = $this->route('guide') instanceof Guide
            ? $this->route('guide')->id
            : $this->route('guide');

        return [
            'title' => ['required', 'string', 'min:5', 'max:200'],
            'slug' => [
                'nullable',
                'string',
                'max:200',
                Rule::unique('guides', 'slug')->ignore($guideId),
            ],
            'category' => ['required', 'string', Rule::in(array_keys(Guide::CATEGORIES))],
            'summary' => ['required', 'string', 'min:20', 'max:1000'],
            'content' => ['required', 'string', 'min:50'],
            'source_name' => ['required', 'string', 'min:3', 'max:160'],
            'source_url' => ['nullable', 'url', 'max:255'],
            'source_notes' => ['nullable', 'string', 'max:500'],
            'reading_time_minutes' => ['nullable', 'integer', 'min:1', 'max:60'],
            'status' => ['required', Rule::in([Guide::STATUS_PUBLISHED, Guide::STATUS_DRAFT])],
            'published_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Kustomisasi pesan validasi bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul panduan wajib diisi.',
            'title.min' => 'Judul panduan minimal 5 karakter.',
            'title.max' => 'Judul panduan maksimal 200 karakter.',
            'slug.unique' => 'Slug ini sudah digunakan oleh panduan lain.',
            'category.required' => 'Kategori panduan wajib dipilih.',
            'category.in' => 'Kategori yang dipilih tidak valid.',
            'summary.required' => 'Pengantar/ringkasan panduan wajib diisi.',
            'summary.min' => 'Ringkasan panduan minimal 20 karakter agar informatif.',
            'content.required' => 'Isi teks panduan keselamatan wajib diisi.',
            'content.min' => 'Isi panduan minimal 50 karakter untuk artikel edukatif.',
            'source_name.required' => 'Sumber rujukan resmi/kredibel wajib dicantumkan.',
            'source_name.min' => 'Nama sumber rujukan minimal 3 karakter.',
            'source_url.url' => 'Format URL sumber rujukan harus berupa tautan yang valid (contoh: https://...).',
            'status.required' => 'Status publikasi wajib dipilih.',
            'status.in' => 'Status publikasi harus berupa "published" atau "draft".',
        ];
    }

    /**
     * Validasi keselamatan tambahan:
     * Menghindari konten tanpa dasar rujukan dan melarang anjuran mendekati api atau
     * melakukan pemadaman mandiri di area berbahaya.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $sourceName = strtolower(trim((string) $this->input('source_name')));
            $invalidSources = ['tidak ada', 'anonim', 'unknown', '-', 'none', 'n/a', 'kosong'];

            if (in_array($sourceName, $invalidSources, true)) {
                $v->errors()->add(
                    'source_name',
                    'Sumber rujukan harus mencantumkan instansi, lembaga, atau literatur yang kredibel (mis. Kemenkes, BMKG, KLHK, BNPB, WHO).'
                );
            }

            $contentLower = strtolower((string) $this->input('content'));

            // Larangan instruksi berbahaya: mendekati api atau memadamkan api tanpa keahlian
            $dangerousPatterns = [
                '/mendekati (titik )?api/i',
                '/masuk ke (dalam )?hutan yang terbakar/i',
                '/padamkan sendiri api besar/i',
                '/dekati kobaran/i',
            ];

            foreach ($dangerousPatterns as $pattern) {
                // Jika frase ada tapi TIDAK didahului oleh kata larangan (jangan/dilarang/hindari/tidak boleh)
                if (preg_match($pattern, $contentLower)) {
                    // Periksa apakah didahului larangan
                    if (!preg_match('/(jangan|dilarang|hindari|tidak boleh|bukan|tanpa)\s+[^\.\,\;\n]*' . substr($pattern, 1, -2) . '/i', $contentLower)) {
                        $v->errors()->add(
                            'content',
                            'Konten mengandung anjuran yang berisiko. Panduan keselamatan tidak boleh menyarankan masyarakat mendekati api atau melakukan pemadaman langsung di area berbahaya.'
                        );
                        break;
                    }
                }
            }
        });
    }
}
