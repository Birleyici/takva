@props(['issueNumber', 'title', 'description', 'theme', 'url' => '#'])

@php
$themeClasses = [
    'primary' => 'from-primary-500 to-primary-600',
    'accent' => 'from-accent-500 to-accent-600',
    'secondary' => 'from-secondary-500 to-secondary-600'
];
$gradientClass = $themeClasses[$theme] ?? $themeClasses['primary'];
@endphp

<div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
    <div class="relative h-48 bg-gradient-to-br {{ $gradientClass }} overflow-hidden">
        <div class="absolute inset-0 bg-black bg-opacity-10"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="text-center text-white">
                <div class="text-sm font-medium opacity-90 mb-2">Sayı #{{ $issueNumber }}</div>
                <h4 class="text-2xl font-heading font-bold">{{ $title }}</h4>
            </div>
        </div>
    </div>
    <div class="p-6 bg-white">
        <h3 class="text-xl font-heading font-semibold text-secondary-800 mb-3">{{ $title }}</h3>
        <p class="text-secondary-600 mb-4 leading-relaxed text-sm">{{ $description }}</p>
        <a href="{{ $url }}" class="inline-flex items-center text-primary-500 hover:text-primary-600 font-medium text-sm group-hover:translate-x-1 transition-all duration-300">
            Devamını Oku 
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>
</div>