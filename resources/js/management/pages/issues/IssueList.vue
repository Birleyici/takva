<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { RouterLink } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useIssueStore } from '../../stores/issueStore';
import ConfirmModal from '../../components/common/ConfirmModal.vue';

const issueStore = useIssueStore();
const { issues, meta, loading, error } = storeToRefs(issueStore);

const searchTerm = ref('');
const selectedYear = ref('');
const selectedMonth = ref('');
const searchTimeout = ref(null);

const deleteModalOpen = ref(false);
const issueToDelete = ref(null);
const deleting = ref(false);
const deleteError = ref('');

const hasIssues = computed(() => !loading.value && issues.value.length > 0);

onMounted(async () => {
    await issueStore.fetchIssues();
});

watch([
    () => selectedYear.value,
    () => selectedMonth.value,
], () => {
    issueStore.fetchIssues({
        search: searchTerm.value,
        year: selectedYear.value,
        month: selectedMonth.value,
    });
});

watch(
    () => searchTerm.value,
    (value) => {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value);
        }

        searchTimeout.value = setTimeout(() => {
            issueStore.fetchIssues({
                search: value,
                year: selectedYear.value,
                month: selectedMonth.value,
                page: 1,
            });
        }, 350);
    },
);

function requestDelete(issue) {
    issueToDelete.value = issue;
    deleteModalOpen.value = true;
    deleteError.value = '';
}

function closeDeleteModal() {
    deleteModalOpen.value = false;
    issueToDelete.value = null;
    deleting.value = false;
    deleteError.value = '';
}

async function confirmDelete() {
    if (!issueToDelete.value) {
        return;
    }

    deleting.value = true;
    deleteError.value = '';

    try {
        await issueStore.deleteIssue(issueToDelete.value.id, {
            search: searchTerm.value,
            year: selectedYear.value,
            month: selectedMonth.value,
        });
        closeDeleteModal();
    } catch (err) {
        deleting.value = false;
        deleteError.value = issueStore.error || 'Sayı silinirken bir hata oluştu.';
    }
}

async function goToPage(page) {
    if (page < 1 || page > meta.value.last_page || page === meta.value.current_page) {
        return;
    }

    await issueStore.fetchIssues({
        page,
        search: searchTerm.value,
        year: selectedYear.value,
        month: selectedMonth.value,
    });
}

const monthOptions = [
    { value: '', label: 'Tüm Aylar' },
    { value: 1, label: 'Ocak' },
    { value: 2, label: 'Şubat' },
    { value: 3, label: 'Mart' },
    { value: 4, label: 'Nisan' },
    { value: 5, label: 'Mayıs' },
    { value: 6, label: 'Haziran' },
    { value: 7, label: 'Temmuz' },
    { value: 8, label: 'Ağustos' },
    { value: 9, label: 'Eylül' },
    { value: 10, label: 'Ekim' },
    { value: 11, label: 'Kasım' },
    { value: 12, label: 'Aralık' },
];
</script>

<template>
    <section class="space-y-6">
        <header
            class="flex flex-col gap-4 rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold text-secondary-900">
                    Dergi Sayıları
                </h1>
                <p class="mt-2 text-sm text-neutral-500">
                    Yayınlanan sayıları yönetin, PDF dosyalarını güncelleyin ve arşivi düzenli tutun.
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
                        placeholder="Sayı ara..."
                    />
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <input
                        v-model="selectedYear"
                        type="number"
                        min="1900"
                        max="2100"
                        placeholder="Yıl"
                        class="rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    />
                    <select
                        v-model="selectedMonth"
                        class="rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                        <option v-for="option in monthOptions" :key="option.value" :value="option.value">
                            {{ option.label }}
                        </option>
                    </select>
                </div>

                <RouterLink
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600"
                    :to="{ name: 'management.issues.create' }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Yeni Sayı
                </RouterLink>
            </div>
        </header>

        <div class="rounded-3xl border border-neutral-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-100">
                    <thead class="bg-neutral-50/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">Başlık</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">Yıl</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">Ay</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">PDF</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        <tr v-if="loading">
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-neutral-500">
                                Sayılar yükleniyor...
                            </td>
                        </tr>
                        <tr v-else-if="!hasIssues">
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-neutral-500">
                                Henüz sayi eklenmemiş.
                            </td>
                        </tr>
                        <tr v-else v-for="issue in issues" :key="issue.id" class="hover:bg-neutral-50/60">
                            <td class="px-6 py-5 text-sm text-secondary-900">
                                <div class="font-semibold">{{ issue.title }}</div>
                                <p class="text-xs text-neutral-500">{{ issue.slug }}</p>
                            </td>
                            <td class="px-6 py-5 text-sm text-secondary-700">{{ issue.year }}</td>
                            <td class="px-6 py-5 text-sm text-secondary-700">{{ issue.month_name }}</td>
                            <td class="px-6 py-5 text-sm">
                                <a
                                    v-if="issue.pdf_url"
                                    :href="issue.pdf_url"
                                    target="_blank"
                                    rel="noopener"
                                    class="inline-flex items-center gap-1 rounded-full border border-primary-100 bg-primary-50/60 px-3 py-1 text-xs font-semibold text-primary-700"
                                >
                                    PDF
                                </a>
                                <span v-else class="text-xs text-neutral-400">Yok</span>
                            </td>
                            <td class="px-6 py-5 text-right text-sm">
                                <div class="inline-flex gap-3">
                                    <RouterLink
                                        :to="{ name: 'management.issues.edit', params: { issue: issue.id } }"
                                        class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-800"
                                    >
                                        Düzenle
                                    </RouterLink>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 text-rose-500 hover:text-rose-700"
                                        @click="requestDelete(issue)"
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
                Sayıyı Sil
            </template>
            <template #description>
                <p>"{{ issueToDelete?.title }}" adlı sayıyı silmek istediğinizden emin misiniz?</p>
                <p class="mt-2 text-xs text-neutral-400" v-if="deleteError">
                    {{ deleteError }}
                </p>
            </template>
        </ConfirmModal>
    </section>
</template>
