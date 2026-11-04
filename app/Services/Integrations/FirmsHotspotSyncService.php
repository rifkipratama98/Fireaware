<?php

namespace App\Services\Integrations;

use App\Models\Hotspot;
use App\Models\Province;
use App\Models\SyncLog;
use App\Services\Integrations\Contracts\SyncServiceInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FirmsHotspotSyncService implements SyncServiceInterface
{
    public const SOURCE_KEY = 'nasa_firms';
    public const SOURCE_NAME = 'NASA FIRMS (Titik Panas Satelit)';
    public const PROVIDER_NAME = 'NASA FIRMS / VIIRS & MODIS NRT Telemetry';
    public const ICON = 'bi-fire';
    public const DEFAULT_AREA = '108.5,-4.5,119.5,4.5';

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
        return filled(config('services.firms.map_key'));
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
                'error_message' => 'Kunci akses NASA FIRMS (FIRMS_MAP_KEY) belum dikonfigurasi pada environment server.',
                'trigger' => $trigger,
                'user_id' => $userId,
            ]);

            return SyncResult::notConfigured(
                sourceKey: self::SOURCE_KEY,
                sourceName: self::SOURCE_NAME,
                reason: 'NASA FIRMS MAP_KEY belum dikonfigurasi pada environment.',
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

        $mapKey = (string) config('services.firms.map_key');
        $baseUrl = rtrim((string) config('services.firms.url', 'https://firms.modaps.eosdis.nasa.gov/api/area/csv'), '/');
        $source = (string) config('services.firms.source', 'VIIRS_SNPP_NRT');
        $area = (string) config('services.firms.area', self::DEFAULT_AREA);
        $dayRange = (int) config('services.firms.day_range', 1);
        $endpoint = "{$baseUrl}/{$mapKey}/{$source}/{$area}/{$dayRange}";

        try {
            $response = Http::timeout(15)->retry(2, 500, throw: false)->get($endpoint);
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);

            if (! $response->successful()) {
                $status = $response->status();
                $body = $response->body();
                $sanitized = $this->sanitizeErrorMessage("HTTP {$status}: " . substr(strip_tags($body), 0, 180), $mapKey);
                $log->update(['status' => SyncLog::STATUS_FAILED, 'completed_at' => now(), 'duration_ms' => $durationMs, 'error_message' => $sanitized]);
                return SyncResult::failed(self::SOURCE_KEY, self::SOURCE_NAME, $sanitized, $log);
            }

            $csvBody = trim($response->body());
            if (str_starts_with(strtolower($csvBody), 'invalid map_key') || str_starts_with(strtolower($csvBody), 'error:')) {
                $sanitized = $this->sanitizeErrorMessage("NASA FIRMS: {$csvBody}", $mapKey);
                $log->update(['status' => SyncLog::STATUS_FAILED, 'completed_at' => now(), 'duration_ms' => $durationMs, 'error_message' => $sanitized]);
                return SyncResult::failed(self::SOURCE_KEY, self::SOURCE_NAME, $sanitized, $log);
            }

            return $this->processCsvBody($csvBody, $source, $log, $startTime, $durationMs);
        } catch (ConnectionException $e) {
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            $msg = 'Batas waktu koneksi (timeout) terlampaui saat menghubungi server NASA FIRMS.';
            $log->update(['status' => SyncLog::STATUS_TIMEOUT, 'completed_at' => now(), 'duration_ms' => $durationMs, 'error_message' => $msg]);
            Log::warning('NASA FIRMS sync timeout.', ['error' => $e->getMessage()]);
            return SyncResult::timeout(self::SOURCE_KEY, self::SOURCE_NAME, $msg, $log);
        } catch (Throwable $e) {
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            $msg = 'Kesalahan saat sinkronisasi data NASA FIRMS: ' . $this->sanitizeErrorMessage($e->getMessage(), $mapKey);
            $log->update(['status' => SyncLog::STATUS_FAILED, 'completed_at' => now(), 'duration_ms' => $durationMs, 'error_message' => $msg]);
            Log::error('NASA FIRMS sync exception.', ['error' => $e->getMessage()]);
            return SyncResult::failed(self::SOURCE_KEY, self::SOURCE_NAME, $msg, $log);
        }
    }

    private function processCsvBody(string $csvBody, string $source, SyncLog $log, float $startTime, int $durationMs): SyncResult
    {
        $lines = preg_split('/\r\n|\r|\n/', $csvBody);
        if (! $lines || count($lines) <= 1) {
            $log->update([
                'status' => SyncLog::STATUS_SUCCESS,
                'records_processed' => 0,
                'records_added' => 0,
                'records_updated' => 0,
                'observation_at' => now(),
                'completed_at' => now(),
                'duration_ms' => $durationMs,
                'payload_summary' => 'Tidak ada titik panas aktif terdeteksi untuk area Kalimantan.',
            ]);

            return SyncResult::success(self::SOURCE_KEY, self::SOURCE_NAME, 0, 0, 0, now(), $log, '0 titik panas terdeteksi.');
        }

        $header = str_getcsv(array_shift($lines));
        $header = array_map(fn ($h) => strtolower(trim((string) $h)), $header);

        $provinces = Province::all();
        $added = 0;
        $updated = 0;
        $processed = 0;
        $latestObs = null;

        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }

            $row = str_getcsv($line);
            if (count($row) < count($header)) {
                continue;
            }

            $data = array_combine($header, array_slice($row, 0, count($header)));
            if (! isset($data['latitude'], $data['longitude'])) {
                continue;
            }

            $lat = (float) $data['latitude'];
            $lng = (float) $data['longitude'];

            if ($lat < -4.5 || $lat > 4.5 || $lng < 108.5 || $lng > 119.5) {
                continue;
            }

            $processed++;

            $detectedAt = $this->parseAcquisitionDateTime($data['acq_date'] ?? null, $data['acq_time'] ?? null);
            if ($detectedAt && ($latestObs === null || $detectedAt->gt($latestObs))) {
                $latestObs = $detectedAt;
            }

            $confidence = $this->parseConfidence($data['confidence'] ?? null);
            $frp = isset($data['frp']) && is_numeric($data['frp']) ? (float) $data['frp'] : null;
            $sensor = $this->resolveSensorName($data['satellite'] ?? null, $data['instrument'] ?? null, $source);
            $provinceId = $this->resolveProvinceId($lat, $lng, $provinces);

            // Cegah duplikasi data
            $existing = Hotspot::query()
                ->where('source', 'nasa_firms')
                ->where('detected_at', $detectedAt)
                ->whereRaw('ABS(latitude - ?) < 0.0001', [$lat])
                ->whereRaw('ABS(longitude - ?) < 0.0001', [$lng])
                ->first();

            if ($existing) {
                $existing->update([
                    'confidence' => $confidence,
                    'frp' => $frp,
                    'sensor' => $sensor,
                    'province_id' => $provinceId ?? $existing->province_id,
                ]);
                $updated++;
            } else {
                Hotspot::create([
                    'source' => 'nasa_firms',
                    'sensor' => $sensor,
                    'province_id' => $provinceId,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'confidence' => $confidence,
                    'frp' => $frp,
                    'detected_at' => $detectedAt,
                ]);
                $added++;
            }
        }

        Cache::forget('hotspot.map.data');
        Cache::forget('home.summary');

        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        $log->update([
            'status' => SyncLog::STATUS_SUCCESS,
            'records_processed' => $processed,
            'records_added' => $added,
            'records_updated' => $updated,
            'observation_at' => $latestObs ?? now(),
            'completed_at' => now(),
            'duration_ms' => $durationMs,
            'payload_summary' => "Berhasil memproses {$processed} rekaman ({$added} baru, {$updated} diperbarui).",
        ]);

        return SyncResult::success(
            self::SOURCE_KEY,
            self::SOURCE_NAME,
            $processed,
            $added,
            $updated,
            $latestObs ?? now(),
            $log,
            "{$added} titik baru ditambahkan, {$updated} diperbarui."
        );
    }

    public function getStatusSummary(): array
    {
        $lastLog = SyncLog::forSource(self::SOURCE_KEY)->latestFirst()->first();
        $totalRecords = Hotspot::where('source', 'nasa_firms')->count();
        $fallbackTotal = Hotspot::count();
        $latestDetection = Hotspot::max('detected_at');
        $isConfigured = $this->isConfigured();

        $errors = [];
        if (! $isConfigured) {
            $errors[] = 'Kunci akses FIRMS_MAP_KEY belum disetel pada konfigurasi (.env).';
        } elseif ($lastLog && $lastLog->status === SyncLog::STATUS_FAILED) {
            $errors[] = $lastLog->error_message ?? 'Sinkronisasi terakhir gagal.';
        } elseif ($lastLog && $lastLog->status === SyncLog::STATUS_TIMEOUT) {
            $errors[] = 'Koneksi ke NASA FIRMS mengalami batas waktu (timeout).';
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
            'last_observation_at' => $lastLog?->observation_at ?? ($latestDetection ? Carbon::parse($latestDetection) : null),
            'last_log' => $lastLog,
            'records_count' => $totalRecords > 0 ? $totalRecords : $fallbackTotal,
            'records_added_last' => $lastLog?->records_added ?? 0,
            'records_updated_last' => $lastLog?->records_updated ?? 0,
            'errors' => $errors,
            'meta' => [
                'Cakupan Wilayah' => 'Batas Koordinat Kalimantan',
                'Sensor Utama' => config('services.firms.source', 'VIIRS_SNPP_NRT'),
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

    private function parseAcquisitionDateTime(?string $acqDate, ?string $acqTime): ?Carbon
    {
        if (! $acqDate) {
            return null;
        }

        try {
            $timeStr = '00:00';
            if ($acqTime !== null && $acqTime !== '') {
                $pad = str_pad(trim($acqTime), 4, '0', STR_PAD_LEFT);
                $timeStr = substr($pad, 0, 2) . ':' . substr($pad, 2, 2);
            }

            return Carbon::createFromFormat('Y-m-d H:i', "{$acqDate} {$timeStr}", 'UTC');
        } catch (Throwable) {
            return null;
        }
    }

    private function parseConfidence(mixed $val): ?int
    {
        if ($val === null || $val === '') {
            return null;
        }

        if (is_numeric($val)) {
            return max(0, min(100, (int) $val));
        }

        $c = strtolower(trim((string) $val));
        return match ($c) {
            'l', 'low' => 30,
            'n', 'nominal', 'med' => 65,
            'h', 'high' => 90,
            default => 50,
        };
    }

    private function resolveSensorName(?string $satellite, ?string $instrument, string $source): string
    {
        if ($satellite && $instrument) {
            return "{$instrument} ({$satellite})";
        }

        return match ($source) {
            'VIIRS_SNPP_NRT' => 'VIIRS S-NPP',
            'VIIRS_NOAA20_NRT' => 'VIIRS NOAA-20',
            'MODIS_NRT' => 'MODIS NRT',
            default => 'Satelit Polar Earth',
        };
    }

    private function resolveProvinceId(float $lat, float $lng, $provinces): ?int
    {
        $closestId = null;
        $shortestDist = PHP_FLOAT_MAX;

        foreach ($provinces as $prov) {
            if ($prov->center_lat === null || $prov->center_lng === null) {
                continue;
            }

            $dist = pow($lat - (float) $prov->center_lat, 2) + pow($lng - (float) $prov->center_lng, 2);
            if ($dist < $shortestDist) {
                $shortestDist = $dist;
                $closestId = $prov->id;
            }
        }

        return $closestId ?? $provinces->first()?->id;
    }

    private function sanitizeErrorMessage(string $message, string $secret): string
    {
        if ($secret !== '') {
            $message = str_replace($secret, '***KEY***', $message);
        }

        return $message;
    }
}
