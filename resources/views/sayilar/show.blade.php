@extends('layouts.app')

@section('title', $issue->title . ' - Takva Dergisi')
@section('description', \Illuminate\Support\Str::limit(strip_tags($issue->description), 150))
@section('keywords', $issue->title)

@section('content')
    <section class="relative overflow-hidden bg-secondary-900 text-white">
        <div class="absolute inset-0">
            <img src="{{ $issue->coverImage->url ?? '/placeholder.jpg' }}" alt="{{ $issue->title }}" class="h-full w-full object-cover opacity-30">
            <div class="absolute inset-0 bg-secondary-900/80"></div>
        </div>
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-20 text-center">
            <p class="text-xs uppercase tracking-[0.3em] text-accent-200 font-semibold">{{ $issue->year }} · {{ $issue->month_name }}</p>
            <h1 class="mt-6 text-4xl sm:text-5xl font-bold leading-tight">{{ $issue->title }}</h1>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                @if($issue->pdf_url)
                    <a href="{{ $issue->pdf_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-2xl bg-accent-500 px-6 py-3 text-sm font-semibold text-secondary-900 shadow-sm transition hover:bg-accent-400">
                        Online Oku
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($issue->coverImage?->url)
                <figure class="mx-auto mb-10 max-w-sm">
                    <div class="overflow-hidden rounded-3xl border border-neutral-200 shadow-lg" style="aspect-ratio:282/401;">
                        <img
                            src="{{ $issue->coverImage->url }}"
                            alt="{{ $issue->title }} kapak görseli"
                            class="h-full w-full object-cover"
                        >
                    </div>
                    <figcaption class="mt-4 text-center text-sm text-neutral-500">
                        {{ $issue->title }} – {{ $issue->year }} {{ $issue->month_name }} sayısı kapak görseli
                    </figcaption>
                </figure>
            @endif

            @if($issue->description)
                <article class="article-body">
                    {!! $issue->description !!}
                </article>
            @else
                <div class="rounded-3xl border border-neutral-200 bg-neutral-50 px-6 py-12 text-center text-neutral-500">
                    Bu sayı için detaylı içerik yakında eklenecek.
                </div>
            @endif
        </div>
    </section>
@endsection
