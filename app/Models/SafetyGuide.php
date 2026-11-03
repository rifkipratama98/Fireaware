<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SafetyGuide extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'audience', 'summary', 'icon', 'sort_order', 'is_active'];

    protected $casts = [
        'audience' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /** Label audiens untuk tampilan. */
    public function audienceLabel(): string
    {
        return match ($this->audience) {
            2 => 'Pelajar',
            3 => 'Petani & Masyarakat Perdesaan',
            default => 'Umum',
        };
    }
}
