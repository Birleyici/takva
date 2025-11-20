@extends('layouts.app')

@section('title', 'Arama Sonuçları - Takva Dergisi')
@section('description', 'Takva Dergisi arama sonuçları sayfası')

@section('content')
    <section class="relative py-20 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-secondary-900/90 via-secondary-800/80 to-primary-900/70"></div>
        <div class="absolute inset-0 islamic-pattern-1 opacity-10"></div>
        <div class="absolute inset-0 arabesque-pattern opacity-10"></div>
        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-heading font-bold text-white mb-6">
                İçerik Arama
            </h1>
            <p class="text-lg text-white/80 mb-8">
                Makaleler, sayılar, yazarlar ve konular arasında dilediğiniz anahtar kelimeyi arayın.
            </p>
            <form action="{{ route('search.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 max-w-2xl mx-auto">
                <input type="search"
                       name="q"
                       value="{{ $query }}"
                       placeholder="Aradığınız kelimeyi yazın..."
                       class="flex-1 px-6 py-4 rounded-lg border border-white/30 bg-white/10 text-white placeholder-white/70 focus:ring-2 focus:ring-primary-400 focus:border-primary-400 focus:outline-none">
                <button type="submit" class="bg-accent-500 hover:bg-accent-600 text-white px-8 py-4 rounded-lg font-semibold transition-colors">
                    Ara
                </button>
            </form>
            @if($query === '')
                <p class="text-white/70 mt-6">Aramak istediğiniz kelimeyi yazıp enter'a basın.</p>
            @else
                <p class="text-white/80 mt-6">
                    "{{ $query }}" için {{ $totalResults }} sonuç bulundu.
                </p>
            @endif
        </div>
    </section>

    @if($query !== '')
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
            <x-search.results-section title="Makaleler" :items="$articles" type="articles" :query="$query" />
            <x-search.results-section title="Sayılar" :items="$issues" type="issues" :query="$query" />
            <x-search.results-section title="Yazarlar" :items="$authors" type="authors" :query="$query" />
            <x-search.results-section title="Konular" :items="$categories" type="categories" :query="$query" />
        </div>
    @endif
@endsection
