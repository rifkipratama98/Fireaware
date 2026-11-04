<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\EducationArticle;
use App\Models\Guide;
use App\Models\Hotspot;
use App\Models\Province;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_non_admin_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user_publik']);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_can_access_dashboard_and_see_components(): void
    {
        $admin = User::factory()->create(['name' => 'Budi Administrator']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard Pengelola');
        $response->assertSee('Budi Administrator');
        $response->assertSee('Panduan Published');
        $response->assertSee('Artikel Edukasi');
        $response->assertSee('Pengumuman Aktif');
        $response->assertSee('Status Integrasi Sumber Data');
        $response->assertSee('Titik Panas (Hotspot)');
        $response->assertSee('Kualitas Udara');
    }

    public function test_dashboard_displays_accurate_counts_from_database(): void
    {
        $admin = User::factory()->create();

        // Panduan: 3 published, 2 draft
        for ($i = 1; $i <= 3; $i++) {
            Guide::create([
                'title' => "Panduan {$i}",
                'slug' => "panduan-{$i}",
                'category' => 'menghadapi-asap',
                'summary' => 'Ringkasan panduan.',
                'content' => 'Konten panduan keselamatan.',
                'source_name' => 'Kemenkes RI',
                'status' => Guide::STATUS_PUBLISHED,
                'published_at' => now(),
            ]);
        }
        for ($i = 4; $i <= 5; $i++) {
            Guide::create([
                'title' => "Panduan {$i}",
                'slug' => "panduan-{$i}",
                'category' => 'menghadapi-asap',
                'summary' => 'Ringkasan panduan draft.',
                'content' => 'Konten panduan keselamatan draft.',
                'source_name' => 'Kemenkes RI',
                'status' => Guide::STATUS_DRAFT,
            ]);
        }

        // Edukasi: 4 published, 1 draft
        for ($i = 1; $i <= 4; $i++) {
            EducationArticle::create([
                'title' => "Artikel Edukasi {$i}",
                'slug' => "artikel-edukasi-{$i}",
                'category' => 'faktor-penyebab',
                'summary' => 'Ringkasan artikel edukasi.',
                'content' => 'Isi artikel edukasi lengkap mengenai karhutla lahan gambut.',
                'status' => EducationArticle::STATUS_PUBLISHED,
                'published_at' => now(),
            ]);
        }
        EducationArticle::create([
            'title' => 'Artikel Edukasi Draf',
            'slug' => 'artikel-edukasi-draf',
            'category' => 'faktor-penyebab',
            'summary' => 'Ringkasan draft.',
            'content' => 'Isi draft artikel edukasi.',
            'status' => EducationArticle::STATUS_DRAFT,
        ]);

        // Pengumuman: 2 active published, 1 draft
        for ($i = 1; $i <= 2; $i++) {
            Announcement::create([
                'title' => "Pengumuman {$i}",
                'slug' => "pengumuman-{$i}",
                'summary' => 'Ringkasan pengumuman.',
                'body' => 'Isi pengumuman resmi.',
                'level' => 1,
                'status' => Announcement::STATUS_PUBLISHED,
                'is_active' => true,
                'published_at' => now(),
            ]);
        }
        Announcement::create([
            'title' => 'Pengumuman Draf',
            'slug' => 'pengumuman-draf',
            'summary' => 'Ringkasan draf.',
            'body' => 'Isi draf pengumuman.',
            'level' => 1,
            'status' => Announcement::STATUS_DRAFT,
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('3'); // 3 published guides
        $response->assertSee('4'); // 4 published articles
        $response->assertSee('2'); // 2 active announcements
    }

    public function test_dashboard_differentiates_connection_sync_and_data_status(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Koneksi:');
        $response->assertSee('Sinkron:');
        $response->assertSee('Data:');
    }

    public function test_admin_can_access_all_management_navigation_routes(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.guides.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.education.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.announcements.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.regions.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.integrations.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.notifications.index'))->assertOk();
    }

    public function test_guest_cannot_access_any_management_routes(): void
    {
        $this->get(route('admin.regions.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.integrations.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.notifications.index'))->assertRedirect(route('admin.login'));
    }
}
