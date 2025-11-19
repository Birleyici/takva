<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'profile_image_id',
    ];

    protected $casts = [
        'profile_image_id' => 'integer',
    ];

    public function profileImage(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'profile_image_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
