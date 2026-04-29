<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'youtube_url' => ['required', 'string', 'max:255'],
            'video_category_id' => ['required', 'integer', 'exists:video_categories,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'video_category_id.required' => 'Video kategorisi seçmek zorunludur.',
            'video_category_id.exists' => 'Seçilen video kategorisi geçersiz.',
        ];
    }
}
