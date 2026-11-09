<?php

namespace App\Http\Controllers;

use App\Models\Hotspot;
use App\Models\Province;
use App\Models\SyncLog;
use App\Services\Integrations\FirmsHotspotSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class HotspotMapController extends Controller
{
    /** Jumlah maksimum titik yang diambil per permintaan. */
    private const MAX_POINTS = 3000;

    /** Batas koordinat geografis wilayah 5 provinsi Kalimantan (SW, NE). */
    private const KALIMANTAN_BOUNDS = [
        [-4.4, 108.8], // Ujung Barat Daya (Selatan Kalsel/Kalteng, Barat Kalbar)
        [4.5, 119.2],  // Ujung Timur Laut (Utara Kaltara, Timur Kaltim)
    ];

    /**
     * Tampilkan halaman Peta Hotspot.
     * Filter awal dibaca dari query string agar URL bisa dibagikan.
     */
    public function index(Request $request): Response
    {
        $provinces = Province::query()->orderBy('name')->get(['id', 'name', 'slug', 'center_lat', 'center_lng']);

        $filter = [
            'provinsi' => $request->query('provinsi', ''),
            'dari' => $this->sanitizeDate($request->query('dari'))?->format('Y-m-d') ?? '',
            'sampai' => $this->sanitizeDate($request->query('sampai'))?->format('Y-m-d') ?? '',
        ];

        $activeProvince = $provinces->firstWhere('slug', $filter['provinsi']);

        // Meta pembaruan terakhir seluruh data hotspot
        $lastUpdatedAt = Hotspot::query()->max('detected_at');
        $totalHotspots = Hotspot::query()->count();

        // Cari catatan sinkronisasi satelit NASA FIRMS terakhir
        $latestSync = SyncLog::query()
            ->whereIn('source_key', [FirmsHotspotSyncService::SOURCE_KEY, 'firms'])
            ->latest('id')
            ->first();

        $syncStatus = $this->determineSyncStatus($latestSync, $lastUpdatedAt, $totalHotspots);

        return Inertia::render('Public/HotspotMap', [
            'provinces' => $provinces,
            'filter' => $filter,
            'activeProvince' => $activeProvince,
            'kalimantanBounds' => self::KALIMANTAN_BOUNDS,
            'lastUpdatedAt' => $lastUpdatedAt ? Carbon::parse($lastUpdatedAt)->toIso8601String() : null,
            'syncStatus' => $syncStatus,
            'maxPoints' => self::MAX_POINTS,
            'apiEndpoint' => route('hotspot.map.data'),
        ]);
    }

    /**
     * Tentukan status pembaruan data secara jujur dan transparan berdasarkan kondisi aktual.
     */
    private function determineSyncStatus(?SyncLog $latestSync, ?string $lastUpdatedAt, int $totalHotspots): array
    {
        if ($totalHotspots === 0 && ! $latestSync) {
            return [
                'code' => 'unavailable',
                'label' => 'Data belum tersedia',
                'description' => 'Belum ada rekaman titik panas yang terdaftar di basis data.',
                'lastSyncAt' => null,
                'lastObservationAt' => null,
            ];
        }

        if ($latestSync) {
            if (in_array($latestSync->status, [SyncLog::STATUS_FAILED, SyncLog::STATUS_TIMEOUT], true)) {
                return [
                    'code' => 'failed',
                    'label' => 'Sinkronisasi gagal',
                    'description' => 'Proses sinkronisasi telemetri satelit terakhir mengalami kendala koneksi.',
                    'lastSyncAt' => $latestSync->completed_at ? Carbon::parse($latestSync->completed_at)->toIso8601String() : null,
                    'lastObservationAt' => $lastUpdatedAt ? Carbon::parse($lastUpdatedAt)->toIso8601String() : null,
                ];
            }

            if ($latestSync->status === SyncLog::STATUS_RUNNING) {
                return [
                    'code' => 'pending',
                    'label' => 'Pembaruan tertunda',
                    'description' => 'Proses penarikan telemetri satelit sedang berlangsung.',
                    'lastSyncAt' => $latestSync->started_at ? Carbon::parse($latestSync->started_at)->toIso8601String() : null,
                    'lastObservationAt' => $lastUpdatedAt ? Carbon::parse($lastUpdatedAt)->toIso8601String() : null,
                ];
            }
        }

        if ($lastUpdatedAt) {
            $parsedDate = Carbon::parse($lastUpdatedAt);
            $hoursAgo = $parsedDate->diffInHours(now());

            if ($hoursAgo <= 48) {
                return [
                    'code' => 'latest',
                    'label' => 'Data terbaru',
                    'description' => 'Telemetri satelit termutakhir terdaftar dalam 48 jam terakhir.',
                    'lastSyncAt' => $latestSync?->completed_at ? Carbon::parse($latestSync->completed_at)->toIso8601String() : null,
                    'lastObservationAt' => $parsedDate->toIso8601String(),
                ];
            }

            return [
                'code' => 'outdated',
                'label' => 'Data sudah lama',
                'description' => 'Observasi terakhir tercatat pada ' . $parsedDate->timezone('Asia/Makassar')->translatedFormat('d M Y, H:i') . ' WITA.',
                'lastSyncAt' => $latestSync?->completed_at ? Carbon::parse($latestSync->completed_at)->toIso8601String() : null,
                'lastObservationAt' => $parsedDate->toIso8601String(),
            ];
        }

        return [
            'code' => 'unavailable',
            'label' => 'Data belum tersedia',
            'description' => 'Tidak ditemukan rekaman data hotspot pada sistem.',
            'lastSyncAt' => null,
            'lastObservationAt' => null,
        ];
    }

    /**
     * Endpoint JSON untuk peta. Filter divalidasi di server.
     */
    public function data(Request $request): JsonResponse
    {
        $provinceSlug = trim((string) $request->query('provinsi', ''));
        $start = $this->sanitizeDate($request->query('dari'));
        $end = $this->sanitizeDate($request->query('sampai'));

        // Validasi provinsi
        $provinceId = null;
        if ($provinceSlug !== '') {
            $provinceId = Province::query()->where('slug', $provinceSlug)->value('id');
            if ($provinceId === null) {
                return response()->json([
                    'error' => 'Provinsi tidak dikenali.',
                    'data' => [],
                    'meta' => ['total' => 0],
                ], 422);
            }
            $provinceId = (int) $provinceId;
        }

        // Rentang tanggal valid
        if ($start && $end && $start->gt($end)) {
            return response()->json([
                'error' => 'Tanggal "dari" tidak boleh setelah "sampai".',
                'data' => [],
                'meta' => ['total' => 0],
            ], 422);
        }

        $query = Hotspot::query()
            ->select(['id', 'province_id', 'latitude', 'longitude', 'confidence', 'frp', 'source', 'sensor', 'city', 'detected_at'])
            ->orderByDesc('detected_at')
            ->limit(self::MAX_POINTS);

        if ($provinceId !== null) {
            $query->where('province_id', $provinceId);
        }
        if ($start) {
            $query->where('detected_at', '>=', $start->startOfDay());
        }
        if ($end) {
            $query->where('detected_at', '<=', $end->endOfDay());
        }

        $hotspots = $query->get();

        $provinceNames = Province::query()->pluck('name', 'id');

        $points = $hotspots->map(function (Hotspot $h) use ($provinceNames) {
            return [
                'id' => $h->id,
                'lat' => $h->latitude,
                'lng' => $h->longitude,
                'confidence' => $h->confidence,
                'frp' => $h->frp,
                'source' => $h->source,
                'sensor' => $h->sensor,
                'city' => $h->city,
                'province' => $provinceNames[$h->province_id] ?? null,
                'detected_at' => $h->detected_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'data' => $points,
            'meta' => [
                'total' => $points->count(),
                'province_id' => $provinceId,
                'province_name' => $provinceId !== null ? ($provinceNames[$provinceId] ?? null) : null,
                'updated_at' => Hotspot::query()->max('detected_at'),
                'source' => 'demo',
                'attribution' => 'Data DEMO FireAware — bukan bukti pasti kebakaran.',
            ],
        ]);
    }

    /**
     * Bersihkan input tanggal menjadi Carbon (null bila tidak valid / kosong).
     */
    private function sanitizeDate($value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', trim($value));
            // Pastikan tanggal benar-benar valid (mis. bukan 2026-13-40)
            if (! $date || $date->format('Y-m-d') !== trim($value)) {
                return null;
            }

            return $date;
        } catch (\Throwable) {
            return null;
        }
    }
}

