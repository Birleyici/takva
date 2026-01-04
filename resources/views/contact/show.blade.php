@extends('layouts.app')

@php
    $contactEmail = $settings->contact_email ?? 'takvasorucevap@gmail.com';
    $contactPhone = $settings->contact_phone ?? '05528227442';
    $contactAddress = $settings->contact_address ?? "Tatlıcak Mh. Uluyatır Sk. No:42/A\nKaratay / KONYA";
    $contactMap = trim($settings->contact_map_embed ?? 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3149.156453270041!2d32.55908747641665!3d37.880023706089375!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14d09b0075c61bc1%3A0xcc7271a74eb4ea46!2sTakva%20Dergisi!5e0!3m2!1str!2str!4v1750237294648');
    $contactMapHasIframe = str_contains(strtolower($contactMap), '<iframe');
    $heroText = $settings->contact_hero_text ?? 'Görüş, öneri ve katkılarınızı memnuniyetle dinliyoruz. Aşağıdaki kanallardan ekibimize ulaşabilirsiniz.';
    $socialTwitter = $settings->social_twitter ?? 'https://x.com/takvadergisi1';
    $socialInstagram = $settings->social_instagram ?? 'https://www.instagram.com/takvadergisi1/';
    $socialYoutube = $settings->social_youtube ?? 'https://www.youtube.com/@takvadergisi';
    $socialFacebook = $settings->social_facebook ?? 'https://www.facebook.com/profile.php?id=100064473440482';
    $socialWhatsappRaw = $settings->social_whatsapp ?? '+90 552 822 74 42';
    $socialWhatsapp = $socialWhatsappRaw ? 'https://wa.me/' . preg_replace('/\D+/', '', $socialWhatsappRaw) : null;
@endphp

@section('title', 'İletişim - Takva Dergisi')
@section('description', 'Takva Dergisi ekibiyle iletişime geçin, önerilerinizi ve sorularınızı paylaşın.')
@section('keywords', 'iletişim, takva dergisi, mail, telefon')

@section('content')
    <section class="relative overflow-hidden bg-secondary-900 text-white">
        <div class="absolute inset-0">
            <img src="/placeholder.jpg" alt="İletişim" class="h-full w-full object-cover opacity-20">
            <div class="absolute inset-0 bg-secondary-900/85"></div>
        </div>
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-20 text-center">
            <p class="text-xs uppercase tracking-[0.3em] text-accent-200 font-semibold">@trupper('Takva Dergisi')</p>
            <h1 class="mt-4 text-4xl sm:text-5xl font-bold leading-tight">Bizimle İletişime Geçin</h1>
            <p class="mt-4 text-sm sm:text-base text-white/70 leading-relaxed">
                {{ $heroText }}
            </p>
        </div>
    </section>

    <section class="py-16 bg-white overflow-x-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-3">
                <div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15A2.25 2.25 0 0 0 2.25 6.75m19.5 0v.243a2.25 2.25 0 0 1-1.012 1.874l-7.5 4.688a2.25 2.25 0 0 1-2.476 0L3.262 8.867a2.25 2.25 0 0 1-1.012-1.874V6.75" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-secondary-900">E-posta</h3>
                    <p class="mt-2 text-sm text-neutral-500">Sorularınız ve yazı gönderileriniz için</p>
                    <a href="mailto:{{ $contactEmail }}" class="mt-4 inline-flex items-center text-primary-600 hover:text-primary-700">
                        {{ $contactEmail }}
                    </a>
                </div>

                <div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 4.5 8.954 6.106.046.032c.3.205.45.308.622.35.154.038.316.038.47 0 .173-.042.324-.145.628-.349l.048-.033L21.75 4.5M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-.32-1.168c-.196-.33-.48-.6-.808-.786L12 1.5 3.378 4.796a2.25 2.25 0 0 0-.808.786A2.25 2.25 0 0 0 2.25 6.75v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-secondary-900">İçerik ve Yayınlar</h3>
                    <p class="mt-2 text-sm text-neutral-500">Dergi içerikleri ve sayılar hakkında</p>
                    <p class="mt-4 text-sm text-secondary-900">
                        <a href="tel:{{ $contactPhone }}" class="hover:text-primary-600">{{ $contactPhone }}</a>
                    </p>
                </div>

                <div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-secondary-900">Adres</h3>
                    <p class="mt-2 text-sm text-neutral-500">Yazışma adresi</p>
                    <p class="mt-4 text-sm text-secondary-900">{!! nl2br(e($contactAddress)) !!}</p>
                </div>
            </div>

            <div class="mt-12 grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
                <div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-semibold text-secondary-900">Bize Yazın</h2>
                    <p class="mt-2 text-sm text-neutral-500">Projeler, yayın önerileri veya iş birlikleri için mesaj bırakabilirsiniz.</p>

                    @if (session('contact_success'))
                        <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ session('contact_success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            Lütfen formdaki hataları düzeltin ve tekrar deneyin.
                        </div>
                    @endif

                    <form class="mt-6 grid gap-4" action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="contact-name" class="sr-only">Adınız Soyadınız</label>
                                <input
                                    id="contact-name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Adınız Soyadınız"
                                    class="w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm text-secondary-900 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100"
                                    required
                                >
                                @error('name')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="contact-email" class="sr-only">E-posta adresiniz</label>
                                <input
                                    id="contact-email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="E-posta adresiniz"
                                    class="w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm text-secondary-900 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100"
                                    required
                                >
                                @error('email')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div>
                            <label for="contact-subject" class="sr-only">Konu</label>
                            <input
                                id="contact-subject"
                                type="text"
                                name="subject"
                                value="{{ old('subject') }}"
                                placeholder="Konu"
                                class="w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm text-secondary-900 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100"
                            >
                            @error('subject')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="contact-message" class="sr-only">Mesajınız</label>
                            <textarea
                                id="contact-message"
                                name="message"
                                rows="5"
                                placeholder="Mesajınız"
                                class="w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm text-secondary-900 focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100"
                                required
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-600/20 transition hover:bg-primary-500">
                            Mesajı Gönder
                        </button>
                    </form>

                    <div class="mt-8">
                        <p class="text-sm text-neutral-500">Dilerseniz sosyal medya hesaplarımız üzerinden de bize ulaşabilirsiniz.</p>
                        <div class="mt-4 flex flex-wrap gap-3">
                            @php
                                $socialLinks = [
                                    ['url' => $socialTwitter, 'label' => 'X (Twitter)', 'icon' => 'M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z'],
                                    ['url' => $socialInstagram, 'label' => 'Instagram', 'icon' => 'M7 2C4.2 2 2 4.2 2 7v10c0 2.8 2.2 5 5 5h10c2.8 0 5-2.2 5-5V7c0-2.8-2.2-5-5-5H7zm0 2h10c1.7 0 3 1.3 3 3v10c0 1.7-1.3 3-3 3H7c-1.7 0-3-1.3-3-3V7c0-1.7 1.3-3 3-3zm11 1a1 1 0 100 2 1 1 0 000-2zm-6 2a5 5 0 100 10 5 5 0 000-10zm0 2a3 3 0 110 6 3 3 0 010-6z'],
                                    ['url' => $socialYoutube, 'label' => 'YouTube', 'icon' => 'M19.615 3.184C21.403 3.742 22 5.368 22 7.987v8.026c0 2.637-.597 4.247-2.385 4.803-4.074.997-11.152.997-15.23 0C2.596 20.26 2 18.65 2 16.013V7.987c0-2.619.597-4.245 2.385-4.803 4.078-.997 11.156-.997 15.23 0zM10 15l5.19-3L10 9v6z'],
                                    ['url' => $socialFacebook, 'label' => 'Facebook', 'icon' => 'M22.675 0h-21.35C.597 0 0 .597 0 1.326v21.348C0 23.403.597 24 1.326 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.894-4.788 4.659-4.788 1.325 0 2.466.099 2.797.143v3.24l-1.92.001c-1.504 0-1.796.715-1.796 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.403 24 24 23.403 24 22.674V1.326C24 .597 23.403 0 22.675 0z'],
                                    ['url' => $socialWhatsapp, 'label' => 'WhatsApp', 'icon' => 'M12 0C5.373 0 0 5.373 0 12c0 2.11.55 4.105 1.606 5.9L0 24l6.242-1.616A11.94 11.94 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 2c5.514 0 10 4.486 10 10 0 5.513-4.486 10-10 10a9.94 9.94 0 0 1-4.9-1.3l-.35-.2-3.7.96.99-3.61-.23-.37A9.94 9.94 0 0 1 2 12C2 6.486 6.486 2 12 2zm4.707 11.293c-.254-.127-1.5-.742-1.732-.826-.232-.085-.401-.127-.57.127-.169.254-.653.826-.8.996-.147.17-.293.19-.547.064-.254-.127-1.074-.394-2.046-1.255-.756-.675-1.266-1.51-1.414-1.764-.147-.254-.015-.392.111-.518.115-.114.254-.296.38-.444.126-.148.168-.254.254-.423.085-.17.042-.317-.021-.444-.064-.127-.57-1.374-.78-1.877-.206-.495-.416-.428-.57-.437-.147-.007-.316-.009-.486-.009-.169 0-.444.064-.677.317s-.89.868-.89 2.117c0 1.249.912 2.455 1.039 2.625.127.17 1.793 2.74 4.348 3.84.607.262 1.081.418 1.45.536.609.194 1.162.167 1.6.101.488-.072 1.5-.612 1.713-1.204.211-.592.211-1.098.148-1.204-.063-.106-.232-.169-.486-.296z'],
                                ];
                            @endphp
                            @foreach ($socialLinks as $link)
                                @if ($link['url'])
                                    <a href="{{ $link['url'] }}" target="_blank" class="flex h-12 w-12 items-center justify-center rounded-2xl border border-neutral-200 text-secondary-900 transition hover:border-primary-200 hover:text-primary-600" aria-label="{{ $link['label'] }}">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="{{ $link['icon'] }}" />
                                        </svg>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-semibold text-secondary-900">Harita</h2>
                    <p class="mt-2 text-sm text-neutral-500">Ziyaret ve yazışmalar için konumumuzu aşağıda bulabilirsiniz.</p>
                    <div class="mt-6 overflow-hidden rounded-2xl border border-neutral-100 shadow contact-map-embed">
                        @if ($contactMapHasIframe)
                            {!! $contactMap !!}
                        @else
                            <iframe
                                src="{{ $contactMap }}"
                                width="100%"
                                height="340"
                                class="block w-full"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .contact-map-embed iframe {
        width: 100% !important;
        max-width: 100%;
        display: block;
    }
</style>
@endpush
