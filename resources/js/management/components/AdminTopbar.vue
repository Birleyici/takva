<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const props = defineProps({
    pageTitle: {
        type: String,
        default: 'Kontrol Paneli',
    },
    breadcrumb: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['toggle-sidebar']);

const triggerSidebar = () => emit('toggle-sidebar');

const managementData = window.__MANAGEMENT_DATA__ ?? {};
const router = useRouter();

const userEmail = managementData.user?.email ?? 'info@takvadergisi.org';
const displayName = 'Takva Dergisi';
const initials = computed(() =>
    displayName
        .split(' ')
        .map((part) => part.charAt(0))
        .join('')
        .slice(0, 2)
        .toUpperCase()
);

const settingsMenuOpen = ref(false);
const dropdownRef = ref(null);
const searchWrapperRef = ref(null);
const searchQuery = ref('');
const searchFocused = ref(false);
const searchActiveIndex = ref(0);
const searchResults = ref([]);
let searchBlurTimeout = null;

const toggleSettingsMenu = () => {
    settingsMenuOpen.value = !settingsMenuOpen.value;
};

const closeSettingsMenu = () => {
    settingsMenuOpen.value = false;
};

const goToAccountSettings = () => {
    closeSettingsMenu();
    router.push({ name: 'management.settings.account' });
};

const handleLogout = async () => {
    closeSettingsMenu();
    try {
        await axios.post(managementData.routes?.logout ?? '/logout');
    } catch (error) {
        // fallback to hard reload
    } finally {
        window.location.href = '/';
    }
};

const searchableRoutes = computed(() =>
    router
        .getRoutes()
        .filter((route) => route.name && route.name.toString().startsWith('management.') && route.meta?.title)
        .filter((route) => !route.name?.toString().endsWith('.edit'))
        .map((route) => ({
            name: route.name?.toString(),
            title: route.meta?.title,
            breadcrumb: route.meta?.breadcrumb ?? route.meta?.title,
        }))
        .sort((a, b) => a.title.localeCompare(b.title, 'tr'))
);

const minQueryLength = 2;

watch(searchQuery, () => {
    searchActiveIndex.value = 0;
    const query = searchQuery.value.trim().toLowerCase();
    if (query.length < minQueryLength) {
        searchResults.value = [];
        return;
    }
    searchResults.value = searchableRoutes.value
        .filter(
            (route) =>
                route.title.toLowerCase().includes(query) ||
                route.breadcrumb?.toLowerCase().includes(query) ||
                route.name?.toLowerCase().includes(query)
        )
        .slice(0, 8);
});

const showSearchDropdown = computed(() => {
    const queryReady = searchQuery.value.trim().length >= minQueryLength;
    return searchFocused.value && queryReady;
});

const navigateToRoute = (routeItem) => {
    if (!routeItem?.name) {
        return;
    }
    router.push({ name: routeItem.name });
    searchQuery.value = '';
    searchFocused.value = false;
    searchResults.value = [];
};

const handleSearchKeydown = (event) => {
    if (!showSearchDropdown.value) {
        return;
    }

    const results = searchResults.value;
    if (!results.length) {
        return;
    }
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        searchActiveIndex.value = (searchActiveIndex.value + 1) % results.length;
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        searchActiveIndex.value =
            (searchActiveIndex.value - 1 + results.length) % results.length;
    } else if (event.key === 'Enter') {
        event.preventDefault();
        navigateToRoute(results[searchActiveIndex.value]);
    } else if (event.key === 'Escape') {
        searchFocused.value = false;
    }
};

const handleSearchFocus = () => {
    searchFocused.value = true;
    if (searchBlurTimeout) {
        clearTimeout(searchBlurTimeout);
    }
};

const handleSearchBlur = () => {
    searchBlurTimeout = setTimeout(() => {
        searchFocused.value = false;
    }, 120);
};

const handleClickOutside = (event) => {
    if (settingsMenuOpen.value && dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        settingsMenuOpen.value = false;
    }

    if (
        searchFocused.value &&
        searchWrapperRef.value &&
        !searchWrapperRef.value.contains(event.target)
    ) {
        searchFocused.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
    if (searchBlurTimeout) {
        clearTimeout(searchBlurTimeout);
    }
});
</script>

