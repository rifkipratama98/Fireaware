<?php

namespace Tests\Feature;

use App\Models\Guide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuideTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_display_public_guide_listing(): void
    {
        $published = Guide::create([
            'title' => 'Panduan Menghadapi Asap Tebal',
            'slug' => 'panduan-menghadapi-asap-tebal',
            'category' => 'menghadapi-asap',
            'summary' => 'Ringkasan lengkap tentang langkah perlindungan diri saat kabut asap memasuki pemukiman.',
            'content' => "### Langkah Pertama\nTutup seluruh ventilasi.\n\n### Langkah Kedua\nGunakan masker N95.",
            'source_name' => 'Kementerian Kesehatan RI',
            'source_url' => 'https://kemkes.go.id',
            'status' => Guide::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $draft = Guide::create([
            'title' => 'Draf Rahasia Tanpa Rilis',
            'slug' => 'draf-rahasia-tanpa-rilis',
            'category' => 'menghadapi-asap',
            'summary' => 'Ringkasan artikel yang belum selesai ditulis oleh editor.',
            'content' => 'Isi draf panduan yang belum boleh dibaca oleh masyarakat umum.',
            'source_name' => 'Internal Editor',
            'status' => Guide::STATUS_DRAFT,
            'published_at' => null,
        ]);

        $response = $this->get(route('guides.index'));

        $response->assertStatus(200);
        $response->assertSee('Panduan Menghadapi Asap Tebal');
        $response->assertSee('Kementerian Kesehatan RI');
        // Draft tidak boleh tampil di halaman publik
        $response->assertDontSee('Draf Rahasia Tanpa Rilis');
    }

    public function test_can_filter_guides_by_category(): void
    {
        Guide::create([
            'title' => 'Prosedur Masker Partikulat',
            'slug' => 'prosedur-masker-partikulat',
            'category' => 'mengurangi-paparan-asap',
            'summary' => 'Cara penggunaan masker N95 yang benar untuk menyaring partikel berbahaya PM2.5.',
            'content' => 'Gunakan masker dengan fit check yang rapat di bagian hidung.',
            'source_name' => 'WHO & Kemenkes',
            'status' => Guide::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        Guide::create([
            'title' => 'Lapor Titik Api Tanpa Mendekat',
            'slug' => 'lapor-titik-api-tanpa-mendekat',
            'category' => 'tindakan-ketika-melihat-indikasi-kebakaran',
            'summary' => 'Protokol pelaporan titik panas ke BPBD dan Manggala Agni tanpa mendekati api.',
            'content' => 'Evakuasi diri segera dan hubungi 112 atau posko siaga.',
            'source_name' => 'KLHK SIPONGI',
            'status' => Guide::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        // Filter kategori 'mengurangi-paparan-asap'
        $response = $this->get(route('guides.index', ['kategori' => 'mengurangi-paparan-asap']));

        $response->assertStatus(200);
        $response->assertSee('Prosedur Masker Partikulat');
        $response->assertDontSee('Lapor Titik Api Tanpa Mendekat');
    }

    public function test_can_search_guides_by_title(): void
    {
        Guide::create([
            'title' => 'Pentingnya Hidrasi Saat Cuaca Berasap',
            'slug' => 'pentingnya-hidrasi-saat-cuaca-berasap',
            'category' => 'persiapan-saat-kualitas-udara-memburuk',
            'summary' => 'Konsumsi air putih minimal 2 liter per hari membantu mukosa pernapasan.',
            'content' => 'Minum air secara berkala untuk menjaga kelembapan tenggorokan.',
            'source_name' => 'Dinas Kesehatan',
            'status' => Guide::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('guides.index', ['q' => 'Hidrasi']));

        $response->assertStatus(200);
        $response->assertSee('Pentingnya Hidrasi Saat Cuaca Berasap');

        $emptyResponse = $this->get(route('guides.index', ['q' => 'KataKunciTidakAda']));
        $emptyResponse->assertStatus(200);
        $emptyResponse->assertSee('Tidak Ada Panduan yang Ditemukan');
    }

    public function test_can_view_published_guide_detail_by_slug(): void
    {
        $guide = Guide::create([
            'title' => 'Kanal Pantauan Resmi BMKG dan SIPONGI',
            'slug' => 'kanal-pantauan-resmi-bmkg-dan-sipongi',
            'category' => 'cara-memperoleh-informasi-resmi',
            'summary' => 'Panduan mengecek informasi akurat sebaran kabut asap dari portal terpercaya.',
            'content' => "### Membaca Data Satelit\nHotspot adalah anomali suhu permukaan bumi.\n\n### Tingkat Kepercayaan\nTingkat di atas 80% menandakan api aktif.",
            'source_name' => 'BMKG & KLHK',
            'source_url' => 'https://sipongi.menlhk.go.id',
            'source_notes' => 'Rujukan modul monitoring satelit.',
            'status' => Guide::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('guides.show', $guide->slug));

        $response->assertStatus(200);
        $response->assertSee('Kanal Pantauan Resmi BMKG dan SIPONGI');
        $response->assertSee('BMKG & KLHK');
        $response->assertSee('https://sipongi.menlhk.go.id');
        $response->assertSee('Kembali ke Panduan');
    }

    public function test_draft_guide_returns_404_on_public_show(): void
    {
        $draft = Guide::create([
            'title' => 'Draf Tertutup',
            'slug' => 'draf-tertutup',
            'category' => 'menghadapi-asap',
            'summary' => 'Ringkasan draf yang belum siap tayang untuk publik.',
            'content' => 'Isi draf panduan keselamatan dalam tahap peninjauan.',
            'source_name' => 'Tim Riset',
            'status' => Guide::STATUS_DRAFT,
            'published_at' => null,
        ]);

        $response = $this->get(route('guides.show', $draft->slug));
        $response->assertStatus(404);
    }

    public function test_admin_can_view_all_guides_including_drafts(): void
    {
        $this->actingAs(User::factory()->create());
        Guide::create([
            'title' => 'Panduan Tayang',
            'slug' => 'panduan-tayang',
            'category' => 'menghadapi-asap',
            'summary' => 'Ringkasan panduan yang sudah dipublikasikan.',
            'content' => 'Isi panduan tayang untuk masyarakat.',
            'source_name' => 'Kemenkes RI',
            'status' => Guide::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        Guide::create([
            'title' => 'Panduan Masih Draft',
            'slug' => 'panduan-masih-draft',
            'category' => 'mengurangi-paparan-asap',
            'summary' => 'Ringkasan panduan yang masih dalam status draf.',
            'content' => 'Isi artikel draf yang sedang disiapkan admin.',
            'source_name' => 'WHO Indonesia',
            'status' => Guide::STATUS_DRAFT,
            'published_at' => null,
        ]);

        $response = $this->get(route('admin.guides.index'));

        $response->assertStatus(200);
        $response->assertSee('Panduan Tayang');
        $response->assertSee('Panduan Masih Draft');
        $response->assertSee('Published');
        $response->assertSee('Draft');
    }

    public function test_admin_can_store_valid_guide(): void
    {
        $this->actingAs(User::factory()->create());
        $payload = [
            'title' => 'Panduan Baru Pembuatan Clean Room',
            'slug' => 'panduan-baru-pembuatan-clean-room',
            'category' => 'mengurangi-paparan-asap',
            'summary' => 'Metode menyekat satu ruangan berpenyaring udara di dalam rumah saat kabut asap pekat.',
            'content' => "### Isolasi Ruangan\nTutup semua jendela rapat-rapat.\n\n### Pembersih Udara\nGunakan penyaring udara berefisiensi tinggi.",
            'source_name' => 'Kementerian Kesehatan RI & WHO',
            'source_url' => 'https://kemkes.go.id',
            'source_notes' => 'Panduan Sanitasi Udara Rumah Tangga Darurat.',
            'status' => Guide::STATUS_PUBLISHED,
            'published_at' => now()->format('Y-m-d H:i:s'),
        ];

        $response = $this->post(route('admin.guides.store'), $payload);

        $response->assertRedirect(route('admin.guides.index'));
        $this->assertDatabaseHas('guides', [
            'slug' => 'panduan-baru-pembuatan-clean-room',
            'status' => Guide::STATUS_PUBLISHED,
        ]);
    }

    public function test_admin_store_validates_source_and_dangerous_content(): void
    {
        $this->actingAs(User::factory()->create());
        // 1. Validasi sumber rujukan kosong / tidak kredibel
        $invalidSourcePayload = [
            'title' => 'Panduan Tanpa Sumber Valid',
            'slug' => 'panduan-tanpa-sumber-valid',
            'category' => 'menghadapi-asap',
            'summary' => 'Ringkasan panduan yang tidak memiliki rujukan kredibel.',
            'content' => 'Isi artikel panduan keselamatan masyarakat.',
            'source_name' => 'tidak ada',
            'status' => Guide::STATUS_PUBLISHED,
        ];

        $response = $this->post(route('admin.guides.store'), $invalidSourcePayload);
        $response->assertSessionHasErrors('source_name');

        // 2. Validasi anjuran berbahaya: menyuruh mendekati api
        $dangerousPayload = [
            'title' => 'Panduan Berbahaya Menghampiri Api',
            'slug' => 'panduan-berbahaya-menghampiri-api',
            'category' => 'tindakan-ketika-melihat-indikasi-kebakaran',
            'summary' => 'Ringkasan panduan yang menyuruh warga melakukan tindakan berisiko.',
            'content' => 'Warga disarankan segera mendekati titik api di lahan gambut lalu siram dengan ember.',
            'source_name' => 'Warga Lokal',
            'status' => Guide::STATUS_PUBLISHED,
        ];

        $response2 = $this->post(route('admin.guides.store'), $dangerousPayload);
        $response2->assertSessionHasErrors('content');
    }

    public function test_admin_can_update_guide(): void
    {
        $this->actingAs(User::factory()->create());
        $guide = Guide::create([
            'title' => 'Judul Awal Panduan',
            'slug' => 'judul-awal-panduan',
            'category' => 'menghadapi-asap',
            'summary' => 'Ringkasan awal sebelum diedit oleh admin redaksi.',
            'content' => 'Isi awal panduan yang memerlukan revisi redaksi.',
            'source_name' => 'Kemenkes RI',
            'status' => Guide::STATUS_DRAFT,
            'published_at' => null,
        ]);

        $updatePayload = [
            'title' => 'Judul Panduan Setelah Revisi',
            'slug' => 'judul-awal-panduan',
            'category' => 'menghadapi-asap',
            'summary' => 'Ringkasan hasil perbaikan yang lebih lengkap dan jelas.',
            'content' => 'Isi panduan hasil revisi yang sudah siap dipublikasikan.',
            'source_name' => 'Kemenkes RI & BPBD',
            'status' => Guide::STATUS_PUBLISHED,
            'published_at' => now()->format('Y-m-d H:i:s'),
        ];

        $response = $this->put(route('admin.guides.update', $guide), $updatePayload);

        $response->assertRedirect(route('admin.guides.index'));
        $this->assertDatabaseHas('guides', [
            'id' => $guide->id,
            'title' => 'Judul Panduan Setelah Revisi',
            'status' => Guide::STATUS_PUBLISHED,
        ]);
    }

    public function test_admin_can_delete_guide(): void
    {
        $this->actingAs(User::factory()->create());
        $guide = Guide::create([
            'title' => 'Panduan Akan Dihapus',
            'slug' => 'panduan-akan-dihapus',
            'category' => 'menghadapi-asap',
            'summary' => 'Ringkasan artikel yang akan dihapus dari sistem.',
            'content' => 'Isi artikel yang sudah usang dan harus dibersihkan.',
            'source_name' => 'Arsip Lama',
            'status' => Guide::STATUS_DRAFT,
        ]);

        $response = $this->delete(route('admin.guides.destroy', $guide));

        $response->assertRedirect(route('admin.guides.index'));
        $this->assertDatabaseMissing('guides', [
            'id' => $guide->id,
        ]);
    }

    public function test_admin_can_view_guide_detail_including_draft(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $draftGuide = Guide::create([
            'title' => 'Panduan Khusus Draf Editor',
            'slug' => 'panduan-khusus-draf-editor',
            'category' => 'menghadapi-asap',
            'summary' => 'Ringkasan panduan yang masih dalam peninjauan internal tim.',
            'content' => '### Bagian 1\nLangkah-langkah terstruktur panduan keselamatan.',
            'source_name' => 'Kemenkes RI',
            'source_url' => 'https://kemkes.go.id',
            'status' => Guide::STATUS_DRAFT,
            'published_at' => null,
        ]);

        $response = $this->get(route('admin.guides.show', $draftGuide));

        $response->assertStatus(200);
        $response->assertSee('Panduan Khusus Draf Editor');
        $response->assertSee('Status: Draf');
        $response->assertSee('Kemenkes RI');
    }

    public function test_admin_can_filter_guides_by_status_and_search(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        Guide::create([
            'title' => 'Panduan Masker Kain vs Medis',
            'slug' => 'panduan-masker-kain-vs-medis',
            'category' => 'mengurangi-paparan-asap',
            'summary' => 'Perbandingan efektivitas filtrasi berbagai jenis masker pernapasan.',
            'content' => 'Masker N95 mampu memfilter partikel PM2.5 hingga 95 persen efektivitas.',
            'source_name' => 'Kemenkes RI',
            'status' => Guide::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        Guide::create([
            'title' => 'Draf SOP Evakuasi Mandiri',
            'slug' => 'draf-sop-evakuasi-mandiri',
            'category' => 'persiapan-saat-kualitas-udara-memburuk',
            'summary' => 'SOP jalur aman evakuasi saat jarak pandang di bawah 50 meter.',
            'content' => 'Hindari rute melewati semak belukar gambut yang berpotensi memiliki bara bawah tanah.',
            'source_name' => 'BPBD Kalbar',
            'status' => Guide::STATUS_DRAFT,
            'published_at' => null,
        ]);

        // Filter status published
        $resPublished = $this->get(route('admin.guides.index', ['status' => 'published']));
        $resPublished->assertStatus(200);
        $resPublished->assertSee('Panduan Masker Kain vs Medis');
        $resPublished->assertDontSee('Draf SOP Evakuasi Mandiri');

        // Filter status draft
        $resDraft = $this->get(route('admin.guides.index', ['status' => 'draft']));
        $resDraft->assertStatus(200);
        $resDraft->assertSee('Draf SOP Evakuasi Mandiri');
        $resDraft->assertDontSee('Panduan Masker Kain vs Medis');

        // Pencarian judul / sumber
        $resSearch = $this->get(route('admin.guides.index', ['q' => 'Evakuasi Mandiri']));
        $resSearch->assertStatus(200);
        $resSearch->assertSee('Draf SOP Evakuasi Mandiri');
        $resSearch->assertDontSee('Panduan Masker Kain vs Medis');
    }

    public function test_admin_store_validates_required_fields(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $response = $this->post(route('admin.guides.store'), []);

        $response->assertSessionHasErrors(['title', 'category', 'summary', 'content', 'source_name', 'status']);
    }
}
