<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Log Sinkronisasi Data Eksternal (NASA FIRMS, BMKG, Kualitas Udara).
     */
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source_key', 50)->index();
            $table->string('source_name', 120);
            $table->string('status', 30)->index(); // success | failed | timeout | not_configured | running
            $table->unsignedInteger('records_processed')->default(0);
            $table->unsignedInteger('records_added')->default(0);
            $table->unsignedInteger('records_updated')->default(0);
            $table->dateTime('observation_at')->nullable()->index();
            $table->dateTime('started_at');
            $table->dateTime('completed_at')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->text('payload_summary')->nullable();
            $table->string('trigger', 20)->default('manual'); // manual | scheduled | command
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['source_key', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
