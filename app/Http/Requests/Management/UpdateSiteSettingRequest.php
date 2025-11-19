<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_email' => ['nullable', 'email', 'max:180'],
            'contact_phone' => ['nullable', 'string', 'max:100'],
            'contact_address' => ['nullable', 'string', 'max:255'],
            'contact_map_embed' => ['nullable', 'string'],
            'contact_hero_text' => ['nullable', 'string'],
            'social_twitter' => ['nullable', 'url'],
            'social_instagram' => ['nullable', 'url'],
            'social_youtube' => ['nullable', 'url'],
            'social_facebook' => ['nullable', 'url'],
            'social_whatsapp' => ['nullable', 'string', 'max:50'],
        ];
    }
}
