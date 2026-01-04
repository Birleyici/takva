<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useVideoStore } from '../../stores/videoStore';
import { useVideoCategoryStore } from '../../stores/videoCategoryStore';

const props = defineProps({
    mode: {
        type: String,
        default: 'create',
    },
    video: {
        type: Object,
        default: null,
    },
    fetchParams: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['saved', 'cancel']);

const videoStore = useVideoStore();
const videoCategoryStore = useVideoCategoryStore();

const form = reactive({
    title: '',
    youtube_url: '',
    video_category_id: null,
    description: '',
    is_active: true,
});

const errors = ref({});
const submitting = ref(false);

const isEditMode = computed(() => props.mode === 'edit');
const formTitle = computed(() => (isEditMode.value ? 'Videoyu Güncelle' : 'Yeni Video'));
const effectiveFetchParams = computed(() => props.fetchParams ?? {});
const videoCategories = computed(() => videoCategoryStore.categories ?? []);

onMounted(async () => {
    if (!videoCategoryStore.categories?.length) {
        await videoCategoryStore.fetchCategories({ per_page: 100 });
    }
});

watch(
    () => props.video,
    (video) => {
        if (video) {
            form.title = video.title ?? '';
            form.youtube_url = video.youtube_url ?? '';
            form.video_category_id = video.video_category_id ?? video.category?.id ?? null;
            form.description = video.description ?? '';
            form.is_active = video.is_active ?? true;
        } else {
            resetForm();
        }

        errors.value = {};
    },
    { immediate: true }
);

function resetForm() {
    form.title = '';
    form.youtube_url = '';
    form.video_category_id = null;
    form.description = '';
    form.is_active = true;
}

function extractYoutubeId(value) {
    if (!value) {
        return null;
    }

    const trimmed = value.trim();
    if (!trimmed) {
        return null;
    }

    if (/^[A-Za-z0-9_-]{11}$/.test(trimmed)) {
        return trimmed;
    }

    const patterns = [
        /youtu\.be\/([A-Za-z0-9_-]{11})/i,
        /youtube\.com\/watch\?v=([A-Za-z0-9_-]{11})/i,
        /youtube\.com\/embed\/([A-Za-z0-9_-]{11})/i,
        /youtube\.com\/shorts\/([A-Za-z0-9_-]{11})/i,
        /youtube\.com\/live\/([A-Za-z0-9_-]{11})/i,
    ];

    for (const pattern of patterns) {
        const match = trimmed.match(pattern);
        if (match) {
            return match[1];
        }
    }

    const queryMatch = trimmed.match(/[?&]v=([A-Za-z0-9_-]{11})/i);
    return queryMatch ? queryMatch[1] : null;
}

const youtubeId = computed(() => extractYoutubeId(form.youtube_url));
const youtubeEmbed = computed(() => (youtubeId.value ? `https://www.youtube.com/embed/${youtubeId.value}` : ''));
const youtubeThumbnail = computed(() => (youtubeId.value ? `https://img.youtube.com/vi/${youtubeId.value}/hqdefault.jpg` : ''));

async function handleSubmit() {
    errors.value = {};
    submitting.value = true;

    const payload = {
        title: form.title,
        youtube_url: form.youtube_url,
        video_category_id: form.video_category_id,
        description: form.description,
        is_active: form.is_active,
    };

    try {
        if (isEditMode.value && props.video?.id) {
            await videoStore.updateVideo(props.video.id, payload, effectiveFetchParams.value);
        } else {
            await videoStore.createVideo(payload, effectiveFetchParams.value);
            resetForm();
        }

        emit('saved');
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
        } else if (videoStore.error) {
            errors.value = { general: [videoStore.error] };
        } else {
            errors.value = { general: ['Beklenmeyen bir hata oluştu. Lütfen tekrar deneyin.'] };
        }
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <aside class="rounded-3xl border border-primary-100 bg-white shadow-sm">
        <header class="border-b border-neutral-100 px-6 py-6">
            <h2 class="text-lg font-semibold text-secondary-900">
                {{ formTitle }}
            </h2>
            <p class="mt-2 text-sm text-neutral-500 leading-relaxed">
                Video başlığı, YouTube bağlantısı ve açıklamasını belirleyin. Yayın durumunu buradan
                yönetebilirsiniz.
            </p>
        </header>

        <form class="space-y-6 px-6 py-6" @submit.prevent="handleSubmit">
            <div>
                <label for="video-title" class="block text-sm font-semibold text-neutral-700">
                    Başlık
                </label>
                <input
                    id="video-title"
                    v-model="form.title"
                    type="text"
                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    placeholder="Video başlığını girin"
                    required
                />
                <p v-if="errors.title" class="mt-2 text-sm text-red-600">
                    {{ errors.title[0] }}
                </p>
            </div>

            <div>
                <label for="video-youtube" class="block text-sm font-semibold text-neutral-700">
                    YouTube Bağlantısı
                </label>
                <input
                    id="video-youtube"
                    v-model="form.youtube_url"
                    type="text"
                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    placeholder="https://www.youtube.com/watch?v=..."
                    required
                />
                <p class="mt-2 text-xs text-neutral-400">
                    Tam bağlantıyı veya 11 haneli video kimliğini girebilirsiniz.
                </p>
                <p v-if="errors.youtube_url" class="mt-2 text-sm text-red-600">
                    {{ errors.youtube_url[0] }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-neutral-700">
                    Video Kategorisi
                </label>
                <select
                    v-model="form.video_category_id"
                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                >
                    <option :value="null">Kategori seçin</option>
                    <option v-for="category in videoCategories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
                <p v-if="errors.video_category_id" class="mt-2 text-sm text-red-600">
                    {{ errors.video_category_id[0] }}
                </p>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-neutral-700">
                        Açıklama
                    </label>
                    <textarea
                        v-model="form.description"
                        rows="6"
                        class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                        placeholder="Video hakkında kısa bir açıklama girin"
                    ></textarea>
                    <p v-if="errors.description" class="mt-2 text-sm text-red-600">
                        {{ errors.description[0] }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-neutral-700">
                        Önizleme
                    </label>
                    <div
                        v-if="youtubeEmbed"
                        class="mt-2 overflow-hidden rounded-2xl border border-neutral-200 bg-neutral-50 shadow-sm"
                    >
                        <div class="aspect-video w-full">
                            <iframe
                                :src="youtubeEmbed"
                                class="h-full w-full"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                title="Video Önizleme"
                            />
                        </div>
                    </div>
                    <div
                        v-else
                        class="mt-2 flex h-full min-h-[200px] items-center justify-center rounded-2xl border border-dashed border-neutral-200 bg-neutral-50 px-4 text-center text-sm text-neutral-400"
                    >
                        Geçerli bir YouTube bağlantısı girildiğinde önizleme görüntülenecek.
                    </div>

                    <div v-if="youtubeThumbnail" class="mt-3 rounded-2xl border border-neutral-200 bg-white p-3">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-neutral-400">Kapak</p>
                        <img
                            :src="youtubeThumbnail"
                            alt="Video küçük görsel"
                            class="mt-2 h-24 w-full rounded-xl object-cover"
                        />
                    </div>
                </div>
            </div>

            <div>
                <label class="flex items-center gap-3">
                    <input
                        v-model="form.is_active"
                        type="checkbox"
                        class="h-5 w-5 rounded border-neutral-300 text-primary-600 focus:ring-primary-500"
                    />
                    <span class="text-sm font-semibold text-neutral-700">
                        Video yayında olsun
                    </span>
                </label>
                <p class="mt-1 text-xs text-neutral-400">
                    Pasif videolar sitede görüntülenmez.
                </p>
                <p v-if="errors.is_active" class="mt-2 text-sm text-red-600">
                    {{ errors.is_active[0] }}
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
    </aside>
</template>
