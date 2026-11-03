<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Informasi Kewaspadaan.
     *
     * Struktur ini sengaja memisahkan:
     * - identitas sumber (information_sources) dari isi informasi, sehingga
     *   informasi dari sumber berbeda tidak pernah digabung tanpa jejak sumber;
     * - konten editorial FireAware (content_origin = editorial) dari data
     *   eksternal yang diringkas dari sumber lain (content_origin = external);
     * - status yang disediakan sumber (status_label/status_key, boleh kosong)
     *   dari status yang dikarang aplikasi — bila sumber tidak menyediakan
     *   status, kolomnya tetap NULL dan UI tidak menampilkan status apa pun.
     *
     * Waktu memakai tipe dateTime (bukan timestamp) agar konsisten dengan
     * tabel kualitas udara dan tidak terkena batas kolom TIMESTAMP MySQL.
     */
    public function up(): void
    {
        // Sumber informasi yang dapat diverifikasi
        Schema::create('information_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug', 120)->unique();
            // authority = otoritas resmi, media = lembaga/media lain,
            // editorial = konten yang ditulis sendiri oleh FireAware
            $table->string('kind', 20)->default('authority');
            $table->string('organization', 150)->nullable();
            $table->string('homepage_url', 255)->nullable();
            $table->text('description')->nullable();
            // Diisi hanya setelah identitas sumber diperiksa manusia.
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();

            $table->index('kind');
        });

        // Daftar informasi kewaspadaan
        Schema::create('information_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('information_source_id')
                ->constrained('information_sources')
                ->cascadeOnDelete();
            $table->string('slug', 190)->unique();
            // editorial = ditulis tim FireAware, external = kutipan/ringkasan sumber
            $table->string('content_origin', 20)->default('editorial');
            $table->string('title', 180);
            $table->text('summary');
            $table->longText('body')->nullable();
            // Status hanya diisi bila sumber memang menyediakannya.
            $table->string('status_label', 60)->nullable();  // label apa adanya dari sumber
            $table->string('status_key', 20)->nullable();    // normalisasi untuk warna
            $table->dateTime('published_at')->nullable();      // waktu terbit di sumber
            $table->dateTime('source_updated_at')->nullable(); // waktu pembaruan di sumber
            $table->string('source_url', 255)->nullable();     // tautan ke sumber asli
            $table->boolean('is_demo')->default(false);        // penanda data contoh
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
            $table->index('status_key');
        });

        // Cakupan wilayah — satu informasi boleh mencakup beberapa provinsi
        Schema::create('information_item_province', function (Blueprint $table) {
            $table->id();
            $table->foreignId('information_item_id')
                ->constrained('information_items')
                ->cascadeOnDelete();
            $table->foreignId('province_id')->constrained()->cascadeOnDelete();

            $table->unique(['information_item_id', 'province_id'], 'info_item_province_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('information_item_province');
        Schema::dropIfExists('information_items');
        Schema::dropIfExists('information_sources');
    }
};
