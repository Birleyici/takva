<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import { useIssueStore } from '../../stores/issueStore';
import MediaModal from '../media/MediaModal.vue';

const props = defineProps({
    mode: {
        type: String,
        default: 'create',
    },
    issue: {
        type: Object,
        default: null,
    },
    fetchParams: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['saved', 'cancel']);

const issueStore = useIssueStore();

const form = reactive({
    title: '',
    year: new Date().getFullYear(),
    month: new Date().getMonth() + 1,
    description: '',
    remove_pdf: false,
    remove_cover_image: false,
});

const pdfFile = ref(null);
const existingPdf = ref(null);
const selectedCover = ref(null);
const showMediaModal = ref(false);
const errors = ref({});
const submitting = ref(false);

const months = [
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

const isEditMode = computed(() => props.mode === 'edit');
const formTitle = computed(() => (isEditMode.value ? 'Sayıyı Güncelle' : 'Yeni Sayı'));
const effectiveFetchParams = computed(() => props.fetchParams ?? {});

watch(
    () => props.issue,
    (issue) => {
        if (issue) {
            form.title = issue.title ?? '';
            form.year = issue.year ?? new Date().getFullYear();
            form.month = issue.month ?? new Date().getMonth() + 1;
            form.description = issue.description ?? '';
            form.remove_pdf = false;
            form.remove_cover_image = false;
            existingPdf.value = issue.pdf_url
                ? {
                    url: issue.pdf_url,
                    name: issue.pdf_original_name,
                    size: issue.pdf_size,
                }
                : null;
            pdfFile.value = null;
            selectedCover.value = issue.cover_image ?? null;
        } else {
            resetForm();
        }

        errors.value = {};
    },
    { immediate: true },
);

function resetForm() {
    form.title = '';
    form.year = new Date().getFullYear();
    form.month = new Date().getMonth() + 1;
    form.description = '';
    form.remove_pdf = false;
    form.remove_cover_image = false;
    existingPdf.value = null;
    pdfFile.value = null;
    selectedCover.value = null;
}

function handlePdfChange(event) {
    const [file] = event.target.files || [];

    if (!file) {
        return;
    }

    pdfFile.value = file;
    form.remove_pdf = false;
}

function clearPdfSelection() {
    pdfFile.value = null;
}

function removeExistingPdf() {
    form.remove_pdf = true;
    existingPdf.value = null;
    pdfFile.value = null;
}

function openMediaModal() {
    showMediaModal.value = true;
}

function closeMediaModal() {
    showMediaModal.value = false;
}

function handleCoverSelected(media) {
    selectedCover.value = media;
    form.remove_cover_image = false;
    showMediaModal.value = false;
}

function clearCoverSelection() {
    selectedCover.value = null;
    form.remove_cover_image = true;
}

function formatSize(bytes) {
    if (!bytes) {
        return '';
    }

    if (bytes < 1024) {
        return `${bytes} B`;
    }

    if (bytes < 1024 * 1024) {
        return `${(bytes / 1024).toFixed(1)} KB`;
    }

    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function buildFormData() {
    const payload = new FormData();
    payload.append('title', form.title);
    payload.append('year', form.year);
    payload.append('month', form.month);
    payload.append('description', form.description ?? '');
    if (selectedCover.value?.id) {
        payload.append('cover_image_id', selectedCover.value.id);
    }
    if (form.remove_cover_image) {
        payload.append('remove_cover_image', '1');
    }

    if (pdfFile.value) {
        payload.append('pdf', pdfFile.value);
    }

    if (form.remove_pdf) {
        payload.append('remove_pdf', '1');
    }

    return payload;
}

async function handleSubmit() {
    errors.value = {};
    submitting.value = true;

    try {
        const payload = buildFormData();

        if (isEditMode.value && props.issue?.id) {
            await issueStore.updateIssue(props.issue.id, payload, effectiveFetchParams.value);
        } else {
            await issueStore.createIssue(payload, effectiveFetchParams.value);
            resetForm();
        }

        emit('saved');
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors ?? {};
        } else {
            errors.value = {
                general: [issueStore.error || 'Beklenmeyen bir hata oluştu.'],
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
                Dergi sayısının temel bilgilerini ve PDF dosyasını yönetin.
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
                    placeholder="Sayı başlığını girin"
                    required
                />
                <p v-if="errors.title" class="mt-2 text-sm text-red-600">
                    {{ errors.title[0] }}
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-neutral-700">
                        Yıl
                    </label>
                    <input
                        v-model="form.year"
                        type="number"
                        min="1900"
                        max="2100"
                        class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    />
                    <p v-if="errors.year" class="mt-2 text-sm text-red-600">
                        {{ errors.year[0] }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-neutral-700">
                        Ay
                    </label>
                    <select
                        v-model="form.month"
                        class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                    >
                        <option v-for="month in months" :key="month.value" :value="month.value">
                            {{ month.label }}
                        </option>
                    </select>
                    <p v-if="errors.month" class="mt-2 text-sm text-red-600">
                        {{ errors.month[0] }}
                    </p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-neutral-700 mb-2">
                    Açıklama
                </label>
                <div class="rounded-2xl border border-neutral-200 bg-white">
                    <QuillEditor
                        v-model:content="form.description"
                        content-type="html"
                        theme="snow"
                        toolbar="full"
                        class="article-editor min-h-[260px]"
                    />
                </div>
                <p v-if="errors.description" class="mt-2 text-sm text-red-600">
                    {{ errors.description[0] }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-neutral-700">
                    Kapak Görseli
                </label>
                <div class="mt-2 flex flex-col gap-4 rounded-2xl border border-neutral-200 bg-neutral-50/60 p-4 sm:flex-row">
                    <div class="relative h-32 w-full overflow-hidden rounded-2xl border border-neutral-200 bg-white sm:w-40">
                        <img
                            v-if="selectedCover?.url"
                            :src="selectedCover.url"
                            :alt="selectedCover.original_name"
                            class="h-full w-full object-cover"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center text-neutral-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-8h.01M6 20h12a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-1.172a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 11.172 8H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2z" />
                            </svg>
                        </div>
                    </div>

                    <div class="flex-1 space-y-3 text-sm">
                        <div>
                            <p class="font-semibold text-secondary-900">
                                {{ selectedCover?.original_name || 'Kapak görseli seçilmedi' }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-primary-200 bg-white px-4 py-2 text-sm font-semibold text-primary-600 transition hover:border-primary-300 hover:text-primary-700"
                                @click="openMediaModal"
                            >
                                Kapak Seç
                            </button>
                            <button
                                v-if="selectedCover"
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-500 transition hover:border-neutral-300 hover:text-neutral-700"
                                @click="clearCoverSelection"
                            >
                                Kaldır
                            </button>
                        </div>
                        <p class="text-xs text-neutral-400">
                            Kapak görseli listede ve detay sayfasında bu sayı için arka plan olarak kullanılacaktır.
                        </p>
                    </div>
                </div>
                <p v-if="errors.cover_image_id" class="mt-2 text-sm text-red-600">
                    {{ errors.cover_image_id[0] }}
                </p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-neutral-700">
                    PDF Dosyası
                </label>
                <div class="mt-2 rounded-2xl border border-dashed border-neutral-300 bg-neutral-50/60 p-4">
                    <div class="flex flex-col gap-3 text-sm text-neutral-600">
                        <div class="flex flex-wrap items-center gap-3">
                            <label
                                class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-primary-200 bg-white px-4 py-2 text-sm font-semibold text-primary-600 transition hover:border-primary-300 hover:text-primary-700"
                            >
                                PDF Yükle
                                <input type="file" accept="application/pdf" class="hidden" @change="handlePdfChange" />
                            </label>
                            <button
                                v-if="pdfFile"
                                type="button"
                                class="inline-flex items-center rounded-xl border border-neutral-200 px-4 py-2 text-sm text-neutral-600 transition hover:border-neutral-300"
                                @click="clearPdfSelection"
                            >
                                Seçimi Temizle
                            </button>
                            <button
                                v-if="existingPdf"
                                type="button"
                                class="inline-flex items-center rounded-xl border border-rose-200 px-4 py-2 text-sm text-rose-600 transition hover:border-rose-300"
                                @click="removeExistingPdf"
                            >
                                PDF'i Kaldır
                            </button>
                        </div>

                        <div v-if="pdfFile" class="rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm">
                            <p class="font-semibold text-secondary-900">{{ pdfFile.name }}</p>
                            <p class="text-xs text-neutral-500">{{ formatSize(pdfFile.size) }}</p>
                        </div>

                        <div v-else-if="existingPdf" class="rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm">
                            <p class="font-semibold text-secondary-900">
                                <a :href="existingPdf.url" target="_blank" rel="noopener" class="hover:text-primary-600">
                                    {{ existingPdf.name ?? 'Yüklü PDF' }}
                                </a>
                            </p>
                            <p class="text-xs text-neutral-500">{{ formatSize(existingPdf.size) }}</p>
                        </div>

                        <p class="text-xs text-neutral-400">
                            PDF dosya boyutu en fazla 50MB olabilir.
                        </p>
                    </div>
                </div>
                <p v-if="errors.pdf" class="mt-2 text-sm text-red-600">
                    {{ errors.pdf[0] }}
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
            :selected-id="selectedCover?.id ?? null"
            @close="closeMediaModal"
            @select="handleCoverSelected"
        />
    </aside>
</template>
