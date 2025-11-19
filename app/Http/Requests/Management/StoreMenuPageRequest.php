<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'summary' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'position' => ['nullable', 'integer', 'between:-1000,1000'],
            'is_active' => ['nullable', 'boolean'],
            'slug' => ['nullable', 'string', 'max:200'],
        ];
    }
}
