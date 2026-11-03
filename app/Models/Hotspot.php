<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hotspot extends Model
{
    use HasFactory;

    protected $table = 'hotspots';

    protected $fillable = ['source', 'sensor', 'province_id', 'latitude', 'longitude', 'confidence', 'frp', 'city', 'detected_at'];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'confidence' => 'integer',
        'frp' => 'float',
        'detected_at' => 'datetime',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    /** Scope: hanya data berlabel demo. */
    public function scopeDemo($query)
    {
        return $this->where('source', 'demo');
    }

    /** Scope: urut dari deteksi terbaru. */
    public function scopeLatestDetected($query)
    {
        return $this->latest('detected_at');
    }
}
