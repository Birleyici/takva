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
            'logo' => ['nullable', 'image', 'max:5120'],
            'remove_logo' => ['nullable', 'boolean'],
            'hero_background' => ['nullable', 'image', 'max:6144'],
            'remove_hero_background' => ['nullable', 'boolean'],
            'header_pattern' => ['nullable', 'image', 'max:4096'],
            'remove_header_pattern' => ['nullable', 'boolean'],
            'header_pattern_opacity' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'header_pattern_placement' => ['nullable', 'in:repeat,cover,contain,fit'],
            'footer_pattern' => ['nullable', 'image', 'max:4096'],
            'remove_footer_pattern' => ['nullable', 'boolean'],
            'footer_pattern_opacity' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'footer_pattern_placement' => ['nullable', 'in:repeat,cover,contain,fit'],
            'home_articles_pattern' => ['nullable', 'image', 'max:4096'],
            'remove_home_articles_pattern' => ['nullable', 'boolean'],
            'home_articles_pattern_opacity' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'home_articles_pattern_placement' => ['nullable', 'in:repeat,cover,contain,fit'],
            'home_issues_pattern' => ['nullable', 'image', 'max:4096'],
            'remove_home_issues_pattern' => ['nullable', 'boolean'],
            'home_issues_pattern_opacity' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'home_issues_pattern_placement' => ['nullable', 'in:repeat,cover,contain,fit'],
            'home_videos_pattern' => ['nullable', 'image', 'max:4096'],
            'remove_home_videos_pattern' => ['nullable', 'boolean'],
            'home_videos_pattern_opacity' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'home_videos_pattern_placement' => ['nullable', 'in:repeat,cover,contain,fit'],
            'social_twitter' => ['nullable', 'url'],
            'social_instagram' => ['nullable', 'url'],
            'social_youtube' => ['nullable', 'url'],
            'social_facebook' => ['nullable', 'url'],
            'social_whatsapp' => ['nullable', 'string', 'max:50'],
        ];
    }
}
