<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_enabled',
        'browser_enabled',
        'email_enabled',
        'webhook_enabled',
        'email_recipients',
        'webhook_url',
        'notify_hotspots',
        'hotspot_min_confidence',
        'hotspot_min_frp',
        'notify_air_quality',
        'air_quality_min_pm25',
        'notify_announcements',
        'announcement_min_level',
        'monitored_provinces',
        'frequency_limit_minutes',
        'quiet_hours_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
        'updated_by',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'browser_enabled' => 'boolean',
        'email_enabled' => 'boolean',
        'webhook_enabled' => 'boolean',
        'notify_hotspots' => 'boolean',
        'hotspot_min_confidence' => 'integer',
        'hotspot_min_frp' => 'float',
        'notify_air_quality' => 'boolean',
        'air_quality_min_pm25' => 'float',
        'notify_announcements' => 'boolean',
        'announcement_min_level' => 'integer',
        'monitored_provinces' => 'array',
        'frequency_limit_minutes' => 'integer',
        'quiet_hours_enabled' => 'boolean',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Mengambil entri konfigurasi saat ini atau membuat nilai standar baru.
     */
    public static function current(): self
    {
        $setting = static::query()->first();

        if (! $setting) {
            $setting = static::create([
                'is_enabled' => true,
                'browser_enabled' => true,
                'email_enabled' => false,
                'webhook_enabled' => false,
                'email_recipients' => 'posko-karhutla@fireaware.id',
                'webhook_url' => null,
                'notify_hotspots' => true,
                'hotspot_min_confidence' => 80,
                'hotspot_min_frp' => 50.00,
                'notify_air_quality' => true,
                'air_quality_min_pm25' => 55.40,
                'notify_announcements' => true,
                'announcement_min_level' => 2,
                'monitored_provinces' => null, // null = seluruh Kalimantan
                'frequency_limit_minutes' => 60,
                'quiet_hours_enabled' => false,
                'quiet_hours_start' => '22:00',
                'quiet_hours_end' => '06:00',
            ]);
        }

        return $setting;
    }

    /**
     * Memeriksa apakah suatu provinsi termasuk dalam wilayah yang dipantau.
     */
    public function isProvinceMonitored(?int $provinceId): bool
    {
        if ($provinceId === null) {
            return true; // Informasi umum / lintas provinsi
        }

        if (empty($this->monitored_provinces)) {
            return true; // null atau array kosong berarti seluruh provinsi Kalimantan
        }

        return in_array($provinceId, $this->monitored_provinces, false)
            || in_array((string) $provinceId, $this->monitored_provinces, true);
    }

    /**
     * Memeriksa apakah saat ini berada dalam rentang jam hening (quiet hours).
     */
    public function isWithinQuietHours(): bool
    {
        if (! $this->quiet_hours_enabled || empty($this->quiet_hours_start) || empty($this->quiet_hours_end)) {
            return false;
        }

        $now = now()->format('H:i');
        $start = $this->quiet_hours_start;
        $end = $this->quiet_hours_end;

        if ($start <= $end) {
            return $now >= $start && $now <= $end;
        }

        // Melewati tengah malam (misal 22:00 s/d 06:00)
        return $now >= $start || $now <= $end;
    }
}
