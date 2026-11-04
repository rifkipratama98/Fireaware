<?php

namespace Tests\Feature;

use App\Models\AirQualityReading;
use App\Models\AirQualityStation;
use App\Models\Announcement;
use App\Models\Hotspot;
use App\Models\Province;
use App\Models\SyncLog;
use App\Models\User;
use App\Services\Integrations\AirQualitySyncService;
use App\Services\Integrations\BmkgKarhutlaSyncService;
use App\Services\Integrations\FirmsHotspotSyncService;
use App\Services\Integrations\IntegrationManagerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IntegrationSyncTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_guest_cannot_access_integrations_page(): void
    {
        $response = $this->get(route('admin.integrations.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_cannot_trigger_sync(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->post(route('admin.integrations.sync', 'nasa_firms'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_integrations_page_with_sources_and_history(): void
    {
        $admin = $this->createAdmin();

        SyncLog::create([
            'source_key' => FirmsHotspotSyncService::SOURCE_KEY,
            'source_name' => FirmsHotspotSyncService::SOURCE_NAME,
            'status' => SyncLog::STATUS_SUCCESS,
            'records_processed' => 12,
            'records_added' => 10,
            'records_updated' => 2,
            'observation_at' => now()->subHours(2),
            'started_at' => now()->subMinutes(10),
            'completed_at' => now()->subMinutes(9),
            'duration_ms' => 850,
            'trigger' => 'manual',
            'user_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.integrations.index'));

        $response->assertStatus(200);
        $response->assertSee('NASA FIRMS');
        $response->assertSee('BMKG Karhutla');
        $response->assertSee('Pemantauan Kualitas Udara');
        $response->assertSee('Riwayat Log Sinkronisasi');
        $response->assertSee('12');
        $response->assertSee('Sinkronisasi Manual');
    }

    public function test_firms_sync_returns_not_configured_when_key_is_missing(): void
    {
        Config::set('services.firms.map_key', null);

        /** @var FirmsHotspotSyncService $service */
        $service = app(FirmsHotspotSyncService::class);
        $result = $service->sync();

        $this->assertTrue($result->isNotConfigured());
        $this->assertDatabaseHas('sync_logs', [
            'source_key' => FirmsHotspotSyncService::SOURCE_KEY,
            'status' => SyncLog::STATUS_NOT_CONFIGURED,
        ]);
    }

    public function test_firms_sync_success_processes_csv_and_prevents_duplicates(): void
    {
        Config::set('services.firms.map_key', 'test_secret_map_key_123');

        Province::create([
            'name' => 'Kalimantan Barat',
            'slug' => 'kalimantan-barat',
            'code' => 'KALBAR',
            'center_lat' => 0.123,
            'center_lng' => 110.750,
        ]);

        $csvContent = "latitude,longitude,bright_ti4,scan,track,acq_date,acq_time,satellite,instrument,confidence,version,bright_ti5,frp,daynight\n" .
                      "-0.0500,109.3000,325.5,0.4,0.4,2026-11-04,0630,N,VIIRS,nominal,2.0NRT,295.1,12.5,D\n" .
                      "0.6000,109.9000,340.2,0.5,0.4,2026-11-04,0630,N,VIIRS,high,2.0NRT,298.4,25.8,D\n";

        Http::fake([
            'firms.modaps.eosdis.nasa.gov/*' => Http::response($csvContent, 200),
        ]);

        /** @var FirmsHotspotSyncService $service */
        $service = app(FirmsHotspotSyncService::class);
        $result = $service->sync('manual');

        $this->assertTrue($result->isSuccess());
        $this->assertEquals(2, $result->recordsProcessed);
        $this->assertEquals(2, $result->recordsAdded);
        $this->assertEquals(0, $result->recordsUpdated);

        $this->assertDatabaseCount('hotspots', 2);

        // Uji deduplikasi: panggil lagi dengan data yang sama
        $result2 = $service->sync('manual');
        $this->assertTrue($result2->isSuccess());
        $this->assertEquals(0, $result2->recordsAdded);
        $this->assertEquals(2, $result2->recordsUpdated);
        $this->assertDatabaseCount('hotspots', 2);
    }
    public function test_firms_sync_handles_empty_hotspot_data(): void
    {
        Config::set('services.firms.map_key', 'test_secret_map_key_123');

        $csvOnlyHeader = "latitude,longitude,bright_ti4,scan,track,acq_date,acq_time,satellite,instrument,confidence,version,bright_ti5,frp,daynight\n";

        Http::fake([
            'firms.modaps.eosdis.nasa.gov/*' => Http::response($csvOnlyHeader, 200),
        ]);

        /** @var FirmsHotspotSyncService $service */
        $service = app(FirmsHotspotSyncService::class);
        $result = $service->sync();

        $this->assertTrue($result->isSuccess());
        $this->assertEquals(0, $result->recordsProcessed);
        $this->assertDatabaseHas('sync_logs', [
            'source_key' => FirmsHotspotSyncService::SOURCE_KEY,
            'status' => SyncLog::STATUS_SUCCESS,
            'records_processed' => 0,
        ]);
    }

    public function test_firms_sync_handles_failure_and_retains_existing_data(): void
    {
        Config::set('services.firms.map_key', 'test_secret_map_key_123');

        $prov = Province::create(['name' => 'Kalteng', 'slug' => 'kalteng']);
        $existing = Hotspot::create([
            'source' => 'nasa_firms',
            'province_id' => $prov->id,
            'latitude' => -1.5,
            'longitude' => 113.0,
            'confidence' => 80,
            'detected_at' => now()->subDay(),
        ]);

        Http::fake([
            'firms.modaps.eosdis.nasa.gov/*' => Http::response('Server Error 500', 500),
        ]);

        /** @var FirmsHotspotSyncService $service */
        $service = app(FirmsHotspotSyncService::class);
        $result = $service->sync();

        $this->assertTrue($result->isFailed());
        $this->assertDatabaseHas('sync_logs', [
            'source_key' => FirmsHotspotSyncService::SOURCE_KEY,
            'status' => SyncLog::STATUS_FAILED,
        ]);

        // Pastikan data valid sebelumnya TIDAK terhapus saat terjadi kegagalan sinkronisasi
        $this->assertDatabaseHas('hotspots', ['id' => $existing->id]);
    }

    public function test_firms_sync_handles_timeout_properly(): void
    {
        Config::set('services.firms.map_key', 'test_secret_map_key_123');

        Http::fake([
            'firms.modaps.eosdis.nasa.gov/*' => function () {
                throw new ConnectionException('cURL error 28: Operation timed out');
            },
        ]);

        /** @var FirmsHotspotSyncService $service */
        $service = app(FirmsHotspotSyncService::class);
        $result = $service->sync();

        $this->assertTrue($result->isTimeout());
        $this->assertDatabaseHas('sync_logs', [
            'source_key' => FirmsHotspotSyncService::SOURCE_KEY,
            'status' => SyncLog::STATUS_TIMEOUT,
        ]);
    }

    public function test_firms_sync_does_not_leak_map_key_in_logs_or_errors(): void
    {
        $secretKey = 'super_secret_firms_map_key_999';
        Config::set('services.firms.map_key', $secretKey);

        Http::fake([
            'firms.modaps.eosdis.nasa.gov/*' => Http::response("Invalid MAP_KEY: {$secretKey}", 403),
        ]);

        /** @var FirmsHotspotSyncService $service */
        $service = app(FirmsHotspotSyncService::class);
        $result = $service->sync();

        $this->assertFalse(str_contains((string) $result->errorMessage, $secretKey));
        $this->assertDatabaseMissing('sync_logs', [
            'error_message' => $secretKey,
        ]);
    }

    public function test_bmkg_sync_returns_not_configured_when_url_missing(): void
    {
        Config::set('services.bmkg.karhutla_url', null);

        /** @var BmkgKarhutlaSyncService $service */
        $service = app(BmkgKarhutlaSyncService::class);
        $result = $service->sync();

        $this->assertTrue($result->isNotConfigured());
        $this->assertDatabaseHas('sync_logs', [
            'source_key' => BmkgKarhutlaSyncService::SOURCE_KEY,
            'status' => SyncLog::STATUS_NOT_CONFIGURED,
        ]);
    }

    public function test_bmkg_sync_success_stores_announcements_and_prevents_duplicates(): void
    {
        Config::set('services.bmkg.karhutla_url', 'https://data.bmkg.go.id/karhutla/feed.json');

        Province::create(['name' => 'Kalimantan Barat', 'slug' => 'kalimantan-barat']);

        $payload = [
            'data' => [
                [
                    'title' => 'Peringatan Dini Cuaca Panas Ekstrem Kalbar',
                    'summary' => 'Suhu udara maksimum berpotensi memicu kerentanan karhutla tinggi.',
                    'body' => 'Diharapkan posko siaga meningkatkan pemantauan patroli darat.',
                    'level' => 'siaga',
                    'wilayah' => 'Kalimantan Barat',
                    'published_at' => '2026-11-04 08:00:00',
                ],
            ],
        ];

        Http::fake([
            'data.bmkg.go.id/*' => Http::response($payload, 200),
        ]);

        /** @var BmkgKarhutlaSyncService $service */
        $service = app(BmkgKarhutlaSyncService::class);
        $result = $service->sync();

        $this->assertTrue($result->isSuccess());
        $this->assertEquals(1, $result->recordsProcessed);
        $this->assertEquals(1, $result->recordsAdded);

        $this->assertDatabaseHas('announcements', [
            'title' => 'Peringatan Dini Cuaca Panas Ekstrem Kalbar',
            'source_name' => 'BMKG',
        ]);

        // Sinkronisasi ulang: cegah duplikasi
        $result2 = $service->sync();
        $this->assertTrue($result2->isSuccess());
        $this->assertEquals(0, $result2->recordsAdded);
        $this->assertEquals(1, $result2->recordsUpdated);
    }
    public function test_bmkg_sync_handles_empty_data(): void
    {
        Config::set('services.bmkg.karhutla_url', 'https://data.bmkg.go.id/karhutla/feed.json');

        Http::fake([
            'data.bmkg.go.id/*' => Http::response(['data' => []], 200),
        ]);

        /** @var BmkgKarhutlaSyncService $service */
        $service = app(BmkgKarhutlaSyncService::class);
        $result = $service->sync();

        $this->assertTrue($result->isSuccess());
        $this->assertEquals(0, $result->recordsProcessed);
    }

    public function test_bmkg_sync_handles_failure_and_timeout(): void
    {
        Config::set('services.bmkg.karhutla_url', 'https://data.bmkg.go.id/karhutla/feed.json');

        Http::fake([
            'data.bmkg.go.id/*' => function () {
                throw new ConnectionException('Connection timed out');
            },
        ]);

        /** @var BmkgKarhutlaSyncService $service */
        $service = app(BmkgKarhutlaSyncService::class);
        $result = $service->sync();

        $this->assertTrue($result->isTimeout());
        $this->assertDatabaseHas('sync_logs', [
            'source_key' => BmkgKarhutlaSyncService::SOURCE_KEY,
            'status' => SyncLog::STATUS_TIMEOUT,
        ]);
    }

    public function test_air_quality_sync_returns_not_configured_when_key_missing(): void
    {
        Config::set('services.openaq.key', null);

        /** @var AirQualitySyncService $service */
        $service = app(AirQualitySyncService::class);
        $result = $service->sync();

        $this->assertTrue($result->isNotConfigured());
        $this->assertDatabaseHas('sync_logs', [
            'source_key' => AirQualitySyncService::SOURCE_KEY,
            'status' => SyncLog::STATUS_NOT_CONFIGURED,
        ]);
    }

    public function test_air_quality_sync_success_with_active_stations(): void
    {
        Config::set('services.openaq.key', 'openaq_test_api_key_456');

        $prov = Province::create(['name' => 'Kalbar', 'slug' => 'kalbar']);
        $station = AirQualityStation::create([
            'province_id' => $prov->id,
            'name' => 'Stasiun Pontianak',
            'slug' => 'stasiun-pontianak',
            'source' => 'openaq',
            'external_id' => '101',
            'is_active' => true,
        ]);

        $payload = [
            'results' => [
                [
                    'parameter' => 'pm25',
                    'value' => 38.5,
                    'datetime' => ['utc' => '2026-11-04T07:00:00Z'],
                ],
            ],
        ];

        Http::fake([
            'api.openaq.org/*' => Http::response($payload, 200),
        ]);

        /** @var AirQualitySyncService $service */
        $service = app(AirQualitySyncService::class);
        $result = $service->sync();

        $this->assertTrue($result->isSuccess());
        $this->assertEquals(1, $result->recordsProcessed);
        $this->assertEquals(1, $result->recordsAdded);

        $this->assertDatabaseHas('air_quality_readings', [
            'air_quality_station_id' => $station->id,
            'value' => 38.5,
            'pollutant' => 'pm25',
        ]);
    }

    public function test_admin_can_trigger_manual_sync_via_web_route(): void
    {
        $admin = $this->createAdmin();

        Config::set('services.firms.map_key', 'test_key');
        Http::fake([
            'firms.modaps.eosdis.nasa.gov/*' => Http::response("latitude,longitude,bright_ti4,scan,track,acq_date,acq_time,satellite,instrument,confidence,version,bright_ti5,frp,daynight\n", 200),
        ]);

        $response = $this->actingAs($admin)
            ->from(route('admin.integrations.index'))
            ->post(route('admin.integrations.sync', 'nasa_firms'));

        $response->assertRedirect(route('admin.integrations.index'));
        $response->assertSessionHas('success');
    }

    public function test_artisan_sync_command_executes_successfully(): void
    {
        Config::set('services.firms.map_key', 'test_key');
        Http::fake([
            'firms.modaps.eosdis.nasa.gov/*' => Http::response("latitude,longitude,bright_ti4,scan,track,acq_date,acq_time,satellite,instrument,confidence,version,bright_ti5,frp,daynight\n", 200),
            'data.bmkg.go.id/*' => Http::response(['data' => []], 200),
            'api.openaq.org/*' => Http::response(['results' => []], 200),
        ]);

        $this->artisan('fireaware:sync firms')
            ->assertExitCode(0);
    }
    public function test_air_quality_sync_handles_failure_and_timeout(): void
    {
        Config::set('services.openaq.key', 'test_key');

        $prov = Province::create(['name' => 'Kalteng', 'slug' => 'kalteng']);
        AirQualityStation::create([
            'province_id' => $prov->id,
            'name' => 'Stasiun Palangka Raya',
            'slug' => 'stasiun-palangka-raya',
            'source' => 'openaq',
            'external_id' => '102',
            'is_active' => true,
        ]);

        Http::fake([
            'api.openaq.org/*' => function () {
                throw new ConnectionException('Timeout connecting to OpenAQ');
            },
        ]);

        /** @var AirQualitySyncService $service */
        $service = app(AirQualitySyncService::class);
        $result = $service->sync();

        $this->assertTrue($result->isTimeout());
        $this->assertDatabaseHas('sync_logs', [
            'source_key' => AirQualitySyncService::SOURCE_KEY,
            'status' => SyncLog::STATUS_TIMEOUT,
        ]);
    }

    public function test_api_keys_are_never_exposed_in_integrations_view(): void
    {
        $admin = $this->createAdmin();

        $secretFirmsKey = 'super_secret_firms_key_abc123';
        $secretOpenAqKey = 'super_secret_openaq_key_xyz789';

        Config::set('services.firms.map_key', $secretFirmsKey);
        Config::set('services.openaq.key', $secretOpenAqKey);

        $response = $this->actingAs($admin)->get(route('admin.integrations.index'));

        $response->assertStatus(200);
        $response->assertDontSee($secretFirmsKey);
        $response->assertDontSee($secretOpenAqKey);
    }



}
