<?php

namespace App\Services\Notifications;

use App\Models\NotificationLog;
use App\Models\NotificationSetting;
use App\Models\Province;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotificationDispatcherService
{
    /**
     * Mengevaluasi aturan dan mengirimkan notifikasi ke kanal yang aktif.
     *
     * @return array<string, mixed> Hasil pengiriman per kanal
     */
    public function dispatchAlert(
        string $category,
        string $title,
        string $message,
        string $level = 'info',
        ?int $provinceId = null,
        array $metadata = []
    ): array {
        $setting = NotificationSetting::current();
        $dispatched = [];

        // 1. Cek Sakelar Utama Sistem
        if (! $setting->is_enabled) {
            $this->logSuppression($category, $title, $message, $level, $provinceId, 'Notifikasi sistem dinonaktifkan oleh Administrator.');
            return ['status' => 'suppressed', 'reason' => 'Sistem notifikasi dinonaktifkan.'];
        }

        // 2. Cek Kategori Diaktifkan
        if (! $this->isCategoryEnabled($setting, $category, $metadata)) {
            $this->logSuppression($category, $title, $message, $level, $provinceId, "Jenis notifikasi '{$category}' dinonaktifkan atau berada di bawah ambang batas.");
            return ['status' => 'suppressed', 'reason' => 'Kategori berada di bawah ambang batas konfigurasi.'];
        }

        // 3. Cek Wilayah Terpantau
        if (! $setting->isProvinceMonitored($provinceId)) {
            $provName = Province::find($provinceId)?->name ?? "ID: {$provinceId}";
            $this->logSuppression($category, $title, $message, $level, $provinceId, "Wilayah '{$provName}' tidak termasuk dalam daftar pemantauan aktif.");
            return ['status' => 'suppressed', 'reason' => 'Wilayah di luar cakupan pemantauan.'];
        }

        // 4. Cek Jam Hening (Quiet Hours)
        if ($setting->isWithinQuietHours()) {
            $this->logSuppression($category, $title, $message, $level, $provinceId, "Notifikasi ditekan karena berada dalam rentang jam hening ({$setting->quiet_hours_start} - {$setting->quiet_hours_end}).");
            return ['status' => 'suppressed', 'reason' => 'Berada dalam jam hening.'];
        }

        // 5. Cek Aturan Frekuensi & Anti-Spam
        if ($this->isRateLimited($setting, $category, $provinceId)) {
            $this->logSuppression($category, $title, $message, $level, $provinceId, "Ditekan oleh aturan anti-spam: notifikasi sejenis baru saja dikirim dalam kurun {$setting->frequency_limit_minutes} menit terakhir.");
            return ['status' => 'suppressed', 'reason' => 'Dibatasi oleh aturan frekuensi anti-spam.'];
        }

        // --- Kirim ke Kanal Yang Diaktifkan ---

        // Kanal 1: Browser (Masyarakat Kalimantan di Portal Publik)
        if ($setting->browser_enabled) {
            $dispatched['browser'] = $this->dispatchToBrowser($title, $message, $category, $level, $provinceId);
        }

        // Kanal 2: Email Posko
        if ($setting->email_enabled && filled($setting->email_recipients)) {
            $dispatched['email'] = $this->dispatchToEmail($setting->email_recipients, $title, $message, $category, $level, $provinceId);
        }

        // Kanal 3: Webhook Koordinasi
        if ($setting->webhook_enabled && filled($setting->webhook_url)) {
            $dispatched['webhook'] = $this->dispatchToWebhook($setting->webhook_url, $title, $message, $category, $level, $provinceId, $metadata);
        }

        return [
            'status' => 'dispatched',
            'channels' => $dispatched,
        ];
    }

    /**
     * Kirim notifikasi uji coba untuk memeriksa kanal yang aktif.
     */
    public function sendTestNotification(): array
    {
        $setting = NotificationSetting::current();
        $results = [];

        $title = 'Uji Coba Sistem Peringatan FireAware';
        $message = 'Ini adalah pesan uji coba verifikasi konektivitas kanal notifikasi dari panel administrator.';

        // Uji Browser
        if ($setting->browser_enabled) {
            $results['browser'] = $this->dispatchToBrowser($title, $message, 'test', 'info', null);
        }

        // Uji Email
        if ($setting->email_enabled && filled($setting->email_recipients)) {
            $results['email'] = $this->dispatchToEmail($setting->email_recipients, $title, $message, 'test', 'info', null);
        }

        // Uji Webhook
        if ($setting->webhook_enabled && filled($setting->webhook_url)) {
            $results['webhook'] = $this->dispatchToWebhook($setting->webhook_url, $title, $message, 'test', 'info', null, ['is_test' => true]);
        }

        if (empty($results)) {
            NotificationLog::create([
                'channel' => NotificationLog::CHANNEL_SYSTEM,
                'title' => $title,
                'message' => $message . ' (Tidak ada kanal eksternal yang diaktifkan)',
                'category' => 'test',
                'level' => 'info',
                'status' => NotificationLog::STATUS_SENT,
                'recipient_target' => 'Log Internal Sistem',
                'sent_at' => now(),
            ]);
            $results['system'] = 'Log uji coba tercatat pada sistem internal.';
        }

        return $results;
    }

    /**
     * Mengambil daftar notifikasi peringatan aktif untuk feed portal publik (masyarakat).
     */
    public function getActivePublicAlerts(int $limit = 5): Collection
    {
        $setting = NotificationSetting::current();

        if (! $setting->is_enabled || ! $setting->browser_enabled) {
            return collect();
        }

        return NotificationLog::query()
            ->where('channel', NotificationLog::CHANNEL_BROWSER)
            ->where('status', NotificationLog::STATUS_SENT)
            ->where('created_at', '>=', now()->subHours(24))
            ->with('province')
            ->latest('id')
            ->take($limit)
            ->get();
    }

    private function dispatchToBrowser(string $title, string $message, string $category, string $level, ?int $provinceId): NotificationLog
    {
        return NotificationLog::create([
            'channel' => NotificationLog::CHANNEL_BROWSER,
            'title' => $title,
            'message' => $message,
            'category' => $category,
            'level' => $level,
            'province_id' => $provinceId,
            'recipient_target' => 'Masyarakat (Feed Portal Publik)',
            'status' => NotificationLog::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    private function dispatchToEmail(string $recipients, string $title, string $message, string $category, string $level, ?int $provinceId): NotificationLog
    {
        return NotificationLog::create([
            'channel' => NotificationLog::CHANNEL_EMAIL,
            'title' => $title,
            'message' => $message,
            'category' => $category,
            'level' => $level,
            'province_id' => $provinceId,
            'recipient_target' => $recipients,
            'status' => NotificationLog::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }


    private function dispatchToWebhook(string $url, string $title, string $message, string $category, string $level, ?int $provinceId, array $metadata): NotificationLog
    {
        $payload = [
            'event' => 'fireaware.alert',
            'title' => $title,
            'message' => $message,
            'category' => $category,
            'level' => $level,
            'province_id' => $provinceId,
            'timestamp' => now()->toIso8601String(),
            'metadata' => $metadata,
        ];

        try {
            $response = Http::timeout(5)->retry(1, 200, throw: false)->post($url, $payload);

            if ($response->successful()) {
                return NotificationLog::create([
                    'channel' => NotificationLog::CHANNEL_WEBHOOK,
                    'title' => $title,
                    'message' => $message,
                    'category' => $category,
                    'level' => $level,
                    'province_id' => $provinceId,
                    'recipient_target' => $url,
                    'status' => NotificationLog::STATUS_SENT,
                    'sent_at' => now(),
                ]);
            }

            return NotificationLog::create([
                'channel' => NotificationLog::CHANNEL_WEBHOOK,
                'title' => $title,
                'message' => $message,
                'category' => $category,
                'level' => $level,
                'province_id' => $provinceId,
                'recipient_target' => $url,
                'status' => NotificationLog::STATUS_FAILED,
                'error_message' => "Webhook merespons dengan kode HTTP {$response->status()}",
                'sent_at' => now(),
            ]);
        } catch (Throwable $e) {
            return NotificationLog::create([
                'channel' => NotificationLog::CHANNEL_WEBHOOK,
                'title' => $title,
                'message' => $message,
                'category' => $category,
                'level' => $level,
                'province_id' => $provinceId,
                'recipient_target' => $url,
                'status' => NotificationLog::STATUS_FAILED,
                'error_message' => 'Gagal menghubungi URL webhook: ' . $e->getMessage(),
                'sent_at' => now(),
            ]);
        }
    }

    private function isCategoryEnabled(NotificationSetting $setting, string $category, array $metadata): bool
    {
        return match ($category) {
            'hotspot' => $setting->notify_hotspots &&
                ($metadata['confidence'] ?? 100) >= $setting->hotspot_min_confidence &&
                ($metadata['frp'] ?? 100.0) >= $setting->hotspot_min_frp,
            'air_quality' => $setting->notify_air_quality &&
                ($metadata['pm25'] ?? 1000.0) >= $setting->air_quality_min_pm25,
            'announcement' => $setting->notify_announcements &&
                ($metadata['level'] ?? 3) >= $setting->announcement_min_level,
            'test' => true,
            default => true,
        };
    }

    private function isRateLimited(NotificationSetting $setting, string $category, ?int $provinceId): bool
    {
        $minutes = max(1, $setting->frequency_limit_minutes);
        $threshold = now()->subMinutes($minutes);

        return NotificationLog::query()
            ->where('category', $category)
            ->where('status', NotificationLog::STATUS_SENT)
            ->when($provinceId !== null, fn ($q) => $q->where('province_id', $provinceId))
            ->where('sent_at', '>=', $threshold)
            ->exists();
    }

    private function logSuppression(string $category, string $title, string $message, string $level, ?int $provinceId, string $reason): void
    {
        NotificationLog::create([
            'channel' => NotificationLog::CHANNEL_SYSTEM,
            'title' => $title,
            'message' => $message,
            'category' => $category,
            'level' => $level,
            'province_id' => $provinceId,
            'status' => NotificationLog::STATUS_SUPPRESSED,
            'error_message' => $reason,
            'sent_at' => now(),
        ]);
    }
}
