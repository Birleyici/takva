<script setup>
import { computed, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useMediaStore } from '../../stores/mediaStore';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    selectedId: {
        type: [Number, String, null],
        default: null,
    },
});

const emit = defineEmits(['close', 'select']);

const mediaStore = useMediaStore();
const { items, meta, loading, uploading, error } = storeToRefs(mediaStore);

const internalSelectedId = ref(null);
const uploadInputRef = ref(null);
const localError = ref('');
const deletingId = ref(null);
const deleteError = ref('');

watch(
    () => props.open,
    async (value) => {
        if (value) {
            internalSelectedId.value = props.selectedId ?? null;
            localError.value = '';
            deleteError.value = '';
            deletingId.value = null;

            if (!items.value.length) {
                await mediaStore.fetchMedia({ page: 1 });
            }
        } else {
            internalSelectedId.value = null;
            deletingId.value = null;
            deleteError.value = '';
        }
    },
);

const isLastPage = computed(
    () => meta.value.current_page >= meta.value.last_page,
);

const hasMedia = computed(
    () => !loading.value && items.value.length > 0,
);

function closeModal() {
    emit('close');
}

function handleSelect(media) {
    internalSelectedId.value = media.id;
    emit('select', media);
    emit('close');
}

async function loadMore() {
    if (isLastPage.value || loading.value) {
        return;
    }

    await mediaStore.fetchMedia({
        page: meta.value.current_page + 1,
        append: true,
    });
}

async function handleUpload(event) {
    const [file] = event.target.files || [];

    if (!file) {
        return;
    }

    localError.value = '';

    try {
        const media = await mediaStore.uploadMedia(file);
        internalSelectedId.value = media.id;
        emit('select', media);
        emit('close');
    } catch (err) {
        localError.value = mediaStore.error || 'Dosya yüklenemedi.';
    } finally {
        if (uploadInputRef.value) {
            uploadInputRef.value.value = '';
        }
    }
}

async function handleDelete(media, event) {
    event.stopPropagation();

    if (deletingId.value) {
        return;
    }

    const confirmed = window.confirm(
        `'${media.original_name}' adlı görseli silmek istediğinize emin misiniz?`,
    );

    if (!confirmed) {
        return;
    }

    deletingId.value = media.id;
    deleteError.value = '';

    try {
        await mediaStore.deleteMedia(media.id);

        if (internalSelectedId.value === media.id) {
            internalSelectedId.value = null;
            emit('select', null);
        }
    } catch (error) {
        deleteError.value = mediaStore.error || 'Medya silinemedi.';
    } finally {
        deletingId.value = null;
    }
}
</script>

<template>
    <transition enter-active-class="duration-200 ease-out" enter-from-class="opacity-0"
        enter-to-class="opacity-100" leave-active-class="duration-150 ease-in" leave-from-class="opacity-100"
        leave-to-class="opacity-0">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-neutral-900/70 backdrop-blur-sm px-4 py-8"
            @click.self="closeModal"
        >
            <transition enter-active-class="duration-200 ease-out"
                enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                enter-to-class="opacity-100 translate-y-0 sm:scale-100" leave-active-class="duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="relative flex w-full max-w-5xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl">
                    <header class="flex items-center justify-between border-b border-neutral-100 px-6 py-5">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-neutral-400">
                                Medya Kitaplığı
                            </p>
                            <h2 class="mt-1 text-xl font-semibold text-secondary-900">
                                Görsel Seçin
                            </h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <label
                                class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-primary-200 bg-primary-50 px-4 py-2 text-sm font-semibold text-primary-700 transition hover:border-primary-300 hover:bg-primary-100"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 16v-5m0 0V7m0 4h4m-4 0H8m-2 4v3a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-3m2-5h.01M5 12h-.01M12 5h0" />
                                </svg>
                                Yeni Yükle
                                <input
                                    ref="uploadInputRef"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    :disabled="uploading"
                                    @change="handleUpload"
                                />
                            </label>
                            <button
                                type="button"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-200 text-neutral-400 transition hover:border-neutral-300 hover:text-neutral-600"
                                @click="closeModal"
                            >
                                <span class="sr-only">Kapat</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </header>

                    <div class="flex flex-1 flex-col overflow-hidden">
                        <div v-if="error || localError || deleteError"
                            class="mx-6 mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-600">
                            {{ localError || deleteError || error }}
                        </div>

                        <div class="flex-1 overflow-y-auto px-6 py-6">
                            <div v-if="loading && !items.length" class="py-14 text-center text-sm text-neutral-500">
                                Görseller yükleniyor...
                            </div>

                            <div v-else-if="!hasMedia"
                                class="flex flex-col items-center justify-center gap-3 rounded-3xl border border-dashed border-neutral-200 bg-neutral-50/60 py-14 text-center text-sm text-neutral-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-neutral-300" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 16v-4m0 0V8m0 4h4m-4 0H8M4 19V5a2 2 0 0 1 2-2h6.586a2 2 0 0 1 1.414.586l5.414 5.414A2 2 0 0 1 20 10.414V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z" />
                                </svg>
                                Henüz medya yüklenmemiş. Yeni yükleme ekleyerek başlayabilirsiniz.
                            </div>

                            <div v-else class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                                <div
                                    v-for="media in items"
                                    :key="media.id"
                                    :class="[
                                        'group relative flex aspect-square flex-col overflow-hidden rounded-2xl border transition',
                                        internalSelectedId === media.id
                                            ? 'border-primary-500 ring-2 ring-primary-200'
                                            : 'border-neutral-200 hover:border-primary-200 hover:ring-2 hover:ring-primary-100',
                                    ]"
                                    role="button"
                                    tabindex="0"
                                    @click="handleSelect(media)"
                                    @keydown.enter.prevent="handleSelect(media)"
                                >
                                    <img
                                        :src="media.url"
                                        :alt="media.original_name"
                                        class="h-full w-full object-cover"
                                        loading="lazy"
                                    >
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/60 to-transparent p-3">
                                        <p class="truncate text-xs font-medium text-white">
                                            {{ media.original_name }}
                                        </p>
                                    </div>
                                    <div v-if="internalSelectedId === media.id"
                                        class="absolute right-3 top-3 inline-flex items-center gap-1 rounded-full bg-primary-500/90 px-2 py-1 text-[11px] font-semibold text-white shadow">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                                        </svg>
                                        Seçildi
                                    </div>
                                    <button
                                        type="button"
                                        class="absolute right-3 bottom-3 inline-flex items-center justify-center rounded-full border border-rose-200 bg-white/90 px-3 py-1 text-[11px] font-semibold text-rose-600 shadow transition hover:bg-rose-50"
                                        :disabled="deletingId === media.id"
                                        @click="(event) => handleDelete(media, event)"
                                    >
                                        Sil
                                    </button>
                                </div>
                            </div>
                        </div>

                        <footer
                            class="flex items-center justify-between border-t border-neutral-100 px-6 py-4 text-xs text-neutral-500">
                            <span>
                                Toplam {{ meta.total }} öğe
                            </span>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-neutral-200 px-4 py-2 text-xs font-semibold text-neutral-600 transition hover:border-primary-200 hover:text-primary-600 disabled:opacity-40"
                                :disabled="isLastPage || loading"
                                @click="loadMore"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Daha Fazla Yükle
                            </button>
                        </footer>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>
