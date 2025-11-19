<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { RouterLink } from 'vue-router';
import { useArticleStore } from '../../stores/articleStore';
import { useCategoryStore } from '../../stores/categoryStore';
import { useAuthorStore } from '../../stores/authorStore';
import ConfirmModal from '../../components/common/ConfirmModal.vue';

const articleStore = useArticleStore();
const categoryStore = useCategoryStore();
const authorStore = useAuthorStore();

const { articles, meta, loading, error } = storeToRefs(articleStore);

const searchTerm = ref('');
const selectedCategoryFilter = ref(null);
const selectedAuthorFilter = ref(null);
const searchTimeout = ref();

const deleteModalOpen = ref(false);
const articleToDelete = ref(null);
const deleting = ref(false);
const deleteError = ref('');

const hasArticles = computed(() => !loading.value && articles.value.length > 0);

const categories = computed(() => categoryStore.categories ?? []);
const authors = computed(() => authorStore.authors ?? []);

onMounted(async () => {
    await Promise.all([
        articleStore.fetchArticles(),
        categoryStore.fetchCategories({ per_page: 100 }),
        authorStore.fetchAuthors({ per_page: 100 }),
    ]);
});

watch(
    [() => selectedCategoryFilter.value, () => selectedAuthorFilter.value],
    () => {
        articleStore.fetchArticles({
            search: searchTerm.value,
            category_id: selectedCategoryFilter.value,
            author_id: selectedAuthorFilter.value,
        });
    },
);

watch(
    () => searchTerm.value,
    (value) => {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value);
        }

        searchTimeout.value = setTimeout(() => {
            articleStore.fetchArticles({
                search: value,
                category_id: selectedCategoryFilter.value,
                author_id: selectedAuthorFilter.value,
                page: 1,
            });
        }, 350);
    },
);

function requestDelete(article) {
    articleToDelete.value = article;
    deleteModalOpen.value = true;
    deleteError.value = '';
}

function closeDeleteModal() {
    deleteModalOpen.value = false;
    articleToDelete.value = null;
    deleting.value = false;
    deleteError.value = '';
}

async function confirmDelete() {
    if (!articleToDelete.value) {
        return;
    }

    deleting.value = true;
    deleteError.value = '';

    try {
        await articleStore.deleteArticle(articleToDelete.value.id, {
            search: searchTerm.value,
            category_id: selectedCategoryFilter.value,
            author_id: selectedAuthorFilter.value,
        });
        closeDeleteModal();
    } catch (err) {
        deleting.value = false;
        deleteError.value = articleStore.error || 'Makale silinirken bir hata oluştu.';
    }
}

async function goToPage(page) {
    if (page < 1 || page > meta.value.last_page || page === meta.value.current_page) {
        return;
    }

    await articleStore.fetchArticles({
        page,
        search: searchTerm.value,
        category_id: selectedCategoryFilter.value,
        author_id: selectedAuthorFilter.value,
    });
}

const formatDate = (value) => {
    if (!value) {
        return '-';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return '-';
    }

    return date.toLocaleDateString('tr-TR');
};
</script>

