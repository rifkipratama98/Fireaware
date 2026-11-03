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
            'body.required' => 'Isi teks pengumuman wajib diisi.',
            'level.required' => 'Tingkat kewaspadaan wajib dipilih.',
            'source_name.required' => 'Instansi sumber atau penerbit pengumuman wajib dicantumkan.',
            'source_url.url' => 'Format URL sumber harus valid (misal: https://...).',
            'status.required' => 'Status publikasi wajib dipilih.',
        ];
    }
}
