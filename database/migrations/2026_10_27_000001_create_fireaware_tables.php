<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Provinsi di Pulau Kalimantan
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->string('code', 10)->nullable();
            $table->decimal('center_lat', 10, 7)->nullable();
            $table->decimal('center_lng', 10, 7)->nullable();
            $table->timestamps();
        });

        // Titik panas (hotspot)
        Schema::create('hotspots', function (Blueprint $table) {
            $table->id();
            $table->string('source')->default('demo'); // demo | viirs | modis
            $table->foreignId('province_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedTinyInteger('confidence')->nullable(); // 0-100
            $table->string('city')->nullable();
            $table->timestamp('detected_at')->nullable();
            $table->timestamps();
            $table->index(['province_id', 'detected_at']);
        });

        // Pengumuman kewaspadaan
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->text('body');
            $table->unsignedTinyInteger('level')->default(1); // 1 info, 2 waspada, 3 darurat
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('hotspots');
        Schema::dropIfExists('provinces');
    }
};
