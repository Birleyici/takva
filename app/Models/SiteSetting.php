<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_email',
        'contact_phone',
        'contact_address',
        'contact_map_embed',
        'contact_hero_text',
        'social_twitter',
        'social_instagram',
        'social_youtube',
        'social_facebook',
        'social_whatsapp',
    ];
}
