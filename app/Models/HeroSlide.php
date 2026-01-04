<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeroSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_id',
        'display_date',
        'link_url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'image_id' => 'integer',
        'display_date' => 'date',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'display_date_label',
        'image_url',
    ];

    public function image(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'image_id');
    }

    public function getDisplayDateLabelAttribute(): ?string
    {
        if (!$this->display_date) {
            return null;
        }

        return $this->display_date->translatedFormat('d F Y');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image?->url;
    }
}
