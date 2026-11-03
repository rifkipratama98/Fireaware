<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'code', 'center_lat', 'center_lng'];

    public function hotspots(): HasMany
    {
        return $this->hasMany(Hotspot::class);
    }

    /** @return BelongsToMany<InformationItem, $this> */
    public function informationItems(): BelongsToMany
    {
        return $this->belongsToMany(InformationItem::class, 'information_item_province')->withTimestamps();
    }
}
