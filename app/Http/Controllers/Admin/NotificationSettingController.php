<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationSettingRequest;
use App\Models\NotificationLog;
use App\Models\NotificationSetting;
use App\Models\Province;
use App\Services\Notifications\NotificationDispatcherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationSettingController extends Controller
{
    public function __construct(
        private readonly NotificationDispatcherService $dispatcher
    ) {
    }

    /**
     * Tampilkan halaman pengelolaan konfigurasi notifikasi dan riwayat pengiriman.
     */
    public function index(Request $request): View
    {
        $setting = NotificationSetting::current();
        $provinces = Province::query()->orderBy('name')->get();

        $channelFilter = trim((string) $request->query('kanal', ''));
        $statusFilter = trim((string) $request->query('status', ''));
        $categoryFilter = trim((string) $request->query('kategori', ''));

        $query = NotificationLog::query()->with('province')->latestFirst();

        if ($channelFilter !== '') {
            $query->where('channel', $channelFilter);
        }

        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        if ($categoryFilter !== '') {
            $query->where('category', $categoryFilter);
        }

        $logs = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => NotificationLog::count(),
            'sent' => NotificationLog::where('status', NotificationLog::STATUS_SENT)->count(),
            'suppressed' => NotificationLog::where('status', NotificationLog::STATUS_SUPPRESSED)->count(),
            'failed' => NotificationLog::where('status', NotificationLog::STATUS_FAILED)->count(),
        ];

        return view('admin.notifications.index', [
            'setting' => $setting,
            'provinces' => $provinces,
            'logs' => $logs,
            'stats' => $stats,
            'channelFilter' => $channelFilter,
            'statusFilter' => $statusFilter,
            'categoryFilter' => $categoryFilter,
        ]);
    }

    /**
     * Perbarui konfigurasi notifikasi sistem, ambang batas, wilayah, dan frekuensi.
     */
    public function update(NotificationSettingRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $setting = NotificationSetting::current();

        $validated['is_enabled'] = $request->boolean('is_enabled');
        $validated['browser_enabled'] = $request->boolean('browser_enabled');
        $validated['email_enabled'] = $request->boolean('email_enabled');
        $validated['webhook_enabled'] = $request->boolean('webhook_enabled');

        $validated['notify_hotspots'] = $request->boolean('notify_hotspots');
        $validated['notify_air_quality'] = $request->boolean('notify_air_quality');
        $validated['notify_announcements'] = $request->boolean('notify_announcements');

        $validated['quiet_hours_enabled'] = $request->boolean('quiet_hours_enabled');
        $validated['updated_by'] = auth()->id();

        // Jika wilayah pantauan tidak dicentang sama sekali, set null (berarti seluruh Kalimantan)
        if (empty($validated['monitored_provinces'])) {
            $validated['monitored_provinces'] = null;
        }

        $setting->update($validated);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Konfigurasi notifikasi dan aturan ambang batas keselamatan berhasil diperbarui.');
    }

    /**
     * Jalankan uji coba pengiriman notifikasi ke kanal yang aktif.
     */
    public function testNotification(): RedirectResponse
    {
        $this->dispatcher->sendTestNotification();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notifikasi uji coba berhasil dikirim dan dicatat pada riwayat pengiriman.');
    }

    /**
     * Endpoint JSON publik untuk feed notifikasi peramban masyarakat (tanpa login).
     */
    public function activePublicAlerts(): JsonResponse
    {
        $alerts = $this->dispatcher->getActivePublicAlerts(5);

        return response()->json([
            'status' => 'ok',
            'count' => $alerts->count(),
            'data' => $alerts->map(fn (NotificationLog $log) => [
                'id' => $log->id,
                'title' => $log->title,
                'message' => $log->message,
                'category' => $log->category,
                'level' => $log->level,
                'province' => $log->province?->name ?? 'Seluruh Kalimantan',
                'sent_at' => $log->sent_at?->diffForHumans() ?? 'Baru saja',
            ]),
        ]);
    }
}

