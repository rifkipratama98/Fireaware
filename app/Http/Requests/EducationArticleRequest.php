<?php

namespace App\Http\Requests;

use App\Models\EducationArticle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EducationArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $articleId = $this->route('education') instanceof EducationArticle
            ? $this->route('education')->id
            : $this->route('education');

        return [
            'title' => ['required', 'string', 'min:5', 'max:200'],
            'slug' => [
                'nullable',
                'string',
                'max:200',
                Rule::unique('education_articles', 'slug')->ignore($articleId),
            ],
            'category' => ['required', 'string', Rule::in(array_keys(EducationArticle::CATEGORIES))],
            'summary' => ['required', 'string', 'min:20', 'max:1000'],
            'content' => ['required', 'string', 'min:50'],
            // Validasi gambar wajib tipe gambar, format jpeg/png/webp, maks 2MB
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'image_caption' => ['nullable', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:120'],
            'reading_time_minutes' => ['nullable', 'integer', 'min:1', 'max:60'],
            'status' => ['required', Rule::in([EducationArticle::STATUS_PUBLISHED, EducationArticle::STATUS_DRAFT])],
            'is_demo' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul artikel edukasi wajib diisi.',
            'title.min' => 'Judul artikel minimal 5 karakter.',
            'category.required' => 'Kategori edukasi wajib dipilih.',
            'category.in' => 'Kategori yang dipilih tidak valid.',
            'summary.required' => 'Ringkasan artikel wajib diisi.',
            'content.required' => 'Isi lengkap artikel edukasi wajib diisi.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar yang didukung adalah JPEG, PNG, JPG, atau WEBP.',
            'image.max' => 'Ukuran file gambar tidak boleh melebihi 2 MB.',
            'status.required' => 'Status publikasi wajib dipilih.',
        ];
    }
}
