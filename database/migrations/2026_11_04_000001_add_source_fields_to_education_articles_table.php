<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom sumber rujukan pada tabel education_articles.
     */
    public function up(): void
    {
        Schema::table('education_articles', function (Blueprint $table) {
            $table->string('source_name', 160)->nullable()->after('author');
            $table->string('source_url', 255)->nullable()->after('source_name');
        });
    }

    /**
     * Batalkan penambahan kolom sumber rujukan.
     */
    public function down(): void
    {
        Schema::table('education_articles', function (Blueprint $table) {
            $table->dropColumn(['source_name', 'source_url']);
        });
    }
};
