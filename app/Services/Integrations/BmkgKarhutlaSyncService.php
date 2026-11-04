<?php

namespace App\Services\Integrations;

use App\Models\Announcement;
use App\Models\Province;
use App\Models\SyncLog;
use App\Services\Integrations\Contracts\SyncServiceInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class BmkgKarhutlaSyncService implements SyncServiceInterface
{
    public const SOURCE_KEY = 'bmkg_karhutla';
    public const SOURCE_NAME = 'BMKG Karhutla & Cuaca Ekstrem';
    public const PROVIDER_NAME = 'Badan Meteorologi, Klimatologi, dan Geofisika (BMKG)';
    public const ICON = 'bi-cloud-lightning-rain';

    public function getSourceKey(): string
    {
        return self::SOURCE_KEY;
    }

    public function getSourceName(): string
    {
        return self::SOURCE_NAME;
    }

    public function getProviderName(): string
    {
        return self::PROVIDER_NAME;
    }

    public function getIcon(): string
    {
        return self::ICON;
    }

    public function isConfigured(): bool
    {
        return filled(config('services.bmkg.karhutla_url'));
    }

    public function sync(string $trigger = 'manual', ?int $userId = null): SyncResult
    {
        $startTime = microtime(true);
        $startedAt = now();

        if (! $this->isConfigured()) {
            $log = SyncLog::create([
                'source_key' => self::SOURCE_KEY,
                'source_name' => self::SOURCE_NAME,
                'status' => SyncLog::STATUS_NOT_CONFIGURED,
                'records_processed' => 0,
                'records_added' => 0,
                'records_updated' => 0,
                'started_at' => $startedAt,
                'completed_at' => now(),
                'duration_ms' => (int) round((microtime(true) - $startTime) * 1000),
                'error_message' => 'Endpoint BMKG Karhutla (BMKG_KARHUTLA_URL) belum dikonfigurasi pada environment server.',
                'trigger' => $trigger,
                'user_id' => $userId,
            ]);

            return SyncResult::notConfigured(
                sourceKey: self::SOURCE_KEY,
                sourceName: self::SOURCE_NAME,
                reason: 'Endpoint BMKG_KARHUTLA_URL belum disetel pada file environment.',
                log: $log
            );
        }

        $log = SyncLog::create([
            'source_key' => self::SOURCE_KEY,
            'source_name' => self::SOURCE_NAME,
            'status' => SyncLog::STATUS_RUNNING,
            'records_processed' => 0,
            'started_at' => $startedAt,
            'trigger' => $trigger,
            'user_id' => $userId,
        ]);

        $url = (string) config('services.bmkg.karhutla_url');

        try {
            $response = Http::timeout(12)->retry(2, 500, throw: false)->get($url);
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);

            if (! $response->successful()) {
                $status = $response->status();
                $msg = "Permintaan data BMKG gagal dengan kode status HTTP {$status}.";
                $log->update(['status' => SyncLog::STATUS_FAILED, 'completed_at' => now(), 'duration_ms' => $durationMs, 'error_message' => $msg]);
                return SyncResult::failed(self::SOURCE_KEY, self::SOURCE_NAME, $msg, $log);
            }

            return $this->parseAndStoreData($response, $log, $startTime);
        } catch (ConnectionException $e) {
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            $msg = 'Batas waktu koneksi (timeout) terlampaui saat menghubungi server BMKG.';
            $log->update(['status' => SyncLog::STATUS_TIMEOUT, 'completed_at' => now(), 'duration_ms' => $durationMs, 'error_message' => $msg]);
            Log::warning('BMKG Karhutla sync timeout.', ['error' => $e->getMessage()]);
            return SyncResult::timeout(self::SOURCE_KEY, self::SOURCE_NAME, $msg, $log);
        } catch (Throwable $e) {
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            $msg = 'Terjadi kesalahan sistem saat sinkronisasi data BMKG: ' . $e->getMessage();
            $log->update(['status' => SyncLog::STATUS_FAILED, 'completed_at' => now(), 'duration_ms' => $durationMs, 'error_message' => $msg]);
            Log::error('BMKG Karhutla sync exception.', ['error' => $e->getMessage()]);
            return SyncResult::failed(self::SOURCE_KEY, self::SOURCE_NAME, $msg, $log);
        }
    }

    private function parseAndStoreData($response, SyncLog $log, float $startTime): SyncResult
    {
        $raw = trim($response->body());
        $items = [];
        $latestObs = null;

        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            $items = $json['data'] ?? $json['items'] ?? $json['peringatan'] ?? (isset($json[0]) ? $json : [$json]);
            if (isset($json['waktu']) || isset($json['tanggal'])) {
                $latestObs = $this->parseDate($json['waktu'] ?? $json['tanggal']);
            }
        } else {
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($raw);
            if ($xml !== false) {
                $jsonXml = json_decode(json_encode($xml), true);
                $items = $jsonXml['item'] ?? $jsonXml['data'] ?? $jsonXml['info'] ?? [$jsonXml];
                if (isset($jsonXml['tanggal'])) {
                    $latestObs = $this->parseDate($jsonXml['tanggal'] . ' ' . ($jsonXml['jam'] ?? '00:00:00'));
                }
            } else {
                $durationMs = (int) round((microtime(true) - $startTime) * 1000);
                $msg = 'Format respons dari BMKG tidak valid (bukan JSON maupun XML).';
                $log->update(['status' => SyncLog::STATUS_FAILED, 'completed_at' => now(), 'duration_ms' => $durationMs, 'error_message' => $msg]);
                return SyncResult::failed(self::SOURCE_KEY, self::SOURCE_NAME, $msg, $log);
            }
        }

        if (empty($items)) {
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            $log->update([
                'status' => SyncLog::STATUS_SUCCESS,
                'records_processed' => 0,
                'records_added' => 0,
                'records_updated' => 0,
                'observation_at' => now(),
                'completed_at' => now(),
                'duration_ms' => $durationMs,
                'payload_summary' => 'Tidak ada buletin atau rilis peringatan cuaca aktif dari BMKG.',
            ]);

            return SyncResult::success(self::SOURCE_KEY, self::SOURCE_NAME, 0, 0, 0, now(), $log, '0 peringatan terdeteksi.');
        }

        $provinces = Province::all();
        $added = 0;
        $updated = 0;
        $processed = 0;

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $title = trim((string) ($item['title'] ?? $item['judul'] ?? $item['peringatan'] ?? ''));
            if ($title === '') {
                continue;
            }

            $body = trim((string) ($item['body'] ?? $item['deskripsi'] ?? $item['uraian'] ?? $item['detail'] ?? $title));
            $summary = trim((string) ($item['summary'] ?? $item['ringkasan'] ?? Str::limit(strip_tags($body), 180)));
            $level = $this->resolveLevel($item['level'] ?? $item['status'] ?? $title);
            $provinceId = $this->matchKalimantanProvince($item, $provinces);

            $publishedAt = $this->parseDate($item['published_at'] ?? $item['tanggal'] ?? $item['datetime'] ?? null) ?? now();
            if ($latestObs === null || $publishedAt->gt($latestObs)) {
                $latestObs = $publishedAt;
            }

            $processed++;
            $slug = Str::slug($title);

            // Cegah duplikasi data
            $existing = Announcement::query()->where('title', $title)->orWhere('slug', $slug)->first();

            if ($existing) {
                $existing->update([
                    'body' => $body,
                    'summary' => $summary,
                    'level' => $level,
                    'province_id' => $provinceId ?? $existing->province_id,
                    'source_name' => 'BMKG',
                    'source_url' => $item['url'] ?? $item['link'] ?? $existing->source_url,
                    'status' => Announcement::STATUS_PUBLISHED,
                    'is_active' => true,
                    'published_at' => $publishedAt,
                ]);
                $updated++;
            } else {
                Announcement::create([
                    'title' => $title,
                    'slug' => $slug,
                    'summary' => $summary,
                    'body' => $body,
                    'level' => $level,
                    'province_id' => $provinceId,
                    'source_name' => 'BMKG',
                    'source_url' => $item['url'] ?? $item['link'] ?? null,
                    'status' => Announcement::STATUS_PUBLISHED,
                    'is_active' => true,
                    'is_demo' => false,
                    'published_at' => $publishedAt,
                ]);
                $added++;
            }
        }

        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        $log->update([
            'status' => SyncLog::STATUS_SUCCESS,
            'records_processed' => $processed,
            'records_added' => $added,
            'records_updated' => $updated,
            'observation_at' => $latestObs ?? now(),
            'completed_at' => now(),
            'duration_ms' => $durationMs,
            'payload_summary' => "Berhasil memproses {$processed} peringatan BMKG ({$added} baru, {$updated} diperbarui).",
        ]);

        return SyncResult::success(
            self::SOURCE_KEY,
            self::SOURCE_NAME,
            $processed,
            $added,
            $updated,
            $latestObs ?? now(),
            $log,
            "{$added} peringatan baru tersinkronisasi, {$updated} diperbarui."
        );
    }

    public function getStatusSummary(): array
    {
        $lastLog = SyncLog::forSource(self::SOURCE_KEY)->latestFirst()->first();
        $isConfigured = $this->isConfigured();
        $totalAnnouncements = Announcement::where('source_name', 'like', '%BMKG%')->count();

        $errors = [];
        if (! $isConfigured) {
            $errors[] = 'Endpoint BMKG_KARHUTLA_URL belum disetel pada file environment (.env).';
        } elseif ($lastLog && $lastLog->status === SyncLog::STATUS_FAILED) {
            $errors[] = $lastLog->error_message ?? 'Sinkronisasi BMKG terakhir gagal.';
        } elseif ($lastLog && $lastLog->status === SyncLog::STATUS_TIMEOUT) {
            $errors[] = 'Koneksi ke BMKG mengalami batas waktu (timeout).';
        }

        return [
            'key' => self::SOURCE_KEY,
            'name' => self::SOURCE_NAME,
            'provider' => self::PROVIDER_NAME,
            'icon' => self::ICON,
            'is_configured' => $isConfigured,
            'connection_status' => $isConfigured ? 'Terkonfigurasi' : 'Belum Dikonfigurasi',
            'sync_status' => $this->deriveSyncStatus($lastLog),
            'last_sync_at' => $lastLog?->completed_at ?? $lastLog?->created_at,
            'last_observation_at' => $lastLog?->observation_at ?? Announcement::where('source_name', 'like', '%BMKG%')->max('published_at'),
            'last_log' => $lastLog,
            'records_count' => $totalAnnouncements,
            'records_added_last' => $lastLog?->records_added ?? 0,
            'records_updated_last' => $lastLog?->records_updated ?? 0,
            'errors' => $errors,
            'meta' => [
                'Wilayah Pantauan' => '5 Provinsi Kalimantan',
                'Kanal Peringatan' => 'Cuaca Ekstrem, Indeks FWI & HTH',
            ],
        ];
    }

    private function deriveSyncStatus(?SyncLog $lastLog): string
    {
        if (! $lastLog) {
            return 'Belum Pernah Sinkron';
        }

        return match ($lastLog->status) {
            SyncLog::STATUS_SUCCESS => 'Berhasil (' . $lastLog->created_at->diffForHumans() . ')',
            SyncLog::STATUS_FAILED => 'Gagal',
            SyncLog::STATUS_TIMEOUT => 'Timeout',
            SyncLog::STATUS_NOT_CONFIGURED => 'Belum Dikonfigurasi',
            SyncLog::STATUS_RUNNING => 'Sedang Berjalan',
            default => ucfirst((string) $lastLog->status),
        };
    }

    private function resolveLevel(mixed $raw): int
    {
        $str = strtolower((string) $raw);
        if (str_contains($str, 'darurat') || str_contains($str, 'ekstrem') || str_contains($str, 'level-3') || $raw === 3) {
            return Announcement::LEVEL_DARURAT;
        }
        if (str_contains($str, 'waspada') || str_contains($str, 'siaga') || str_contains($str, 'level-2') || $raw === 2) {
            return Announcement::LEVEL_WASPADA;
        }

        return Announcement::LEVEL_INFO;
    }

    private function matchKalimantanProvince(array $item, $provinces): ?int
    {
        $text = strtolower(json_encode($item));

        foreach ($provinces as $prov) {
            $name = strtolower((string) $prov->name);
            $code = strtolower((string) $prov->code);

            if (str_contains($text, $name) || ($code !== '' && str_contains($text, $code))) {
                return $prov->id;
            }
        }

        return null;
    }

    private function parseDate(?string $val): ?Carbon
    {
        if (! $val) {
            return null;
        }

        try {
            return Carbon::parse($val);
        } catch (Throwable) {
            return null;
        }
    }
}
