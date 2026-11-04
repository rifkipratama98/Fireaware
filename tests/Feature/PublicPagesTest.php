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
        $response->assertSee('Pantau titik panas dan risiko kebakaran hutan');
        $response->assertSee('Kalimantan Barat');
        $response->assertSee('Pengumuman Cuaca Panas');
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
        $response->assertSee('Peta Hotspot Kalimantan');
        $response->assertSee('Kalimantan Timur');
        $response->assertSee('id="leafletMap"', false);
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
        $response->assertSee('Stasiun Pontianak Kota');
        $response->assertSee('42');
        $response->assertSee('Sedang');
    }
}
