<?php use Illuminate\Support\Str; ?>
@props([
    'title' => '',
    'items' => collect(),
    'type' => 'articles',
    'query' => '',
])

@php
    $items = collect($items);
    $seeAllRoutes = [
        'articles' => route('articles.index'),
        'issues' => route('issues.index'),
        'authors' => route('authors.index'),
        'categories' => route('articles.index'),
    ];
@endphp

<section>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-heading font-semibold text-secondary-900">
                {{ $title }}
                <span class="text-primary-500 text-lg">({{ $items->count() }})</span>
            </h2>
        </div>
        @if($items->isNotEmpty() && isset($seeAllRoutes[$type]))
            <a href="{{ $seeAllRoutes[$type] }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                Tümünü Gör
            </a>
        @endif
    </div>

    @if($items->isEmpty())
        <p class="text-secondary-500">Bu bölümde sonuç bulunamadı.</p>
    @else
        <div class="grid gap-6 {{ $type === 'articles' ? 'md:grid-cols-2' : 'md:grid-cols-3' }}">
            @foreach($items as $item)
                @if($type === 'articles')
                    <a href="{{ route('articles.show', $item) }}" class="group flex flex-col md:flex-row bg-white border border-neutral-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="md:w-48 h-48 md:h-auto overflow-hidden">
                            <img src="{{ $item->featureImage?->url ?? '/placeholder.jpg' }}"
                                 alt="{{ $item->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <div class="p-6 flex-1">
                            <p class="text-xs uppercase tracking-wide text-neutral-500 mb-2">
                                @trupper(($item->category?->name ?? 'Genel') . ' · ' . optional($item->published_at)->translatedFormat('d F Y'))
                            </p>
                            <h3 class="text-xl font-semibold text-secondary-900 group-hover:text-primary-600 transition-colors">{{ $item->title }}</h3>
                            <p class="text-secondary-600 mt-3 text-sm leading-relaxed">{{ Str::limit(strip_tags($item->excerpt ?: $item->content), 150) }}</p>
                        </div>
                    </a>
                @elseif($type === 'issues')
                    <a href="{{ route('issues.show', $item) }}" class="bg-white border border-neutral-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-20 overflow-hidden rounded-lg bg-neutral-100">
                                <img src="{{ $item->coverImage?->url ?? '/placeholder.jpg' }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="text-sm text-neutral-500">{{ $item->month_name ?? '' }} {{ $item->year }}</p>
                                <h3 class="text-lg font-semibold text-secondary-900">{{ $item->title }}</h3>
                            </div>
                        </div>
                        <p class="text-secondary-600 text-sm">{{ Str::limit(strip_tags($item->description), 140) }}</p>
                    </a>
                @elseif($type === 'authors')
                    <a href="{{ route('articles.author', ['author' => $item->slug]) }}" class="bg-white border border-neutral-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full overflow-hidden bg-neutral-100">
                            <img src="{{ $item->profileImage?->url ?? 'https://ui-avatars.com/api/?name=' . urlencode($item->name) . '&background=0f766e&color=fff' }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-secondary-900">{{ $item->name }}</h3>
                            <p class="text-sm text-neutral-500">{{ $item->articles_count ?? $item->articles()->where('is_published', true)->count() }} makale</p>
                        </div>
                    </a>
                @elseif($type === 'categories')
                    <a href="{{ route('articles.category', ['category' => $item->slug]) }}" class="bg-white border border-neutral-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col gap-2">
                        <h3 class="text-lg font-semibold text-secondary-900">{{ $item->name }}</h3>
                        <p class="text-secondary-600 text-sm flex-1">{{ Str::limit(strip_tags($item->description), 150) ?: 'Kategori açıklaması yakında.' }}</p>
                        <span class="text-xs uppercase tracking-wide text-neutral-500">@trupper(($item->articles_count ?? 0) . ' makale')</span>
                    </a>
                @endif
            @endforeach
        </div>
    @endif
</section>
