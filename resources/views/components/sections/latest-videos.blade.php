@props(['videos' => null, 'siteSettings' => null])

@php
    $videos = collect($videos);
    $themeSettings = optional($siteSettings)->theme_settings ?? [];
    $videosPattern = $themeSettings['home_videos'] ?? [];
    $videosPatternPath = $videosPattern['pattern_path'] ?? null;
    $videosPatternOpacityValue = $videosPattern['opacity'] ?? null;
    $videosPatternOpacity = is_numeric($videosPatternOpacityValue)
        ? max(0, min(100, (float) $videosPatternOpacityValue)) / 100
        : 0.2;
    $videosPatternPlacement = $videosPattern['placement'] ?? 'repeat';
    $videosPatternUrl = $videosPatternPath
        ? \Illuminate\Support\Facades\Storage::disk('public')->url($videosPatternPath)
        : null;
    $videosPatternRepeat = $videosPatternPlacement === 'repeat' ? 'repeat' : 'no-repeat';
    $videosPatternSize = match ($videosPatternPlacement) {
        'cover' => 'cover',
        'contain' => 'contain',
        default => 'auto',
    };
    $videosPatternPosition = $videosPatternPlacement === 'repeat' ? 'top left' : 'top center';
    $showVideosPattern = $videosPatternUrl && $videosPatternOpacity > 0;
    $videosPatternStyle = $showVideosPattern
        ? "background-image: url('{$videosPatternUrl}'); background-repeat: {$videosPatternRepeat}; background-size: {$videosPatternSize}; background-position: {$videosPatternPosition}; opacity: {$videosPatternOpacity};"
        : '';
@endphp

<section class="relative overflow-hidden py-20 bg-white">
    @if ($showVideosPattern)
        <div class="absolute left-0 right-0 top-0 h-[400px] max-h-full pointer-events-none" style="{{ $videosPatternStyle }}"></div>
    @endif
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-heading font-bold text-secondary-900 mb-6">
                Son <span class="text-primary-500">Vİdeolar</span>
            </h2>
            <p class="text-xl text-secondary-600 max-w-3xl mx-auto leading-relaxed">
                Takva Dergisi videolarıyla güncel içeriklerimizi izleyin.
            </p>
        </div>

        @if($videos->isNotEmpty())
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($videos as $video)
                    <article class="group rounded-3xl border border-neutral-200 bg-white shadow-sm overflow-hidden transition hover:-translate-y-1 hover:shadow-xl">
                        <a href="{{ route('videos.show', $video->slug) }}" class="block relative">
                            <div class="relative aspect-video overflow-hidden">
                                <img
                                    src="{{ $video->thumbnail_url ?? '/placeholder.jpg' }}"
                                    alt="{{ $video->title }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-secondary-900/60 via-secondary-900/20 to-transparent"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/90 text-secondary-900 shadow-lg transition group-hover:scale-110">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                        <div class="px-6 py-6 space-y-4">
                            <p class="text-xs uppercase tracking-[0.3em] text-neutral-400 font-semibold">
                                {{ $video->created_at?->translatedFormat('d F Y') }}
                            </p>
                            <h3 class="text-xl font-semibold text-secondary-900 group-hover:text-primary-600 transition">
                                <a href="{{ route('videos.show', $video->slug) }}">
                                    {{ $video->title }}
                                </a>
                            </h3>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="text-center mt-16">
                <a href="{{ route('videos.index') }}" class="group inline-flex items-center bg-accent-600 hover:bg-accent-700 text-white px-10 py-4 rounded-xl font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <span class="flex items-center">
                        Tüm Videoları Gör
                        <svg class="w-5 h-5 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </span>
                </a>
            </div>
        @else
            <p class="text-center text-secondary-500 mt-10">Henüz yayınlanmış video bulunmuyor.</p>
        @endif
    </div>
</section>
