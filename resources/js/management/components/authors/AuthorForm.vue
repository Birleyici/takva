<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { useAuthorStore } from '../../stores/authorStore';
import MediaModal from '../media/MediaModal.vue';

const props = defineProps({
    mode: {
        type: String,
        default: 'create',
    },
    author: {
        type: Object,
        default: null,
    },
    fetchParams: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['saved', 'cancel']);

const authorStore = useAuthorStore();

const form = reactive({
    name: '',
    profile_image_id: null,
});

const selectedMedia = ref(null);
const showMediaModal = ref(false);
const errors = ref({});
const submitting = ref(false);

const isEditMode = computed(() => props.mode === 'edit');
const formTitle = computed(() => (isEditMode.value ? 'Yazarı Güncelle' : 'Yeni Yazar'));

const effectiveFetchParams = computed(() => props.fetchParams ?? {});

watch(
    () => props.author,
    (author) => {
        if (author) {
            form.name = author.name ?? '';
            form.profile_image_id = author.profile_image?.id ?? null;
            selectedMedia.value = author.profile_image ?? null;
        } else {
            resetForm();
        }

        errors.value = {};
    },
    { immediate: true },
);

function resetForm() {
    form.name = '';
    form.profile_image_id = null;
    selectedMedia.value = null;
}

function openMediaModal() {
    showMediaModal.value = true;
}

function closeMediaModal() {
    showMediaModal.value = false;
}

function handleMediaSelected(media) {
    selectedMedia.value = media;
    form.profile_image_id = media?.id ?? null;
}

function clearSelectedMedia() {
    selectedMedia.value = null;
    form.profile_image_id = null;
}

async function handleSubmit() {
    errors.value = {};
    submitting.value = true;

    try {
        if (isEditMode.value && props.author?.id) {
            await authorStore.updateAuthor(props.author.id, { ...form }, effectiveFetchParams.value);
        } else {
            await authorStore.createAuthor({ ...form }, effectiveFetchParams.value);
            resetForm();
        }

        emit('saved');
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
        } else if (authorStore.error) {
            errors.value = {
                general: [authorStore.error],
            };
        } else {
            errors.value = {
                general: ['Beklenmeyen bir hata oluştu. Lütfen tekrar deneyin.'],
            };
        }
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <aside class="rounded-3xl border border-primary-100 bg-white shadow-sm lg:max-w-md">
        <header class="border-b border-neutral-100 px-6 py-6">
            <h2 class="text-lg font-semibold text-secondary-900">
                {{ formTitle }}
            </h2>
            <p class="mt-2 text-sm text-neutral-500 leading-relaxed">
                Yazar bilgilerini ekleyebilir, profil görselini seçebilir ve güncelleyebilirsiniz.
            </p>
        </header>

        <form class="space-y-6 px-6 py-6" @submit.prevent="handleSubmit">
            <div>
                <label for="author-name" class="block text-sm font-semibold text-neutral-700">
                    Yazar Adı
                </label>
                <input
                    id="author-name"
                    v-model="form.name"
                    type="text"
                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    placeholder="Yazar adı"
                    required
                />
                <p v-if="errors.name" class="mt-2 text-sm text-red-600">
                    {{ errors.name[0] }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-neutral-700">
                    Profil Görseli
                </label>
                <div
                    class="mt-2 flex items-start gap-4 rounded-2xl border border-neutral-200 bg-neutral-50/60 p-4"
                >
                    <div class="relative h-20 w-20 overflow-hidden rounded-2xl border border-neutral-200 bg-white">
                        <img
                            v-if="selectedMedia?.url"
                            :src="selectedMedia.url"
                            :alt="selectedMedia.original_name"
                            class="h-full w-full object-cover"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center text-neutral-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-8h.01M6 20h12a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-1.172a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 11.172 8H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2z" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex-1 space-y-3 text-sm">
                        <p class="text-neutral-600">
                            <strong>{{ selectedMedia?.original_name || 'Görsel seçilmedi' }}</strong>
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-primary-200 bg-white px-4 py-2 text-sm font-semibold text-primary-600 transition hover:border-primary-300 hover:text-primary-700"
                                @click="openMediaModal"
                            >
                                Görsel Seç
                            </button>
                            <button
                                v-if="selectedMedia"
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-500 transition hover:border-neutral-300 hover:text-neutral-700"
                                @click="clearSelectedMedia"
                            >
                                Kaldır
                            </button>
                        </div>
                        <p class="text-xs text-neutral-400">
                            Yüklenen görseller medya kitaplığında saklanır ve diğer alanlarda da kullanılabilir.
                        </p>
                    </div>
                </div>
                <p v-if="errors.profile_image_id" class="mt-2 text-sm text-red-600">
                    {{ errors.profile_image_id[0] }}
                </p>
            </div>

            <p v-if="errors.general" class="text-sm text-red-600">
                {{ errors.general[0] }}
            </p>

            <div class="flex items-center gap-3">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-primary-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600 disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="submitting"
                >
                    {{ isEditMode ? 'Güncelle' : 'Oluştur' }}
                </button>
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-6 py-3 text-sm font-semibold text-neutral-600 transition hover:border-neutral-300 hover:text-neutral-800"
                    @click="emit('cancel')"
                >
                    Vazgeç
                </button>
            </div>
        </form>

        <MediaModal
            :open="showMediaModal"
            :selected-id="selectedMedia?.id ?? null"
            @close="closeMediaModal"
            @select="handleMediaSelected"
        />
    </aside>
</template>
