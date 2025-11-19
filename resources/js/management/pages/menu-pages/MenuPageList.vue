<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { RouterLink } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useMenuPageStore } from '../../stores/menuPageStore';
import ConfirmModal from '../../components/common/ConfirmModal.vue';

const menuPageStore = useMenuPageStore();
const { pages, meta, loading, error } = storeToRefs(menuPageStore);

const searchTerm = ref('');
const selectedStatus = ref('');
const searchTimeout = ref(null);

const deleteModalOpen = ref(false);
const pageToDelete = ref(null);
const deleting = ref(false);
const deleteError = ref('');

const hasPages = computed(() => !loading.value && pages.value.length > 0);

onMounted(async () => {
    await menuPageStore.fetchPages();
});

watch(
    () => selectedStatus.value,
    () => {
        menuPageStore.fetchPages({
            search: searchTerm.value,
            is_active: selectedStatus.value === '' ? undefined : selectedStatus.value === '1',
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
            menuPageStore.fetchPages({
                search: value,
                is_active: selectedStatus.value === '' ? undefined : selectedStatus.value === '1',
                page: 1,
            });
        }, 300);
    },
);

function requestDelete(page) {
    pageToDelete.value = page;
    deleteModalOpen.value = true;
    deleteError.value = '';
}

function closeDeleteModal() {
    deleteModalOpen.value = false;
    pageToDelete.value = null;
    deleting.value = false;
    deleteError.value = '';
}

async function confirmDelete() {
    if (!pageToDelete.value) {
        return;
    }

    deleting.value = true;
    deleteError.value = '';

    try {
        await menuPageStore.deletePage(pageToDelete.value.id, {
            search: searchTerm.value,
            is_active: selectedStatus.value === '' ? undefined : selectedStatus.value === '1',
        });
        closeDeleteModal();
    } catch (err) {
        deleting.value = false;
        deleteError.value = menuPageStore.error || 'Sayfa silinirken bir hata oluştu.';
    }
}

async function goToPage(page) {
    if (page < 1 || page > meta.value.last_page || page === meta.value.current_page) {
        return;
    }

    await menuPageStore.fetchPages({
        page,
        search: searchTerm.value,
        is_active: selectedStatus.value === '' ? undefined : selectedStatus.value === '1',
    });
}
</script>

<template>
    <section class="space-y-6">
        <header class="flex flex-col gap-4 rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-secondary-900">Menü Sayfaları</h1>
                <p class="mt-2 text-sm text-neutral-500">
                    Statik içerikli özel menü sayfalarını oluşturun ve yönetin.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                        </svg>
                    </span>
                    <input
                        v-model="searchTerm"
                        type="search"
                        class="w-full rounded-xl border border-neutral-200 bg-white py-3 pl-10 pr-4 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200 sm:w-64"
                        placeholder="Sayfa ara..."
                    />
                </div>

                <select
                    v-model="selectedStatus"
                    class="rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                >
                    <option value="">Tüm Durumlar</option>
                    <option value="1">Aktif</option>
                    <option value="0">Pasif</option>
                </select>

                <RouterLink
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600"
                    :to="{ name: 'management.menu-pages.create' }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Yeni Sayfa
                </RouterLink>
            </div>
        </header>

        <div class="rounded-3xl border border-neutral-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-100">
                    <thead class="bg-neutral-50/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">Başlık</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">Slug</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">Durum</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">Sıra</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        <tr v-if="loading">
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-neutral-500">
                                Sayfalar yükleniyor...
                            </td>
                        </tr>
                        <tr v-else-if="!hasPages">
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-neutral-500">
                                Henüz sayfa eklenmemiş.
                            </td>
                        </tr>
                        <tr v-else v-for="page in pages" :key="page.id" class="hover:bg-neutral-50/60">
                            <td class="px-6 py-5 text-sm text-secondary-900">
                                <div class="font-semibold">{{ page.title }}</div>
                                <p class="text-xs text-neutral-500 line-clamp-1">{{ page.summary }}</p>
                            </td>
                            <td class="px-6 py-5 text-sm text-secondary-700">
                                /tr/menu/{{ page.slug }}
                            </td>
                            <td class="px-6 py-5 text-sm">
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold',
                                        page.is_active
                                            ? 'bg-emerald-50 text-emerald-700'
                                            : 'bg-neutral-100 text-neutral-500',
                                    ]"
                                >
                                    {{ page.is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-sm text-secondary-700">
                                {{ page.position }}
                            </td>
                            <td class="px-6 py-5 text-right text-sm">
                                <div class="inline-flex gap-3">
                                    <RouterLink
                                        :to="{ name: 'management.menu-pages.edit', params: { page: page.id } }"
                                        class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-800"
                                    >
                                        Düzenle
                                    </RouterLink>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 text-rose-500 hover:text-rose-700"
                                        @click="requestDelete(page)"
                                    >
                                        Sil
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <footer v-if="meta.last_page > 1" class="flex items-center justify-between border-t border-neutral-100 px-6 py-4 text-sm">
                <p class="text-neutral-500">
                    Toplam {{ meta.total }} kayıt içinden sayfa {{ meta.current_page }}
                </p>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="rounded-xl border border-neutral-200 px-3 py-1 text-sm text-neutral-600 hover:border-neutral-300"
                        :disabled="meta.current_page === 1"
                        @click="goToPage(meta.current_page - 1)"
                    >Önceki</button>
                    <button
                        type="button"
                        class="rounded-xl border border-neutral-200 px-3 py-1 text-sm text-neutral-600 hover:border-neutral-300"
                        :disabled="meta.current_page === meta.last_page"
                        @click="goToPage(meta.current_page + 1)"
                    >Sonraki</button>
                </div>
            </footer>
        </div>

        <ConfirmModal
            :open="deleteModalOpen"
            :loading="deleting"
            confirm-text="Sil"
            confirm-variant="danger"
            @close="closeDeleteModal"
            @confirm="confirmDelete"
        >
            <template #title>
                Sayfayı Sil
            </template>
            <template #description>
                <p>"{{ pageToDelete?.title }}" adlı sayfayı silmek istediğinizden emin misiniz?</p>
                <p class="mt-2 text-xs text-neutral-400" v-if="deleteError">
                    {{ deleteError }}
                </p>
            </template>
        </ConfirmModal>
    </section>
</template>
