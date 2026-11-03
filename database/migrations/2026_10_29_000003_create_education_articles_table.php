<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Artikel Edukasi Karhutla (EducationArticle).
     *
     * Berisi literasi komprehensif mengenai ekosistem gambut, penyebab,
     * dampak ekologis, teknik pencegahan, dan hukum kebakaran hutan/lahan.
     */
    public function up(): void
    {
        Schema::create('education_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->string('category', 60)->index();
            $table->text('summary');
            $table->longText('content');
            $table->string('image_path', 255)->nullable();
            $table->string('image_caption', 255)->nullable();
            $table->string('author', 120)->nullable();
            $table->unsignedTinyInteger('reading_time_minutes')->default(3);
            $table->string('status', 20)->default('published')->index();
            $table->boolean('is_demo')->default(false);
            $table->dateTime('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('education_articles');
    }
};
