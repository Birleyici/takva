<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreContactMessageRequest;
use App\Services\ContactMessageService;
use Illuminate\Http\RedirectResponse;

class ContactMessageController extends Controller
{
    public function __construct(
        private readonly ContactMessageService $contactMessageService
    ) {
    }

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->userAgent();

        $this->contactMessageService->create($data);
        $request->session()->forget('contact_captcha_answer');

        return redirect()
            ->route('contact.show')
            ->with('contact_success', 'Mesajınız başarıyla gönderildi. En kısa sürede dönüş sağlayacağız inşaAllah.');
    }
}
