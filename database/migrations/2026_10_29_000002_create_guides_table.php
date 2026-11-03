<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Panduan Keselamatan (Model Guide).
     *
     * Berisi artikel panduan edukasi keselamatan masyarakat saat menghadapi
     * kabut asap atau indikasi kebakaran hutan dan lahan di Kalimantan.
     *
     * Seluruh konten harus memiliki rujukan sumber yang valid dan tidak
     * menyarankan masyarakat mendekati titik api berbahaya.
     */
    public function up(): void
    {
        Schema::create('guides', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->string('category', 60)->index();
            $table->text('summary');
            $table->longText('content');
            $table->string('source_name', 160);
            $table->string('source_url', 255)->nullable();
            $table->text('source_notes')->nullable();
            $table->unsignedTinyInteger('reading_time_minutes')->default(3);
            $table->enum('status', ['published', 'draft'])->default('published')->index();
            $table->dateTime('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guides');
    }
};
