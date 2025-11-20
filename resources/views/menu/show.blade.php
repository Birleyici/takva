@extends('layouts.app')

@section('title', $page->title . ' - Takva Dergisi')
@section('description', $page->summary ?? \Illuminate\Support\Str::limit(strip_tags($page->content), 150))
@section('keywords', $page->title)

@section('content')
    <section class="relative overflow-hidden bg-secondary-900 text-white">
        <div class="absolute inset-0">
            <img src="/placeholder.jpg" alt="{{ $page->title }}" class="h-full w-full object-cover opacity-20">
            <div class="absolute inset-0 bg-secondary-900/80"></div>
        </div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-16 text-center">
            <p class="text-xs uppercase tracking-[0.3em] text-accent-200 font-semibold">MENÜ</p>
            <h1 class="mt-4 text-4xl sm:text-5xl font-bold leading-tight">{{ $page->title }}</h1>
            @if($page->summary)
                <p class="mt-4 text-sm sm:text-base text-white/70 leading-relaxed">{{ $page->summary }}</p>
            @endif
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="article-paper">
                <article class="article-body">
                    {!! $page->content !!}
                </article>
            </div>
        </div>
    </section>
@endsection
