<script setup>
import { reactive } from 'vue';
import { RouterLink, useRoute } from 'vue-router';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const navigationItems = [
    {
        label: 'Genel Bakış',
        to: { name: 'management.dashboard' },
        icon: 'M3 12l2-2 4 4 8-8 2 2-10 10z',
    },
    {
        label: 'Kategoriler',
        to: { name: 'management.categories' },
        icon: 'M4 6h16M4 12h16M4 18h16',
    },
    {
        label: 'Yazarlar',
        to: { name: 'management.authors' },
        icon: 'M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0z M12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z',
    },
    {
        label: 'Makaleler',
        to: { name: 'management.articles' },
        icon: 'M4 5h16M4 12h16M4 19h10',
    },
    {
        label: 'Sayılar',
        to: { name: 'management.issues' },
        icon: 'M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm3 4v10m4-10v10m4-10v10',
    },
    {
        label: 'Menü Sayfaları',
        to: { name: 'management.menu-pages' },
        icon: 'M4 6h16M4 12h16M4 18h16',
    },
    {
        label: 'İletişim Mesajları',
        to: { name: 'management.contact-messages' },
        icon: 'M21 8V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v1m18 0v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8m18 0-9 6-9-6',
    },
    {
        label: 'Ayarlar',
        icon: 'M12 3v18m9-9H3',
        children: [
            {
                label: 'Site Ayarları',
                to: { name: 'management.settings.site' },
            },
            {
                label: 'Hesap Ayarları',
                to: { name: 'management.settings.account' },
            },
        ],
    },
];

const route = useRoute();
const openGroups = reactive({});

const closeSidebar = () => emit('close');

const isRouteActive = (routeName) => {
    if (!routeName) {
        return false;
    }

    const current = route.name?.toString() ?? '';
    const target = routeName.toString();

    return current === target || current.startsWith(`${target}.`);
};

const isActive = (item) => {
    if (item.children?.length) {
        return item.children.some((child) => isRouteActive(child.to?.name));
    }

    return isRouteActive(item.to?.name);
};

const navigateTo = (navigate) => {
    navigate();
    emit('close');
};

const toggleGroup = (item) => {
    openGroups[item.label] = !openGroups[item.label];
};

const isGroupOpen = (item) => {
    if (typeof openGroups[item.label] === 'undefined') {
        openGroups[item.label] = isActive(item);
    }

    return openGroups[item.label];
};
</script>

