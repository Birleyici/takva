<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { useHeroSlideStore } from '../../stores/heroSlideStore';
import HeroSlideCropModal from './HeroSlideCropModal.vue';

const props = defineProps({
    mode: {
        type: String,
        default: 'create',
    },
    slide: {
        type: Object,
        default: null,
    },
    fetchParams: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['saved', 'cancel']);

const heroSlideStore = useHeroSlideStore();

const form = reactive({
    image_id: null,
    display_date: '',
    link_url: '',
    sort_order: 0,
    is_active: true,
});

const selectedMedia = ref(null);
const showCropModal = ref(false);
const errors = ref({});
const submitting = ref(false);

const isEditMode = computed(() => props.mode === 'edit');
const formTitle = computed(() =>
    isEditMode.value ? 'Slider Guncelle' : 'Yeni Slider',
);

const effectiveFetchParams = computed(() => props.fetchParams ?? {});

watch(
    () => props.slide,
    (slide) => {
        if (slide) {
            form.image_id = slide.image?.id ?? slide.image_id ?? null;
            form.display_date = slide.display_date ?? '';
            form.link_url = slide.link_url ?? '';
            form.sort_order = slide.sort_order ?? 0;
            form.is_active = Boolean(slide.is_active ?? true);
            selectedMedia.value = slide.image ?? null;
        } else {
            resetForm();
        }

        errors.value = {};
    },
    { immediate: true },
);

function resetForm() {
    form.image_id = null;
    form.display_date = '';
    form.link_url = '';
    form.sort_order = 0;
    form.is_active = true;
    selectedMedia.value = null;
}

function openCropModal() {
    showCropModal.value = true;
}

function closeCropModal() {
    showCropModal.value = false;
}

function handleMediaSelected(media) {
    selectedMedia.value = media;
    form.image_id = media?.id ?? null;
    showCropModal.value = false;
}

function clearSelectedMedia() {
    selectedMedia.value = null;
    form.image_id = null;
}

async function handleSubmit() {
    errors.value = {};
    submitting.value = true;

    try {
        const payload = { ...form };

        if (isEditMode.value && props.slide?.id) {
            await heroSlideStore.updateSlide(
                props.slide.id,
                payload,
                effectiveFetchParams.value,
            );
        } else {
            await heroSlideStore.createSlide(payload, effectiveFetchParams.value);
            resetForm();
        }

        emit('saved');
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
        } else if (heroSlideStore.error) {
            errors.value = {
                general: [heroSlideStore.error],
            };
        } else {
            errors.value = {
                general: ['Beklenmeyen bir hata olustu.'],
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
                Hero slider gorseli, tarih bilgisi ve siralama ayarlarini yonetin.
            </p>
        </header>

        <form class="space-y-6 px-6 py-6" @submit.prevent="handleSubmit">
            <div>
                <label class="block text-sm font-semibold text-neutral-700">
                    Slider Gorseli
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-8h.01M6 20h12a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-1.172a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 11.172 8H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2z" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex-1 space-y-3 text-sm">
                        <p class="text-neutral-600">
                            <strong>{{ selectedMedia?.original_name || 'Gorsel secilmedi' }}</strong>
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-primary-200 bg-white px-4 py-2 text-sm font-semibold text-primary-600 transition hover:border-primary-300 hover:text-primary-700"
                                @click="openCropModal"
                            >
                                Gorsel Yukle ve Kirp
                            </button>
                            <button
                                v-if="selectedMedia"
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-500 transition hover:border-neutral-300 hover:text-neutral-700"
                                @click="clearSelectedMedia"
                            >
                                Kaldir
                            </button>
                        </div>
                        <p class="text-xs text-neutral-400">
                            Slider gorselleri zorunlu olarak 16:9 oraninda kirpilir.
                        </p>
                    </div>
                </div>
                <p v-if="errors.image_id" class="mt-2 text-sm text-red-600">
                    {{ errors.image_id[0] }}
                </p>
            </div>

            <div>
                <label for="slider-date" class="block text-sm font-semibold text-neutral-700">
                    Tarih
                </label>
                <input
                    id="slider-date"
                    v-model="form.display_date"
                    type="date"
                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                />
                <p v-if="errors.display_date" class="mt-2 text-sm text-red-600">
                    {{ errors.display_date[0] }}
                </p>
            </div>

            <div>
                <label for="slider-url" class="block text-sm font-semibold text-neutral-700">
                    URL
                </label>
                <input
                    id="slider-url"
                    v-model="form.link_url"
                    type="text"
                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    placeholder="https://... veya /sayilar"
                />
                <p class="mt-1 text-xs text-neutral-400">
                    Tiklandiginda bu adrese yonlendirilir.
                </p>
                <p v-if="errors.link_url" class="mt-2 text-sm text-red-600">
                    {{ errors.link_url[0] }}
                </p>
            </div>

            <div>
                <label for="slider-order" class="block text-sm font-semibold text-neutral-700">
                    Sira
                </label>
                <input
                    id="slider-order"
                    v-model.number="form.sort_order"
                    type="number"
                    min="0"
                    max="9999"
                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                />
                <p v-if="errors.sort_order" class="mt-2 text-sm text-red-600">
                    {{ errors.sort_order[0] }}
                </p>
            </div>

            <div>
                <label class="flex items-center gap-3">
                    <input
                        v-model="form.is_active"
                        type="checkbox"
                        class="h-5 w-5 rounded border-neutral-300 text-primary-600 focus:ring-primary-500"
                    />
                    <span class="text-sm font-semibold text-neutral-700">
                        Slider aktif olsun
                    </span>
                </label>
                <p class="mt-1 text-xs text-neutral-400">
                    Pasif sliderlar ana sayfada gosterilmez.
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
                    {{ isEditMode ? 'Guncelle' : 'Olustur' }}
                </button>
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-6 py-3 text-sm font-semibold text-neutral-600 transition hover:border-neutral-300 hover:text-neutral-800"
                    @click="emit('cancel')"
                >
                    Vazgec
                </button>
            </div>
        </form>

        <HeroSlideCropModal
            :open="showCropModal"
            :aspect-ratio="16 / 9"
            :output-width="1920"
            ratio-label="16:9"
            @close="closeCropModal"
            @select="handleMediaSelected"
        />
    </aside>
</template>
