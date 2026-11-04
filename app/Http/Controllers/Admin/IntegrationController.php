<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AirQualityReading;
use App\Models\AirQualityStation;
use App\Models\Hotspot;
use App\Models\InformationItem;
use App\Models\InformationSource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IntegrationController extends Controller
{
    public function index(): View
    {
        $integrations = [
            $this->hotspotDetails(),
            $this->airQualityDetails(),
            $this->sourceDetails(),
        ];

        return view('admin.integrations.index', compact('integrations'));
    }

    private function hotspotDetails(): array
    {
        $total = Hotspot::count();
        $latest = Hotspot::max('detected_at');
        $fetched = Hotspot::max('created_at');

        $history = $total > 0
            ? Hotspot::where('detected_at', '>=', now()->subDays(14))
                ->select(DB::raw('DATE(detected_at) as tanggal'), DB::raw('COUNT(*) as jumlah'))
                ->groupBy('tanggal')->orderByDesc('tanggal')->get()
                ->map(fn ($r) => ['date' => $r->tanggal, 'count' => $r->jumlah])->all()
            : [];

        $errors = [];
        if ($total === 0) {
            $errors[] = 'Belum ada data titik panas yang tersimpan di basis data.';
        } elseif ($latest && Carbon::parse($latest)->lt(now()->subDays(3))) {
            $errors[] = 'Data deteksi titik panas terakhir berusia lebih dari 3 hari.';
        }

        $syncAt = $fetched ? Carbon::parse($fetched) : null;

        return [
            'id'                => 'hotspot',
            'name'              => 'Titik Panas Satelit',
            'provider'          => 'NASA FIRMS / MODIS & VIIRS Telemetry',
            'icon'              => 'bi-fire',
            'connection_status' => 'Terkonfigurasi',
            'sync_status'       => $this->computeSyncStatus($syncAt),
            'data_status'       => $total > 0 ? 'Tersedia' : 'Kosong',
            'total_records'     => $total,
            'recent_records'    => Hotspot::where('detected_at', '>=', now()->subDays(7))->count(),
            'last_sync_at'      => $syncAt,
            'last_data_at'      => $latest ? Carbon::parse($latest) : null,
            'sync_history'      => $history,
            'errors'            => $errors,
            'meta'              => [
                'Cakupan'       => 'Kalimantan (Lat -4.5 s/d 4.5, Lng 108.5 s/d 119.5)',
                'Protokol'      => 'REST Ingestion / GeoJSON Format',
            ],
        ];
    }

    private function airQualityDetails(): array
    {
        $stations = AirQualityStation::where('is_active', true)->count();
        $total = AirQualityReading::count();
        $latest = AirQualityReading::max('measured_at');
        $fetched = AirQualityReading::max('fetched_at');

        $history = $total > 0
            ? AirQualityReading::where('measured_at', '>=', now()->subDays(14))
                ->select(DB::raw('DATE(measured_at) as tanggal'), DB::raw('COUNT(*) as jumlah'))
                ->groupBy('tanggal')->orderByDesc('tanggal')->get()
                ->map(fn ($r) => ['date' => $r->tanggal, 'count' => $r->jumlah])->all()
            : [];

        $errors = [];
        if ($stations === 0) {
            $errors[] = 'Belum ada stasiun pemantau aktif yang terdaftar.';
        } elseif ($total === 0) {
            $errors[] = 'Stasiun pemantau terdaftar, namun belum ada rekaman pembacaan PM2.5/ISPU.';
        } elseif ($latest && Carbon::parse($latest)->lt(now()->subDays(3))) {
            $errors[] = 'Data pembacaan kualitas udara terakhir berusia lebih dari 3 hari.';
        }

        $syncAt = $fetched ? Carbon::parse($fetched) : null;

        return [
            'id'                => 'air_quality',
            'name'              => 'Pemantauan Kualitas Udara',
            'provider'          => 'OpenAQ / Jaringan Stasiun ISPU Daerah',
            'icon'              => 'bi-cloud-haze2',
            'connection_status' => $stations > 0 ? 'Terkonfigurasi' : 'Belum Dikonfigurasi',
            'sync_status'       => $this->computeSyncStatus($syncAt),
            'data_status'       => $total > 0 ? 'Tersedia' : 'Kosong',
            'total_records'     => $total,
            'recent_records'    => AirQualityReading::where('measured_at', '>=', now()->subDays(7))->count(),
            'last_sync_at'      => $syncAt,
            'last_data_at'      => $latest ? Carbon::parse($latest) : null,
            'sync_history'      => $history,
            'errors'            => $errors,
            'meta'              => [
                'Stasiun Aktif' => "{$stations} stasiun",
                'Parameter'     => 'PM2.5 (µg/m³) & Indeks ISPU',
            ],
        ];
    }

    private function sourceDetails(): array
    {
        $sources = InformationSource::count();
        $total = InformationItem::count();
        $latest = InformationItem::max('published_at');
        $fetched = InformationItem::max('created_at');

        $history = $total > 0
            ? InformationItem::where('created_at', '>=', now()->subDays(14))
                ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('COUNT(*) as jumlah'))
                ->groupBy('tanggal')->orderByDesc('tanggal')->get()
                ->map(fn ($r) => ['date' => $r->tanggal, 'count' => $r->jumlah])->all()
            : [];

        $errors = [];
        if ($sources === 0) {
            $errors[] = 'Belum ada sumber informasi resmi terverifikasi yang didaftarkan.';
        } elseif ($total === 0) {
            $errors[] = 'Sumber terdaftar, namun belum ada publikasi peringatan resmi tersinkron.';
        }

        $syncAt = $fetched ? Carbon::parse($fetched) : null;

        return [
            'id'                => 'info_sources',
            'name'              => 'Sumber Informasi Resmi',
            'provider'          => 'BMKG, KLHK SIPONGI, BPBD Provinsi',
            'icon'              => 'bi-patch-check',
            'connection_status' => $sources > 0 ? 'Terkonfigurasi' : 'Belum Dikonfigurasi',
            'sync_status'       => $this->computeSyncStatus($syncAt),
            'data_status'       => $total > 0 ? 'Tersedia' : 'Kosong',
            'total_records'     => $total,
            'recent_records'    => InformationItem::where('created_at', '>=', now()->subDays(7))->count(),
            'last_sync_at'      => $syncAt,
            'last_data_at'      => $latest ? Carbon::parse($latest) : null,
            'sync_history'      => $history,
            'errors'            => $errors,
            'meta'              => [
                'Sumber Resmi'  => "{$sources} instansi/lembaga",
                'Kanal'         => 'Waspada Dini, Siaga Karhutla, Peringatan Asap',
            ],
        ];
    }

    private function computeSyncStatus(?Carbon $lastSync): string
    {
        if (! $lastSync) {
            return 'Belum Pernah Sinkron';
        }
        $diffHours = $lastSync->diffInHours(now());
        if ($diffHours <= 24) {
            return 'Mutakhir';
        }
        if ($diffHours <= 72) {
            return 'Perlu Perhatian';
        }
        return 'Terlambat (> 3 hari)';
    }
}
