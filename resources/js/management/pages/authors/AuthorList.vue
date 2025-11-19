<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useAuthorStore } from '../../stores/authorStore';
import AuthorForm from '../../components/authors/AuthorForm.vue';
import ConfirmModal from '../../components/common/ConfirmModal.vue';

const authorStore = useAuthorStore();
const { authors, meta, loading, error } = storeToRefs(authorStore);

const showForm = ref(false);
const formMode = ref('create');
const selectedAuthor = ref(null);
const searchTerm = ref('');
const searchTimeout = ref();

const deleteModalOpen = ref(false);
const authorToDelete = ref(null);
const deleting = ref(false);
const deleteError = ref('');

const hasAuthors = computed(() => !loading.value && authors.value.length > 0);

const formFetchParams = computed(() => ({
    search: searchTerm.value,
    per_page: meta.value.per_page,
    page: formMode.value === 'create' ? 1 : meta.value.current_page,
}));

onMounted(async () => {
    await authorStore.fetchAuthors();
});

watch(
    () => searchTerm.value,
    (value) => {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value);
        }

        searchTimeout.value = setTimeout(() => {
            authorStore.fetchAuthors({
                search: value,
                page: 1,
            });
        }, 350);
    },
);

function openCreateForm() {
    selectedAuthor.value = null;
    formMode.value = 'create';
    showForm.value = true;
}

function openEditForm(author) {
    selectedAuthor.value = author;
    formMode.value = 'edit';
    showForm.value = true;
}

function closeForm() {
    showForm.value = false;
    selectedAuthor.value = null;
}

async function handleSaved() {
    closeForm();
}

function requestDelete(author) {
    authorToDelete.value = author;
    deleteModalOpen.value = true;
    deleteError.value = '';
}

function closeDeleteModal() {
    deleteModalOpen.value = false;
    authorToDelete.value = null;
    deleting.value = false;
    deleteError.value = '';
}

async function confirmDelete() {
    if (!authorToDelete.value) {
        return;
    }

    deleting.value = true;
    deleteError.value = '';

    try {
        await authorStore.deleteAuthor(authorToDelete.value.id, {
            search: searchTerm.value,
            per_page: meta.value.per_page,
        });
        closeDeleteModal();
    } catch (err) {
        deleting.value = false;
        deleteError.value = authorStore.error || 'Yazar silinirken bir hata oluştu.';
    }
}

async function goToPage(page) {
    if (page < 1 || page > meta.value.last_page || page === meta.value.current_page) {
        return;
    }

    await authorStore.fetchAuthors({
        page,
        search: searchTerm.value,
    });
}

const avatarInitial = (author) => author.name?.charAt(0)?.toUpperCase() ?? '?';

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
                    Yazarlar
                </h1>
                <p class="mt-2 text-sm text-neutral-500">
                    Takva Dergisi için içerik üreten yazarları yönetin, profil görsellerini düzenleyin.
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
                        placeholder="Yazar ara..."
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
                    Yeni Yazar
                </button>
            </div>
        </header>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <div class="rounded-3xl border border-neutral-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-100">
                        <thead class="bg-neutral-50/80">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Yazar
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Oluşturma
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Güncelleme
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    İşlemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            <tr v-if="loading">
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-neutral-500">
                                    Yazarlar yükleniyor...
                                </td>
                            </tr>
                            <tr v-else-if="!hasAuthors">
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-neutral-500">
                                    Henüz yazar eklenmemiş. İlk yazarınızı oluşturmak için sağ üstteki butonu kullanın.
                                </td>
                            </tr>
                            <tr v-else v-for="author in authors" :key="author.id"
                                class="hover:bg-neutral-50/60 transition">
                                <td class="whitespace-nowrap px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-xl border border-neutral-200 bg-neutral-100 text-lg font-semibold text-neutral-600">
                                            <img
                                                v-if="author.profile_image?.url"
                                                :src="author.profile_image.url"
                                                :alt="author.profile_image.original_name"
                                                class="h-full w-full object-cover"
                                            />
                                            <span v-else>
                                                {{ avatarInitial(author) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-secondary-900">
                                                {{ author.name }}
                                            </p>
                                            <p class="text-xs text-neutral-400">
                                                #{{ author.id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-5 text-sm text-neutral-500">
                                    {{ formatDate(author.created_at) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-5 text-sm text-neutral-500">
                                    {{ formatDate(author.updated_at) }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            type="button"
                                            class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-3 py-2 text-xs font-semibold text-neutral-600 transition hover:border-primary-200 hover:text-primary-600"
                                            @click="openEditForm(author)"
                                        >
                                            Düzenle
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex items-center justify-center rounded-xl border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"
                                            @click="requestDelete(author)"
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

            <AuthorForm
                v-if="showForm"
                :author="selectedAuthor"
                :mode="formMode"
                :fetch-params="formFetchParams"
                @saved="handleSaved"
                @cancel="closeForm"
            />
            <div
                v-else
                class="hidden rounded-3xl border border-dashed border-neutral-200 bg-white p-6 text-sm text-neutral-400 shadow-sm lg:flex lg:flex-col lg:items-center lg:justify-center"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-neutral-200" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0ZM12 14a7 7 0 0 0-7 7h14a7 7 0 0 0-7-7z" />
                </svg>
                <p class="mt-4 text-center">
                    Yeni yazarları eklemek veya mevcut yazarları düzenlemek için sağ üstteki butonu kullanın.
                </p>
            </div>
        </div>

        <p v-if="error" class="text-sm text-red-600">
            {{ error }}
        </p>

        <ConfirmModal
            :open="deleteModalOpen"
            title="Yazarı Sil"
            :message="authorToDelete
                ? `<strong>${authorToDelete.name}</strong> adlı yazarı silmek üzeresiniz.<br>Bu işlem geri alınamaz.` + (deleteError ? `<br><span class='mt-3 inline-block rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-600'>${deleteError}</span>` : '')
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
