@extends('layouts.app')

@section('title', $article->title . ' - Takva Dergisi')
@section('description', $article->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($article->content), 150))
@section('keywords', $article->category?->name)

@section('content')
    <section class="relative overflow-hidden bg-secondary-900 text-white">
        <div class="absolute inset-0">
            <img src="{{ $article->featureImage->url ?? 'https://images.unsplash.com/photo-1471107340929-a87cd0f5b5f3?auto=format&fit=crop&w=1200&q=80' }}" alt="{{ $article->title }}" class="h-full w-full object-cover opacity-30">
            <div class="absolute inset-0 bg-secondary-900/80"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-20">
            @if($article->category)
                <a href="{{ route('articles.category', ['category' => $article->category->slug]) }}" class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-xs font-semibold text-white/90 border border-white/20">
                    {{ $article->category->name }}
                </a>
            @endif
            <h1 class="mt-6 text-4xl sm:text-5xl font-bold leading-tight">{{ $article->title }}</h1>
            <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-white/70">
                @if($article->author)
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full overflow-hidden bg-white/10 flex items-center justify-center">
                            @if($article->author->profileImage?->url)
                                <img src="{{ $article->author->profileImage->url }}" alt="{{ $article->author->name }}" class="h-full w-full object-cover">
                            @else
                                <span class="font-semibold">{{ \Illuminate\Support\Str::of($article->author->name)->substr(0, 1)->upper() }}</span>
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <a href="{{ route('articles.author', $article->author->slug) }}" class="font-semibold text-white hover:text-accent-300 transition">{{ $article->author->name }}</a>
                            <span class="text-xs text-white/60">{{ __('Yazar') }}</span>
                        </div>
                    </div>
                @endif
                <span>•</span>
                <span>{{ optional($article->published_at)->translatedFormat('d F Y') ?? $article->created_at->translatedFormat('d F Y') }}</span>
            </div>
        </div>
    </section>

    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <article class="article-body">
                {!! $article->content !!}
            </article>
        </div>
    </section>

    @if($relatedArticles->count())
        <section class="pb-20 bg-neutral-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-primary-500 font-semibold">Benzer Yazılar</p>
                        <h2 class="mt-2 text-2xl font-semibold text-secondary-900">İlginizi Çekebilecek Diğer Yazılar</h2>
                    </div>
                    <a href="{{ route('articles.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700">
                        Tüm makaleler
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($relatedArticles as $related)
                        <article class="group rounded-2xl border border-neutral-200 bg-white shadow-sm overflow-hidden transition hover:-translate-y-1 hover:shadow-lg">
                            <a href="{{ route('articles.show', $related->slug) }}" class="block">
                                <img src="{{ $related->featureImage->url ?? 'https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $related->title }}" class="h-44 w-full object-cover">
                                <div class="p-5 space-y-3">
                                    <p class="text-xs text-neutral-400">{{ optional($related->published_at)->translatedFormat('d F Y') ?? $related->created_at->translatedFormat('d F Y') }}</p>
                                    <h3 class="text-lg font-semibold text-secondary-900 group-hover:text-primary-600 transition leading-snug">
                                        {{ $related->title }}
                                    </h3>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
