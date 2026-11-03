<?php

namespace App\Http\Controllers;

use App\Models\Hotspot;
use App\Models\Province;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HotspotMapController extends Controller
{
    /** Jumlah maksimum titik yang diambil per permintaan. */
    private const MAX_POINTS = 3000;

    /**
     * Tampilkan halaman Peta Hotspot.
     * Filter awal dibaca dari query string agar URL bisa dibagikan.
     */
    public function index(Request $request): View
    {
        $provinces = Province::query()->orderBy('name')->get(['id', 'name', 'slug']);

        $filter = [
            'provinsi' => $request->query('provinsi', ''),
            'dari' => $this->sanitizeDate($request->query('dari')),
            'sampai' => $this->sanitizeDate($request->query('sampai')),
        ];

        $activeProvince = $provinces->firstWhere('slug', $filter['provinsi']);

        // Meta pembaruan terakhir seluruh data
        $lastUpdatedAt = Hotspot::query()->max('detected_at');

        return view('peta-hotspot', [
            'provinces' => $provinces,
            'filter' => $filter,
            'activeProvince' => $activeProvince,
            'lastUpdatedAt' => $lastUpdatedAt ? \Illuminate\Support\Carbon::parse($lastUpdatedAt) : null,
            'maxPoints' => self::MAX_POINTS,
        ]);
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
    private function sanitizeDate($value): ?\Illuminate\Support\Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            $date = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', trim($value));
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
