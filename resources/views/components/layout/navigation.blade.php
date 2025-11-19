<!-- Navigation - Transparent Header -->
<nav class="absolute top-0 left-0 right-0 z-50 bg-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-3">
                    <div class="flex items-center space-x-3">
                        <!-- Takva Logo -->
                        <img 
                            src="https://www.takvadergisi.org/images/takva-logo-red.png" 
                            alt="Takva Dergisi Logo" 
                            class="h-12 w-auto filter brightness-0 invert"
                        />
                    </div>
                </a>
            </div>
            
            <div class="hidden md:flex items-center space-x-6">
                <a href="/" class="text-white hover:text-accent-400 transition-colors font-medium">
                    Ana Sayfa
                </a>
                <a href="{{ route('issues.index') }}" class="text-white hover:text-accent-400 transition-colors font-medium">
                    Sayılar
                </a>
                <a href="{{ route('articles.index') }}" class="text-white hover:text-accent-400 transition-colors font-medium">
                    Makaleler
                </a>
                <div class="relative group">
                    <button class="inline-flex items-center gap-2 text-white hover:text-accent-400 transition-colors font-medium">
                        Konular
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200 absolute left-1/2 top-full mt-3 -translate-x-1/2">
                        <div class="rounded-2xl border border-white/20 bg-white/95 backdrop-blur-sm shadow-xl min-w-[240px] py-4">
                            @php($navCategories = $navCategories ?? collect())
                            @forelse($navCategories as $category)
                                <a href="{{ route('articles.category', ['category' => $category->slug]) }}"
                                   class="flex items-center justify-between px-5 py-2 text-sm text-secondary-700 hover:text-primary-600 hover:bg-primary-50/70 transition">
                                    <span>{{ $category->name }}</span>
                                    <span class="inline-flex items-center justify-center rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-semibold text-neutral-500">
                                        {{ $category->articles_count ?? 0 }}
                                    </span>
                                </a>
                            @empty
                                <div class="px-5 py-2 text-sm text-neutral-500">Konular yakında</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <a href="{{ route('authors.index') }}" class="text-white hover:text-accent-400 transition-colors font-medium">
                    Yazarlar
                </a>
                @php($navMenuPages = $navMenuPages ?? collect())
                @foreach($navMenuPages as $menuPage)
                    <a href="{{ route('menu.show', $menuPage->slug) }}" class="text-white hover:text-accent-400 transition-colors font-medium">
                        {{ $menuPage->title }}
                    </a>
                @endforeach
                <a href="{{ route('contact.show') }}" class="text-white hover:text-accent-400 transition-colors font-medium">
                    İletişim
                </a>
                
                <!-- CTA Button -->
                <a href="{{ route('contact.show') }}" class="bg-accent-500 hover:bg-accent-600 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg">
                    Abone Ol
                </a>
            </div>
            
            <!-- Mobile Menu Button -->
            <button class="md:hidden text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </div>
</nav>
