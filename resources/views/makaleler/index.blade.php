@extends('layouts.app')

@section('title', 'Makaleler - Takva Dergisi')
@section('description', 'Takva Dergisi tarafından hazırlanan güncel makaleleri inceleyin.')
@section('keywords', 'makaleler, islam, takva dergisi, kültür, bilim')

@section('content')
    <section class="relative overflow-hidden pt-32 pb-20">
        <div class="absolute inset-0 bg-gradient-to-br from-secondary-900 via-secondary-900 to-primary-900/90"></div>
        <div class="absolute inset-0 opacity-30 mix-blend-overlay" style="background-image: url('https://www.takvadergisi.org/images/pattern.png'); background-size: cover;"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-xs sm:text-sm uppercase tracking-[0.3em] text-accent-200 font-semibold">@trupper('Makaleler')</p>
                    <h1 class="mt-4 text-4xl sm:text-5xl font-bold leading-tight">{{ $heroTitle }}</h1>
                    <p class="mt-4 text-sm sm:text-base text-white/70 leading-relaxed">
                        {{ $heroDescription }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if($selectedCategory)
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-4 py-2 text-xs font-medium">
                            Konu: {{ $selectedCategory->name }}
                            <a
                                href="{{ $selectedAuthor ? route('articles.author', $selectedAuthor->slug) : route('articles.index') }}"
                                class="text-accent-200 hover:text-white transition"
                            >
                                ×
                            </a>
                        </span>
                    @endif
                    @if($selectedAuthor)
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-4 py-2 text-xs font-medium">
                            Yazar: {{ $selectedAuthor->name }}
                            <a
                                href="{{ $selectedCategory ? route('articles.category', ['category' => $selectedCategory->slug]) : route('articles.index') }}"
                                class="text-accent-200 hover:text-white transition"
                            >
                                ×
                            </a>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="relative -mt-12 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-12 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
                <div class="space-y-10">
                    @if($articles->count())
                        <div class="grid gap-8 sm:grid-cols-2">
                            @foreach($articles as $article)
                                <article class="group rounded-3xl border border-neutral-200 bg-white shadow-sm overflow-hidden transition hover:-translate-y-1 hover:shadow-xl">
                                    <a href="{{ route('articles.show', $article->slug) }}" class="block relative">
                                        <img
                                            src="{{ $article->featureImage->url ?? '/placeholder.jpg' }}"
                                            alt="{{ $article->title }}"
                                            class="h-52 w-full object-cover transition duration-500 group-hover:scale-105"
                                        >
                                        @if($article->category)
                                            <span class="absolute left-4 top-4 inline-flex items-center rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-secondary-800">
                                                {{ $article->category->name }}
                                            </span>
                                        @endif
                                    </a>
                                    <div class="p-6 space-y-4">
                                        <div class="flex items-center gap-3 text-xs text-neutral-500">
                                            <span>{{ optional($article->published_at)->translatedFormat('d F Y') ?? $article->created_at->translatedFormat('d F Y') }}</span>
                                            @if($article->author)
                                                <span>•</span>
                                                <a
                                                    href="{{ $selectedCategory
                                                        ? route('articles.category', ['category' => $selectedCategory->slug, 'author' => $article->author->slug])
                                                        : route('articles.author', $article->author->slug)
                                                    }}"
                                                    class="hover:text-primary-600 transition"
                                                >
                                                    {{ $article->author->name }}
                                                </a>
                                            @endif
                                        </div>
                                        <a href="{{ route('articles.show', $article->slug) }}" class="block">
                                            <h2 class="text-xl font-semibold text-secondary-900 group-hover:text-primary-600 transition">
                                                {{ $article->title }}
                                            </h2>
                                        </a>
                                        <p class="text-sm text-neutral-600 leading-relaxed line-clamp-3">
                                            {{ $article->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($article->content), 140) }}
                                        </p>
                                        <div>
                                            <a href="{{ route('articles.show', $article->slug) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-600 hover:text-primary-700">
                                                Devamını Oku
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="pt-4">
                            {{ $articles->links() }}
                        </div>
                    @else
                        <div class="rounded-3xl border border-dashed border-neutral-200 bg-neutral-50 px-6 py-16 text-center text-neutral-500">
                            Şu anda görüntülenebilecek makale bulunmuyor.
                        </div>
                    @endif
                </div>

                <aside class="space-y-8">
                    <div class="rounded-3xl border border-neutral-200 bg-white shadow-sm overflow-hidden">
                        <div class="border-b border-neutral-100 px-6 py-5">
                            <h3 class="text-sm font-semibold text-secondary-900 uppercase tracking-[0.2em]">Konular</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            @foreach($categories as $category)
                                <a href="{{ route('articles.category', ['category' => $category->slug]) }}" class="flex items-center justify-between rounded-xl border border-transparent px-4 py-3 text-sm text-secondary-700 hover:border-primary-200 hover:bg-primary-50/60 hover:text-primary-700 transition">
                                    <span>{{ $category->name }}</span>
                                    <span class="inline-flex items-center justify-center rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-semibold text-neutral-500">{{ $category->articles_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-3xl border border-neutral-200 bg-white shadow-sm overflow-hidden">
                        <div class="border-b border-neutral-100 px-6 py-5">
                            <h3 class="text-sm font-semibold text-secondary-900 uppercase tracking-[0.2em]">Öne Çıkan Yazarlar</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            @foreach($featuredAuthors as $author)
                                <a
                                    href="{{ $selectedCategory
                                        ? route('articles.category', ['category' => $selectedCategory->slug, 'author' => $author->slug])
                                        : route('articles.author', $author->slug)
                                    }}"
                                    class="flex items-center gap-3 rounded-xl border border-transparent px-3 py-2 text-sm text-secondary-700 hover:border-primary-200 hover:bg-primary-50/60 hover:text-primary-700 transition"
                                >
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-neutral-100 text-neutral-500 overflow-hidden">
                                        @if($author->profileImage?->url)
                                            <img src="{{ $author->profileImage->url }}" alt="{{ $author->name }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="font-semibold">{{ \Illuminate\Support\Str::of($author->name)->substr(0, 1)->upper() }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-medium">{{ $author->name }}</p>
                                        <p class="text-xs text-neutral-400">{{ $author->articles_count }} makale</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-3xl border border-neutral-200 bg-white shadow-sm overflow-hidden">
                        <div class="border-b border-neutral-100 px-6 py-5">
                            <h3 class="text-sm font-semibold text-secondary-900 uppercase tracking-[0.2em]">Popüler Yazılar</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach($popularArticles as $popular)
                                <a href="{{ route('articles.show', $popular->slug) }}" class="flex items-center gap-3 rounded-xl border border-transparent px-3 py-2 text-sm text-secondary-700 hover:border-primary-200 hover:bg-primary-50/60 hover:text-primary-700 transition">
                                    <div class="h-12 w-12 overflow-hidden rounded-xl bg-neutral-100">
                                        <img src="{{ $popular->featureImage->url ?? '/placeholder.jpg' }}" alt="{{ $popular->title }}" class="h-full w-full object-cover">
                                    </div>
                                    <div>
                                        <p class="font-medium line-clamp-2">{{ $popular->title }}</p>
                                        <p class="text-xs text-neutral-400">{{ optional($popular->published_at)->translatedFormat('d F Y') ?? $popular->created_at->translatedFormat('d F Y') }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
