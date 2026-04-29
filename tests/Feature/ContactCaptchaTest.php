<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactCaptchaTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_renders_captcha_question(): void
    {
        $response = $this->get(route('contact.show'));

        $response->assertOk();
        $response->assertSee('Güvenlik doğrulaması:');
        $this->assertNotNull(session('contact_captcha_answer'));
    }

    public function test_contact_form_rejects_wrong_captcha_answer(): void
    {
        $response = $this
            ->from(route('contact.show'))
            ->withSession(['contact_captcha_answer' => 9])
            ->post(route('contact.store'), [
                'name' => 'Test Kullanici',
                'email' => 'test@example.com',
                'subject' => 'Deneme',
                'message' => 'Bu bir test mesajidir.',
                'captcha' => 7,
            ]);

        $response->assertRedirect(route('contact.show'));
        $response->assertSessionHasErrors('captcha');
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contact_form_accepts_correct_captcha_answer(): void
    {
        $response = $this
            ->withSession(['contact_captcha_answer' => 9])
            ->post(route('contact.store'), [
                'name' => 'Test Kullanici',
                'email' => 'test@example.com',
                'subject' => 'Deneme',
                'message' => 'Bu bir test mesajidir.',
                'captcha' => 9,
            ]);

        $response->assertRedirect(route('contact.show'));
        $response->assertSessionHas('contact_success');
        $response->assertSessionMissing('contact_captcha_answer');
        $this->assertDatabaseCount('contact_messages', 1);
        $this->assertDatabaseHas('contact_messages', [
            'email' => 'test@example.com',
            'name' => 'Test Kullanici',
        ]);
    }
}
