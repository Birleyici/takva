<script setup>
import { useRoute, useRouter } from 'vue-router';
import MenuPageForm from '../../components/menu-pages/MenuPageForm.vue';

const router = useRouter();
const route = useRoute();

function handleSaved() {
    router.push({ name: 'management.menu-pages', query: { ...route.query } });
}
</script>

<template>
    <section class="space-y-6">
        <header class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <h1 class="text-2xl font-bold text-secondary-900">Yeni Menü Sayfası</h1>
            <p class="mt-2 text-sm text-neutral-500">
                Menünüze yeni bir özel sayfa ekleyin ve içeriğini düzenleyin.
            </p>
        </header>

        <MenuPageForm mode="create" @saved="handleSaved" @cancel="handleSaved" />
    </section>
</template>
