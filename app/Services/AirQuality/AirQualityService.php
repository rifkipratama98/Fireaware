<?php

namespace App\Services\AirQuality;

use App\Models\AirQualityReading;
use App\Models\AirQualityStation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Integrasi sumber data kualitas udara eksternal.
 *
 * Sumber default: OpenAQ v3 (butuh API key). Selama API key belum dikonfigurasi,
 * service ini TIDAK membuat nilai apa pun — ia mengembalikan status
 * "not_configured" agar UI menampilkan "data belum tersedia" secara jujur.
 */
class AirQualityService
{
    public const STATUS_OK = 'ok';
    public const STATUS_NOT_CONFIGURED = 'not_configured';
    public const STATUS_ERROR = 'error';

    private const CACHE_TTL = 600; // 10 menit

    public function isConfigured(): bool
    {
        return filled(config('services.openaq.key'));
    }

    /**
     * @return array{status:string,reading:?AirQualityReading,message:?string}
     */
    public function latestForStation(AirQualityStation $station): array
    {
        $cached = Cache::remember("aq.latest.{$station->id}", self::CACHE_TTL, function () use ($station) {
            $reading = $station->latestReading('pm25');

            return [
                'status' => $reading ? self::STATUS_OK : self::STATUS_NOT_CONFIGURED,
                'reading_id' => $reading?->id,
                'message' => $reading ? null : 'Belum ada data pengukuran untuk stasiun ini.',
            ];
        });

        if ($cached['reading_id'] === null) {
            return [
                'status' => $cached['status'],
                'reading' => null,
                'message' => $cached['message'],
            ];
        }

        // Model tidak disimpan di cache (hindari serialisasi Eloquent yang rapuh)
        $reading = AirQualityReading::query()->find($cached['reading_id']);

        return [
            'status' => $reading ? self::STATUS_OK : self::STATUS_ERROR,
            'reading' => $reading,
            'message' => $reading ? null : 'Data pembacaan tidak dapat dimuat.',
        ];
    }

    /**
     * @return array{available:bool,points:array<int,array{t:string,v:float}>,message:?string}
     */
    public function historyForStation(AirQualityStation $station, int $days = 7): array
    {
        $days = max(1, min($days, 30));

        $rows = $station->readings()
            ->where('pollutant', 'pm25')
            ->where('measured_at', '>=', now()->subDays($days))
            ->orderBy('measured_at')
            ->get(['measured_at', 'value']);

        // Minimal 3 titik agar tren bermakna
        if ($rows->count() < 3) {
            return [
                'available' => false,
                'points' => [],
                'message' => 'Riwayat pengukuran belum cukup untuk menampilkan grafik tren.',
            ];
        }

        return [
            'available' => true,
            'points' => $rows->map(fn ($r) => [
                't' => $r->measured_at?->toIso8601String(),
                'v' => (float) $r->value,
            ])->all(),
            'message' => null,
        ];
    }

    /**
     * @return array{status:string,saved:int,message:string}
     */
    public function syncStation(AirQualityStation $station): array
    {
        if (! $this->isConfigured()) {
            Log::info('AirQuality sync dilewati: OPENAQ_API_KEY belum dikonfigurasi.', [
                'station' => $station->slug,
            ]);

            return [
                'status' => self::STATUS_NOT_CONFIGURED,
                'saved' => 0,
                'message' => 'Sumber data eksternal belum dikonfigurasi.',
            ];
        }

        if (! $station->external_id) {
            return [
                'status' => self::STATUS_ERROR,
                'saved' => 0,
                'message' => 'Stasiun belum memiliki ID eksternal.',
            ];
        }

        $url = rtrim((string) config('services.openaq.url', 'https://api.openaq.org/v3'), '/')
            . '/locations/' . $station->external_id . '/latest';

        try {
            $response = Http::withHeaders(['X-API-Key' => config('services.openaq.key')])
                ->timeout(10)
                ->acceptJson()
                ->get($url);

            if (! $response->successful()) {
                Log::warning('AirQuality sync gagal: respons tidak sukses.', [
                    'station' => $station->slug,
                    'status' => $response->status(),
                ]);

                return [
                    'status' => self::STATUS_ERROR,
                    'saved' => 0,
                    'message' => 'Sumber data tidak merespons dengan benar.',
                ];
            }

            $saved = $this->storeFromPayload($station, $response->json());

            return [
                'status' => self::STATUS_OK,
                'saved' => $saved,
                'message' => $saved > 0 ? 'Data berhasil disimpan.' : 'Tidak ada nilai PM2.5 valid.',
            ];
        } catch (Throwable $e) {
            Log::error('AirQuality sync exception.', [
                'station' => $station->slug,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => self::STATUS_ERROR,
                'saved' => 0,
                'message' => 'Terjadi kesalahan saat mengambil data.',
            ];
        }
    }

    /**
     * Validasi payload lalu simpan. Nilai di luar rentang wajar ditolak.
     */
    private function storeFromPayload(AirQualityStation $station, array $payload): int
    {
        $saved = 0;
        $results = $payload['results'] ?? [];

        foreach ($results as $row) {
            $parameter = strtolower((string) ($row['parameter'] ?? ''));
            if (! in_array($parameter, ['pm25', 'pm2.5'], true)) {
                continue;
            }

            $value = $row['value'] ?? null;
            if (! is_numeric($value)) {
                continue;
            }
            $value = (float) $value;

            // Validasi rentang wajar PM2.5 (µg/m³): 0–1000
            if ($value < 0 || $value > 1000) {
                Log::warning('AirQuality nilai PM2.5 di luar rentang wajar, dilewati.', [
                    'station' => $station->slug,
                    'value' => $value,
                ]);
                continue;
            }

            $measuredAt = $this->parseDate($row['datetime']['utc'] ?? $row['datetime']['local'] ?? null);
            if ($measuredAt === null) {
                continue;
            }

            $category = AirQualityCategory::forPm25($value);

            AirQualityReading::query()->updateOrCreate(
                [
                    'air_quality_station_id' => $station->id,
                    'pollutant' => 'pm25',
                    'measured_at' => $measuredAt,
                ],
                [
                    'value' => round($value, 2),
                    'unit' => 'µg/m³',
                    'aqi_value' => null, // tidak dihitung ulang: sumber tidak menyediakannya
                    'aqi_category' => $category['label'],
                    'standard' => 'ISPU',
                    'fetched_at' => now(),
                    'source' => $station->source,
                ]
            );

            $saved++;
        }

        if ($saved > 0) {
            Cache::forget("aq.latest.{$station->id}");
        }

        return $saved;
    }

    private function parseDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }
}

