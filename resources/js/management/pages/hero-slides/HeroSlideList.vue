<script setup>
import { computed, onMounted, ref } from 'vue';
import { storeToRefs } from 'pinia';
import ConfirmModal from '../../components/common/ConfirmModal.vue';
import HeroSlideForm from '../../components/hero-slides/HeroSlideForm.vue';
import { useHeroSlideStore } from '../../stores/heroSlideStore';

const heroSlideStore = useHeroSlideStore();
const { slides, meta, loading } = storeToRefs(heroSlideStore);

const showForm = ref(false);
const formMode = ref('create');
const selectedSlide = ref(null);

const deleteModalOpen = ref(false);
const slideToDelete = ref(null);
const deleting = ref(false);
const deleteError = ref('');

const hasSlides = computed(() => !loading.value && slides.value.length > 0);

const formFetchParams = computed(() => ({
    per_page: meta.value.per_page,
    page: formMode.value === 'create' ? 1 : meta.value.current_page,
}));

onMounted(async () => {
    await heroSlideStore.fetchSlides();
});

function openCreateForm() {
    selectedSlide.value = null;
    formMode.value = 'create';
    showForm.value = true;
}

function openEditForm(slide) {
    selectedSlide.value = slide;
    formMode.value = 'edit';
    showForm.value = true;
}

function closeForm() {
    showForm.value = false;
    selectedSlide.value = null;
}

async function handleSaved() {
    closeForm();
}

function requestDelete(slide) {
    slideToDelete.value = slide;
    deleteModalOpen.value = true;
    deleteError.value = '';
}

function closeDeleteModal() {
    deleteModalOpen.value = false;
    slideToDelete.value = null;
    deleting.value = false;
    deleteError.value = '';
}

async function confirmDelete() {
    if (!slideToDelete.value) {
        return;
    }

    deleting.value = true;
    deleteError.value = '';

    try {
        await heroSlideStore.deleteSlide(slideToDelete.value.id, {
            per_page: meta.value.per_page,
        });
        closeDeleteModal();
    } catch (err) {
        deleting.value = false;
        deleteError.value = heroSlideStore.error || 'Slider silinemedi.';
    }
}

async function goToPage(page) {
    if (page < 1 || page > meta.value.last_page || page === meta.value.current_page) {
        return;
    }

    await heroSlideStore.fetchSlides({
        page,
        per_page: meta.value.per_page,
    });
}

const statusBadgeClass = (isActive) =>
    isActive
        ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
        : 'bg-rose-50 text-rose-600 border border-rose-100';

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
                    Hero Slider
                </h1>
                <p class="mt-2 text-sm text-neutral-500">
                    Ana sayfa banner slider gorsellerini yonetin.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <button
                    type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600"
                    @click="openCreateForm"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Yeni Slider
                </button>
            </div>
        </header>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <div class="min-w-0 rounded-3xl border border-neutral-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-100">
                        <thead class="bg-neutral-50/80">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Gorsel
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Tarih
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    URL
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Sira
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Durum
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Olusturma
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Islemler
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            <tr v-if="loading">
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-neutral-500">
                                    Sliderlar yukleniyor...
                                </td>
                            </tr>
                            <tr v-else-if="!hasSlides">
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-neutral-500">
                                    Henuz slider eklenmedi. Yeni slider olusturabilirsiniz.
                                </td>
                            </tr>
                            <tr v-else v-for="slide in slides" :key="slide.id" class="hover:bg-neutral-50/60 transition">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-14 w-14 overflow-hidden rounded-2xl border border-neutral-200 bg-neutral-100">
                                            <img
                                                v-if="slide.image?.url || slide.image_url"
                                                :src="slide.image?.url || slide.image_url"
                                                :alt="slide.image?.original_name || 'Slider image'"
                                                class="h-full w-full object-cover"
                                            />
                                        </div>
                                        <div class="text-sm text-neutral-500">
                                            {{ slide.image?.original_name || 'Gorsel yok' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-sm text-secondary-700">
                                    {{ slide.display_date_label || '-' }}
                                </td>
                                <td class="px-6 py-5 text-sm text-secondary-700">
                                    <span class="block max-w-[220px] truncate">
                                        {{ slide.link_url || '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm text-secondary-700">
                                    {{ slide.sort_order }}
                                </td>
                                <td class="px-6 py-5 text-sm">
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="statusBadgeClass(slide.is_active)"
                                    >
                                        {{ slide.is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm text-secondary-700">
                                    {{ formatDate(slide.created_at) }}
                                </td>
                                <td class="px-6 py-5 text-right text-sm">
                                    <div class="inline-flex gap-3">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-800"
                                            @click="openEditForm(slide)"
                                        >
                                            Duzenle
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 text-rose-500 hover:text-rose-700"
                                            @click="requestDelete(slide)"
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
                        Toplam {{ meta.total }} kayit icinden sayfa {{ meta.current_page }}
                    </p>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            class="rounded-xl border border-neutral-200 px-3 py-1 text-sm text-neutral-600 hover:border-neutral-300"
                            :disabled="meta.current_page === 1"
                            @click="goToPage(meta.current_page - 1)"
                        >Onceki</button>
                        <button
                            type="button"
                            class="rounded-xl border border-neutral-200 px-3 py-1 text-sm text-neutral-600 hover:border-neutral-300"
                            :disabled="meta.current_page === meta.last_page"
                            @click="goToPage(meta.current_page + 1)"
                        >Sonraki</button>
                    </div>
                </footer>
            </div>

            <HeroSlideForm
                v-if="showForm"
                :mode="formMode"
                :slide="selectedSlide"
                :fetch-params="formFetchParams"
                @saved="handleSaved"
                @cancel="closeForm"
            />
            <div v-else class="hidden lg:block rounded-3xl border border-dashed border-neutral-200 bg-white/60 px-6 py-10 text-center text-sm text-neutral-500">
                Yeni slider eklemek icin "Yeni Slider" butonunu kullanin.
            </div>
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
                Slider Sil
            </template>
            <template #description>
                <p>"{{ slideToDelete?.display_date_label || 'Secili slider' }}" kaydini silmek istediginizden emin misiniz?</p>
                <p class="mt-2 text-xs text-neutral-400" v-if="deleteError">
                    {{ deleteError }}
                </p>
            </template>
        </ConfirmModal>
    </section>
</template>
