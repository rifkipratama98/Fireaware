<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationSettingRequest extends FormRequest
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
        return [
            'is_enabled' => ['nullable', 'boolean'],
            'browser_enabled' => ['nullable', 'boolean'],
            'email_enabled' => ['nullable', 'boolean'],
            'webhook_enabled' => ['nullable', 'boolean'],
            'email_recipients' => ['nullable', 'string', 'max:1000', 'required_if_accepted:email_enabled'],
            'webhook_url' => ['nullable', 'url', 'max:255', 'required_if_accepted:webhook_enabled'],

            'notify_hotspots' => ['nullable', 'boolean'],
            'hotspot_min_confidence' => ['required', 'integer', 'min:0', 'max:100'],
            'hotspot_min_frp' => ['required', 'numeric', 'min:0', 'max:10000'],

            'notify_air_quality' => ['nullable', 'boolean'],
            'air_quality_min_pm25' => ['required', 'numeric', 'min:0', 'max:1000'],

            'notify_announcements' => ['nullable', 'boolean'],
            'announcement_min_level' => ['required', 'integer', 'in:1,2,3'],

            'monitored_provinces' => ['nullable', 'array'],
            'monitored_provinces.*' => ['integer', 'exists:provinces,id'],

            'frequency_limit_minutes' => ['required', 'integer', 'min:5', 'max:1440'],
            'quiet_hours_enabled' => ['nullable', 'boolean'],
            'quiet_hours_start' => ['nullable', 'string', 'max:5', 'required_if_accepted:quiet_hours_enabled'],
            'quiet_hours_end' => ['nullable', 'string', 'max:5', 'required_if_accepted:quiet_hours_enabled'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email_recipients.required_if_accepted' => 'Alamat email posko penerima wajib diisi saat kanal email diaktifkan.',
            'webhook_url.required_if_accepted' => 'URL Webhook wajib diisi saat kanal webhook diaktifkan.',
            'webhook_url.url' => 'Format URL Webhook harus berupa tautan yang valid (contoh: https://...).',
            'hotspot_min_confidence.required' => 'Tingkat kepercayaan minimum hotspot wajib ditentukan.',
            'hotspot_min_frp.required' => 'Nilai minimum FRP titik panas wajib ditentukan.',
            'air_quality_min_pm25.required' => 'Ambang batas minimum PM2.5 wajib ditentukan.',
            'frequency_limit_minutes.required' => 'Batas frekuensi minimum antar notifikasi wajib diisi.',
            'frequency_limit_minutes.min' => 'Batas frekuensi minimal 5 menit untuk mencegah lonjakan spam.',
        ];
    }
}
