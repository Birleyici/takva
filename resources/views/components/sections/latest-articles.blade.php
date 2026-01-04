@props([
    'latestArticles' => null,
    'popularArticles' => null,
    'siteSettings' => null,
])

@php
    $latestArticles = collect($latestArticles);
    $popularArticles = collect($popularArticles);
    $articlePlaceholder = '/placeholder.jpg';
    $cleanArticleSnippet = function (?string $value): string {
        $value = trim(strip_tags((string) $value));
        if ($value === '') {
            return '';
        }

        $value = preg_replace(
            '/^\s*(?:\d{1,3}\s*\.?\s*(?:sayi|sayı)|(?:sayi|sayı)\s*\d{1,3})\s*[-–—:]?\s*/iu',
            '',
            $value
        );

        return trim($value);
    };
    $headlineArticle = $latestArticles->first();
    $headlineSnippet = null;
    if ($headlineArticle) {
        $headlineSource = $cleanArticleSnippet($headlineArticle->content ?: $headlineArticle->excerpt);
        if ($headlineSource !== '') {
            $headlineSnippet = \Illuminate\Support\Str::limit($headlineSource, 150, '...');
            if (!\Illuminate\Support\Str::endsWith($headlineSnippet, '...')) {
                $headlineSnippet .= '...';
            }
        }
    }
    $themeSettings = optional($siteSettings)->theme_settings ?? [];
    $articlesPattern = $themeSettings['home_articles'] ?? [];
    $articlesPatternPath = $articlesPattern['pattern_path'] ?? null;
    $articlesPatternOpacityValue = $articlesPattern['opacity'] ?? null;
    $articlesPatternOpacity = is_numeric($articlesPatternOpacityValue)
        ? max(0, min(100, (float) $articlesPatternOpacityValue)) / 100
        : 0.2;
    $articlesPatternPlacement = $articlesPattern['placement'] ?? 'repeat';
    $articlesPatternUrl = $articlesPatternPath
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($articlesPatternPath)
        : null;
    $articlesPatternRepeat = $articlesPatternPlacement === 'repeat' ? 'repeat' : 'no-repeat';
    $articlesPatternSize = match ($articlesPatternPlacement) {
        'cover' => 'cover',
        'contain' => 'contain',
        default => 'auto',
    };
    $articlesPatternPosition = $articlesPatternPlacement === 'repeat' ? 'top left' : 'top center';
    $showArticlesPattern = $articlesPatternUrl && $articlesPatternOpacity > 0;
    $articlesPatternStyle = $showArticlesPattern
        ? "background-image: url('{$articlesPatternUrl}'); background-repeat: {$articlesPatternRepeat}; background-size: {$articlesPatternSize}; background-position: {$articlesPatternPosition}; opacity: {$articlesPatternOpacity};"
        : '';
@endphp

