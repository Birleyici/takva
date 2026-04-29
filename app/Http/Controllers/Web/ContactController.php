<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __invoke(): View
    {
        $settings = SiteSetting::query()->first();
        $firstOperand = random_int(1, 9);
        $secondOperand = random_int(1, 9);

        session(['contact_captcha_answer' => $firstOperand + $secondOperand]);

        return view('contact.show', [
            'settings' => $settings,
            'captchaQuestion' => sprintf('%d + %d', $firstOperand, $secondOperand),
        ]);
    }
}
