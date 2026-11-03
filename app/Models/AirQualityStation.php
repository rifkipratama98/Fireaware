<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AirQualityStation extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id', 'name', 'slug', 'source', 'external_id',
        'latitude', 'longitude', 'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function readings(): HasMany
    {
        return $this->hasMany(AirQualityReading::class);
    }

    /** Pembacaan terbaru untuk polutan tertentu. */
    public function latestReading(string $pollutant = 'pm25'): ?AirQualityReading
    {
        return $this->readings()
            ->where('pollutant', $pollutant)
            ->orderByDesc('measured_at')
            ->first();
    }
}