<template>
    <div>
        <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="open" class="fixed inset-0 z-30 bg-neutral-900/40 backdrop-blur-sm lg:hidden"
                @click="closeSidebar" />
        </transition>

        <aside
            :class="['fixed inset-y-0 left-0 z-40 w-72 bg-white border-r border-neutral-200 shadow-xl lg:shadow-none lg:translate-x-0 transform transition-transform duration-300 ease-out', open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0']">
            <div class="flex h-full flex-col">
                <div class="flex items-center justify-between px-6 py-5">
                    <div>
                        <p class="text-xs uppercase tracking-[0.28em] text-primary-500 font-semibold">
                            Takva
                        </p>
                        <h2 class="mt-1 text-lg font-semibold text-secondary-900">
                            Yönetim Arayüzü
                        </h2>
                    </div>

                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-200 text-neutral-500 hover:text-neutral-800 hover:border-neutral-300 transition lg:hidden"
                        @click="closeSidebar">
                        <span class="sr-only">Menüyü kapat</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-6">
                    <div class="rounded-2xl bg-primary-50 border border-primary-100 px-4 py-5">
                        <p class="text-sm font-medium text-primary-700">
                            Merhaba, yönetim paneline hoş geldiniz.
                        </p>
                        <p class="mt-1 text-xs text-primary-500 leading-relaxed">
                            Navigasyon menüsünden dilediğiniz bölüme ilerleyebilirsiniz.
                        </p>
                    </div>
                </div>

                <nav class="mt-6 flex-1 space-y-2 px-4 overflow-y-auto">
                    <p class="px-2 text-xs font-semibold uppercase tracking-[0.2em] text-neutral-400">
                        Menü
                    </p>

                    <ul class="mt-2 space-y-1">
                        <li v-for="item in navigationItems" :key="item.label">
                            <template v-if="item.children?.length">
                                <button
                                    type="button"
                                    class="flex w-full items-center justify-between rounded-xl px-3 py-3 text-left transition-all duration-200 ease-out border"
                                    :class="[
                                        isActive(item)
                                            ? 'bg-primary-500 text-white border-primary-500 shadow-lg shadow-primary-500/20'
                                            : 'border-transparent text-neutral-600 hover:border-primary-100 hover:bg-primary-50/70 hover:text-primary-700',
                                    ]"
                                    @click="toggleGroup(item)"
                                >
                                    <span class="flex items-center gap-3">
                                        <span
                                            :class="[
                                                'flex h-9 w-9 items-center justify-center rounded-xl border text-base transition',
                                                isActive(item)
                                                    ? 'border-white/40 bg-white/10 text-white'
                                                    : 'border-neutral-200 bg-white text-neutral-500 group-hover:border-primary-300 group-hover:text-primary-600',
                                            ]"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
                                            </svg>
                                        </span>

                                        <span class="text-sm font-semibold tracking-tight">
                                            {{ item.label }}
                                        </span>
                                    </span>

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition"
                                        :class="isGroupOpen(item) ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                                    </svg>
                                </button>

                                <transition enter-active-class="duration-200 ease-out" enter-from-class="opacity-0 -translate-y-1"
                                    enter-to-class="opacity-100 translate-y-0" leave-active-class="duration-150 ease-in"
                                    leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-1">
                                    <ul v-show="isGroupOpen(item)" class="mt-1 space-y-1 pl-11">
                                        <li v-for="child in item.children" :key="child.label">
                                            <RouterLink :to="child.to" custom v-slot="{ href, navigate }">
                                                <a
                                                    :href="href"
                                                    @click.prevent="navigateTo(navigate)"
                                                    class="flex items-center rounded-xl px-3 py-2 text-sm font-medium transition"
                                                    :class="isRouteActive(child.to?.name)
                                                        ? 'text-primary-600 bg-primary-50/80'
                                                        : 'text-neutral-500 hover:text-primary-600 hover:bg-primary-50/60'"
                                                >
                                                    {{ child.label }}
                                                </a>
                                            </RouterLink>
                                        </li>
                                    </ul>
                                </transition>
                            </template>
                            <template v-else>
                                <RouterLink :to="item.to" custom v-slot="{ href, navigate }">
                                    <a
                                        :href="href"
                                        @click.prevent="navigateTo(navigate)"
                                        :class="[
                                            'group flex items-center justify-between rounded-xl px-3 py-3 transition-all duration-200 ease-out border',
                                            isActive(item)
                                                ? 'bg-primary-500 text-white border-primary-500 shadow-lg shadow-primary-500/20'
                                                : 'border-transparent text-neutral-600 hover:border-primary-100 hover:bg-primary-50/70 hover:text-primary-700',
                                        ]"
                                    >
                                        <span class="flex items-center gap-3">
                                            <span
                                                :class="[
                                                    'flex h-9 w-9 items-center justify-center rounded-xl border text-base transition',
                                                    isActive(item)
                                                        ? 'border-white/40 bg-white/10 text-white'
                                                        : 'border-neutral-200 bg-white text-neutral-500 group-hover:border-primary-300 group-hover:text-primary-600',
                                                ]"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="1.8"
                                                >
                                                    <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
                                                </svg>
                                            </span>

                                            <span class="text-sm font-semibold tracking-tight">
                                                {{ item.label }}
                                            </span>
                                        </span>
                                    </a>
                                </RouterLink>
                            </template>
                        </li>
                    </ul>
                </nav>

                <div class="px-6 py-6 border-t border-neutral-100 bg-neutral-50/60">
                    <div class="rounded-2xl border border-neutral-200 bg-white px-4 py-4 shadow-sm">
                        <p class="text-sm font-semibold text-neutral-700">
                            Yardıma mı ihtiyacınız var?
                        </p>
                        <p class="mt-1 text-xs text-neutral-500">
                            Destek ekibiyle iletişime geçerek hızlıca yardım alabilirsiniz.
                        </p>
                        <a
                            href="https://wa.me/905466024812"
                            target="_blank"
                            rel="noopener"
                            class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-secondary-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-secondary-800"
                        >
                            Destek Talebi
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</template>
