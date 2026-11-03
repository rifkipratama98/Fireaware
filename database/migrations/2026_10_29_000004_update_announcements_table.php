<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Memperbarui tabel announcements agar mendukung slug, wilayah terkait,
     * penerbit/sumber rujukan resmi, ringkasan, dan status publikasi.
     */
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->string('slug', 200)->nullable()->unique()->after('title');
            $table->text('summary')->nullable()->after('slug');
            $table->foreignId('province_id')->nullable()->after('summary')->constrained('provinces')->nullOnDelete();
            $table->string('source_name', 160)->nullable()->after('level');
            $table->string('source_url', 255)->nullable()->after('source_name');
            $table->string('status', 20)->default('published')->index()->after('source_url');
            $table->boolean('is_demo')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropColumn([
                'slug',
                'summary',
                'province_id',
                'source_name',
                'source_url',
                'status',
                'is_demo',
            ]);
        });
    }
};
