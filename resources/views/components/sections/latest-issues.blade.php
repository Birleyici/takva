@props(['issues' => null])

@php
    $issues = collect($issues);
@endphp

<!-- Latest Issues Section - Sade Renkli -->
<section class="py-20 bg-gradient-to-br from-slate-50 via-gray-50 to-neutral-100 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-heading font-bold text-secondary-900 mb-6">
                Son <span class="text-primary-500">Sayılarımız</span>
            </h2>
            <p class="text-xl text-secondary-600 max-w-3xl mx-auto leading-relaxed">
                En güncel sayılarımızla manevi yolculuğunuza eşlik edin
            </p>
        </div>
        
        @if($issues->isNotEmpty())
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
        @else
            <p class="text-center text-secondary-500 mt-10">Henüz yayınlanmış sayı bulunmuyor.</p>
        @endif
        
        <!-- View All Issues Button -->
        <div class="text-center mt-16">
            <a href="{{ route('issues.index') }}" class="group inline-flex items-center bg-accent-600 hover:bg-accent-700 text-white px-10 py-4 rounded-xl font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                <span class="flex items-center">
                    Tüm Sayıları Gör
                    <svg class="w-5 h-5 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </span>
            </a>
        </div>
    </div>
</section>
