<template>
    <div>
        <button
            type="button"
            class="p-2 rounded-full border border-white/30 text-white hover:text-accent-300 hover:border-accent-300 transition"
            aria-label="Site içinde ara"
            @click="openModal"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
        </button>

        <transition name="fade">
            <div v-if="isOpen" class="fixed inset-0 z-[1200]">
                <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="closeModal"></div>
                <div class="absolute inset-0 overflow-y-auto">
                    <div class="min-h-full flex items-start justify-center p-4 mt-16">
                        <div class="relative w-full max-w-3xl bg-white rounded-3xl shadow-2xl ring-1 ring-black/5">
                            <button type="button" class="absolute -top-3 -right-3 bg-neutral-50 text-neutral-500 hover:text-neutral-700 border border-neutral-200 rounded-full p-2 shadow-sm" @click="closeModal" aria-label="Kapat">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <div class="p-6 pb-8 space-y-6">
                                <div class="flex items-center gap-3 border border-neutral-200 rounded-2xl px-4 py-3 focus-within:ring-2 focus-within:ring-primary-500 focus-within:border-primary-200">
                                    <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                                    </svg>
                                    <input
                                        ref="inputRef"
                                        v-model="query"
                                        type="search"
                                        placeholder="Makaleler, sayılar, yazarlar..."
                                        class="flex-1 text-base text-secondary-900 placeholder-neutral-400 focus:outline-none focus:ring-0 border-0 appearance-none"
                                        @keydown.escape.prevent="closeModal"
                                        @keyup.enter="goToFullResults"
                                    />
                                    <span class="text-xs text-neutral-400 border border-neutral-200 rounded-md px-2 py-0.5">ESC</span>
                                </div>

                                <div v-if="infoMessage" class="text-center text-neutral-500 py-10">
                                    {{ infoMessage }}
                                </div>

                                <div v-else class="space-y-8">
                                    <div v-for="group in visibleGroups" :key="group.key" class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-semibold tracking-wide text-neutral-500 uppercase">{{ group.title }}</h3>
                                            <span class="text-xs text-neutral-400">{{ group.items.length }} sonuç</span>
                                        </div>
                                        <div class="space-y-2">
                                            <a
                                                v-for="item in group.items"
                                                :key="item.id"
                                                :href="item.url"
                                                class="block border border-neutral-100 rounded-2xl p-4 hover:border-primary-200 hover:bg-primary-50/40 transition"
                                            >
                                                <div class="flex items-start gap-3">
                                                    <div v-if="item.image" class="w-14 h-14 rounded-xl overflow-hidden bg-neutral-100 flex-shrink-0">
                                                        <img :src="item.image" :alt="item.title || item.name" class="w-full h-full object-cover" />
                                                    </div>
                                                    <div class="flex-1">
                                                        <p class="text-sm text-primary-500 mb-1" v-if="item.meta">{{ item.meta }}</p>
                                                        <p class="text-lg font-semibold text-secondary-900">
                                                            {{ item.title || item.name }}
                                                        </p>
                                                        <p class="text-sm text-neutral-600" v-if="item.description">
                                                            {{ item.description }}
                                                        </p>
                                                        <p class="text-xs text-neutral-400 mt-1" v-if="item.metaInfo">
                                                            {{ item.metaInfo }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>

                                    <div v-if="totalResults > 0" class="text-right">
                                        <a :href="fullResultsUrl" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 transition">
                                            Tüm sonuçları gör
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, reactive, ref, watch, nextTick } from 'vue';
import axios from 'axios';

const props = defineProps({
    endpoint: {
        type: String,
        required: true,
    },
    pageUrl: {
        type: String,
        required: true,
    },
});

const isOpen = ref(false);
const query = ref('');
const loading = ref(false);
const errorMessage = ref('');
const inputRef = ref(null);
const results = reactive({
    articles: [],
    issues: [],
    authors: [],
    categories: [],
});
const totalResults = ref(0);
let debounceId;

const groups = computed(() => [
    {
        key: 'articles',
        title: 'Makaleler',
        items: results.articles ?? [],
    },
    {
        key: 'issues',
        title: 'Sayılar',
        items: results.issues ?? [],
    },
    {
        key: 'authors',
        title: 'Yazarlar',
        items: results.authors ?? [],
    },
    {
        key: 'categories',
        title: 'Konular',
        items: results.categories ?? [],
    },
]);

const visibleGroups = computed(() =>
    groups.value.filter((group) => Array.isArray(group.items) && group.items.length > 0),
);

const infoMessage = computed(() => {
    if (!query.value) {
        return 'Aramak istediğiniz kelimeyi yazın. Ctrl + K ile hızlıca açabilirsiniz.';
    }

    if (loading.value) {
        return 'Sonuçlar getiriliyor...';
    }

    if (errorMessage.value) {
        return errorMessage.value;
    }

    if (totalResults.value === 0) {
        return 'Herhangi bir sonuç bulunamadı.';
    }

    return '';
});

const fullResultsUrl = computed(() => {
    if (!query.value) {
        return props.pageUrl;
    }

    const connector = props.pageUrl.includes('?') ? '&' : '?';
    return `${props.pageUrl}${connector}q=${encodeURIComponent(query.value)}`;
});

const resetResults = () => {
    results.articles = [];
    results.issues = [];
    results.authors = [];
    results.categories = [];
    totalResults.value = 0;
    errorMessage.value = '';
};

const fetchResults = async (term) => {
    if (!term) {
        resetResults();
        return;
    }

    loading.value = true;
    errorMessage.value = '';

    try {
        const { data } = await axios.get(props.endpoint, {
            params: { q: term },
        });

        results.articles = data.articles || [];
        results.issues = data.issues || [];
        results.authors = data.authors || [];
        results.categories = data.categories || [];
        totalResults.value = data.total || 0;
    } catch (error) {
        errorMessage.value = 'Bir şeyler ters gitti. Lütfen tekrar deneyin.';
        console.error(error);
    } finally {
        loading.value = false;
    }
};

watch(query, (value) => {
    clearTimeout(debounceId);
    if (!value) {
        resetResults();
        return;
    }

    debounceId = setTimeout(() => fetchResults(value), 350);
});

const openModal = () => {
    isOpen.value = true;
    document.body.style.overflow = 'hidden';
    nextTick(() => {
        if (inputRef.value) {
            inputRef.value.focus();
        }
    });
};

const closeModal = () => {
    isOpen.value = false;
    document.body.style.overflow = '';
    query.value = '';
    resetResults();
};

const goToFullResults = () => {
    if (!query.value) {
        return;
    }

    window.location.href = fullResultsUrl.value;
};

const handleGlobalShortcut = (event) => {
    if ((event.key === 'k' && (event.metaKey || event.ctrlKey)) || event.key === '/') {
        event.preventDefault();
        openModal();
    }

    if (event.key === 'Escape' && isOpen.value) {
        event.preventDefault();
        closeModal();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleGlobalShortcut);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleGlobalShortcut);
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

input[type='search'] {
    -webkit-appearance: none;
    appearance: none;
    outline: none;
    box-shadow: none;
}

input[type='search']::-webkit-search-decoration,
input[type='search']::-webkit-search-cancel-button,
input[type='search']::-webkit-search-results-button,
input[type='search']::-webkit-search-results-decoration {
    display: none;
}
</style>
