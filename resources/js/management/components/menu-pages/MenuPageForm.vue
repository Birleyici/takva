<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { useMenuPageStore } from '../../stores/menuPageStore';

const props = defineProps({
    mode: {
        type: String,
        default: 'create',
    },
    page: {
        type: Object,
        default: null,
    },
    fetchParams: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['saved', 'cancel']);

const menuPageStore = useMenuPageStore();

const form = reactive({
    title: '',
    summary: '',
    content: '',
    position: 0,
    is_active: true,
    slug: '',
});

const errors = ref({});
const submitting = ref(false);

const isEditMode = computed(() => props.mode === 'edit');
const formTitle = computed(() => (isEditMode.value ? 'Sayfayı Güncelle' : 'Yeni Menü Sayfası'));
const effectiveFetchParams = computed(() => props.fetchParams ?? {});

watch(
    () => props.page,
    (page) => {
        if (page) {
            form.title = page.title ?? '';
            form.summary = page.summary ?? '';
            form.content = page.content ?? '';
            form.position = page.position ?? 0;
            form.is_active = page.is_active ?? true;
            form.slug = page.slug ?? '';
        } else {
            resetForm();
        }

        errors.value = {};
    },
    { immediate: true },
);

function resetForm() {
    form.title = '';
    form.summary = '';
    form.content = '';
    form.position = 0;
    form.is_active = true;
    form.slug = '';
}

async function handleSubmit() {
    errors.value = {};
    submitting.value = true;

    try {
        const payload = { ...form };

        if (!payload.slug) {
            delete payload.slug;
        }

        if (isEditMode.value && props.page?.id) {
            await menuPageStore.updatePage(props.page.id, payload, effectiveFetchParams.value);
        } else {
            await menuPageStore.createPage(payload, effectiveFetchParams.value);
            resetForm();
        }

        emit('saved');
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
        } else {
            errors.value = {
                general: [menuPageStore.error || 'Beklenmeyen bir hata oluştu.'],
            };
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
                Menüde gösterilecek özel sayfa içeriğini yönetin.
            </p>
        </header>

        <form class="space-y-6 px-6 py-6" @submit.prevent="handleSubmit">
            <div>
                <label class="block text-sm font-semibold text-neutral-700">
                    Başlık
                </label>
                <input
                    v-model="form.title"
                    type="text"
                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    placeholder="Sayfa başlığı"
                    required
                />
                <p v-if="errors.title" class="mt-2 text-sm text-red-600">
                    {{ errors.title[0] }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-neutral-700">
                    Kısa Açıklama
                </label>
                <textarea
                    v-model="form.summary"
                    rows="2"
                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    placeholder="Menüde görünecek kısa açıklama"
                ></textarea>
                <p v-if="errors.summary" class="mt-2 text-sm text-red-600">
                    {{ errors.summary[0] }}
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

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-neutral-700">
                        Sıra
                    </label>
                    <input
                        v-model="form.position"
                        type="number"
                        class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    />
                    <p v-if="errors.position" class="mt-2 text-sm text-red-600">
                        {{ errors.position[0] }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-neutral-700">
                        Slug (isteğe bağlı)
                    </label>
                    <input
                        v-model="form.slug"
                        type="text"
                        class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                        placeholder="tr/menu/..."
                    />
                    <p v-if="errors.slug" class="mt-2 text-sm text-red-600">
                        {{ errors.slug[0] }}
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-between rounded-2xl border border-neutral-200 bg-white px-4 py-3">
                <div>
                    <p class="text-sm font-semibold text-neutral-700">Aktiflik</p>
                    <p class="text-xs text-neutral-400">Pasif olan sayfalar menüde gösterilmez.</p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input
                        v-model="form.is_active"
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
    </aside>
</template>
