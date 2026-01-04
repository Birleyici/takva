@props(['mode' => 'solid'])

@php
    $hasCustomLogo = filled(optional($siteSettings)->logo_url ?? null);
    $navigationLogo = $hasCustomLogo ? $siteSettings->logo_url : asset('logo.png');
    $isOverlay = $mode === 'overlay';
    $navClasses = $isOverlay
        ? 'absolute top-0 left-0 right-0 z-50 bg-transparent text-white'
        : 'relative z-50 bg-secondary-700 text-white border-b border-secondary-600/50';
    $themeSettings = optional($siteSettings)->theme_settings ?? [];
    $headerPattern = $themeSettings['header'] ?? [];
    $headerPatternPath = $headerPattern['pattern_path'] ?? null;
    $headerPatternOpacityValue = $headerPattern['opacity'] ?? null;
    $headerPatternOpacity = is_numeric($headerPatternOpacityValue)
        ? max(0, min(100, (float) $headerPatternOpacityValue)) / 100
        : 0.2;
    $headerPatternPlacement = $headerPattern['placement'] ?? 'repeat';
    $headerPatternUrl = $headerPatternPath
        ? Storage::disk('public')->url($headerPatternPath)
        : null;
    $headerPatternRepeat = $headerPatternPlacement === 'repeat' ? 'repeat' : 'no-repeat';
    $headerPatternSize = match ($headerPatternPlacement) {
        'cover' => 'cover',
        'contain' => 'contain',
        default => 'auto',
    };
    $showHeaderPattern = !$isOverlay && $headerPatternUrl && $headerPatternOpacity > 0;
    $headerPatternStyle = $showHeaderPattern
        ? "background-image: url('{$headerPatternUrl}'); background-repeat: {$headerPatternRepeat}; background-size: {$headerPatternSize}; background-position: center; opacity: {$headerPatternOpacity};"
        : '';
@endphp

