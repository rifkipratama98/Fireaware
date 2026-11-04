<?php

namespace App\Http\Requests;

use App\Models\Announcement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnnouncementRequest extends FormRequest
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
        $announcementId = $this->route('announcement') instanceof Announcement
            ? $this->route('announcement')->id
            : $this->route('announcement');

        return [
            'title' => ['required', 'string', 'min:5', 'max:200'],
            'slug' => [
                'nullable',
                'string',
                'max:200',
                Rule::unique('announcements', 'slug')->ignore($announcementId),
            ],
            'summary' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'min:15'],
            'level' => ['required', 'integer', Rule::in([1, 2, 3])],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'source_name' => ['required', 'string', 'min:3', 'max:160'],
            'source_url' => ['nullable', 'url', 'max:255'],
            'status' => ['required', Rule::in([Announcement::STATUS_PUBLISHED, Announcement::STATUS_DRAFT])],
            'is_demo' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul pengumuman wajib diisi.',
            'title.min' => 'Judul pengumuman minimal 5 karakter.',
            'title.max' => 'Judul pengumuman maksimal 200 karakter.',
            'slug.unique' => 'Slug ini sudah digunakan oleh pengumuman lain.',
            'body.required' => 'Isi teks pengumuman wajib diisi.',
            'body.min' => 'Isi pengumuman minimal 15 karakter.',
            'level.required' => 'Tingkat kewaspadaan wajib dipilih.',
            'level.in' => 'Tingkat kewaspadaan yang dipilih tidak valid.',
            'source_name.required' => 'Instansi sumber atau penerbit pengumuman wajib dicantumkan.',
            'source_name.min' => 'Nama instansi sumber minimal 3 karakter.',
            'source_url.url' => 'Format URL sumber harus valid (misal: https://...).',
            'status.required' => 'Status publikasi wajib dipilih.',
            'status.in' => 'Status publikasi harus berupa "published" atau "draft".',
        ];
    }
}
