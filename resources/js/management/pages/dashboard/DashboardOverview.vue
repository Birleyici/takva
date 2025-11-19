<script setup>
import { RouterLink } from 'vue-router';

const quickActions = [
    {
        title: 'Yeni Makale',
        description: 'Gündemdeki konular için hızlıca makale oluşturun.',
        to: { name: 'management.articles.create' },
        icon: 'M12 4v16m8-8H4',
        color: 'from-primary-500/90 to-primary-600',
    },
    {
        title: 'Yeni Sayı',
        description: 'Derginin yeni sayısını hazırlamaya başlayın.',
        to: { name: 'management.issues.create' },
        icon: 'M5 4h14v16H5z M9 8h6',
        color: 'from-secondary-500/90 to-secondary-600',
    },
    {
        title: 'Yazarlar',
        description: 'Yeni yazar ekleyin veya mevcutları düzenleyin.',
        to: { name: 'management.authors' },
        icon: 'M12 7a4 4 0 1 1-4 4 4 4 0 0 1 4-4zm0 7c-4 0-6 2-6 4v2h12v-2c0-2-2-4-6-4z',
        color: 'from-accent-500/90 to-accent-600',
    },
    {
        title: 'Site Ayarları',
        description: 'İletişim bilgileri ve menü içeriklerini güncelleyin.',
        to: { name: 'management.settings.site' },
        icon: 'M12 8a4 4 0 1 1-4 4 4 4 0 0 1 4-4zm8 4a8 8 0 1 1-8-8 8 8 0 0 1 8 8z',
        color: 'from-rose-400/90 to-rose-500',
    },
];

const tips = [
    'Menü sayfalarını yöneterek yeni özel içerikler ekleyebilirsiniz.',
    'Yazar profillerine kapak görseli ve biyografi eklemeyi unutmayın.',
    'PDF ve kapak görsellerinde 50 MB sınırını aşmamaya dikkat edin.',
    'Site ayarlarından iletişim bilgilerini güncel tutun.',
];
</script>

<template>
    <section class="space-y-8">
        <div class="rounded-3xl border border-neutral-200 bg-gradient-to-r from-secondary-900 via-secondary-800 to-primary-900 text-white shadow-sm">
            <div class="px-6 py-8 sm:px-10 sm:py-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-3 max-w-2xl">
                    <p class="text-xs uppercase tracking-[0.35em] text-accent-200 font-semibold">
                        Takva Yönetim Paneli
                    </p>
                    <h1 class="text-3xl sm:text-4xl font-bold leading-tight">
                        Hoş geldiniz, içerikleriniz sizi bekliyor.
                    </h1>
                    <p class="text-sm text-white/80">
                        Makale, sayı ve yazar işlemlerinizi buradan hızla yönetebilirsiniz. Panelde yeni
                        geliştirdiğimiz özellikler aşağıdaki yönlendirmelerde listelendi.
                    </p>
                </div>
                <div class="rounded-2xl border border-white/20 bg-white/5 px-6 py-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.25em] text-white/60 font-semibold">
                        Durum
                    </p>
                    <p class="mt-2 text-lg font-semibold tracking-wide">
                        Panel aktif
                    </p>
                    <p class="text-xs text-white/60">
                        {{ new Date().toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                    </p>
                </div>
            </div>
        </div>

        <section class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <header class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-secondary-900">Hızlı İşlemler</h2>
                    <p class="text-sm text-neutral-500">
                        Sık yapılan işlemler için kısayolları kullanın.
                    </p>
                </div>
            </header>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <RouterLink
                    v-for="action in quickActions"
                    :key="action.title"
                    :to="action.to"
                    class="group flex h-full flex-col gap-4 rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-primary-200/80"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl text-white shadow-inner shadow-black/10"
                        :class="['bg-gradient-to-br', action.color]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" :d="action.icon" />
                        </svg>
                    </div>
                    <div class="mt-4 space-y-2">
                        <h3 class="text-base font-semibold text-secondary-900">
                            {{ action.title }}
                        </h3>
                        <p class="text-sm text-neutral-500 leading-relaxed">
                            {{ action.description }}
                        </p>
                    </div>
                    <div class="mt-auto pt-4 text-sm font-semibold text-primary-600">
                        Başlat →
                    </div>
                </RouterLink>
            </div>
        </section>

        <section class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <header class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-secondary-900">Panel İpuçları</h2>
                    <p class="text-sm text-neutral-500">Daha düzenli bir içerik yönetimi için öneriler:</p>
                </div>
            </header>
            <ul class="space-y-3">
                <li
                    v-for="tip in tips"
                    :key="tip"
                    class="flex items-start gap-3 rounded-2xl border border-neutral-200/70 bg-neutral-50 px-4 py-3 text-sm text-neutral-700"
                >
                    <span class="mt-1 h-2 w-2 rounded-full bg-primary-400"></span>
                    <p class="leading-relaxed">{{ tip }}</p>
                </li>
            </ul>
        </section>
    </section>
</template>
