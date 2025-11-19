@extends('layouts.app')

@section('title', 'Sayılar - Takva Dergisi')
@section('description', 'Takva Dergisi sayı arşivine göz atın, yıl ve aya göre filtreleyin.')
@section('keywords', 'takva dergisi sayıları, dergi arşivi, yıl ay filtre')

@section('content')
    <section class="relative overflow-hidden pt-32 pb-20">
        <div class="absolute inset-0 bg-gradient-to-br from-secondary-900 via-secondary-900 to-primary-900/90"></div>
        <div class="absolute inset-0 opacity-30 mix-blend-overlay" style="background-image: url('https://www.takvadergisi.org/images/pattern.png'); background-size: cover;"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-xs sm:text-sm uppercase tracking-[0.3em] text-accent-200 font-semibold">Dergi Arşivi</p>
                    <h1 class="mt-4 text-4xl sm:text-5xl font-bold leading-tight">Sayılarımızı Keşfedin</h1>
                    <p class="mt-4 text-sm sm:text-base text-white/70 leading-relaxed">
                        Geçmiş sayılarımızı yıl ve aya göre filtreleyerek takip edebilir, dilediğiniz sayının PDF'ini görüntüleyebilirsiniz.
                    </p>
                </div>

                <form method="GET" class="w-full max-w-md space-y-3 rounded-3xl border border-white/20 bg-white/5 p-5 backdrop-blur">
                    <div>
                        <label for="year" class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Yıl</label>
                        <select id="year" name="year" class="mt-2 w-full rounded-2xl border border-white/40 bg-white/10 px-4 py-2 text-sm text-white/90 focus:border-accent-200 focus:bg-white/20 focus:outline-none">
                            <option value="">Tümü</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" @selected($selectedYear == $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="month" class="text-xs font-semibold uppercase tracking-[0.2em] text-white/70">Ay</label>
                        <select id="month" name="month" class="mt-2 w-full rounded-2xl border border-white/40 bg-white/10 px-4 py-2 text-sm text-white/90 focus:border-accent-200 focus:bg-white/20 focus:outline-none">
                            <option value="">Tümü</option>
                            @foreach($months as $value => $label)
                                <option value="{{ $value }}" @selected($selectedMonth == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 rounded-2xl bg-accent-500 px-4 py-2 text-sm font-semibold text-secondary-900 shadow-sm transition hover:bg-accent-400">
                            Filtrele
                        </button>
                        @if($selectedMonth || $selectedYear)
                            <a href="{{ route('issues.index') }}" class="rounded-2xl border border-white/30 px-4 py-2 text-sm font-semibold text-white/80 transition hover:bg-white/10">
                                Sıfırla
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="relative -mt-12 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($issues->count())
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($issues as $issue)
                        <article class="group rounded-3xl border border-neutral-200 bg-white shadow-sm overflow-hidden transition hover:-translate-y-1 hover:shadow-xl">
                            <a
                                href="{{ route('issues.show', $issue->slug) }}"
                                class="relative block w-full overflow-hidden"
                                style="aspect-ratio:282/401;"
                            >
                                @if($issue->coverImage?->url)
                                    <img src="{{ $issue->coverImage->url }}" alt="{{ $issue->title }}" class="h-full w-full object-cover" />
                                    <div class="absolute inset-0 bg-gradient-to-t from-secondary-900/80 via-secondary-900/40 to-transparent"></div>
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-br from-primary-600/40 via-secondary-900/60 to-secondary-900/90"></div>
                                @endif
                                <div class="absolute inset-0 flex flex-col justify-end px-6 py-5 text-white">
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-accent-200">{{ $issue->year }} · {{ $issue->month_name }}</p>
                                    <h2 class="mt-2 text-xl font-semibold leading-snug">
                                        {{ $issue->title }}
                                    </h2>
                                </div>
                            </a>
                            <div class="px-6 py-6 space-y-4">
                                <p class="text-sm text-neutral-600 leading-relaxed line-clamp-3">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($issue->description), 160) }}
                                </p>
                                <div class="flex flex-wrap gap-3 text-sm font-semibold">
                                    <a href="{{ route('issues.show', $issue->slug) }}" class="inline-flex flex-1 items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 text-neutral-700 transition hover:border-primary-200 hover:text-primary-700">
                                        Detayları Gör
                                    </a>
                                    @if($issue->pdf_url)
                                        <a href="{{ $issue->pdf_url }}" target="_blank" rel="noopener" class="inline-flex flex-1 items-center justify-center rounded-xl bg-primary-500 px-4 py-2 text-white transition hover:bg-primary-600">
                                            Online Oku
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pt-10">
                    {{ $issues->links() }}
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-neutral-200 bg-white px-6 py-16 text-center text-neutral-500 shadow-sm">
                    Seçili filtrelere uygun sayı bulunamadı.
                </div>
            @endif
        </div>
    </section>
@endsection
