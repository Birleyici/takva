@extends('layouts.app')

@section('title', 'Videolar - Takva Dergisi')
@section('description', 'Takva Dergisi videolarını keşfedin, güncel içeriklerimizi izleyin.')
@section('keywords', 'takva dergisi videolar, youtube videoları, islami içerikler')

@section('content')
    <section class="relative overflow-hidden pt-32 pb-20">
        <div class="absolute inset-0 bg-gradient-to-br from-secondary-900 via-secondary-900 to-primary-900/90"></div>
        <div class="absolute inset-0 opacity-30 mix-blend-overlay" style="background-image: url('https://www.takvadergisi.org/images/pattern.png'); background-size: cover;"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-xs sm:text-sm uppercase tracking-[0.3em] text-accent-200 font-semibold">@trupper('Videolar')</p>
                    <h1 class="mt-4 text-4xl sm:text-5xl font-bold leading-tight">Video Arşivi</h1>
                    <p class="mt-4 text-sm sm:text-base text-white/70 leading-relaxed">
                        YouTube kanalımızdaki güncel videoları takip edebilir, Takva Dergisi içeriklerine hızlıca ulaşabilirsiniz.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="relative -mt-12 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-10 rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-secondary-900">Kategoriye göre filtrele</h2>
                        <p class="mt-1 text-sm text-neutral-500">
                            Dilediğiniz video kategorisini seçerek listeyi daraltabilirsiniz.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a
                            href="{{ route('videos.index') }}"
                            class="inline-flex items-center rounded-full border px-4 py-2 text-xs font-semibold transition {{ $selectedCategory ? 'border-neutral-200 text-neutral-600 hover:border-primary-200 hover:text-primary-700' : 'border-primary-500 bg-primary-500 text-white shadow-sm' }}"
                        >
                            Tümü
                        </a>
                        @foreach($videoCategories as $category)
                            <a
                                href="{{ route('videos.index', ['category' => $category->slug]) }}"
                                class="inline-flex items-center rounded-full border px-4 py-2 text-xs font-semibold transition {{ $selectedCategory?->id === $category->id ? 'border-primary-500 bg-primary-500 text-white shadow-sm' : 'border-neutral-200 text-neutral-600 hover:border-primary-200 hover:text-primary-700' }}"
                            >
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            @if($videos->count())
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
                                @if($video->category)
                                    <span class="inline-flex w-fit items-center rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700">
                                        {{ $video->category->name }}
                                    </span>
                                @endif
                                <h2 class="text-xl font-semibold text-secondary-900 group-hover:text-primary-600 transition">
                                    <a href="{{ route('videos.show', $video->slug) }}">
                                        {{ $video->title }}
                                    </a>
                                </h2>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pt-10">
                    {{ $videos->links() }}
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-neutral-200 bg-white px-6 py-16 text-center text-neutral-500 shadow-sm">
                    Henüz yayınlanmış video bulunmuyor.
                </div>
            @endif
        </div>
    </section>
@endsection
