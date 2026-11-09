<?php

namespace Tests\Feature;

use App\Models\AirQualityReading;
use App\Models\AirQualityStation;
use App\Models\Announcement;
use App\Models\EducationArticle;
use App\Models\Guide;
use App\Models\Hotspot;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_successfully_with_data(): void
    {
        $prov = Province::create([
            'name' => 'Kalimantan Barat',
            'slug' => 'kalimantan-barat',
            'code' => 'KALBAR',
            'center_lat' => 0.123,
            'center_lng' => 110.750,
        ]);

        Hotspot::create([
            'source' => 'demo',
            'province_id' => $prov->id,
            'latitude' => 0.1,
            'longitude' => 110.7,
            'confidence' => 85,
            'frp' => 45.0,
            'detected_at' => now()->subHours(2),
        ]);

        Announcement::create([
            'title' => 'Pengumuman Cuaca Panas',
            'slug' => 'pengumuman-cuaca-panas',
            'summary' => 'Ringkasan pengumuman',
            'body' => 'Isi teks pengumuman',
            'level' => 1,
            'status' => Announcement::STATUS_PUBLISHED,
            'is_active' => true,
            'published_at' => now()->subHour(),
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('Public/Home')
            ->has('provinces')
            ->has('announcements', 1)
            ->where('announcements.0.title', 'Pengumuman Cuaca Panas')
            ->where('provinces.0.name', 'Kalimantan Barat')
        );
    }

    public function test_home_summary_api_returns_valid_json(): void
    {
        $prov = Province::create([
            'name' => 'Kalimantan Tengah',
            'slug' => 'kalimantan-tengah',
            'code' => 'KALTENG',
            'center_lat' => -1.5,
            'center_lng' => 113.0,
        ]);

        Hotspot::create([
            'source' => 'demo',
            'province_id' => $prov->id,
            'latitude' => -1.5,
            'longitude' => 113.0,
            'confidence' => 90,
            'detected_at' => now()->subHours(5),
        ]);

        $response = $this->get(route('home.summary', ['provinsi' => 'kalimantan-tengah']));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'activeProvince',
            'summary' => ['total_7d', 'last_24h', 'high_confidence', 'last_detected_at'],
            'provinces',
            'updated_at',
        ]);
        $response->assertJson([
            'activeProvince' => ['slug' => 'kalimantan-tengah'],
            'summary' => ['total_7d' => 1],
        ]);
    }

    public function test_hotspot_map_page_loads_with_provinces(): void
    {
        Province::create(['name' => 'Kalimantan Timur', 'slug' => 'kalimantan-timur']);

        $response = $this->get(route('hotspot.map'));

        $response->assertStatus(200);
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('Public/HotspotMap')
            ->has('provinces', 1)
            ->where('provinces.0.name', 'Kalimantan Timur')
        );
    }

    public function test_hotspot_map_data_api_filters_correctly(): void
    {
        $kalsel = Province::create(['name' => 'Kalsel', 'slug' => 'kalsel']);
        $kaltara = Province::create(['name' => 'Kaltara', 'slug' => 'kaltara']);

        Hotspot::create([
            'source' => 'demo',
            'province_id' => $kalsel->id,
            'latitude' => -3.1,
            'longitude' => 115.4,
            'confidence' => 80,
            'detected_at' => now()->subDay(),
        ]);

        Hotspot::create([
            'source' => 'demo',
            'province_id' => $kaltara->id,
            'latitude' => 3.1,
            'longitude' => 116.2,
            'confidence' => 60,
            'detected_at' => now()->subDays(3),
        ]);

        $response = $this->get(route('hotspot.map.data', ['provinsi' => 'kalsel']));

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals(1, $data['meta']['total']);
        $this->assertEquals('Kalsel', $data['data'][0]['province']);
    }

    public function test_air_quality_page_loads_and_displays_measurements(): void
    {
        $prov = Province::create(['name' => 'Kalbar', 'slug' => 'kalbar']);
        $station = AirQualityStation::create([
            'province_id' => $prov->id,
            'name' => 'Stasiun Pontianak Kota',
            'slug' => 'stasiun-pontianak-kota',
            'source' => 'openaq',
            'latitude' => -0.02,
            'longitude' => 109.33,
            'is_active' => true,
        ]);

        AirQualityReading::create([
            'air_quality_station_id' => $station->id,
            'pollutant' => 'pm25',
            'value' => 42.0,
            'unit' => 'µg/m³',
            'aqi_category' => 'Sedang',
            'measured_at' => now()->subHours(1),
            'fetched_at' => now(),
            'source' => 'openaq',
        ]);

        $response = $this->get(route('air.quality', ['stasiun' => 'stasiun-pontianak-kota']));

        $response->assertStatus(200);
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('Public/AirQuality')
            ->has('provinces')
            ->has('stations', 1)
            ->where('activeStation.name', 'Stasiun Pontianak Kota')
            ->where('latest.value', fn ($val) => (float) $val === 42.0)
            ->where('latest.aqi_category', 'Sedang')
            ->where('category.label', 'Sedang')
        );
    }

    public function test_air_quality_page_displays_friendly_empty_state_when_station_has_no_reading(): void
    {
        $prov = Province::create(['name' => 'Kalteng', 'slug' => 'kalteng']);
        AirQualityStation::create([
            'province_id' => $prov->id,
            'name' => 'Stasiun Palangka Raya Mandiri',
            'slug' => 'stasiun-palangka-raya-mandiri',
            'source' => 'openaq',
            'is_active' => true,
        ]);

        $response = $this->get(route('air.quality', ['stasiun' => 'stasiun-palangka-raya-mandiri']));

        $response->assertStatus(200);
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('Public/AirQuality')
            ->where('activeStation.name', 'Stasiun Palangka Raya Mandiri')
            ->where('latest', null)
            ->has('allCategories')
        );
    }

    public function test_air_quality_page_displays_friendly_empty_state_when_no_stations(): void
    {
        $prov = Province::create(['name' => 'Kaltara', 'slug' => 'kaltara']);

        $response = $this->get(route('air.quality', ['provinsi' => 'kaltara']));

        $response->assertStatus(200);
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('Public/AirQuality')
            ->where('activeStation', null)
            ->has('stations', 0)
            ->has('allCategories')
        );
    }

    public function test_air_quality_page_renders_chart_and_stats_when_history_available(): void
    {
        $prov = Province::create(['name' => 'Kalsel', 'slug' => 'kalsel']);
        $station = AirQualityStation::create([
            'province_id' => $prov->id,
            'name' => 'Stasiun Banjarbaru',
            'slug' => 'stasiun-banjarbaru',
            'source' => 'openaq',
            'is_active' => true,
        ]);

        // Buat 3 titik pembacaan berkala agar riwayat valid
        AirQualityReading::create([
            'air_quality_station_id' => $station->id,
            'pollutant' => 'pm25',
            'value' => 20.0,
            'unit' => 'µg/m³',
            'aqi_category' => 'Baik',
            'measured_at' => now()->subDays(2),
            'fetched_at' => now(),
            'source' => 'openaq',
        ]);
        AirQualityReading::create([
            'air_quality_station_id' => $station->id,
            'pollutant' => 'pm25',
            'value' => 35.0,
            'unit' => 'µg/m³',
            'aqi_category' => 'Baik',
            'measured_at' => now()->subDay(),
            'fetched_at' => now(),
            'source' => 'openaq',
        ]);
        AirQualityReading::create([
            'air_quality_station_id' => $station->id,
            'pollutant' => 'pm25',
            'value' => 50.0,
            'unit' => 'µg/m³',
            'aqi_category' => 'Baik',
            'measured_at' => now()->subHours(2),
            'fetched_at' => now(),
            'source' => 'openaq',
        ]);

        $response = $this->get(route('air.quality', ['stasiun' => 'stasiun-banjarbaru']));

        $response->assertStatus(200);
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('Public/AirQuality')
            ->where('activeStation.name', 'Stasiun Banjarbaru')
            ->where('history.available', true)
            ->has('history.points', 3)
            ->where('historyStats.min', fn ($v) => (float) $v === 20.0)
            ->where('historyStats.max', fn ($v) => (float) $v === 50.0)
            ->where('historyStats.avg', fn ($v) => (float) $v === 35.0)
        );
    }

    public function test_about_page_loads_successfully_with_provinces_and_disclaimer(): void
    {
        Province::create(['name' => 'Kalimantan Barat', 'slug' => 'kalimantan-barat', 'code' => 'KALBAR']);
        Province::create(['name' => 'Kalimantan Tengah', 'slug' => 'kalimantan-tengah', 'code' => 'KALTENG']);

        $response = $this->get(route('about'));

        $response->assertStatus(200);
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('Public/About')
            ->has('provinces', 2)
            ->where('provinces.0.name', 'Kalimantan Barat')
            ->where('provinces.1.name', 'Kalimantan Tengah')
        );
    }

    public function test_announcements_index_page_loads_with_inertia_and_props(): void
    {
        $prov = Province::create(['name' => 'Kalimantan Timur', 'slug' => 'kalimantan-timur', 'code' => 'KALTIM']);

        Announcement::create([
            'title' => 'Peringatan Asap Tebal Kutai Kartanegara',
            'slug' => 'peringatan-asap-tebal-kukar',
            'summary' => 'Ringkasan rilis siaga kabut asap wilayah Kukar.',
            'body' => 'Posko BPBD mengimbau masyarakat untuk mengurangi aktivitas luar ruang.',
            'level' => Announcement::LEVEL_WASPADA,
            'province_id' => $prov->id,
            'source_name' => 'BPBD Kaltim',
            'status' => Announcement::STATUS_PUBLISHED,
            'is_active' => true,
            'published_at' => now()->subHours(3),
        ]);

        $response = $this->get(route('announcements.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('Public/Announcements/Index')
            ->has('announcements.data', 1)
            ->has('provinces', 1)
            ->has('regionalStatus')
            ->has('categories', 4)
            ->has('stats')
            ->where('announcements.data.0.title', 'Peringatan Asap Tebal Kutai Kartanegara')
            ->where('announcements.data.0.level_label', 'Waspada')
        );
    }

    public function test_announcement_detail_page_loads_with_inertia(): void
    {
        $prov = Province::create(['name' => 'Kalimantan Utara', 'slug' => 'kalimantan-utara', 'code' => 'KALTARA']);

        $ann = Announcement::create([
            'title' => 'Imbauan Kesiapsiagaan Karhutla Nunukan',
            'slug' => 'imbauan-kesiapsiagaan-karhutla-nunukan',
            'summary' => 'Ringkasan imbauan pencegahan karhutla.',
            'body' => "Pemerintah Kabupaten Nunukan mengeluarkan surat edaran pencegahan pembukaan lahan dengan cara membakar.\n\nPatroli gabungan ditingkatkan.",
            'level' => Announcement::LEVEL_INFO,
            'province_id' => $prov->id,
            'source_name' => 'Pemkab Nunukan',
            'source_url' => 'https://nunukankab.go.id',
            'status' => Announcement::STATUS_PUBLISHED,
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('announcements.show', $ann->slug));

        $response->assertStatus(200);
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('Public/Announcements/Show')
            ->where('announcement.title', 'Imbauan Kesiapsiagaan Karhutla Nunukan')
            ->where('announcement.source_name', 'Pemkab Nunukan')
            ->where('announcement.source_url', 'https://nunukankab.go.id')
            ->has('recentAnnouncements')
            ->has('back_label')
        );
    }
}
