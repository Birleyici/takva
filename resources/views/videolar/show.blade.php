@extends('layouts.app')

@section('title', $video->title . ' - Takva Dergisi')
@section('description', $video->description ? \Illuminate\Support\Str::limit(strip_tags($video->description), 150) : 'Takva Dergisi video içeriği')
@section('keywords', 'takva dergisi video, youtube, islami içerikler')

@section('content')
    <section class="relative overflow-hidden bg-secondary-900 text-white">
        <div class="absolute inset-0">
            <img src="{{ $video->thumbnail_url ?? '/placeholder.jpg' }}" alt="{{ $video->title }}" class="h-full w-full object-cover opacity-30">
            <div class="absolute inset-0 bg-secondary-900/80"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-20">
            <p class="text-xs uppercase tracking-[0.3em] text-accent-200 font-semibold">@trupper('Takva Videoları')</p>
            <h1 class="mt-4 text-4xl sm:text-5xl font-bold leading-tight">{{ $video->title }}</h1>
            <p class="mt-4 text-sm text-white/70">
                {{ $video->created_at?->translatedFormat('d F Y') }}
            </p>
            @if($video->category)
                <span class="mt-4 inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-xs font-semibold text-white/80 border border-white/20">
                    {{ $video->category->name }}
                </span>
            @endif
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="overflow-hidden rounded-3xl border border-neutral-200 shadow-lg">
                <div class="aspect-video w-full">
                    <iframe
                        src="{{ $video->embed_url }}"
                        class="h-full w-full"
                        title="{{ $video->title }}"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </div>
            </div>

            @if($video->description)
                <div class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-secondary-900">Video Hakkında</h2>
                    <p class="mt-4 text-sm text-neutral-600 leading-relaxed">
                        {{ $video->description }}
                    </p>
                </div>
            @endif

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('videos.index') }}" class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-6 py-3 text-sm font-semibold text-neutral-700 transition hover:border-primary-200 hover:text-primary-700">
                    Tüm Videolar
                </a>
                <a href="{{ $video->youtube_url }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-xl bg-primary-500 px-6 py-3 text-sm font-semibold text-white transition hover:bg-primary-600">
                    YouTube'da İzle
                </a>
            </div>
        </div>
    </section>
@endsection
