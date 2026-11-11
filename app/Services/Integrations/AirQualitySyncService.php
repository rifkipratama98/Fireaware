<?php

namespace App\Services\Integrations;

use App\Models\AirQualityReading;
use App\Models\AirQualityStation;
use App\Models\SyncLog;
use App\Services\AirQuality\AirQualityCategory;
use App\Services\Integrations\Contracts\SyncServiceInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class AirQualitySyncService implements SyncServiceInterface
{
    public const SOURCE_KEY = 'air_quality';
    public const SOURCE_NAME = 'Pemantauan Kualitas Udara (OpenAQ/ISPU)';
    public const PROVIDER_NAME = 'OpenAQ v3 / Jaringan Stasiun ISPU Daerah';
    public const ICON = 'bi-cloud-haze2';

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
        return filled(config('services.openaq.key'));
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
                'error_message' => 'Kunci akses OpenAQ API (OPENAQ_API_KEY) belum dikonfigurasi pada environment server.',
                'trigger' => $trigger,
                'user_id' => $userId,
            ]);

            return SyncResult::notConfigured(
                sourceKey: self::SOURCE_KEY,
                sourceName: self::SOURCE_NAME,
                reason: 'Kunci akses OPENAQ_API_KEY belum disetel pada file environment.',
                log: $log
            );
        }

        $stations = AirQualityStation::where('is_active', true)->get();
        if ($stations->isEmpty()) {
            $log = SyncLog::create([
                'source_key' => self::SOURCE_KEY,
                'source_name' => self::SOURCE_NAME,
                'status' => SyncLog::STATUS_FAILED,
                'records_processed' => 0,
                'started_at' => $startedAt,
                'completed_at' => now(),
                'duration_ms' => (int) round((microtime(true) - $startTime) * 1000),
                'error_message' => 'Belum ada stasiun pemantau kualitas udara aktif yang terdaftar di basis data.',
                'trigger' => $trigger,
                'user_id' => $userId,
            ]);

            return SyncResult::failed(
                sourceKey: self::SOURCE_KEY,
                sourceName: self::SOURCE_NAME,
                errorMessage: 'Tidak ada stasiun pemantau kualitas udara aktif.',
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

        $apiKey = (string) config('services.openaq.key');
        $baseUrl = rtrim((string) config('services.openaq.url', 'https://api.openaq.org/v3'), '/');

        return $this->fetchFromStations($stations, $apiKey, $baseUrl, $log, $startTime);
    }

    private function fetchFromStations($stations, string $apiKey, string $baseUrl, SyncLog $log, float $startTime): SyncResult
    {
        $added = 0;
        $updated = 0;
        $processed = 0;
        $latestObs = null;
        $errors = [];

        foreach ($stations as $station) {
            $locationId = $station->external_id ?: $station->id;
            $url = "{$baseUrl}/locations/{$locationId}/latest";

            try {
                $response = Http::withHeaders(['X-API-Key' => $apiKey])
                    ->timeout(12)
                    ->retry(2, 500, throw: false)
                    ->get($url);

                if (! $response->successful()) {
                    $errors[] = "Stasiun {$station->name}: HTTP {$response->status()}";
                    continue;
                }

                $json = $response->json();
                $results = $json['results'] ?? [];

                foreach ($results as $row) {
                    $param = strtolower((string) ($row['parameter'] ?? $row['pollutant'] ?? ''));
                    if (! in_array($param, ['pm25', 'pm2.5'], true)) {
                        continue;
                    }

                    $val = $row['value'] ?? null;
                    if (! is_numeric($val)) {
                        continue;
                    }
                    $val = (float) $val;

                    if ($val < 0 || $val > 1000) {
                        continue;
                    }

                    $measuredAt = $this->parseDate($row['datetime']['utc'] ?? $row['datetime']['local'] ?? $row['datetime'] ?? null);
                    if ($measuredAt === null) {
                        continue;
                    }

                    if ($latestObs === null || $measuredAt->gt($latestObs)) {
                        $latestObs = $measuredAt;
                    }

                    $processed++;
                    $cat = AirQualityCategory::forPm25($val);

                    // Cegah duplikasi data
                    $existing = AirQualityReading::query()
                        ->where('air_quality_station_id', $station->id)
                        ->where('pollutant', 'pm25')
                        ->where('measured_at', $measuredAt)
                        ->first();

                    if ($existing) {
                        $existing->update([
                            'value' => round($val, 2),
                            'unit' => 'µg/m³',
                            'aqi_category' => $cat['label'],
                            'fetched_at' => now(),
                            'source' => $station->source ?? 'openaq',
                        ]);
                        $updated++;
                    } else {
                        AirQualityReading::create([
                            'air_quality_station_id' => $station->id,
                            'pollutant' => 'pm25',
                            'measured_at' => $measuredAt,
                            'value' => round($val, 2),
                            'unit' => 'µg/m³',
                            'aqi_category' => $cat['label'],
                            'standard' => 'ISPU',
                            'fetched_at' => now(),
                            'source' => $station->source ?? 'openaq',
                        ]);
                        $added++;
                    }

                    Cache::forget("aq.latest.{$station->id}");
                }
            } catch (ConnectionException $e) {
                $errors[] = "Stasiun {$station->name}: Batas waktu koneksi (timeout).";
            } catch (Throwable $e) {
                $errors[] = "Stasiun {$station->name}: " . $e->getMessage();
            }
        }

        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        if ($processed === 0 && ! empty($errors)) {
            $msg = 'Gagal mengambil data dari stasiun pemantau: ' . implode('; ', array_slice($errors, 0, 3));
            $isTimeout = str_contains($msg, 'timeout');
            $status = $isTimeout ? SyncLog::STATUS_TIMEOUT : SyncLog::STATUS_FAILED;

            $log->update([
                'status' => $status,
                'completed_at' => now(),
                'duration_ms' => $durationMs,
                'error_message' => $msg,
            ]);

            return $isTimeout
                ? SyncResult::timeout(self::SOURCE_KEY, self::SOURCE_NAME, $msg, $log)
                : SyncResult::failed(self::SOURCE_KEY, self::SOURCE_NAME, $msg, $log);
        }

        $summaryText = "Diproses {$processed} pengukuran ({$added} baru, {$updated} diperbarui) dari " . count($stations) . ' stasiun.';
        $log->update([
            'status' => SyncLog::STATUS_SUCCESS,
            'records_processed' => $processed,
            'records_added' => $added,
            'records_updated' => $updated,
            'observation_at' => $latestObs ?? now(),
            'completed_at' => now(),
            'duration_ms' => $durationMs,
            'payload_summary' => $summaryText,
        ]);

        return SyncResult::success(self::SOURCE_KEY, self::SOURCE_NAME, $processed, $added, $updated, $latestObs ?? now(), $log, $summaryText);
    }

    public function getStatusSummary(): array
    {
        $lastLog = SyncLog::forSource(self::SOURCE_KEY)->latestFirst()->first();
        $isConfigured = $this->isConfigured();
        $totalStations = AirQualityStation::where('is_active', true)->count();
        $totalReadings = AirQualityReading::count();
        $latestReading = AirQualityReading::max('measured_at');

        $errors = [];
        if (! $isConfigured) {
            $errors[] = 'Kunci akses OPENAQ_API_KEY belum disetel pada file environment (.env).';
        } elseif ($totalStations === 0) {
            $errors[] = 'Belum ada stasiun pemantau aktif yang terdaftar di basis data.';
        } elseif ($lastLog && $lastLog->status === SyncLog::STATUS_FAILED) {
            $errors[] = $lastLog->error_message ?? 'Sinkronisasi kualitas udara terakhir gagal.';
        } elseif ($lastLog && $lastLog->status === SyncLog::STATUS_TIMEOUT) {
            $errors[] = 'Koneksi ke penyedia kualitas udara mengalami timeout.';
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
            'last_observation_at' => $lastLog?->observation_at ?? ($latestReading ? Carbon::parse($latestReading) : null),
            'last_log' => $lastLog,
            'records_count' => $totalReadings,
            'records_added_last' => $lastLog?->records_added ?? 0,
            'records_updated_last' => $lastLog?->records_updated ?? 0,
            'errors' => $errors,
            'meta' => [
                'Stasiun Aktif' => "{$totalStations} stasiun terdaftar",
                'Parameter' => 'PM2.5 (µg/m³) & Kategori ISPU',
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
