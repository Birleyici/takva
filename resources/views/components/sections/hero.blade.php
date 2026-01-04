@props(['latestIssue' => null, 'siteSettings' => null, 'heroSlides' => null])

@php
    $issuePdfUrl = $latestIssue?->pdf_url;
    if ($issuePdfUrl) {
        $ctaUrl = $issuePdfUrl;
        $ctaTarget = '_blank';
    } elseif ($latestIssue) {
        $ctaUrl = route('issues.show', $latestIssue);
        $ctaTarget = '_self';
    } else {
        $ctaUrl = route('issues.index');
        $ctaTarget = '_self';
    }

    $heroBackground = optional($siteSettings)->hero_background_url
        ?: 'https://images.pexels.com/photos/34414797/pexels-photo-34414797.jpeg';

    $heroSlides = collect($heroSlides ?? []);
    $hasSlides = $heroSlides->isNotEmpty();
@endphp

@if($hasSlides)
    <section class="relative w-full py-0 md:py-8 bg-gradient-to-br from-slate-50 via-gray-50 to-neutral-100 overflow-hidden">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.8),rgba(255,255,255,0)_55%)]"></div>
            <div class="absolute -left-16 top-16 h-px w-[120%] bg-gradient-to-r from-transparent via-primary-200/70 to-transparent"></div>
            <div class="absolute -right-10 bottom-20 h-px w-[120%] bg-gradient-to-r from-transparent via-accent-200/60 to-transparent"></div>
            <div class="absolute left-10 top-0 h-full w-px bg-gradient-to-b from-transparent via-neutral-200/80 to-transparent"></div>
            <div class="absolute right-14 top-0 h-full w-px bg-gradient-to-b from-transparent via-neutral-200/70 to-transparent"></div>
        </div>
        <div class="relative z-10 max-w-7xl mx-auto px-0 md:px-6 lg:px-8">
            <div class="relative w-full overflow-hidden rounded-none md:rounded-md bg-neutral-900">
                <div class="absolute inset-0 islamic-pattern-1 opacity-10 pointer-events-none"></div>
                <div
                    class="relative w-full"
                    data-hero-slider
                    data-hero-slides='@json($heroSlides)'
                    data-hero-slider-autoplay="true"
                    data-hero-slider-interval="6500"
                ></div>
            </div>
        </div>
    </section>
@else
    <!-- Hero Section - Shaha Style -->
    <section class="relative h-screen  flex items-center justify-center overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img
                src="{{ $heroBackground }}"
                alt="Islamic Architecture Background"
                class="w-full h-full object-cover" />
            <!-- Lighter Dark Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            <!-- Islamic Pattern Overlay -->
            <div class="absolute inset-0 islamic-pattern-1 opacity-10"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 text-center text-white max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Heading -->
            <h1 class="text-6xl md:text-8xl lg:text-9xl font-decorative font-bold mb-12 leading-tight">
                <span class="text-accent-400">Takva</span>
                <span class="block text-white">Dergİsİ</span>
            </h1>
            <p class="text-xl md:text-2xl text-white/90 max-w-2xl mx-auto mb-10 leading-relaxed">
                Azık edinin! Şüphesiz azığın en hayırlısı takvâdır. <br><span class="text-base md:text-lg italic text-white/80">(Bakara 197)</span>
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                <a href="{{ $ctaUrl }}"
                   @if($ctaTarget === '_blank') target="_blank" rel="noopener" @endif
                   class="group bg-accent-600 hover:bg-accent-700 text-white px-12 py-5 rounded-lg font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl">
                    <span class="flex items-center">
                        Son Sayıyı Oku
                        <svg class="w-5 h-5 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </span>
                </a>
            </div>
        </div>
    </section>
@endif
