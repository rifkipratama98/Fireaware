<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel Konfigurasi Notifikasi dan Riwayat Pengiriman.
     */
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            // Sakelar utama & kanal pengiriman
            $table->boolean('is_enabled')->default(true);
            $table->boolean('browser_enabled')->default(true);
            $table->boolean('email_enabled')->default(false);
            $table->boolean('webhook_enabled')->default(false);
            $table->text('email_recipients')->nullable();
            $table->string('webhook_url', 255)->nullable();

            // Jenis informasi layak beritahu & ambang batas pemicu
            $table->boolean('notify_hotspots')->default(true);
            $table->unsignedTinyInteger('hotspot_min_confidence')->default(80);
            $table->decimal('hotspot_min_frp', 8, 2)->default(50.00);

            $table->boolean('notify_air_quality')->default(true);
            $table->decimal('air_quality_min_pm25', 8, 2)->default(55.40);

            $table->boolean('notify_announcements')->default(true);
            $table->unsignedTinyInteger('announcement_min_level')->default(2); // 1 info, 2 waspada, 3 darurat

            // Wilayah pantauan (null = seluruh Kalimantan, atau array IDs)
            $table->json('monitored_provinces')->nullable();

            // Aturan frekuensi anti-spam dan jam hening
            $table->unsignedInteger('frequency_limit_minutes')->default(60);
            $table->boolean('quiet_hours_enabled')->default(false);
            $table->string('quiet_hours_start', 5)->nullable()->default('22:00');
            $table->string('quiet_hours_end', 5)->nullable()->default('06:00');

            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 30)->index(); // browser | email | webhook | system
            $table->string('title', 200);
            $table->text('message');
            $table->string('category', 50)->index(); // hotspot | air_quality | announcement | test
            $table->string('level', 20)->default('info'); // info | warning | danger
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->string('recipient_target', 255)->nullable();
            $table->string('status', 30)->index(); // sent | failed | suppressed | pending
            $table->text('error_message')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();

            $table->index(['category', 'created_at']);
            $table->index(['channel', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notification_settings');
    }
};
