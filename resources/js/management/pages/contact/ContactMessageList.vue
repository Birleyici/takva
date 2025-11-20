<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useContactMessageStore } from '../../stores/contactMessageStore';

const contactMessageStore = useContactMessageStore();
const { messages, meta, loading, error, selected, detailLoading } = storeToRefs(contactMessageStore);

const searchTerm = ref('');
const statusFilter = ref('all');
const searchTimeout = ref(null);
const deletingId = ref(null);

const statusOptions = [
    { label: 'Tümü', value: 'all' },
    { label: 'Okunmamış', value: 'unread' },
    { label: 'Okunan', value: 'read' },
];

const hasMessages = computed(() => messages.value.length > 0);
const selectedMessage = computed(() => selected.value);

function formatDate(value) {
    if (!value) {
        return '-';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return '-';
    }

    return date.toLocaleString('tr-TR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function statusLabel(message) {
    return message.is_read ? 'Okundu' : 'Okunmadı';
}

function statusClass(message) {
    return message.is_read
        ? 'bg-emerald-50 text-emerald-600 border border-emerald-100'
        : 'bg-amber-50 text-amber-600 border border-amber-100';
}

function statusQueryValue() {
    return statusFilter.value === 'all' ? undefined : statusFilter.value;
}

async function fetchMessages(page = 1) {
    await contactMessageStore.fetchMessages({
        page,
        search: searchTerm.value || undefined,
        status: statusQueryValue(),
    });
}

watch(
    () => statusFilter.value,
    () => {
        fetchMessages(1);
    },
);

watch(
    () => searchTerm.value,
    (value) => {
        if (searchTimeout.value) {
            clearTimeout(searchTimeout.value);
        }

        searchTimeout.value = setTimeout(() => {
            fetchMessages(1);
        }, 400);
    },
);

async function handleSelect(message) {
    await contactMessageStore.fetchMessage(message.id);
}

async function goToPage(page) {
    if (loading.value || page < 1 || page > meta.value.last_page || page === meta.value.current_page) {
        return;
    }

    await fetchMessages(page);
}

async function toggleReadStatus() {
    if (!selectedMessage.value) {
        return;
    }

    await contactMessageStore.updateStatus(selectedMessage.value.id, !selectedMessage.value.is_read);
}

async function handleDelete(message) {
    const confirmed = window.confirm(`'${message.subject || 'İsimsiz'}' başlıklı mesajı silmek istediğinize emin misiniz?`);

    if (!confirmed) {
        return;
    }

    deletingId.value = message.id;

    try {
        await contactMessageStore.deleteMessage(message.id);
        if (!messages.value.length && meta.value.current_page > 1) {
            await fetchMessages(meta.value.current_page - 1);
        }
    } finally {
        deletingId.value = null;
    }
}

onMounted(async () => {
    await fetchMessages(1);
});
</script>

<template>
    <section class="space-y-6">
        <header
            class="flex flex-col gap-4 rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1 class="text-2xl font-bold text-secondary-900">
                    İletişim Mesajları
                </h1>
                <p class="mt-2 text-sm text-neutral-500">
                    Ziyaretçiler tarafından gönderilen mesajları görüntüleyin, durumlarını güncelleyin ve gerektiğinde silin.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-neutral-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                        </svg>
                    </span>
                    <input
                        v-model="searchTerm"
                        type="search"
                        class="w-full rounded-xl border border-neutral-200 bg-white py-3 pl-10 pr-4 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200 sm:w-64"
                        placeholder="Mesaj ara..."
                    />
                </div>
                <select
                    v-model="statusFilter"
                    class="rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                >
                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                        {{ option.label }}
                    </option>
                </select>
            </div>
        </header>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <div class="rounded-3xl border border-neutral-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-neutral-100">
                        <thead class="bg-neutral-50/80">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Gönderen
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Konu
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Durum
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.14em] text-neutral-400">
                                    Tarih
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            <tr v-if="loading && !messages.length">
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-neutral-500">
                                    Mesajlar yükleniyor...
                                </td>
                            </tr>
                            <tr v-else-if="!loading && !hasMessages">
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-neutral-500">
                                    Henüz gönderilmiş bir mesaj bulunmuyor.
                                </td>
                            </tr>
                            <tr
                                v-else
                                v-for="message in messages"
                                :key="message.id"
                                class="cursor-pointer transition hover:bg-primary-50/40"
                                :class="selectedMessage?.id === message.id ? 'bg-primary-50/30' : ''"
                                @click="handleSelect(message)"
                            >
                                <td class="px-6 py-4 text-sm"
                                    :class="message.is_read ? 'text-secondary-700' : 'text-secondary-900 font-semibold'"
                                >
                                    <div>{{ message.name }}</div>
                                    <div class="text-xs text-neutral-400">
                                        {{ message.email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-secondary-700">
                                    <p class="font-medium text-secondary-900">{{ message.subject || 'Konu belirtilmemiş' }}</p>
                                    <p class="text-xs text-neutral-400 line-clamp-1">
                                        {{ message.message }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold" :class="statusClass(message)">
                                    {{ statusLabel(message) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-xs text-neutral-500">
                                    {{ formatDate(message.created_at) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <footer
                    class="flex flex-col gap-4 border-t border-neutral-100 px-6 py-4 text-sm text-neutral-500 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        Toplam {{ meta.total }} kayıt · Sayfa {{ meta.current_page }} / {{ meta.last_page }}
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-3 py-2 text-xs font-semibold text-neutral-600 transition hover:border-primary-200 hover:text-primary-600 disabled:opacity-40"
                            :disabled="meta.current_page <= 1 || loading"
                            @click="goToPage(meta.current_page - 1)"
                        >
                            Önceki
                        </button>
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-3 py-2 text-xs font-semibold text-neutral-600 transition hover:border-primary-200 hover:text-primary-600 disabled:opacity-40"
                            :disabled="meta.current_page >= meta.last_page || loading"
                            @click="goToPage(meta.current_page + 1)"
                        >
                            Sonraki
                        </button>
                    </div>
                </footer>
            </div>

            <div class="rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm">
                <div v-if="detailLoading" class="rounded-2xl border border-dashed border-neutral-200 bg-neutral-50/60 px-4 py-6 text-center text-sm text-neutral-500">
                    Mesaj yükleniyor...
                </div>
                <template v-else>
                    <div v-if="selectedMessage" class="space-y-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-semibold text-secondary-900">
                                    {{ selectedMessage.subject || 'Konu belirtilmemiş' }}
                                </h3>
                                <p class="mt-1 text-sm text-neutral-500">
                                    {{ formatDate(selectedMessage.created_at) }}
                                </p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold" :class="statusClass(selectedMessage)">
                                {{ statusLabel(selectedMessage) }}
                            </span>
                        </div>

                        <div class="rounded-2xl bg-neutral-50 px-4 py-3 text-sm text-secondary-700">
                            <p class="font-semibold text-secondary-900">Gönderen</p>
                            <p>{{ selectedMessage.name }}</p>
                            <a :href="`mailto:${selectedMessage.email}`" class="text-primary-600 hover:text-primary-700">
                                {{ selectedMessage.email }}
                            </a>
                        </div>

                        <div class="rounded-2xl border border-neutral-100 bg-white px-4 py-3 text-sm text-secondary-800 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-neutral-400">
                                Mesaj
                            </p>
                            <p class="mt-3 whitespace-pre-line">
                                {{ selectedMessage.message }}
                            </p>
                        </div>

                        <div class="flex flex-col gap-2 text-xs text-neutral-400">
                            <span>IP Adresi: {{ selectedMessage.ip_address || '-' }}</span>
                            <span>Okunma Tarihi: {{ selectedMessage.read_at ? formatDate(selectedMessage.read_at) : '-' }}</span>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-700 transition hover:border-primary-200 hover:text-primary-600"
                                @click="toggleReadStatus"
                            >
                                {{ selectedMessage.is_read ? 'Okunmadı Olarak İşaretle' : 'Okundu Olarak İşaretle' }}
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-rose-200 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50"
                                :disabled="deletingId === selectedMessage.id"
                                @click="handleDelete(selectedMessage)"
                            >
                                Sil
                            </button>
                        </div>
                    </div>
                    <div v-else class="flex h-full flex-col items-center justify-center gap-3 text-center text-sm text-neutral-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-neutral-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 4.26a2 2 0 0 0 2.22 0L21 8M5 19h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2z" />
                        </svg>
                        <p>Bir mesaj seçerek detaylarını görüntüleyebilirsiniz.</p>
                    </div>
                </template>
            </div>
        </div>

        <p v-if="error" class="text-sm text-rose-600">
            {{ error }}
        </p>
    </section>
</template>
