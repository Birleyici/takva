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
        'social_twitter',
        'social_instagram',
        'social_youtube',
        'social_facebook',
        'social_whatsapp',
    ];

    protected $appends = [
        'logo_url',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo_path) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }
}