<template>
    <header class="sticky top-0 z-30 bg-neutral-50/80 backdrop-blur-xl border-b border-neutral-200/70">
        <div class="px-4 py-4 sm:px-6 lg:px-10">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <button type="button"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-neutral-200 bg-white text-neutral-600 transition hover:border-primary-300 hover:text-primary-600 lg:hidden"
                        @click="triggerSidebar">
                        <span class="sr-only">Menüyü aç</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10" />
                        </svg>
                    </button>

                    <div class="hidden lg:flex flex-col">
                        <span class="text-xs font-medium uppercase tracking-[0.28em] text-neutral-400">
                            Yönetim
                        </span>
                        <span class="text-lg font-semibold text-secondary-900">
                            {{ props.pageTitle }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-1 items-center justify-end gap-4">
                    <div
                        ref="searchWrapperRef"
                        class="relative hidden md:flex items-center rounded-2xl border border-neutral-200 bg-white px-4 py-2.5 shadow-sm w-full max-w-md focus-within:border-primary-300 focus-within:ring-2 focus-within:ring-primary-200/70"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                        </svg>
                        <input
                            type="search"
                            class="ml-3 flex-1 border-none bg-transparent text-sm placeholder-neutral-400 focus:ring-0"
                            placeholder="Panel içinde ara"
                            v-model="searchQuery"
                            @focus="handleSearchFocus"
                            @blur="handleSearchBlur"
                            @keydown="handleSearchKeydown"
                        />

                        <transition enter-active-class="transition ease-out duration-150" enter-from-class="opacity-0 translate-y-1"
                            enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-100"
                            leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
                            <div
                                v-if="showSearchDropdown"
                                class="absolute left-0 right-0 top-full mt-2 rounded-2xl border border-neutral-200 bg-white shadow-lg shadow-neutral-900/5 overflow-hidden"
                            >
                                <template v-if="searchResults.length">
                                    <ul class="divide-y divide-neutral-100">
                                        <li
                                            v-for="(routeItem, index) in searchResults"
                                            :key="routeItem.name"
                                        >
                                            <button
                                                type="button"
                                                class="flex w-full items-start gap-3 px-4 py-3 text-left text-sm transition"
                                                :class="index === searchActiveIndex ? 'bg-primary-50 text-primary-700' : 'text-neutral-600 hover:bg-neutral-50'"
                                                @mousedown.prevent="navigateToRoute(routeItem)"
                                            >
                                                <div class="flex flex-col">
                                                    <span class="font-semibold">{{ routeItem.title }}</span>
                                                    <span class="text-xs text-neutral-400">
                                                        {{ routeItem.breadcrumb }}
                                                    </span>
                                                </div>
                                            </button>
                                        </li>
                                    </ul>
                                </template>
                                <div v-else class="px-4 py-3 text-sm text-neutral-500">
                                    Arama yapacak içerik bulunamadı
                                </div>
                            </div>
                        </transition>
                    </div>

                    <div class="relative" ref="dropdownRef">
                        <div class="flex items-center gap-3 rounded-2xl border border-neutral-200 bg-white px-3 py-2 shadow-sm">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-100 text-primary-700 font-semibold">
                                {{ initials }}
                            </div>
                            <div class="hidden sm:flex flex-col">
                                <span class="text-sm font-semibold text-secondary-900">{{ displayName }}</span>
                                <span class="text-xs text-neutral-400">{{ userEmail }}</span>
                            </div>
                            <button type="button"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-neutral-200 text-neutral-400 transition hover:text-primary-600 hover:border-primary-200"
                                @click.stop="toggleSettingsMenu">
                                <span class="sr-only">Ayarlar</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.313.83 2.373 2.373a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.83 3.313-2.373 2.373a1.724 1.724 0 0 0-2.572 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.065c-1.543.94-3.313-.83-2.373-2.373a1.724 1.724 0 0 0-1.066-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.83-3.313 2.373-2.373.996.607 2.296.07 2.572-1.066z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                </svg>
                            </button>
                        </div>
                        <transition enter-active-class="transition ease-out duration-150" enter-from-class="opacity-0 translate-y-1"
                            enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-100"
                            leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
                            <div v-if="settingsMenuOpen"
                                class="absolute right-0 mt-3 w-56 rounded-2xl border border-neutral-200 bg-white shadow-xl shadow-neutral-900/5 focus:outline-none">
                                <div class="py-2">
                                    <button type="button"
                                        class="flex w-full items-center gap-2 px-4 py-2 text-sm font-medium text-neutral-700 hover:bg-primary-50 hover:text-primary-700"
                                        @click="goToAccountSettings">
                                        Hesap Ayarları
                                    </button>
                                    <button type="button"
                                        class="flex w-full items-center gap-2 px-4 py-2 text-sm font-medium text-rose-600 hover:bg-rose-50"
                                        @click="handleLogout">
                                        Çıkış Yap
                                    </button>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
            </div>
            <div class="mt-3 lg:hidden">
                <p class="text-sm font-semibold text-secondary-900">
                    {{ props.pageTitle }}
                </p>
                <p v-if="props.breadcrumb" class="text-xs text-neutral-400">
                    {{ props.breadcrumb }}
                </p>
            </div>
        </div>
    </header>
</template>
