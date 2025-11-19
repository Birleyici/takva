@props(['latestIssue' => null])

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
@endphp

<!-- Hero Section - Shaha Style -->
<section class="relative h-screen flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 z-0">
        <img
            src="https://images.pexels.com/photos/34414797/pexels-photo-34414797.jpeg"
            alt="Islamic Architecture Background"
            class="w-full h-full object-cover" />
        <!-- Lighter Dark Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
        <!-- Islamic Pattern Overlay -->
        <div class="absolute inset-0 islamic-pattern-1 opacity-10"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 text-center text-white max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Allah Lafzı -->
        <div class="mb-8">
            <p class="text-accent-400 text-3xl md:text-4xl font-arabic mb-6">ﷲ</p>
        </div>

        <!-- Main Heading -->
        <h1 class="text-6xl md:text-8xl lg:text-9xl font-decorative font-bold mb-12 leading-tight">
            <span class="text-accent-400">Takva</span>
            <span class="block text-white">Dergİsİ</span>
        </h1>

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
            <a href="{{ route('contact.show') }}" class="border-2 border-white text-white hover:bg-white hover:text-primary-900 px-12 py-5 rounded-lg font-semibold text-lg transition-all duration-300 transform hover:scale-105">
                Abone Ol
            </a>
        </div>
    </div>
</section>
