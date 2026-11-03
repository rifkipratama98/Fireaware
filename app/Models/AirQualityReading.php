<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AirQualityReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'air_quality_station_id', 'pollutant', 'value', 'unit',
        'aqi_value', 'aqi_category', 'standard',
        'measured_at', 'fetched_at', 'source',
    ];

    protected $casts = [
        'value' => 'float',
        'aqi_value' => 'integer',
        'measured_at' => 'datetime',
        'fetched_at' => 'datetime',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(AirQualityStation::class, 'air_quality_station_id');
    }
}