<template>
    <section class="space-y-6">
        <header
            class="flex flex-col gap-4 rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold text-secondary-900">
                    Makaleler
                </h1>
                <p class="mt-2 text-sm text-neutral-500">
                    Yayınlanan makaleleri yönetin, içeriklerini düzenleyin ve kategorilere/yazarlara göre filtreleyin.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                        </svg>
                    </span>
                    <input
                        v-model="searchTerm"
                        type="search"
                        class="w-full rounded-xl border border-neutral-200 bg-white py-3 pl-10 pr-4 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200 sm:w-64"
                        placeholder="Makale ara..."
                    />
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <select
                        v-model="selectedCategoryFilter"
                        class="rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                        <option :value="null">Tüm Kategoriler</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                    <select
                        v-model="selectedAuthorFilter"
                        class="rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                        <option :value="null">Tüm Yazarlar</option>
                        <option v-for="author in authors" :key="author.id" :value="author.id">
                            {{ author.name }}
                        </option>
                    </select>
                </div>

                <RouterLink
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600"
                    :to="{ name: 'management.articles.create' }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Yeni Makale
                </RouterLink>
            </div>
        </header>

        <div class="rounded-3xl border border-neutral-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-100">
                        <thead class="bg-neutral-50/80">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Başlık
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Kategori
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Yazar
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Yayın
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            <tr v-if="loading">
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-neutral-500">
                                    Makaleler yükleniyor...
                                </td>
                            </tr>
                            <tr v-else-if="!hasArticles">
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-neutral-500">
                                    Henüz makale eklenmemiş. İlk makalenizi oluşturmak için sağ üstteki butonu kullanın.
                                </td>
                            </tr>
                            <tr v-else v-for="article in articles" :key="article.id"
                                class="hover:bg-neutral-50/60 transition">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-12 w-12 overflow-hidden rounded-xl border border-neutral-200 bg-neutral-100">
                                            <img
                                                v-if="article.feature_image?.url"
                                                :src="article.feature_image.url"
                                                :alt="article.feature_image.original_name"
                                                class="h-full w-full object-cover"
                                            />
                                            <div v-else class="flex h-full w-full items-center justify-center text-neutral-300 text-xs">
                                                Görsel yok
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-secondary-900">
                                                {{ article.title }}
                                            </p>
                                            <p class="text-xs text-neutral-400">
                                                #{{ article.id }} · {{ article.slug }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-5 text-sm text-neutral-500">
                                    {{ article.category?.name || '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-5 text-sm text-neutral-500">
                                    {{ article.author?.name || '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-5 text-sm">
                                    <span
                                        :class="[
                                            'inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold',
                                            article.is_published
                                                ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                                : 'bg-neutral-100 text-neutral-500 border border-neutral-200',
                                        ]"
                                    >
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        {{ article.is_published ? 'Yayında' : 'Taslak' }}
                                    </span>
                                    <div class="text-xs text-neutral-400">
                                        {{ formatDate(article.published_at || article.created_at) }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <RouterLink
                                            class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-3 py-2 text-xs font-semibold text-neutral-600 transition hover:border-primary-200 hover:text-primary-600"
                                            :to="{ name: 'management.articles.edit', params: { article: article.id } }"
                                        >
                                            Düzenle
                                        </RouterLink>
                                        <button
                                            type="button"
                                            class="inline-flex items-center justify-center rounded-xl border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"
                                            @click="requestDelete(article)"
                                        >
                                            Sil
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <footer
                    class="flex flex-col gap-4 border-t border-neutral-100 px-6 py-4 text-sm text-neutral-500 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        Toplam {{ meta.total }} kayıt · Sayfa {{ meta.current_page }} / {{ meta.last_page }}
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-3 py-2 text-xs font-semibold text-neutral-600 transition hover:border-primary-200 hover:text-primary-600 disabled:opacity-40"
                            :disabled="meta.current_page <= 1"
                            @click="goToPage(meta.current_page - 1)"
                        >
                            Önceki
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-3 py-2 text-xs font-semibold text-neutral-600 transition hover:border-primary-200 hover:text-primary-600 disabled:opacity-40"
                            :disabled="meta.current_page >= meta.last_page"
                            @click="goToPage(meta.current_page + 1)"
                        >
                            Sonraki
                        </button>
                    </div>
                </footer>
        </div>

        <p v-if="error" class="text-sm text-red-600">
            {{ error }}
        </p>

        <ConfirmModal
            :open="deleteModalOpen"
            title="Makaleyi Sil"
            :message="articleToDelete
                ? `<strong>${articleToDelete.title}</strong> adlı makaleyi silmek üzeresiniz.<br>Bu işlem geri alınamaz.` + (deleteError ? `<br><span class='mt-3 inline-block rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-600'>${deleteError}</span>` : '')
                : ''"
            confirm-label="Evet, Sil"
            cancel-label="Vazgeç"
            kind="danger"
            :processing="deleting"
            @cancel="closeDeleteModal"
            @confirm="confirmDelete"
        />
    </section>
</template>
