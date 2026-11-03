<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Panduan keselamatan (konten edukasi pilihan)
        Schema::create('safety_guides', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->string('slug', 160)->unique();
            $table->unsignedTinyInteger('audience')->default(1); // 1 umum, 2 pelajar, 3 petani
            $table->text('summary');
            $table->string('icon', 50)->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('safety_guides');
    }
};
