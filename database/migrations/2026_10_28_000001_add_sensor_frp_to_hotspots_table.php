<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan atribut detail untuk halaman Peta Hotspot:
     * - sensor   : satelit/sensor sumber (mis. "VIIRS S-NPP", "MODIS Aqua")
     * - frp      : Fire Radiative Power dalam megawatt (MW), bila tersedia
     */
    public function up(): void
    {
        Schema::table('hotspots', function (Blueprint $table) {
            $table->string('sensor', 50)->nullable()->after('source');
            $table->decimal('frp', 8, 2)->nullable()->after('confidence');
            $table->index(['detected_at']);
        });
    }

    public function down(): void
    {
        Schema::table('hotspots', function (Blueprint $table) {
            $table->dropColumn(['sensor', 'frp']);
        });
    }
};
