@props(['title', 'description', 'image', 'url' => '#', 'reverse' => false])

<div class="flex flex-col {{ $reverse ? 'lg:flex-row-reverse' : 'lg:flex-row' }} gap-8 items-center">
    <div class="lg:w-1/2">
        <img 
            src="{{ $image }}" 
            alt="{{ $title }}" 
            class="w-full h-64 lg:h-80 object-cover rounded-2xl shadow-lg"
        />
    </div>
    <div class="lg:w-1/2">
        <h3 class="text-2xl md:text-3xl font-decorative font-bold text-secondary-900 mb-4">
            {{ $title }}
        </h3>
        <p class="text-secondary-600 leading-relaxed mb-6">
            {{ $description }}
        </p>
        <a href="{{ $url }}" class="inline-flex items-center text-primary-500 hover:text-primary-600 font-semibold transition-colors">
            Devamını Oku
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
            </svg>
        </a>
    </div>
</div>