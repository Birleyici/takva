<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useMenuPageStore } from '../../stores/menuPageStore';
import MenuPageForm from '../../components/menu-pages/MenuPageForm.vue';

const route = useRoute();
const router = useRouter();
const menuPageStore = useMenuPageStore();

const page = ref(null);
const loading = ref(true);
const loadError = ref('');

onMounted(async () => {
    const id = Number(route.params.page);

    if (!id) {
        loadError.value = 'Geçersiz sayfa bilgisi.';
        loading.value = false;
        return;
    }

    try {
        const data = await menuPageStore.fetchPage(id);
        page.value = data;
    } catch (error) {
        loadError.value = menuPageStore.error || 'Sayfa bulunamadı.';
    } finally {
        loading.value = false;
    }
});

function handleSaved() {
    router.push({ name: 'management.menu-pages', query: { ...route.query } });
}
</script>

<template>
    <section class="space-y-6">
        <header class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <h1 class="text-2xl font-bold text-secondary-900">Menü Sayfasını Düzenle</h1>
            <p class="mt-2 text-sm text-neutral-500">
                Sayfa içeriğini ve görünürlüğünü güncelleyin.
            </p>
        </header>

        <div v-if="loading" class="rounded-3xl border border-neutral-200 bg-white px-6 py-10 text-center text-sm text-neutral-500">
            Sayfa yükleniyor...
        </div>
        <div v-else-if="loadError" class="rounded-3xl border border-rose-200 bg-rose-50 px-6 py-6 text-rose-700">
            {{ loadError }}
        </div>
        <MenuPageForm
            v-else
            mode="edit"
            :page="page"
            @saved="handleSaved"
            @cancel="handleSaved"
        />
    </section>
</template>
