<?php

namespace App\Http\Requests\Web;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:180'],
            'subject' => ['nullable', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:2000'],
            'captcha' => [
                'required',
                'integer',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $expectedAnswer = $this->session()->get('contact_captcha_answer');

                    if ($expectedAnswer === null || (int) $value !== (int) $expectedAnswer) {
                        $fail('Güvenlik doğrulaması cevabı hatalı.');
                    }
                },
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'captcha' => 'güvenlik doğrulaması',
        ];
    }
}
