<!-- Newsletter Section - İslami Desenli -->
<section class="py-20 bg-secondary-50 relative overflow-hidden">
    <!-- İslami Desen Arka Plan -->
    <!-- Cami Silueti -->
    <div class="absolute inset-0 mosque-silhouette"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-heading font-bold text-secondary-900 mb-4">
            Bültenimize <span class="text-primary-500">Abone Olun</span>
        </h2>
        <p class="text-xl text-secondary-600 mb-8 leading-relaxed">
            Yeni sayılarımızdan ve özel içeriklerimizden haberdar olun
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto">
            <input 
                type="email" 
                placeholder="E-posta adresiniz" 
                class="flex-1 px-6 py-4 rounded-lg border border-neutral-200 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 focus:outline-none text-secondary-700"
            >
            <a href="{{ route('contact.show') }}" class="bg-primary-500 hover:bg-primary-600 text-white px-8 py-4 rounded-lg font-medium transition-colors text-center">
                Abone Ol
            </a>
        </div>
    </div>
</section>
