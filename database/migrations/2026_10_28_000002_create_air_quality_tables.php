<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel kualitas udara.
     *
     * Tidak ada nilai yang diisi otomatis di sini — data hanya masuk melalui
     * service integrasi eksternal yang memvalidasi nilai sebelum disimpan.
     */
    public function up(): void
    {
        // Stasiun pemantauan
        Schema::create('air_quality_stations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 150)->unique();
            $table->string('source', 50);           // openaq | bmkg | manual
            $table->string('external_id', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['province_id', 'is_active']);
        });

        // Pembacaan (reading) per waktu ukur
        Schema::create('air_quality_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('air_quality_station_id')
                ->constrained('air_quality_stations')
                ->cascadeOnDelete();
            $table->string('pollutant', 20)->default('pm25'); // hanya PM2.5 untuk saat ini
            $table->decimal('value', 8, 2);                   // µg/m³
            $table->string('unit', 20)->default('µg/m³');
            $table->unsignedSmallInteger('aqi_value')->nullable();     // ISPU
            $table->string('aqi_category', 40)->nullable();            // Baik, Sedang, ...
            $table->string('standard', 20)->default('ISPU');           // standar kategori
            // dateTime dipakai (bukan timestamp) karena MySQL membatasi jumlah
            // kolom TIMESTAMP NOT NULL tanpa nilai default pada satu tabel.
            $table->dateTime('measured_at');                           // waktu pengukuran
            $table->dateTime('fetched_at');                            // waktu pengambilan data
            $table->string('source', 50);
            $table->timestamps();

            // Satu pembacaan per stasiun + polutan + waktu ukur
            $table->unique(['air_quality_station_id', 'pollutant', 'measured_at'], 'aqr_station_pollutant_time_unique');
            $table->index(['air_quality_station_id', 'measured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('air_quality_readings');
        Schema::dropIfExists('air_quality_stations');
    }
};
