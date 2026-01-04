<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'youtube_url',
        'youtube_id',
        'video_category_id',
        'description',
        'is_active',
    ];

    protected $casts = [
        'video_category_id' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'thumbnail_url',
        'embed_url',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->youtube_id) {
            return null;
        }

        return "https://img.youtube.com/vi/{$this->youtube_id}/hqdefault.jpg";
    }

    public function getEmbedUrlAttribute(): ?string
    {
        if (!$this->youtube_id) {
            return null;
        }

        return "https://www.youtube.com/embed/{$this->youtube_id}";
    }

    public function category()
    {
        return $this->belongsTo(VideoCategory::class, 'video_category_id');
    }
}
