<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { RouterLink } from 'vue-router';
import { storeToRefs } from 'pinia';
import { useVideoStore } from '../../stores/videoStore';
import ConfirmModal from '../../components/common/ConfirmModal.vue';

const videoStore = useVideoStore();
const { videos, meta, loading, error } = storeToRefs(videoStore);

const searchTerm = ref('');
const statusFilter = ref('');
const searchTimeout = ref(null);

const deleteModalOpen = ref(false);
const videoToDelete = ref(null);
const deleting = ref(false);
const deleteError = ref('');

const hasVideos = computed(() => !loading.value && videos.value.length > 0);
const activeFilter = computed(() => (typeof statusFilter.value === 'boolean' ? statusFilter.value : undefined));

const statusOptions = [
    { label: 'Tümü', value: '' },
    { label: 'Aktif', value: true },
    { label: 'Pasif', value: false },
];

onMounted(async () => {
    await videoStore.fetchVideos();
});

watch(
    () => statusFilter.value,
    () => {
        videoStore.fetchVideos({
            search: searchTerm.value,
            is_active: activeFilter.value,
        });
    }
);

watch(
    () => searchTerm.value,
    (value) => {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value);
        }

        searchTimeout.value = setTimeout(() => {
            videoStore.fetchVideos({
                search: value,
                is_active: activeFilter.value,
                page: 1,
            });
        }, 350);
    }
);

function requestDelete(video) {
    videoToDelete.value = video;
    deleteModalOpen.value = true;
    deleteError.value = '';
}

function closeDeleteModal() {
    deleteModalOpen.value = false;
    videoToDelete.value = null;
    deleting.value = false;
    deleteError.value = '';
}

async function confirmDelete() {
    if (!videoToDelete.value) {
        return;
    }

    deleting.value = true;
    deleteError.value = '';

    try {
        await videoStore.deleteVideo(videoToDelete.value.id, {
            search: searchTerm.value,
            is_active: activeFilter.value,
        });
        closeDeleteModal();
    } catch (err) {
        deleting.value = false;
        deleteError.value = videoStore.error || 'Video silinirken bir hata oluştu.';
    }
}

async function goToPage(page) {
    if (page < 1 || page > meta.value.last_page || page === meta.value.current_page) {
        return;
    }

    await videoStore.fetchVideos({
        page,
        search: searchTerm.value,
        is_active: activeFilter.value,
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
                    Videolar
                </h1>
                <p class="mt-2 text-sm text-neutral-500">
                    YouTube videolarını yönetin, bağlantıları güncelleyin ve yayın durumunu belirleyin.
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
                        placeholder="Video ara..."
                    />
                </div>

                <select
                    v-model="statusFilter"
                    class="rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                >
                    <option v-for="option in statusOptions" :key="option.label" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>

                <RouterLink
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600"
                    :to="{ name: 'management.videos.create' }"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Yeni Video
                </RouterLink>
            </div>
        </header>

        <div class="min-w-0 rounded-3xl border border-neutral-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-neutral-100">
                    <thead class="bg-neutral-50/80">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                Video
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                Kategori
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                YouTube
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                Durum
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                Tarih
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                İşlemler
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        <tr v-if="loading">
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-neutral-500">
                                Videolar yükleniyor...
                            </td>
                        </tr>
                        <tr v-else-if="!hasVideos">
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-neutral-500">
                                Henüz video eklenmemiş.
                            </td>
                        </tr>
                        <tr v-else v-for="video in videos" :key="video.id" class="hover:bg-neutral-50/60">
                            <td class="px-6 py-5 text-sm text-secondary-900">
                                <div class="flex items-center gap-4">
                                    <div class="h-14 w-24 overflow-hidden rounded-xl bg-neutral-100">
                                        <img
                                            :src="video.thumbnail_url || '/placeholder.jpg'"
                                            :alt="video.title"
                                            class="h-full w-full object-cover"
                                        />
                                    </div>
                                    <div>
                                        <div class="font-semibold">{{ video.title }}</div>
                                        <p class="text-xs text-neutral-500">{{ video.slug }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-sm text-secondary-700">
                                <span v-if="video.category" class="inline-flex items-center rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700">
                                    {{ video.category.name }}
                                </span>
                                <span v-else class="text-xs text-neutral-400">-</span>
                            </td>
                            <td class="px-6 py-5 text-sm text-secondary-700">
                                <a
                                    :href="video.youtube_url"
                                    target="_blank"
                                    rel="noopener"
                                    class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-800"
                                >
                                    {{ video.youtube_id || 'YouTube' }}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5h5v5m0-5L10 14" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 19h14a2 2 0 0 0 2-2V9" />
                                    </svg>
                                </a>
                            </td>
                            <td class="px-6 py-5 text-sm">
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="video.is_active
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-neutral-100 text-neutral-500'"
                                >
                                    {{ video.is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-sm text-secondary-700">
                                {{ formatDate(video.created_at) }}
                            </td>
                            <td class="px-6 py-5 text-right text-sm">
                                <div class="inline-flex gap-3">
                                    <RouterLink
                                        :to="{ name: 'management.videos.edit', params: { video: video.id } }"
                                        class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-800"
                                    >
                                        Düzenle
                                    </RouterLink>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-1 text-rose-500 hover:text-rose-700"
                                        @click="requestDelete(video)"
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

        <p v-if="error" class="text-sm text-red-600">
            {{ error }}
        </p>

        <ConfirmModal
            :open="deleteModalOpen"
            title="Videoyu Sil"
            :message="videoToDelete
                ? `<strong>${videoToDelete.title}</strong> videosunu silmek üzeresiniz.<br>Bu işlem geri alınamaz.` + (deleteError ? `<br><span class='mt-3 inline-block rounded-lg bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-600'>${deleteError}</span>` : '')
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
