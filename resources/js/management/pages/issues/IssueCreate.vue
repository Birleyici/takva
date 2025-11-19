<script setup>
import { useRoute, useRouter } from 'vue-router';
import IssueForm from '../../components/issues/IssueForm.vue';

const router = useRouter();
const route = useRoute();

function handleSaved() {
    router.push({ name: 'management.issues', query: { ...route.query } });
}
</script>

<template>
    <section class="space-y-6">
        <header class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <h1 class="text-2xl font-bold text-secondary-900">Yeni Sayı</h1>
            <p class="mt-2 text-sm text-neutral-500">
                Arşive yeni bir sayı ekleyin, açıklamasını yazın ve PDF dosyasını yükleyin.
            </p>
        </header>

        <IssueForm mode="create" @saved="handleSaved" @cancel="handleSaved" />
    </section>
</template>
