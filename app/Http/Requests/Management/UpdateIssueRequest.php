<?php

namespace App\Http\Requests\Management;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'year' => ['sometimes', 'required', 'integer', 'min:1900', 'max:2100'],
            'month' => ['sometimes', 'required', 'integer', 'between:1,12'],
            'description' => ['nullable', 'string'],
            'cover_image_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'remove_cover_image' => ['nullable', 'boolean'],
            'pdf' => ['nullable', 'file', 'mimes:pdf', 'max:51200'],
            'remove_pdf' => ['nullable', 'boolean'],
        ];
    }
}
