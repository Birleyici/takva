<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useArticleStore } from '../../stores/articleStore';
import { useCategoryStore } from '../../stores/categoryStore';
import { useAuthorStore } from '../../stores/authorStore';
import MediaModal from '../media/MediaModal.vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
    mode: {
        type: String,
        default: 'create',
    },
    article: {
        type: Object,
        default: null,
    },
    fetchParams: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['saved', 'cancel']);

const articleStore = useArticleStore();
const categoryStore = useCategoryStore();
const authorStore = useAuthorStore();

const form = reactive({
    title: '',
    excerpt: '',
    content: '',
    category_id: null,
    author_id: null,
    feature_image_id: null,
    is_published: true,
});

const selectedMedia = ref(null);
const showMediaModal = ref(false);
const errors = ref({});
const submitting = ref(false);

const isEditMode = computed(() => props.mode === 'edit');
const formTitle = computed(() => (isEditMode.value ? 'Makaleyi Güncelle' : 'Yeni Makale'));
const effectiveFetchParams = computed(() => props.fetchParams ?? {});

const categories = computed(() => categoryStore.categories ?? []);
const authors = computed(() => authorStore.authors ?? []);

onMounted(async () => {
    if (!categoryStore.categories?.length && categoryStore.fetchCategories) {
        await categoryStore.fetchCategories();
    }
    if (!authorStore.authors?.length) {
        await authorStore.fetchAuthors();
    }
});

watch(
    () => props.article,
    (article) => {
        if (article) {
            form.title = article.title ?? '';
            form.excerpt = article.excerpt ?? '';
            form.content = article.content ?? '';
            form.category_id = article.category?.id ?? null;
            form.author_id = article.author?.id ?? null;
            form.feature_image_id = article.feature_image?.id ?? null;
            form.is_published = article.is_published ?? true;
            selectedMedia.value = article.feature_image ?? null;
        } else {
            resetForm();
        }

        errors.value = {};
    },
    { immediate: true },
);

function resetForm() {
    form.title = '';
    form.excerpt = '';
    form.content = '';
    form.category_id = null;
    form.author_id = null;
    form.feature_image_id = null;
    form.is_published = true;
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
    form.feature_image_id = media?.id ?? null;
}

function clearSelectedMedia() {
    selectedMedia.value = null;
    form.feature_image_id = null;
}

async function handleSubmit() {
    errors.value = {};
    submitting.value = true;

    const payload = { ...form };

    try {
        if (isEditMode.value && props.article?.id) {
            await articleStore.updateArticle(props.article.id, payload, effectiveFetchParams.value);
        } else {
            await articleStore.createArticle(payload, effectiveFetchParams.value);
            resetForm();
        }

        emit('saved');
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
        } else if (articleStore.error) {
            errors.value = {
                general: [articleStore.error],
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
    <aside class="rounded-3xl border border-primary-100 bg-white shadow-sm ">
        <header class="border-b border-neutral-100 px-6 py-6">
            <h2 class="text-lg font-semibold text-secondary-900">
                {{ formTitle }}
            </h2>
            <p class="mt-2 text-sm text-neutral-500 leading-relaxed">
                Makale başlığı, kategorisi, yazarı ve içeriğini düzenleyin. Görsel ve yayın durumunu belirleyin.
            </p>
        </header>

        <form class="space-y-6 px-6 py-6" @submit.prevent="handleSubmit">
            <div>
                <label for="article-title" class="block text-sm font-semibold text-neutral-700">
                    Başlık
                </label>
                <input
                    id="article-title"
                    v-model="form.title"
                    type="text"
                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    placeholder="Makale başlığını girin"
                    required
                />
                <p v-if="errors.title" class="mt-2 text-sm text-red-600">
                    {{ errors.title[0] }}
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-neutral-700">
                        Kategori
                    </label>
                    <select
                        v-model="form.category_id"
                        class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                        <option :value="null">Kategori seçin</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                    <p v-if="errors.category_id" class="mt-2 text-sm text-red-600">
                        {{ errors.category_id[0] }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-neutral-700">
                        Yazar
                    </label>
                    <select
                        v-model="form.author_id"
                        class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                        <option :value="null">Yazar seçin</option>
                        <option v-for="author in authors" :key="author.id" :value="author.id">
                            {{ author.name }}
                        </option>
                    </select>
                    <p v-if="errors.author_id" class="mt-2 text-sm text-red-600">
                        {{ errors.author_id[0] }}
                    </p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-neutral-700">
                    Kısa Açıklama
                </label>
                <textarea
                    v-model="form.excerpt"
                    rows="3"
                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    placeholder="Makaleye dair kısa bir özet yazın"
                />
                <p v-if="errors.excerpt" class="mt-2 text-sm text-red-600">
                    {{ errors.excerpt[0] }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-neutral-700 mb-2">
                    İçerik
                </label>
                <div class="rounded-2xl border border-neutral-200 bg-white">
                    <QuillEditor
                        v-model:content="form.content"
                        content-type="html"
                        theme="snow"
                        toolbar="full"
                        class="article-editor min-h-[260px]"
                    />
                </div>
                <p v-if="errors.content" class="mt-2 text-sm text-red-600">
                    {{ errors.content[0] }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-neutral-700">
                    Kapak Görseli
                </label>
                <div class="mt-2 flex items-start gap-4 rounded-2xl border border-neutral-200 bg-neutral-50/60 p-4">
                    <div class="relative h-20 w-20 overflow-hidden rounded-2xl border border-neutral-200 bg-white">
                        <img
                            v-if="selectedMedia?.url"
                            :src="selectedMedia.url"
                            :alt="selectedMedia.original_name"
                            class="h-full w-full object-cover"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center text-neutral-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-8h.01M6 20h12a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-1.172a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 11.172 8H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2z" />
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
                            Kapak görseli medyada saklanır ve gerektiğinde diğer alanlarda da kullanılabilir.
                        </p>
                    </div>
                </div>
                <p v-if="errors.feature_image_id" class="mt-2 text-sm text-red-600">
                    {{ errors.feature_image_id[0] }}
                </p>
            </div>

            <div class="flex items-center justify-between rounded-2xl border border-neutral-200 bg-white px-4 py-3">
                <div>
                    <p class="text-sm font-semibold text-neutral-700">Yayın Durumu</p>
                    <p class="text-xs text-neutral-400">Pasif hale getirildiğinde makale listelerde gösterilmez.</p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input
                        v-model="form.is_published"
                        type="checkbox"
                        class="peer sr-only"
                    />
                    <div class="peer h-6 w-11 rounded-full bg-neutral-300 transition peer-checked:bg-primary-500"></div>
                    <div class="absolute left-0.5 top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-white shadow transition peer-checked:translate-x-5"></div>
                </label>
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
