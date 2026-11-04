<?php

namespace Tests\Feature;

use App\Models\NotificationLog;
use App\Models\NotificationSetting;
use App\Models\Province;
use App\Models\User;
use App\Services\Notifications\NotificationDispatcherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NotificationManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_guest_cannot_access_notification_settings_page(): void
    {
        $response = $this->get(route('admin.notifications.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_cannot_access_or_update_notification_settings(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $resIndex = $this->actingAs($user)->get(route('admin.notifications.index'));
        $resIndex->assertStatus(403);

        $resUpdate = $this->actingAs($user)->put(route('admin.notifications.update'), [
            'is_enabled' => false,
            'hotspot_min_confidence' => 80,
            'hotspot_min_frp' => 50,
            'air_quality_min_pm25' => 55.4,
            'announcement_min_level' => 2,
            'frequency_limit_minutes' => 60,
        ]);
        $resUpdate->assertStatus(403);
    }

    public function test_admin_can_view_notification_settings_page(): void
    {
        $admin = $this->createAdmin();

        NotificationLog::create([
            'channel' => NotificationLog::CHANNEL_BROWSER,
            'title' => 'Peringatan Asap Tebal',
            'message' => 'Kualitas udara kategori Sangat Tidak Sehat di Pontianak.',
            'category' => 'air_quality',
            'level' => 'danger',
            'status' => NotificationLog::STATUS_SENT,
            'sent_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Konfigurasi Notifikasi & Peringatan Dini');
        $response->assertSee('Sakelar Master Notifikasi Sistem');
        $response->assertSee('Siaran Peramban Web (Browser Notification API)');
        $response->assertSee('Riwayat Log Pengiriman Notifikasi');
        $response->assertSee('Peringatan Asap Tebal');
    }

    public function test_admin_can_update_notification_settings(): void
    {
        $admin = $this->createAdmin();
        $prov = Province::create(['name' => 'Kalimantan Barat', 'slug' => 'kalimantan-barat']);

        $payload = [
            'is_enabled' => '1',
            'browser_enabled' => '1',
            'email_enabled' => '1',
            'email_recipients' => 'posko-utama@fireaware.id, bpb-kalteng@example.com',
            'webhook_enabled' => '0',
            'notify_hotspots' => '1',
            'hotspot_min_confidence' => 85,
            'hotspot_min_frp' => 60.5,
            'notify_air_quality' => '1',
            'air_quality_min_pm25' => 65.0,
            'notify_announcements' => '1',
            'announcement_min_level' => 3,
            'monitored_provinces' => [$prov->id],
            'frequency_limit_minutes' => 45,
            'quiet_hours_enabled' => '1',
            'quiet_hours_start' => '23:00',
            'quiet_hours_end' => '05:00',
        ];

        $response = $this->actingAs($admin)
            ->from(route('admin.notifications.index'))
            ->put(route('admin.notifications.update'), $payload);

        $response->assertRedirect(route('admin.notifications.index'));
        $response->assertSessionHas('success');

        $setting = NotificationSetting::current();
        $this->assertTrue($setting->is_enabled);
        $this->assertTrue($setting->email_enabled);
        $this->assertEquals(85, $setting->hotspot_min_confidence);
        $this->assertEquals(60.5, $setting->hotspot_min_frp);
        $this->assertEquals(3, $setting->announcement_min_level);
        $this->assertEquals(45, $setting->frequency_limit_minutes);
        $this->assertEquals('23:00', $setting->quiet_hours_start);
    }

    public function test_validation_requires_email_when_email_channel_enabled(): void
    {
        $admin = $this->createAdmin();

        $payload = [
            'is_enabled' => '1',
            'email_enabled' => '1',
            'email_recipients' => '', // kosong, harus ditolak
            'hotspot_min_confidence' => 80,
            'hotspot_min_frp' => 50,
            'air_quality_min_pm25' => 55.4,
            'announcement_min_level' => 2,
            'frequency_limit_minutes' => 60,
        ];

        $response = $this->actingAs($admin)
            ->from(route('admin.notifications.index'))
            ->put(route('admin.notifications.update'), $payload);

        $response->assertSessionHasErrors('email_recipients');
    }

    public function test_validation_requires_valid_webhook_url_when_webhook_enabled(): void
    {
        $admin = $this->createAdmin();

        $payload = [
            'is_enabled' => '1',
            'webhook_enabled' => '1',
            'webhook_url' => 'bukan-url-valid', // format salah
            'hotspot_min_confidence' => 80,
            'hotspot_min_frp' => 50,
            'air_quality_min_pm25' => 55.4,
            'announcement_min_level' => 2,
            'frequency_limit_minutes' => 60,
        ];

        $response = $this->actingAs($admin)
            ->from(route('admin.notifications.index'))
            ->put(route('admin.notifications.update'), $payload);

        $response->assertSessionHasErrors('webhook_url');
    }
    public function test_admin_can_send_test_notification(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
            ->from(route('admin.notifications.index'))
            ->post(route('admin.notifications.test'));

        $response->assertRedirect(route('admin.notifications.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('notification_logs', [
            'category' => 'test',
            'status' => NotificationLog::STATUS_SENT,
        ]);
    }

    public function test_notification_dispatcher_suppresses_when_system_disabled(): void
    {
        $setting = NotificationSetting::current();
        $setting->update(['is_enabled' => false]);

        /** @var NotificationDispatcherService $dispatcher */
        $dispatcher = app(NotificationDispatcherService::class);
        $result = $dispatcher->dispatchAlert('hotspot', 'Api Besar', 'Deteksi api besar di rawa', 'danger');

        $this->assertEquals('suppressed', $result['status']);
        $this->assertDatabaseHas('notification_logs', [
            'category' => 'hotspot',
            'status' => NotificationLog::STATUS_SUPPRESSED,
        ]);
    }

    public function test_notification_dispatcher_suppresses_unmonitored_province(): void
    {
        $prov1 = Province::create(['name' => 'Kalsel', 'slug' => 'kalsel']);
        $prov2 = Province::create(['name' => 'Kaltim', 'slug' => 'kaltim']);

        $setting = NotificationSetting::current();
        $setting->update([
            'is_enabled' => true,
            'monitored_provinces' => [$prov1->id], // hanya pantau Kalsel
        ]);

        /** @var NotificationDispatcherService $dispatcher */
        $dispatcher = app(NotificationDispatcherService::class);

        // Kirim untuk Kaltim (tidak dipantau) -> harus ditekan (suppressed)
        $res = $dispatcher->dispatchAlert('hotspot', 'Api di Kaltim', 'Titik api', 'warning', $prov2->id);
        $this->assertEquals('suppressed', $res['status']);

        // Kirim untuk Kalsel (dipantau) -> harus diteruskan (dispatched)
        $res2 = $dispatcher->dispatchAlert('hotspot', 'Api di Kalsel', 'Titik api di Banjarbaru', 'warning', $prov1->id, [
            'confidence' => 90,
            'frp' => 60,
        ]);
        $this->assertEquals('dispatched', $res2['status']);
    }

    public function test_notification_dispatcher_rate_limiting_prevents_spam(): void
    {
        $setting = NotificationSetting::current();
        $setting->update([
            'is_enabled' => true,
            'frequency_limit_minutes' => 60,
        ]);

        /** @var NotificationDispatcherService $dispatcher */
        $dispatcher = app(NotificationDispatcherService::class);

        // Pengiriman pertama
        $res1 = $dispatcher->dispatchAlert('air_quality', 'PM2.5 Naik', 'Indeks tidak sehat', 'warning', null, ['pm25' => 75]);
        $this->assertEquals('dispatched', $res1['status']);

        // Pengiriman kedua segera setelahnya (di bawah 60 menit) -> harus ditekan oleh aturan anti-spam
        $res2 = $dispatcher->dispatchAlert('air_quality', 'PM2.5 Naik Lagi', 'Indeks masih tidak sehat', 'warning', null, ['pm25' => 78]);
        $this->assertEquals('suppressed', $res2['status']);

        $this->assertDatabaseHas('notification_logs', [
            'title' => 'PM2.5 Naik Lagi',
            'status' => NotificationLog::STATUS_SUPPRESSED,
        ]);
    }

    public function test_active_public_alerts_endpoint_returns_json_for_masyarakat(): void
    {
        NotificationSetting::current()->update([
            'is_enabled' => true,
            'browser_enabled' => true,
        ]);

        NotificationLog::create([
            'channel' => NotificationLog::CHANNEL_BROWSER,
            'title' => 'Peringatan Asap Pekat Sintang',
            'message' => 'Kualitas udara memburuk akibat kebakaran lahan gambut.',
            'category' => 'air_quality',
            'level' => 'danger',
            'status' => NotificationLog::STATUS_SENT,
            'sent_at' => now(),
        ]);

        $response = $this->get(route('notifications.active'));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok',
            'count' => 1,
        ]);
        $response->assertJsonFragment([
            'title' => 'Peringatan Asap Pekat Sintang',
        ]);
    }

    public function test_active_public_alerts_returns_empty_when_browser_channel_disabled(): void
    {
        NotificationSetting::current()->update([
            'is_enabled' => true,
            'browser_enabled' => false, // dinonaktifkan admin
        ]);

        NotificationLog::create([
            'channel' => NotificationLog::CHANNEL_BROWSER,
            'title' => 'Peringatan Rahasia',
            'message' => 'Pesan ini tidak boleh tayang di feed publik.',
            'category' => 'announcement',
            'level' => 'danger',
            'status' => NotificationLog::STATUS_SENT,
            'sent_at' => now(),
        ]);

        $response = $this->get(route('notifications.active'));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok',
            'count' => 0,
            'data' => [],
        ]);
    }

}
