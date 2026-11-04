<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AirQualityReading;
use App\Models\AirQualityStation;
use App\Models\Announcement;
use App\Models\EducationArticle;
use App\Models\Guide;
use App\Models\Hotspot;
use App\Models\InformationItem;
use App\Models\InformationSource;
use App\Models\Province;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'contentStats'    => $this->contentStats(),
            'regionStats'     => $this->regionStats(),
            'integrations'    => $this->integrationStatus(),
            'trendChart'      => $this->hotspotTrendChart(),
        ]);
    }

    private function contentStats(): array
    {
        return [
            'guides' => [
                'published' => Guide::where('status', Guide::STATUS_PUBLISHED)->count(),
                'draft'     => Guide::where('status', Guide::STATUS_DRAFT)->count(),
            ],
            'education' => [
                'published' => EducationArticle::where('status', EducationArticle::STATUS_PUBLISHED)->count(),
                'draft'     => EducationArticle::where('status', EducationArticle::STATUS_DRAFT)->count(),
            ],
            'announcements' => [
                'active' => Announcement::where('status', Announcement::STATUS_PUBLISHED)
                    ->where('is_active', true)->count(),
                'draft'  => Announcement::where('status', Announcement::STATUS_DRAFT)->count(),
            ],
        ];
    }

    private function regionStats(): array
    {
        return [
            'provinces'  => Province::count(),
            'hotspots'   => [
                'total'  => Hotspot::count(),
                'recent' => Hotspot::where('detected_at', '>=', now()->subDays(7))->count(),
            ],
            'stations'   => AirQualityStation::where('is_active', true)->count(),
        ];
    }

    private function integrationStatus(): array
    {
        return [
            $this->hotspotIntegration(),
            $this->airQualityIntegration(),
            $this->informationSourceIntegration(),
        ];
    }

    private function hotspotIntegration(): array
    {
        $total   = Hotspot::count();
        $latest  = Hotspot::max('detected_at');
        $fetched = Hotspot::max('created_at');
        $syncAt  = $fetched ? Carbon::parse($fetched) : null;

        $daily = $total > 0
            ? Hotspot::where('detected_at', '>=', now()->subDays(7))
                ->select(DB::raw('DATE(detected_at) as tanggal'), DB::raw('COUNT(*) as jumlah'))
                ->groupBy('tanggal')->orderByDesc('tanggal')->get()
                ->map(fn ($r) => ['date' => $r->tanggal, 'count' => $r->jumlah])->all()
            : [];

        $errors = [];
        if ($total === 0) {
            $errors[] = 'Belum ada data titik panas yang tersimpan di basis data.';
        } elseif ($latest && Carbon::parse($latest)->lt(now()->subDays(3))) {
            $errors[] = 'Data titik panas terakhir diterima lebih dari 3 hari lalu.';
        }

        return [
            'id'                => 'hotspot',
            'name'              => 'Titik Panas (Hotspot)',
            'provider'          => 'NASA FIRMS / VIIRS & MODIS',
            'icon'              => 'bi-fire',
            'connection_status' => filled(config('services.firms.map_key')) ? 'Terkonfigurasi' : 'Belum Dikonfigurasi',
            'sync_status'       => $this->computeSyncStatus($syncAt),
            'data_status'       => $total > 0 ? 'Tersedia' : 'Kosong',
            'total_records'     => $total,
            'recent_records'    => Hotspot::where('detected_at', '>=', now()->subDays(7))->count(),
            'last_sync_at'      => $syncAt,
            'last_data_at'      => $latest ? Carbon::parse($latest) : null,
            'sync_history'      => $daily,
            'errors'            => $errors,
        ];
    }

    private function airQualityIntegration(): array
    {
        $stations = AirQualityStation::where('is_active', true)->count();
        $total    = AirQualityReading::count();
        $latest   = AirQualityReading::max('measured_at');
        $fetched  = AirQualityReading::max('fetched_at');
        $syncAt   = $fetched ? Carbon::parse($fetched) : null;

        $daily = $total > 0
            ? AirQualityReading::where('measured_at', '>=', now()->subDays(7))
                ->select(DB::raw('DATE(measured_at) as tanggal'), DB::raw('COUNT(*) as jumlah'))
                ->groupBy('tanggal')->orderByDesc('tanggal')->get()
                ->map(fn ($r) => ['date' => $r->tanggal, 'count' => $r->jumlah])->all()
            : [];

        $errors = [];
        if ($stations === 0) {
            $errors[] = 'Belum ada stasiun pemantauan aktif yang terdaftar di basis data.';
        } elseif ($total === 0) {
            $errors[] = 'Stasiun pemantau terdaftar, namun belum ada rekaman pembacaan kualitas udara.';
        } elseif ($latest && Carbon::parse($latest)->lt(now()->subDays(3))) {
            $errors[] = 'Pembacaan kualitas udara terakhir diterima lebih dari 3 hari lalu.';
        }

        return [
            'id'                => 'air_quality',
            'name'              => 'Kualitas Udara (PM2.5 / ISPU)',
            'provider'          => 'OpenAQ / Jaringan ISPU Daerah',
            'icon'              => 'bi-cloud-haze2',
            'connection_status' => $stations > 0 ? 'Terkonfigurasi' : 'Belum Dikonfigurasi',
            'sync_status'       => $this->computeSyncStatus($syncAt),
            'data_status'       => $total > 0 ? 'Tersedia' : 'Kosong',
            'total_records'     => $total,
            'recent_records'    => AirQualityReading::where('measured_at', '>=', now()->subDays(7))->count(),
            'last_sync_at'      => $syncAt,
            'last_data_at'      => $latest ? Carbon::parse($latest) : null,
            'sync_history'      => $daily,
            'errors'            => $errors,
        ];
    }

    private function informationSourceIntegration(): array
    {
        $sources = InformationSource::count();
        $total   = InformationItem::count();
        $latest  = InformationItem::max('published_at');
        $fetched = InformationItem::max('created_at');
        $syncAt  = $fetched ? Carbon::parse($fetched) : null;

        $daily = $total > 0
            ? InformationItem::where('created_at', '>=', now()->subDays(7))
                ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('COUNT(*) as jumlah'))
                ->groupBy('tanggal')->orderByDesc('tanggal')->get()
                ->map(fn ($r) => ['date' => $r->tanggal, 'count' => $r->jumlah])->all()
            : [];

        $errors = [];
        if ($sources === 0) {
            $errors[] = 'Belum ada sumber informasi resmi terverifikasi yang didaftarkan.';
        } elseif ($total === 0) {
            $errors[] = 'Sumber terdaftar, namun belum ada rilis informasi kewaspadaan resmi.';
        }

        return [
            'id'                => 'info_sources',
            'name'              => 'Sumber Informasi Resmi',
            'provider'          => 'BMKG / KLHK SIPONGI / BPBD',
            'icon'              => 'bi-patch-check',
            'connection_status' => $sources > 0 ? 'Terkonfigurasi' : 'Belum Dikonfigurasi',
            'sync_status'       => $this->computeSyncStatus($syncAt),
            'data_status'       => $total > 0 ? 'Tersedia' : 'Kosong',
            'total_records'     => $total,
            'recent_records'    => InformationItem::where('created_at', '>=', now()->subDays(7))->count(),
            'last_sync_at'      => $syncAt,
            'last_data_at'      => $latest ? Carbon::parse($latest) : null,
            'sync_history'      => $daily,
            'errors'            => $errors,
        ];
    }

    private function hotspotTrendChart(): array
    {
        $labels = [];
        $values = [];
        $countsByDate = Hotspot::query()
            ->where('detected_at', '>=', now()->subDays(6)->startOfDay())
            ->select(DB::raw('DATE(detected_at) as tgl'), DB::raw('COUNT(*) as total'))
            ->groupBy('tgl')
            ->pluck('total', 'tgl');

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->translatedFormat('d M');
            $values[] = (int) ($countsByDate[$date] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
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


