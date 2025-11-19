@extends('layouts.app')

@section('title', 'Arama Sonuçları - Takva Dergisi')
@section('description', 'Takva Dergisi arama sonuçları sayfası')

@section('content')
    <section class="bg-gradient-to-br from-neutral-50 via-white to-primary-50 py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-heading font-bold text-secondary-900 mb-6">
                İçerik Arama
            </h1>
            <p class="text-lg text-secondary-600 mb-8">
                Makaleler, sayılar, yazarlar ve konular arasında dilediğiniz anahtar kelimeyi arayın.
            </p>
            <form action="{{ route('search.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 max-w-2xl mx-auto">
                <input type="search"
                       name="q"
                       value="{{ $query }}"
                       placeholder="Örneğin: Ramazan, Bilal Özbuğday, Takva 32. Sayı"
                       class="flex-1 px-6 py-4 rounded-lg border border-neutral-200 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:outline-none text-secondary-700">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-lg font-semibold transition-colors">
                    Ara
                </button>
            </form>
            @if($query === '')
                <p class="text-secondary-500 mt-6">Aramak istediğiniz kelimeyi yazıp enter'a basın.</p>
            @else
                <p class="text-secondary-500 mt-6">
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
