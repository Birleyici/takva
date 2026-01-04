<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_email',
        'contact_phone',
        'contact_address',
        'contact_map_embed',
        'contact_hero_text',
        'logo_path',
        'hero_background_path',
        'theme_settings',
        'social_twitter',
        'social_instagram',
        'social_youtube',
        'social_facebook',
        'social_whatsapp',
    ];

    protected $casts = [
        'theme_settings' => 'array',
    ];

    protected $appends = [
        'logo_url',
        'hero_background_url',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo_path) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    public function getHeroBackgroundUrlAttribute(): ?string
    {
        if (!$this->hero_background_path) {
            return null;
        }

        return Storage::disk('public')->url($this->hero_background_path);
    }
}
