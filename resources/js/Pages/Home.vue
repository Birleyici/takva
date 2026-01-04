<template>
    <Head title="Ana Sayfa - Takva Dergisi" />
    
    <div class="min-h-screen bg-white">
        <!-- Navigation - Transparent Header -->
        <nav class="absolute top-0 left-0 right-0 z-50 bg-transparent">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <div class="flex items-center">
                        <Link href="/" class="flex items-center space-x-3">
                            <div class="flex items-center space-x-3">
                                <!-- Takva Logo -->
                                <img 
                                    src="https://www.takvadergisi.org/images/takva-logo-red.png" 
                                    alt="Takva Dergisi Logo" 
                                    class="h-12 w-auto filter brightness-0 invert"
                                />
                                
                            </div>
                        </Link>
                    </div>
                    
                    <div class="hidden md:flex items-center space-x-8">
                        <Link href="/" class="text-white hover:text-accent-400 transition-colors font-medium">
                            Ana Sayfa
                        </Link>
                        <a href="#" class="text-white hover:text-accent-400 transition-colors font-medium">
                            Sayılar
                        </a>
                        <a href="#" class="text-white hover:text-accent-400 transition-colors font-medium">
                            Makaleler
                        </a>
                        <a href="#" class="text-white hover:text-accent-400 transition-colors font-medium">
                            Hakkımızda
                        </a>
                        <a href="#" class="text-white hover:text-accent-400 transition-colors font-medium">
                            İletişim
                        </a>
                        
                        <!-- CTA Button -->
                        <Link :href="route('issues.latest')" class="bg-accent-500 hover:bg-accent-600 text-white px-6 py-3 rounded-lg font-medium transition-colors shadow-lg">
                            Son Sayı
                        </Link>
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

        <!-- Hero Section - Shaha Style -->
        <section class="relative h-screen flex items-center justify-center overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0">
                <img 
                    src="https://images.pexels.com/photos/34414797/pexels-photo-34414797.jpeg" 
                    alt="Islamic Architecture Background" 
                    class="w-full h-full object-cover"
                />
                <!-- Lighter Dark Overlay -->
                <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                <!-- Islamic Pattern Overlay -->
                <div class="absolute inset-0 islamic-pattern-1 opacity-10"></div>
                <!-- White Gradient Fade at Bottom -->
            </div>
            
            <!-- Content -->
            <div class="relative z-10 text-center text-white max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Allah Lafzı -->
                <div class="mb-8">
                    <p class="text-accent-400 text-3xl md:text-4xl font-arabic mb-6">ﷲ</p>
                </div>

                <!-- Main Heading -->
                <h1 class="text-6xl md:text-8xl lg:text-9xl font-decorative font-bold mb-12 leading-tight">
                    <span class="text-accent-400">Takva</span>
                    <span class="block text-white">Dergİsİ</span>
                </h1>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                    <button class="group bg-accent-600 hover:bg-accent-700 text-white px-12 py-5 rounded-lg font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl">
                        <span class="flex items-center">
                            Son Sayıyı Oku
                            <svg class="w-5 h-5 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </span>
                    </button>
                    <Link :href="route('issues.latest')" class="border-2 border-white text-white hover:bg-white hover:text-primary-900 px-12 py-5 rounded-lg font-semibold text-lg transition-all duration-300 transform hover:scale-105">
                        Son Sayı
                    </Link>
                </div>
            </div>
        </section>

        <!-- Latest Articles Section -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-decorative font-bold text-secondary-900 mb-6">
                        Son Eklenen <span class="text-primary-500">Makaleler</span>
                    </h2>
                    <p class="text-xl text-secondary-600 max-w-3xl mx-auto leading-relaxed">
                        En güncel makalelerimizle İslami bilim ve kültür dünyasından haberdar olun
                    </p>
                </div>
                
                <div class="space-y-12">
                    <!-- Article 1 -->
                    <div class="flex flex-col lg:flex-row gap-8 items-center">
                        <div class="lg:w-1/2">
                            <img 
                                src="/placeholder.jpg" 
                                alt="İslami Bilim Tarihi" 
                                class="w-full h-64 lg:h-80 object-cover rounded-2xl shadow-lg"
                            />
                        </div>
                        <div class="lg:w-1/2">
                            <h3 class="text-2xl md:text-3xl font-decorative font-bold text-secondary-900 mb-4">
                                İslam Medeniyetinde Bilim ve Teknoloji
                            </h3>
                            <p class="text-secondary-600 leading-relaxed mb-6">
                                İslam medeniyetinin altın çağında bilim ve teknoloji alanında yaşanan gelişmeler, günümüz modern biliminin temellerini oluşturmuştur. Bu makalede, Müslüman bilginlerin matematik, tıp, astronomi ve kimya alanlarındaki...
                            </p>
                            <a href="#" class="inline-flex items-center text-primary-500 hover:text-primary-600 font-semibold transition-colors">
                                Devamını Oku
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Article 2 -->
                    <div class="flex flex-col lg:flex-row-reverse gap-8 items-center">
                        <div class="lg:w-1/2">
                            <img 
                                src="/placeholder.jpg" 
                                alt="İslami Sanat" 
                                class="w-full h-64 lg:h-80 object-cover rounded-2xl shadow-lg"
                            />
                        </div>
                        <div class="lg:w-1/2">
                            <h3 class="text-2xl md:text-3xl font-decorative font-bold text-secondary-900 mb-4">
                                İslami Sanatın Manevi Boyutları
                            </h3>
                            <p class="text-secondary-600 leading-relaxed mb-6">
                                İslami sanat, sadece estetik bir ifade biçimi değil, aynı zamanda manevi bir yolculuğun da göstergesidir. Geometrik desenler, hat sanatı ve mimari yapılar aracılığıyla Allah'ın birliği ve sonsuzluğu...
                            </p>
                            <a href="#" class="inline-flex items-center text-primary-500 hover:text-primary-600 font-semibold transition-colors">
                                Devamını Oku
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Article 3 -->
                    <div class="flex flex-col lg:flex-row gap-8 items-center">
                        <div class="lg:w-1/2">
                            <img 
                                src="/placeholder.jpg" 
                                alt="İslami Felsefe" 
                                class="w-full h-64 lg:h-80 object-cover rounded-2xl shadow-lg"
                            />
                        </div>
                        <div class="lg:w-1/2">
                            <h3 class="text-2xl md:text-3xl font-decorative font-bold text-secondary-900 mb-4">
                                Modern Çağda İslami Düşünce
                            </h3>
                            <p class="text-secondary-600 leading-relaxed mb-6">
                                Günümüz dünyasında İslami düşüncenin rolü ve önemi giderek artmaktadır. Küreselleşen dünyada Müslümanların karşılaştığı zorluklar ve bu zorluklara İslami perspektiften yaklaşım biçimleri...
                            </p>
                            <a href="#" class="inline-flex items-center text-primary-500 hover:text-primary-600 font-semibold transition-colors">
                                Devamını Oku
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- View All Articles Button -->
                <div class="text-center mt-16">
                    <a href="#" class="group inline-flex items-center bg-primary-500 hover:bg-primary-600 text-white px-10 py-4 rounded-xl font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <span class="flex items-center">
                            Tüm Makaleleri Gör
                            <svg class="w-5 h-5 ml-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </span>
                    </a>
                </div>
            </div>
        </section>

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
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Magazine Issue 1 -->
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
                        <div class="relative h-48 bg-gradient-to-br from-primary-500 to-primary-600 overflow-hidden">
                            
                            <div class="absolute inset-0 bg-black bg-opacity-10"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="text-sm font-medium opacity-90 mb-2">Sayı #1</div>
                                    <h4 class="text-2xl font-heading font-bold">Ramazan Özel</h4>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 bg-white">
                            <h3 class="text-xl font-heading font-semibold text-secondary-800 mb-3">Ramazan ve Manevi Uyanış</h3>
                            <p class="text-secondary-600 mb-4 leading-relaxed text-sm">Mübarek Ramazan ayının manevi derinlikleri ve oruç ibadetinin hikmetleri üzerine...</p>
                            <a href="#" class="inline-flex items-center text-primary-500 hover:text-primary-600 font-medium text-sm group-hover:translate-x-1 transition-all duration-300">
                                Devamını Oku 
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Magazine Issue 2 -->
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
                        <div class="relative h-48 bg-gradient-to-br from-accent-500 to-accent-600 overflow-hidden">
                            
                            <div class="absolute inset-0 bg-black bg-opacity-10"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="text-sm font-medium opacity-90 mb-2">Sayı #2</div>
                                    <h4 class="text-2xl font-heading font-bold">Hac Özel</h4>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 bg-white">
                            <h3 class="text-xl font-heading font-semibold text-secondary-800 mb-3">Hac İbadeti ve Manevi Yolculuk</h3>
                            <p class="text-secondary-600 mb-4 leading-relaxed text-sm">Beytullah'a yolculuk, hac ibadetinin ruhani boyutları ve manevi temizlenme...</p>
                            <a href="#" class="inline-flex items-center text-primary-500 hover:text-primary-600 font-medium text-sm group-hover:translate-x-1 transition-all duration-300">
                                Devamını Oku 
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Magazine Issue 3 -->
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group">
                        <div class="relative h-48 bg-gradient-to-br from-secondary-500 to-secondary-600 overflow-hidden">
                            
                            <div class="absolute inset-0 bg-black bg-opacity-10"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center text-white">
                                    <div class="text-sm font-medium opacity-90 mb-2">Sayı #3</div>
                                    <h4 class="text-2xl font-heading font-bold">Aile Özel</h4>
                                </div>
                            </div>
                        </div>
                        <div class="p-6 bg-white">
                            <h3 class="text-xl font-heading font-semibold text-secondary-800 mb-3">İslam'da Aile ve Çocuk Terbiyesi</h3>
                            <p class="text-secondary-600 mb-4 leading-relaxed text-sm">Müslüman ailenin temelleri, çocuk terbiyesi ve İslami değerlerle yetiştirme...</p>
                            <a href="#" class="inline-flex items-center text-primary-500 hover:text-primary-600 font-medium text-sm group-hover:translate-x-1 transition-all duration-300">
                                Devamını Oku 
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- View All Issues Button -->
                <div class="text-center mt-16">
                    <a href="#" class="group inline-flex items-center bg-accent-600 hover:bg-accent-700 text-white px-10 py-4 rounded-xl font-semibold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
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

        <!-- Newsletter Section - İslami Desenli -->
        <section class="py-20 bg-secondary-50 relative overflow-hidden">
            <!-- İslami Desen Arka Plan -->
            
            <!-- Cami Silueti -->
            <div class="absolute inset-0 mosque-silhouette"></div>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-heading font-bold text-secondary-900 mb-4">
                    Dergi <span class="text-primary-500">Arşivi</span>
                </h2>
                <p class="text-xl text-secondary-600 mb-8 leading-relaxed">
                    Güncel ve geçmiş sayılarımıza online olarak erişin
                </p>
                
                <div class="flex justify-center">
                    <Link :href="route('issues.index')" class="bg-primary-500 hover:bg-primary-600 text-white px-8 py-4 rounded-lg font-medium transition-colors text-center">
                        Sayıları Gör
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer - İslami Desenli -->
        <footer class="bg-secondary-800 text-white relative overflow-hidden">
            <!-- Footer Arka Plan Deseni -->
            <div class="absolute inset-0 opacity-10">
                <div class="islamic-pattern-2"></div>
                <div class="arabesque-pattern"></div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center space-x-3 mb-6">
                          
                            <div>
 <img 
                                    src="https://www.takvadergisi.org/images/takva-logo-red.png" 
                                    alt="Takva Dergisi Logo" 
                                    class="h-12 w-auto filter brightness-0 invert"
                                />                           
                                 </div>
                        </div>
                        <p class="text-neutral-300 mb-6 leading-relaxed max-w-md">
                            Şüphesiz azığın en hayırlısı takvâdır. (Bakara 197)
                        </p>
                        <div class="flex space-x-3">
                            <a href="#" class="w-10 h-10 bg-neutral-700 hover:bg-primary-500 rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-neutral-700 hover:bg-primary-500 rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                                </svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-neutral-700 hover:bg-primary-500 rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-heading font-semibold mb-6">Hızlı Linkler</h4>
                        <ul class="space-y-3 text-neutral-300">
                            <li><Link href="/" class="hover:text-primary-400 transition-colors text-sm">Ana Sayfa</Link></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors text-sm">Son Sayılar</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors text-sm">Makaleler</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors text-sm">Yazarlar</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors text-sm">Hakkımızda</a></li>
                            <li><a href="#" class="hover:text-primary-400 transition-colors text-sm">İletişim</a></li>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-heading font-semibold mb-6">İletişim</h4>
                        <ul class="space-y-3 text-neutral-300 text-sm">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-3 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                info@takvadergisi.com
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-3 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                +90 XXX XXX XX XX
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-3 mt-0.5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                İstanbul, Türkiye
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-neutral-700 mt-12 pt-8 text-center">
                    <p class="text-neutral-400 mb-2 text-sm">&copy; {{ new Date().getFullYear() }} Takva Dergisi.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
</script>