<!-- Navigation -->
<nav class="{{ $navClasses }}">
    @if ($showHeaderPattern)
        <div class="absolute inset-0 pointer-events-none z-0" style="{{ $headerPatternStyle }}"></div>
    @endif
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex justify-between items-center h-20">
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-3">
                    <div class="flex items-center space-x-3">
                        <!-- Takva Logo -->
                        <img
                            src="{{ $navigationLogo }}" 
                            alt="Takva Dergisi Logo" 
                            class="h-12 w-auto object-contain {{ $hasCustomLogo ? '' : 'filter brightness-0 invert' }}"
                        />
                    </div>
                </a>
            </div>
            
            <div class="hidden md:flex items-center space-x-6">
                <a href="/" class="text-white hover:text-primary-400 transition-colors font-medium">
                    Ana Sayfa
                </a>
                <a href="{{ route('issues.index') }}" class="text-white hover:text-primary-400 transition-colors font-medium">
                    Sayılar
                </a>
                <a href="{{ route('articles.index') }}" class="text-white hover:text-primary-400 transition-colors font-medium">
                    Makaleler
                </a>
                <a href="{{ route('videos.index') }}" class="text-white hover:text-primary-400 transition-colors font-medium">
                    Videolar
                </a>
                <div class="relative group">
                    <button class="inline-flex items-center gap-2 text-white hover:text-primary-400 transition-colors font-medium">
                        Konular
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 absolute left-1/2 top-full mt-3 -translate-x-1/2">
                        <div class="rounded-2xl border border-neutral-200 bg-white shadow-xl min-w-[240px] py-4">
                            @php($navCategories = $navCategories ?? collect())
                            @forelse($navCategories as $category)
                                <a href="{{ route('articles.category', ['category' => $category->slug]) }}"
                                   class="flex items-center justify-between px-5 py-2 text-sm text-secondary-700 hover:text-primary-600 hover:bg-primary-50/70 transition">
                                    <span>{{ $category->name }}</span>
                                    <span class="inline-flex items-center justify-center rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-semibold text-neutral-500">
                                        {{ $category->articles_count ?? 0 }}
                                    </span>
                                </a>
                            @empty
                                <div class="px-5 py-2 text-sm text-neutral-500">Konular yakında</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <a href="{{ route('authors.index') }}" class="text-white hover:text-primary-400 transition-colors font-medium">
                    Yazarlar
                </a>
                @php($navMenuPages = $navMenuPages ?? collect())
                @foreach($navMenuPages as $menuPage)
                    <a href="{{ route('menu.show', $menuPage->slug) }}" class="text-white hover:text-primary-400 transition-colors font-medium">
                        {{ $menuPage->title }}
                    </a>
                @endforeach
                <a href="{{ route('contact.show') }}" class="text-white hover:text-primary-400 transition-colors font-medium">
                    İletişim
                </a>

                <div class="hidden lg:block">
                    <div
                        class="search-component"
                        data-search-component="true"
                        data-search-endpoint="{{ route('search.api') }}"
                        data-search-page="{{ route('search.index') }}"
                    ></div>
                </div>
                
                <!-- CTA Button -->
                <a href="{{ route('issues.latest') }}" class="bg-accent-500 hover:bg-accent-600 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg">
                    Son Sayı
                </a>
            </div>
            
            <div class="md:hidden flex items-center gap-3">
                <div
                    class="search-component"
                    data-search-component="true"
                    data-search-endpoint="{{ route('search.api') }}"
                    data-search-page="{{ route('search.index') }}"
                ></div>
                <button
                    class="text-white"
                    aria-label="Menüyü aç"
                    type="button"
                    data-mobile-menu-trigger
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div
        class="fixed inset-0 z-40 hidden bg-secondary-900/70 backdrop-blur-sm"
        data-mobile-menu-overlay
    ></div>
    <div
        class="fixed inset-y-0 right-0 z-50 flex w-72 max-h-screen translate-x-full transform flex-col overflow-hidden bg-white shadow-2xl transition duration-300"
        data-mobile-menu-panel
    >
        <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-4">
            <div>
                <p class="text-xs uppercase tracking-[0.28em] text-primary-500 font-semibold">Takva</p>
                <p class="text-sm font-semibold text-secondary-900">Menü</p>
            </div>
            <button
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-200 text-neutral-500"
                aria-label="Menüyü kapat"
                data-mobile-menu-close
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto px-5 py-6">
            <nav class="space-y-4 text-secondary-900">
                <a href="/" class="block text-base font-semibold transition hover:text-primary-600">
                    Ana Sayfa
                </a>
                <a href="{{ route('issues.index') }}" class="block text-base font-semibold transition hover:text-primary-600">
                    Sayılar
                </a>
                <a href="{{ route('articles.index') }}" class="block text-base font-semibold transition hover:text-primary-600">
                    Makaleler
                </a>
                <a href="{{ route('videos.index') }}" class="block text-base font-semibold transition hover:text-primary-600">
                    Videolar
                </a>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-neutral-400">
                        Konular
                    </p>
                    <ul class="mt-2 space-y-2 text-sm">
                        @php($navCategories = $navCategories ?? collect())
                        @forelse($navCategories as $category)
                            <li>
                                <a href="{{ route('articles.category', ['category' => $category->slug]) }}" class="flex items-center justify-between rounded-xl border border-neutral-100 px-3 py-2 text-secondary-700 transition hover:border-primary-100 hover:text-primary-600">
                                    <span>{{ $category->name }}</span>
                                    <span class="text-xs text-neutral-400">{{ $category->articles_count ?? 0 }}</span>
                                </a>
                            </li>
                        @empty
                            <li class="text-neutral-400">Konular yakında</li>
                        @endforelse
                    </ul>
                </div>
                <a href="{{ route('authors.index') }}" class="block text-base font-semibold transition hover:text-primary-600">
                    Yazarlar
                </a>
                @php($navMenuPages = $navMenuPages ?? collect())
                @foreach($navMenuPages as $menuPage)
                    <a href="{{ route('menu.show', $menuPage->slug) }}" class="block text-base font-semibold transition hover:text-primary-600">
                        {{ $menuPage->title }}
                    </a>
                @endforeach
                <a href="{{ route('contact.show') }}" class="block text-base font-semibold transition hover:text-primary-600">
                    İletişim
                </a>
            </nav>

            <div class="mt-6">
                <a href="{{ route('issues.latest') }}" class="inline-flex w-full items-center justify-center rounded-2xl bg-accent-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-accent-500/20 transition hover:bg-accent-600">
                    Son Sayı
                </a>
            </div>
        </div>
    </div>
</nav>
