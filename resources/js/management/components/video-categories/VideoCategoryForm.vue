<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { useVideoCategoryStore } from '../../stores/videoCategoryStore';

const props = defineProps({
    mode: {
        type: String,
        default: 'create',
    },
    category: {
        type: Object,
        default: null,
    },
    fetchParams: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['saved', 'cancel']);

const videoCategoryStore = useVideoCategoryStore();

const form = reactive({
    name: '',
    description: '',
    is_active: true,
});

const errors = ref({});
const submitting = ref(false);

const isEditMode = computed(() => props.mode === 'edit');
const effectiveFetchParams = computed(() => props.fetchParams ?? {});
const formTitle = computed(() => (isEditMode.value ? 'Video Kategorisi Güncelle' : 'Yeni Video Kategorisi'));

watch(
    () => props.category,
    (category) => {
        if (category) {
            form.name = category.name ?? '';
            form.description = category.description ?? '';
            form.is_active = Boolean(category.is_active ?? true);
        } else {
            resetForm();
        }

        errors.value = {};
    },
    { immediate: true },
);

function resetForm() {
    form.name = '';
    form.description = '';
    form.is_active = true;
}

async function handleSubmit() {
    errors.value = {};
    submitting.value = true;

    try {
        if (isEditMode.value && props.category?.id) {
            await videoCategoryStore.updateCategory(props.category.id, { ...form }, effectiveFetchParams.value);
        } else {
            await videoCategoryStore.createCategory({ ...form }, effectiveFetchParams.value);
            resetForm();
        }

        emit('saved');
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
        } else if (videoCategoryStore.error) {
            errors.value = {
                general: [videoCategoryStore.error],
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
                Videoları gruplandırmak için kategori adı, açıklama ve durum bilgisini girin.
            </p>
        </header>

        <form class="space-y-6 px-6 py-6" @submit.prevent="handleSubmit">
            <div>
                <label for="video-category-name" class="block text-sm font-semibold text-neutral-700">
                    Kategori Adı
                </label>
                <input
                    id="video-category-name"
                    v-model="form.name"
                    type="text"
                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    placeholder="Örn. Sohbetler"
                    required
                />
                <p v-if="errors.name" class="mt-2 text-sm text-red-600">
                    {{ errors.name[0] }}
                </p>
            </div>

            <div>
                <label for="video-category-description" class="block text-sm font-semibold text-neutral-700">
                    Açıklama
                </label>
                <textarea
                    id="video-category-description"
                    v-model="form.description"
                    rows="4"
                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    placeholder="Kategori hakkında kısa bir açıklama girin."
                ></textarea>
                <p v-if="errors.description" class="mt-2 text-sm text-red-600">
                    {{ errors.description[0] }}
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
                        Kategori aktif olsun
                    </span>
                </label>
                <p class="mt-1 text-xs text-neutral-400">
                    Pasif kategoriler kullanıcı arayüzünde listelenmez.
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
