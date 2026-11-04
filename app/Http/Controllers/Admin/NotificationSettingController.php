<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Hotspot;
use Illuminate\View\View;

class NotificationSettingController extends Controller
{
    /**
     * Tampilkan konfigurasi ambang batas peringatan dini dan notifikasi.
     * Tidak menampilkan rahasia/token autentikasi eksternal.
     */
    public function index(): View
    {
        $thresholds = [
            [
                'parameter'     => 'Kualitas Udara PM2.5 (ISPU)',
                'level'         => 'Waspada / Tidak Sehat',
                'rule'          => 'PM2.5 > 55.4 µg/m³ (ISPU > 100)',
                'action'        => 'Buat pengumuman level Waspada otomatis & kirim notifikasi posko',
                'status'        => 'Aktif',
            ],
            [
                'parameter'     => 'Kualitas Udara Ekstrem',
                'level'         => 'Siaga Darurat',
                'rule'          => 'PM2.5 > 150.4 µg/m³ (ISPU > 200 / Sangat Tidak Sehat)',
                'action'        => 'Kibarkan banner darurat portal publik & peringatan broadcast',
                'status'        => 'Aktif',
            ],
            [
                'parameter'     => 'Klaster Titik Panas Tinggi',
                'level'         => 'Siaga Lapangan',
                'rule'          => 'Confidence >= 80% dan FRP > 50 MW dalam radius 5 km',
                'action'        => 'Tandai titik merah pada peta interaktif & arsipkan ke laporan',
                'status'        => 'Aktif',
            ],
        ];

        $channels = [
            [
                'name'          => 'Email Posko Operasi',
                'target'        => 'posko-karhutla@fireaware.id',
                'type'          => 'Email Digest & Alert',
                'is_active'     => true,
            ],
            [
                'name'          => 'Webhook Koordinasi Lapangan',
                'target'        => 'https://api.posko.kalimantan.go.id/v1/alert (Tersamar)',
                'type'          => 'HTTP POST Webhook',
                'is_active'     => false,
            ],
        ];

        $recentTriggers = Announcement::query()
            ->whereIn('level', [Announcement::LEVEL_WASPADA, Announcement::LEVEL_DARURAT])
            ->where('is_active', true)
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('admin.notifications.index', compact('thresholds', 'channels', 'recentTriggers'));
    }
}
