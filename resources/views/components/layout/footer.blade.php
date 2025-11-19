<!-- Footer - İslami Desenli -->
<footer class="bg-secondary-800 text-white relative overflow-hidden">
   
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center space-x-3 mb-6">
                    <img src="https://www.takvadergisi.org/images/takva-logo-red.png" alt="Takva Dergisi Logo" class="h-12 w-auto filter brightness-0 invert" />
                </div>
                <p class="text-neutral-300 mb-6 leading-relaxed max-w-md">
                    Şüphesiz azığın en hayırlısı takvâdır. (Bakara 197)
                </p>
                <div class="flex space-x-3">
                    @php
                        $rawWhatsapp = optional($siteSettings)->social_whatsapp;
                        $whatsappLink = $rawWhatsapp ? 'https://wa.me/' . preg_replace('/\D+/', '', $rawWhatsapp) : null;
                        $socialLinks = [
                            ['url' => optional($siteSettings)->social_twitter, 'svg' => 'M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z'],
                            ['url' => optional($siteSettings)->social_instagram, 'svg' => 'M7 2C4.2 2 2 4.2 2 7v10c0 2.8 2.2 5 5 5h10c2.8 0 5-2.2 5-5V7c0-2.8-2.2-5-5-5H7zm0 2h10c1.7 0 3 1.3 3 3v10c0 1.7-1.3 3-3 3H7c-1.7 0-3-1.3-3-3V7c0-1.7 1.3-3 3-3zm11 1a1 1 0 100 2 1 1 0 000-2zm-6 2a5 5 0 100 10 5 5 0 000-10zm0 2a3 3 0 110 6 3 3 0 010-6z'],
                            ['url' => optional($siteSettings)->social_youtube, 'svg' => 'M19.615 3.184C21.403 3.742 22 5.368 22 7.987v8.026c0 2.637-.597 4.247-2.385 4.803-4.074.997-11.152.997-15.23 0C2.596 20.26 2 18.65 2 16.013V7.987c0-2.619.597-4.245 2.385-4.803 4.078-.997 11.156-.997 15.23 0zM10 15l5.19-3L10 9v6z'],
                            ['url' => optional($siteSettings)->social_facebook, 'svg' => 'M22.675 0h-21.35C.597 0 0 .597 0 1.326v21.348C0 23.403.597 24 1.326 24H12.82v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.894-4.788 4.659-4.788 1.325 0 2.466.099 2.797.143v3.24l-1.92.001c-1.504 0-1.796.715-1.796 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.403 24 24 23.403 24 22.674V1.326C24 .597 23.403 0 22.675 0z'],
                            ['url' => $whatsappLink, 'svg' => 'M12 0C5.373 0 0 5.373 0 12c0 2.11.55 4.105 1.606 5.9L0 24l6.242-1.616A11.94 11.94 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 2c5.514 0 10 4.486 10 10 0 5.513-4.486 10-10 10a9.94 9.94 0 0 1-4.9-1.3l-.35-.2-3.7.96.99-3.61-.23-.37A9.94 9.94 0 0 1 2 12C2 6.486 6.486 2 12 2zm4.707 11.293c-.254-.127-1.5-.742-1.732-.826-.232-.085-.401-.127-.57.127-.169.254-.653.826-.8.996-.147.17-.293.19-.547.064-.254-.127-1.074-.394-2.046-1.255-.756-.675-1.266-1.51-1.414-1.764-.147-.254-.015-.392.111-.518.115-.114.254-.296.38-.444.126-.148.168-.254.254-.423.085-.17.042-.317-.021-.444-.064-.127-.57-1.374-.78-1.877-.206-.495-.416-.428-.57-.437-.147-.007-.316-.009-.486-.009-.169 0-.444.064-.677.317s-.89.868-.89 2.117c0 1.249.912 2.455 1.039 2.625.127.17 1.793 2.74 4.348 3.84.607.262 1.081.418 1.45.536.609.194 1.162.167 1.6.101.488-.072 1.5-.612 1.713-1.204.211-.592.211-1.098.148-1.204-.063-.106-.232-.169-.486-.296z'],
                        ];
                    @endphp
                    @foreach ($socialLinks as $link)
                        @if ($link['url'])
                            <a href="{{ $link['url'] }}" target="_blank" class="w-10 h-10 bg-neutral-700 hover:bg-primary-500 rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="{{ $link['svg'] }}" />
                                </svg>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div>
                <h4 class="text-lg font-heading font-semibold mb-6">Hızlı Linkler</h4>
                <ul class="space-y-3 text-neutral-300 text-sm">
                    <li><a href="/" class="hover:text-primary-400 transition-colors">Ana Sayfa</a></li>
                    <li><a href="{{ route('issues.index') }}" class="hover:text-primary-400 transition-colors">Sayılar</a></li>
                    <li><a href="{{ route('articles.index') }}" class="hover:text-primary-400 transition-colors">Makaleler</a></li>
                    <li><a href="{{ route('authors.index') }}" class="hover:text-primary-400 transition-colors">Yazarlar</a></li>
                    <li><a href="{{ route('contact.show') }}" class="hover:text-primary-400 transition-colors">İletişim</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-lg font-heading font-semibold mb-6">İletişim</h4>
                <ul class="space-y-3 text-neutral-300 text-sm">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-3 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        {{ $siteSettings->contact_email ?? 'takvasorucevap@gmail.com' }}
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-3 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <a href="tel:{{ $siteSettings->contact_phone ?? '05528227442' }}" class="hover:text-primary-400 transition-colors">
                            {{ $siteSettings->contact_phone ?? '05528227442' }}
                        </a>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 mr-3 mt-0.5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {!! nl2br(e($siteSettings->contact_address ?? "Tatlıcak Mh. Uluyatır Sk. No:42/A\nKaratay / KONYA")) !!}
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-neutral-700 mt-12 pt-8 text-center">
            <p class="text-neutral-400 mb-2 text-sm">&copy; {{ date('Y') }} Takva Dergisi.</p>
        </div>
    </div>
</footer>
