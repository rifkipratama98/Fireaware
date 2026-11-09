<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_display_public_announcement_listing(): void
    {
        $province = Province::create([
            'name' => 'Kalimantan Barat',
            'slug' => 'kalimantan-barat',
        ]);

        $published = Announcement::create([
            'title' => 'Peringatan Asap Tebal Kubu Raya',
            'slug' => 'peringatan-asap-tebal-kubu-raya',
            'summary' => 'Ringkasan pengumuman status siaga kabut asap.',
            'body' => 'Diharapkan warga mengenakan masker standar dan mengurangi aktivitas luar ruang.',
            'level' => Announcement::LEVEL_WASPADA,
            'province_id' => $province->id,
            'source_name' => 'BPBD Kalbar',
            'status' => Announcement::STATUS_PUBLISHED,
            'is_active' => true,
            'published_at' => now()->subHours(2),
        ]);

        $draft = Announcement::create([
            'title' => 'Rilis Draf Rahasia',
            'slug' => 'rilis-draf-rahasia',
            'summary' => 'Ringkasan rilis draf.',
            'body' => 'Isi draf pengumuman yang belum disahkan pimpinan.',
            'level' => Announcement::LEVEL_INFO,
            'source_name' => 'Internal',
            'status' => Announcement::STATUS_DRAFT,
            'is_active' => false,
            'published_at' => null,
        ]);

        $response = $this->get(route('announcements.index'));

        $response->assertStatus(200);
        $response->assertSee('Peringatan Asap Tebal Kubu Raya');
        $response->assertSee('BPBD Kalbar');
        $response->assertDontSee('Rilis Draf Rahasia');
    }

    public function test_can_filter_announcements_by_province_and_level(): void
    {
        $kalsel = Province::create(['name' => 'Kalimantan Selatan', 'slug' => 'kalimantan-selatan']);
        $kalteng = Province::create(['name' => 'Kalimantan Tengah', 'slug' => 'kalimantan-tengah']);

        Announcement::create([
            'title' => 'Siaga Karhutla Banjarbaru',
            'slug' => 'siaga-karhutla-banjarbaru',
            'body' => 'Peningkatan titik panas di Banjarbaru.',
            'level' => Announcement::LEVEL_DARURAT,
            'province_id' => $kalsel->id,
            'source_name' => 'BPBD Kalsel',
            'status' => Announcement::STATUS_PUBLISHED,
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        Announcement::create([
            'title' => 'Sosialisasi MPA Palangka Raya',
            'slug' => 'sosialisasi-mpa-palangka-raya',
            'body' => 'Pelatihan kelompok masyarakat peduli api.',
            'level' => Announcement::LEVEL_INFO,
            'province_id' => $kalteng->id,
            'source_name' => 'Dishut Kalteng',
            'status' => Announcement::STATUS_PUBLISHED,
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        // Filter provinsi Kalimantan Selatan
        $resProv = $this->get(route('announcements.index', ['provinsi' => 'kalimantan-selatan']));
        $resProv->assertStatus(200);
        $resProv->assertSee('Siaga Karhutla Banjarbaru');
        $resProv->assertDontSee('Sosialisasi MPA Palangka Raya');

        // Filter level Darurat (3)
        $resLevel = $this->get(route('announcements.index', ['level' => 3]));
        $resLevel->assertStatus(200);
        $resLevel->assertSee('Siaga Karhutla Banjarbaru');
        $resLevel->assertDontSee('Sosialisasi MPA Palangka Raya');
    }

    public function test_can_view_announcement_detail_by_slug(): void
    {
        $ann = Announcement::create([
            'title' => 'Rilis Kedaruratan Bencana Asap Provinsi',
            'slug' => 'rilis-kedaruratan-bencana-asap-provinsi',
            'summary' => 'Ringkasan rilis kedaruratan kabut asap pekat.',
            'body' => "Pemerintah menetapkan status siaga darurat kabut asap selama 14 hari ke depan.\n\nFasilitas kesehatan dan posko oksigen gratis dibuka di setiap puskesmas.",
            'level' => Announcement::LEVEL_DARURAT,
            'source_name' => 'Pos Komando Darurat Karhutla',
            'source_url' => 'https://bpbd.go.id/darurat',
            'status' => Announcement::STATUS_PUBLISHED,
            'is_active' => true,
            'published_at' => now()->subHours(1),
        ]);

        $response = $this->get(route('announcements.show', $ann->slug));

        $response->assertStatus(200);
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('Public/Announcements/Show')
            ->where('announcement.title', 'Rilis Kedaruratan Bencana Asap Provinsi')
            ->where('announcement.source_name', 'Pos Komando Darurat Karhutla')
            ->where('announcement.source_url', 'https://bpbd.go.id/darurat')
        );
        $response->assertSee('Kembali ke Daftar Pengumuman');
    }

    public function test_draft_announcement_returns_404_on_public_show(): void
    {
        $draft = Announcement::create([
            'title' => 'Pengumuman Draf Internal',
            'slug' => 'pengumuman-draf-internal',
            'body' => 'Isi draf pengumuman internal.',
            'level' => Announcement::LEVEL_INFO,
            'status' => Announcement::STATUS_DRAFT,
            'is_active' => false,
            'published_at' => null,
        ]);

        $response = $this->get(route('announcements.show', $draft->slug));
        $response->assertStatus(404);
    }

    public function test_admin_can_store_and_update_announcement(): void
    {
        $this->actingAs(User::factory()->create());
        $province = Province::create(['name' => 'Kalimantan Timur', 'slug' => 'kalimantan-timur']);

        $payload = [
            'title' => 'Peringatan Hotspot Wilayah Berau',
            'slug' => 'peringatan-hotspot-wilayah-berau',
            'summary' => 'Ringkasan pantauan satelit di Berau.',
            'body' => 'Terpantau beberapa anomali panas di kawasan perbukitan.',
            'level' => 2,
            'province_id' => $province->id,
            'source_name' => 'BPBD Kabupaten Berau',
            'source_url' => 'https://beraukab.go.id',
            'status' => Announcement::STATUS_PUBLISHED,
            'is_demo' => false,
            'published_at' => now()->format('Y-m-d H:i:s'),
        ];

        $response = $this->post(route('admin.announcements.store'), $payload);
        $response->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'slug' => 'peringatan-hotspot-wilayah-berau',
            'source_name' => 'BPBD Kabupaten Berau',
            'status' => Announcement::STATUS_PUBLISHED,
        ]);

        $ann = Announcement::where('slug', 'peringatan-hotspot-wilayah-berau')->first();

        // Update
        $updatePayload = [
            'title' => 'Peringatan Hotspot Berau Diperbarui',
            'slug' => 'peringatan-hotspot-wilayah-berau',
            'summary' => 'Ringkasan hasil groundcheck tim posko.',
            'body' => 'Ground check telah selesai dilaksanakan dan situasi terkendali.',
            'level' => 1,
            'province_id' => $province->id,
            'source_name' => 'BPBD Kabupaten Berau',
            'status' => Announcement::STATUS_PUBLISHED,
        ];

        $resUpdate = $this->put(route('admin.announcements.update', $ann), $updatePayload);
        $resUpdate->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseHas('announcements', [
            'id' => $ann->id,
            'title' => 'Peringatan Hotspot Berau Diperbarui',
            'level' => 1,
        ]);

        // Delete
        $resDelete = $this->delete(route('admin.announcements.destroy', $ann));
        $resDelete->assertRedirect(route('admin.announcements.index'));
        $this->assertDatabaseMissing('announcements', ['id' => $ann->id]);
    }

    public function test_admin_can_view_announcement_detail_including_draft(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $draft = Announcement::create([
            'title' => 'Pengumuman Draf Siaga Karhutla',
            'slug' => 'pengumuman-draf-siaga-karhutla',
            'summary' => 'Ringkasan pengumuman status siaga.',
            'body' => 'Isi draf pengumuman kedaruratan yang belum dirilis resmi.',
            'level' => Announcement::LEVEL_DARURAT,
            'source_name' => 'BPBD Provinsi',
            'status' => Announcement::STATUS_DRAFT,
            'is_active' => false,
            'published_at' => null,
        ]);

        $response = $this->get(route('admin.announcements.show', $draft));

        $response->assertStatus(200);
        $response->assertSee('Pengumuman Draf Siaga Karhutla');
        $response->assertSee('Siaga Darurat');
        $response->assertSee('Status: Draf');
    }

    public function test_admin_can_filter_announcements_by_status_and_search(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $prov = Province::create(['name' => 'Kalimantan Barat', 'slug' => 'kalimantan-barat']);

        Announcement::create([
            'title' => 'Peringatan Asap Pekat Sintang',
            'slug' => 'peringatan-asap-pekat-sintang',
            'summary' => 'Kabut asap tebal melanda wilayah Kabupaten Sintang.',
            'body' => 'Kualitas udara menurun pada kategori sangat tidak sehat.',
            'level' => Announcement::LEVEL_WASPADA,
            'province_id' => $prov->id,
            'source_name' => 'BPBD Kalbar',
            'status' => Announcement::STATUS_PUBLISHED,
            'is_active' => true,
            'published_at' => now()->subDay(),
        ]);

        Announcement::create([
            'title' => 'Draf Rilis Pantauan Satelit Harian',
            'slug' => 'draf-rilis-pantauan-satelit-harian',
            'summary' => 'Ringkasan pantauan satelit modis belum dipublikasikan.',
            'body' => 'Data hotspot harian dalam proses validasi posko.',
            'level' => Announcement::LEVEL_INFO,
            'source_name' => 'Pusdalops BPBD',
            'status' => Announcement::STATUS_DRAFT,
            'is_active' => false,
            'published_at' => null,
        ]);

        // Filter status published
        $resPublished = $this->get(route('admin.announcements.index', ['status' => 'published']));
        $resPublished->assertStatus(200);
        $resPublished->assertSee('Peringatan Asap Pekat Sintang');
        $resPublished->assertDontSee('Draf Rilis Pantauan Satelit Harian');

        // Filter status draft
        $resDraft = $this->get(route('admin.announcements.index', ['status' => 'draft']));
        $resDraft->assertStatus(200);
        $resDraft->assertSee('Draf Rilis Pantauan Satelit Harian');
        $resDraft->assertDontSee('Peringatan Asap Pekat Sintang');

        // Search kata kunci
        $resSearch = $this->get(route('admin.announcements.index', ['q' => 'Sintang']));
        $resSearch->assertStatus(200);
        $resSearch->assertSee('Peringatan Asap Pekat Sintang');
        $resSearch->assertDontSee('Draf Rilis Pantauan Satelit Harian');
    }

    public function test_admin_store_validates_required_fields(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $response = $this->post(route('admin.announcements.store'), []);

        $response->assertSessionHasErrors(['title', 'body', 'level', 'source_name', 'status']);
    }
}
