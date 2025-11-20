<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import CategoryForm from '../../components/categories/CategoryForm.vue';
import ConfirmModal from '../../components/common/ConfirmModal.vue';
import { useCategoryStore } from '../../stores/categoryStore';

const categoryStore = useCategoryStore();
const { categories, meta, loading, error } = storeToRefs(categoryStore);

const showForm = ref(false);
const formMode = ref('create');
const selectedCategory = ref(null);
const searchTerm = ref('');
const searchTimeout = ref();
const deleteModalOpen = ref(false);
const categoryToDelete = ref(null);
const deleting = ref(false);
const deleteError = ref('');

const hasCategories = computed(() => !loading.value && categories.value.length > 0);

const formFetchParams = computed(() => ({
    search: searchTerm.value,
    per_page: meta.value.per_page,
    page: formMode.value === 'create' ? 1 : meta.value.current_page,
}));

onMounted(async () => {
    await categoryStore.fetchCategories();
});

watch(
    () => searchTerm.value,
    (value) => {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value);
        }

        searchTimeout.value = setTimeout(() => {
            categoryStore.fetchCategories({
                search: value,
                page: 1,
            });
        }, 350);
    },
);

function openCreateForm() {
    selectedCategory.value = null;
    formMode.value = 'create';
    showForm.value = true;
}

function openEditForm(category) {
    selectedCategory.value = category;
    formMode.value = 'edit';
    showForm.value = true;
}

function closeForm() {
    showForm.value = false;
    selectedCategory.value = null;
}

async function handleSaved() {
    closeForm();
}

function requestDelete(category) {
    categoryToDelete.value = category;
    deleteModalOpen.value = true;
}

function closeDeleteModal() {
    deleteModalOpen.value = false;
    categoryToDelete.value = null;
    deleting.value = false;
    deleteError.value = '';
}

async function confirmDelete() {
    if (!categoryToDelete.value) {
        return;
    }

    deleting.value = true;
    deleteError.value = '';

    try {
        await categoryStore.deleteCategory(categoryToDelete.value.id, {
            search: searchTerm.value,
            per_page: meta.value.per_page,
        });
        closeDeleteModal();
    } catch (err) {
        deleting.value = false;
        deleteError.value = categoryStore.error || 'Kategori silinirken bir hata oluştu.';
    }
}

async function goToPage(page) {
    if (page < 1 || page > meta.value.last_page || page === meta.value.current_page) {
        return;
    }

    await categoryStore.fetchCategories({
        page,
        search: searchTerm.value,
    });
}

const statusBadgeClass = (isActive) =>
    isActive
        ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
        : 'bg-rose-50 text-rose-600 border border-rose-100';

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
                    Kategoriler
                </h1>
                <p class="mt-2 text-sm text-neutral-500">
                    İçeriklerinizi gruplayın, düzenleyin ve yayın akışınızı güçlendirin.
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
                        placeholder="Kategori ara..."
                    />
                </div>

                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600"
                    @click="openCreateForm"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Yeni Kategori
                </button>
            </div>
        </header>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <div class="min-w-0 rounded-3xl border border-neutral-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-100">
                        <thead class="bg-neutral-50/80">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Adı
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Slug
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Durum
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Oluşturma
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
                                    Kategoriler yükleniyor...
                                </td>
                            </tr>
                            <tr v-else-if="!hasCategories">
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-neutral-500">
                                    Henüz kategori eklenmemiş. İlk kategorinizi oluşturmak için sağ üstteki butonu kullanın.
                                </td>
                            </tr>
                            <tr v-else v-for="category in categories" :key="category.id"
                                class="hover:bg-neutral-50/60 transition">
                                <td class="whitespace-nowrap px-6 py-5">
                                    <div class="font-semibold text-secondary-900">
                                        {{ category.name }}
                                    </div>
                                    <p class="mt-1 text-sm text-neutral-500 line-clamp-2">
                                        {{ category.description || 'Açıklama eklenmemiş' }}
                                    </p>
                                </td>
                                <td class="whitespace-nowrap px-6 py-5 text-sm text-neutral-500">
                                    {{ category.slug }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-5">
                                    <span
                                        :class="['inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold', statusBadgeClass(category.is_active)]">
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                        {{ category.is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-5 text-sm text-neutral-500">
                                    {{ formatDate(category.created_at) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            type="button"
                                            class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-3 py-2 text-xs font-semibold text-neutral-600 transition hover:border-primary-200 hover:text-primary-600"
                                            @click="openEditForm(category)"
                                        >
                                            Düzenle
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex items-center justify-center rounded-xl border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"
                                            @click="requestDelete(category)"
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

            <CategoryForm
                v-if="showForm"
                class="min-w-0"
                :category="selectedCategory"
                :mode="formMode"
                :fetch-params="formFetchParams"
                @saved="handleSaved"
                @cancel="closeForm"
            />
            <div
                v-else
                class="hidden min-w-0 rounded-3xl border border-dashed border-neutral-200 bg-white p-6 text-sm text-neutral-400 shadow-sm lg:flex lg:flex-col lg:items-center lg:justify-center"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-neutral-200" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 4v16m8-8H4m16 0a8 8 0 1 0-16 0 8 8 0 0 0 16 0z" />
                </svg>
                <p class="mt-4 text-center">
                    Yeni bir kategori oluşturmak veya mevcut kategorileri düzenlemek için sağ üstteki butonu
                    kullanın.
                </p>
            </div>
        </div>

        <p v-if="error" class="text-sm text-red-600">
            {{ error }}
        </p>

        <ConfirmModal
            :open="deleteModalOpen"
            title="Kategoriyi Sil"
            :message="categoryToDelete
                ? `<strong>${categoryToDelete.name}</strong> kategorisini silmek üzeresiniz.<br>Bu işlem geri alınamaz.` + (deleteError ? `<br><span class='mt-3 inline-block rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-600'>${deleteError}</span>` : '')
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
