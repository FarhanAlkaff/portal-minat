<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSlider extends Model
{
    protected $fillable = [
        'image_path',
        'caption',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getUrlAttribute(): string
    {
        return asset($this->image_path);
    }
}