<!-- Latest Articles Section -->
<section class="relative overflow-hidden py-20 bg-gradient-to-br from-slate-50 via-gray-50 to-neutral-100">
    @if ($showArticlesPattern)
        <div class="absolute left-0 right-0 top-0 h-[400px] max-h-full pointer-events-none" style="{{ $articlesPatternStyle }}"></div>
    @endif
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-decorative font-bold text-secondary-900 mb-6">
                <span class="inline-flex flex-wrap items-baseline justify-center gap-x-4 gap-y-2">
                    <span class="text-primary-500">@trupper('Makaleler')</span>

                </span>
            </h2>
            <p class="text-xl text-secondary-600 max-w-3xl mx-auto leading-relaxed mb-12">
                En güncel makalelerimizle İslami bilim ve kültür dünyasından haberdar olun
            </p>

            <!-- Tab Navigation -->
            <div class="flex justify-center mb-8">
                <div class="bg-gray-100 p-1 rounded-xl shadow-inner">
                    <div class="flex space-x-1">
                        <button
                            class="tab-button active px-8 py-3 rounded-lg font-semibold text-sm transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-opacity-50"
                            data-tab="latest">
                            Son Eklenenler
                        </button>
                        <button
                            class="tab-button px-8 py-3 rounded-lg font-semibold text-sm transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-opacity-50"
                            data-tab="popular">
                            Çok Okunanlar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content relative">
            <!-- Latest Articles Tab -->
            <div id="latest-content" class="tab-pane active space-y-12">
                @forelse ($latestArticles as $article)
                    <x-partials.article-card
                        :title="$article->title"
                        :description="\Illuminate\Support\Str::limit($cleanArticleSnippet($article->content ?: $article->excerpt), 150, '...')"
                        :image="$article->featureImage?->url ?? $articlePlaceholder"
                        :url="route('articles.show', $article)"
                        :reverse="$loop->even"
                    />
                @empty
                    <p class="text-center text-secondary-500">Henüz yayınlanmış makale bulunmuyor.</p>
                @endforelse
            </div>

            <!-- Popular Articles Tab -->
            <div id="popular-content" class="tab-pane space-y-12">
                @forelse ($popularArticles as $article)
                    <x-partials.article-card
                        :title="$article->title"
                        :description="\Illuminate\Support\Str::limit($cleanArticleSnippet($article->content ?: $article->excerpt), 150, '...')"
                        :image="$article->featureImage?->url ?? $articlePlaceholder"
                        :url="route('articles.show', $article)"
                        :reverse="$loop->even"
                    />
                @empty
                    <p class="text-center text-secondary-500">Henüz popüler makale bulunmuyor.</p>
                @endforelse
            </div>
        </div>

        <!-- View All Articles Button -->
        <div class="text-center mt-16">
            <a href="{{ route('articles.index') }}" class="group inline-flex items-center bg-primary-500 hover:bg-primary-600 text-white px-10 py-4 rounded-xl font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                <span class="flex items-center">
                    Tüm Makaleleri Gör
                    <svg class="w-5 h-5 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </span>
            </a>
        </div>
    </div>
</section>

<style>
    .tab-button {
        color: #6b7280;
        background-color: transparent;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .tab-button.active {
        color: white;
        background-color: #f59e0b;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transform: translateY(-1px);
    }

    .tab-button:hover:not(.active) {
        color: #374151;
        background-color: rgba(255, 255, 255, 0.7);
        transform: translateY(-0.5px);
    }

    .tab-content {
        position: relative;
    }

    .tab-pane {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
        transition: all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        pointer-events: auto;
        display: block;
    }

    .tab-pane:not(.active) {
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px) scale(0.98);
        pointer-events: none;
        display: none;
    }

    /* Smooth fade crossover effect */
    .tab-pane.fade-out {
        opacity: 0;
        transform: translateY(-10px) scale(1.02);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.6, 1);
    }

    .tab-pane.fade-in {
        opacity: 1;
        transform: translateY(0) scale(1);
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.1s;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabPanes = document.querySelectorAll('.tab-pane');
        let isAnimating = false;

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (isAnimating) return; // Prevent multiple clicks during animation

                const targetTab = this.getAttribute('data-tab');
                const currentActivePane = document.querySelector('.tab-pane.active');
                const targetPane = document.getElementById(targetTab + '-content');

                // If clicking the same tab, do nothing
                if (currentActivePane === targetPane) return;

                isAnimating = true;

                // Update button states
                tabButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Phase 1: Fade out current content
                if (currentActivePane) {
                    currentActivePane.classList.add('fade-out');
                    currentActivePane.classList.remove('fade-in');
                }

                // Phase 2: After fade out, switch content and fade in
                setTimeout(() => {
                    // Remove active from all panes
                    tabPanes.forEach(pane => {
                        pane.classList.remove('active', 'fade-out', 'fade-in');
                    });

                    // Activate target pane
                    if (targetPane) {
                        targetPane.classList.add('active');

                        // Small delay for smooth transition
                        setTimeout(() => {
                            targetPane.classList.add('fade-in');

                            // Reset animation lock after complete transition
                            setTimeout(() => {
                                isAnimating = false;
                            }, 400);
                        }, 50);
                    }
                }, 300);
            });
        });

        // Initialize first tab
        const firstPane = document.querySelector('.tab-pane.active');
        if (firstPane) {
            firstPane.classList.add('fade-in');
        }
    });
</script>
