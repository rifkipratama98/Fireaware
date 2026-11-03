<?php

namespace Tests\Feature;

use App\Models\EducationArticle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EducationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_display_public_education_listing(): void
    {
        $published = EducationArticle::create([
            'title' => 'Mengenal Kedalaman Gambut',
            'slug' => 'mengenal-kedalaman-gambut',
            'category' => 'ekosistem-gambut',
            'summary' => 'Artikel tentang kedalaman gambut dan kapasitas penyerapan air.',
            'content' => 'Lahan gambut tebal di pedalaman Kalimantan menyimpan air dan cadangan karbon melimpah.',
            'author' => 'Tim Riset',
            'status' => EducationArticle::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $draft = EducationArticle::create([
            'title' => 'Draf Riset Gambut Rahasia',
            'slug' => 'draf-riset-gambut-rahasia',
            'category' => 'ekosistem-gambut',
            'summary' => 'Ringkasan draf yang masih dalam penyusunan.',
            'content' => 'Isi draf rahasia.',
            'author' => 'Internal',
            'status' => EducationArticle::STATUS_DRAFT,
            'published_at' => null,
        ]);

        $response = $this->get(route('edukasi.index'));

        $response->assertStatus(200);
        $response->assertSee('Mengenal Kedalaman Gambut');
        $response->assertDontSee('Draf Riset Gambut Rahasia');
    }

    public function test_can_filter_education_by_category(): void
    {
        EducationArticle::create([
            'title' => 'Faktor El Nino dan Kemarau Panjang',
            'slug' => 'faktor-el-nino-dan-kemarau-panjang',
            'category' => 'faktor-penyebab',
            'summary' => 'Pengaruh osilasi iklim terhadap kekeringan hutan tropis.',
            'content' => 'Penurunan curah hujan drastis memicu peningkatan kerentanan.',
            'status' => EducationArticle::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        EducationArticle::create([
            'title' => 'Regulasi Sanksi Pembakaran Hutan',
            'slug' => 'regulasi-sanksi-pembakaran-hutan',
            'category' => 'hukum-kebijakan',
            'summary' => 'Pasal-pasal pidana lingkungan hidup terkait karhutla.',
            'content' => 'UU No 32 Tahun 2009 melarang tegas pembukaan lahan dengan cara membakar.',
            'status' => EducationArticle::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('edukasi.index', ['kategori' => 'faktor-penyebab']));

        $response->assertStatus(200);
        $response->assertSee('Faktor El Nino dan Kemarau Panjang');
        $response->assertDontSee('Regulasi Sanksi Pembakaran Hutan');
    }

    public function test_can_view_education_detail_by_slug(): void
    {
        $article = EducationArticle::create([
            'title' => 'Sistem Sekat Kanal untuk Restorasi',
            'slug' => 'sistem-sekat-kanal-untuk-restorasi',
            'category' => 'teknik-pencegahan',
            'summary' => 'Metode canal blocking untuk menjaga muka air tanah gambut.',
            'content' => "### Prinsip Kerja\nSekat kanal menahan air agar tidak terbuang.\n\n### Manfaat Hidrologis\nMencegah bara api bawah tanah merambat.",
            'author' => 'Spesialis Gambut',
            'status' => EducationArticle::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get(route('edukasi.show', $article->slug));

        $response->assertStatus(200);
        $response->assertSee('Sistem Sekat Kanal untuk Restorasi');
        $response->assertSee('Spesialis Gambut');
        $response->assertSee('Kembali ke Edukasi');
    }

    public function test_draft_education_article_returns_404_on_public_show(): void
    {
        $draft = EducationArticle::create([
            'title' => 'Artikel Belum Terbit',
            'slug' => 'artikel-belum-terbit',
            'category' => 'faktor-penyebab',
            'summary' => 'Ringkasan draf.',
            'content' => 'Isi draf belum boleh diakses publik.',
            'status' => EducationArticle::STATUS_DRAFT,
            'published_at' => null,
        ]);

        $response = $this->get(route('edukasi.show', $draft->slug));
        $response->assertStatus(404);
    }

    public function test_admin_can_view_all_education_articles(): void
    {
        EducationArticle::create([
            'title' => 'Artikel Published',
            'slug' => 'artikel-published',
            'category' => 'faktor-penyebab',
            'summary' => 'Ringkasan artikel tayang.',
            'content' => 'Isi materi artikel edukasi.',
            'status' => EducationArticle::STATUS_PUBLISHED,
        ]);

        EducationArticle::create([
            'title' => 'Artikel Masih Draft',
            'slug' => 'artikel-masih-draft',
            'category' => 'ekosistem-gambut',
            'summary' => 'Ringkasan draf yang belum terbit.',
            'content' => 'Isi materi draf.',
            'status' => EducationArticle::STATUS_DRAFT,
        ]);

        $response = $this->get(route('admin.education.index'));

        $response->assertStatus(200);
        $response->assertSee('Artikel Published');
        $response->assertSee('Artikel Masih Draft');
    }

    public function test_admin_can_store_education_article_with_image_upload(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('gambut.jpg', 600, 400);

        $payload = [
            'title' => 'Mekanisme Pemadaman Ground Fire',
            'slug' => 'mekanisme-pemadaman-ground-fire',
            'category' => 'teknik-pencegahan',
            'summary' => 'Langkah-langkah teknis penyuntikan air ke dalam gambut membara.',
            'content' => 'Menggunakan nozzle suntik gambut bertekanan tinggi untuk menjangkau lapisan bara.',
            'image' => $file,
            'image_caption' => 'Penyemprotan gambut dalam.',
            'author' => 'Tim Manggala Agni',
            'status' => EducationArticle::STATUS_PUBLISHED,
            'published_at' => now()->format('Y-m-d H:i:s'),
        ];

        $response = $this->post(route('admin.education.store'), $payload);

        $response->assertRedirect(route('admin.education.index'));

        $this->assertDatabaseHas('education_articles', [
            'slug' => 'mekanisme-pemadaman-ground-fire',
            'author' => 'Tim Manggala Agni',
            'status' => EducationArticle::STATUS_PUBLISHED,
        ]);

        $article = EducationArticle::where('slug', 'mekanisme-pemadaman-ground-fire')->first();
        $this->assertNotNull($article->image_path);
        Storage::disk('public')->assertExists($article->image_path);
    }

    public function test_image_validation_rejects_non_image_files(): void
    {
        Storage::fake('public');

        $invalidFile = UploadedFile::fake()->create('dokumen.pdf', 500, 'application/pdf');

        $payload = [
            'title' => 'Uji Coba Unggah File Salah',
            'category' => 'faktor-penyebab',
            'summary' => 'Ringkasan artikel pengujian validasi berkas.',
            'content' => 'Isi artikel edukasi pengujian berkas lampiran.',
            'image' => $invalidFile,
            'status' => EducationArticle::STATUS_PUBLISHED,
        ];

        $response = $this->post(route('admin.education.store'), $payload);
        $response->assertSessionHasErrors('image');
    }

    public function test_admin_can_update_and_delete_education_article(): void
    {
        $article = EducationArticle::create([
            'title' => 'Artikel Sebelum Revisi',
            'slug' => 'artikel-sebelum-revisi',
            'category' => 'faktor-penyebab',
            'summary' => 'Ringkasan lama.',
            'content' => 'Isi lama materi edukasi.',
            'status' => EducationArticle::STATUS_DRAFT,
        ]);

        $updatePayload = [
            'title' => 'Artikel Setelah Revisi Lengkap',
            'slug' => 'artikel-sebelum-revisi',
            'category' => 'dampak-lingkungan',
            'summary' => 'Ringkasan baru yang diperbaiki.',
            'content' => 'Isi baru materi edukasi karhutla.',
            'status' => EducationArticle::STATUS_PUBLISHED,
            'published_at' => now()->format('Y-m-d H:i:s'),
        ];

        $resUpdate = $this->put(route('admin.education.update', $article), $updatePayload);
        $resUpdate->assertRedirect(route('admin.education.index'));

        $this->assertDatabaseHas('education_articles', [
            'id' => $article->id,
            'title' => 'Artikel Setelah Revisi Lengkap',
            'category' => 'dampak-lingkungan',
            'status' => EducationArticle::STATUS_PUBLISHED,
        ]);

        $resDelete = $this->delete(route('admin.education.destroy', $article));
        $resDelete->assertRedirect(route('admin.education.index'));
        $this->assertDatabaseMissing('education_articles', ['id' => $article->id]);
    }
}
