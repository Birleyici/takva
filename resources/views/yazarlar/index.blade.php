@extends('layouts.app')

@section('title', 'Yazarlar - Takva Dergisi')
@section('description', 'Takva Dergisi yazarlarını keşfedin ve yazılarına ulaşın.')
@section('keywords', 'yazarlar, takva dergisi')

@section('content')
    <section class="relative overflow-hidden pt-32 pb-20">
        <div class="absolute inset-0 bg-gradient-to-br from-secondary-900 via-secondary-900 to-primary-900/90"></div>
        <div class="absolute inset-0 opacity-30 mix-blend-overlay" style="background-image: url('https://www.takvadergisi.org/images/pattern.png'); background-size: cover;"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-3xl text-center lg:text-left">
                    <p class="text-xs sm:text-sm uppercase tracking-[0.3em] text-accent-200 font-semibold">Takva Yazarları</p>
                    <h1 class="mt-4 text-4xl sm:text-5xl font-bold leading-tight">Yazarlar Listesi</h1>
                    <p class="mt-4 text-sm sm:text-base text-white/70 leading-relaxed">
                        Takva Dergisi’nin yazarlarını görüntüleyebilir, kalemlerinden çıkan makalelere tek bir yerden ulaşabilirsiniz.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="relative -mt-12 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($authors->count())
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($authors as $author)
                        <article class="rounded-3xl border border-neutral-200 bg-white shadow-sm overflow-hidden transition hover:-translate-y-1 hover:shadow-xl">
                            <div class="relative h-40 bg-gradient-to-br from-primary-500/70 to-secondary-900/80">
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="h-20 w-20 rounded-full border-4 border-white shadow-lg overflow-hidden bg-white/80 flex items-center justify-center text-secondary-700 text-xl font-semibold">
                                        @if($author->profileImage?->url)
                                            <img src="{{ $author->profileImage->url }}" alt="{{ $author->name }}" class="h-full w-full object-cover">
                                        @else
                                            <span>{{ \Illuminate\Support\Str::of($author->name)->substr(0, 1)->upper() }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="px-6 pb-6 pt-12 text-center space-y-4">
                                <div>
                                    <h2 class="text-lg font-semibold text-secondary-900">{{ $author->name }}</h2>
                                    <p class="mt-1 text-sm text-neutral-500">{{ $author->articles_count }} makale</p>
                                </div>
                                <div class="flex flex-col gap-2 text-sm font-semibold">
                                    <a href="{{ route('articles.author', $author->slug) }}" class="inline-flex w-full items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 text-neutral-600 transition hover:border-neutral-300">
                                        Makaleleri Görüntüle
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pt-10">
                    {{ $authors->links() }}
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-neutral-200 bg-white px-6 py-16 text-center text-neutral-500 shadow-sm">
                    Yazar bilgileri yakında eklenecek.
                </div>
            @endif
        </div>
    </section>
@endsection
