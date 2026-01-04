<script setup>
import { useRoute, useRouter } from 'vue-router';
import VideoForm from '../../components/videos/VideoForm.vue';

const router = useRouter();
const route = useRoute();

function handleSaved() {
    router.push({ name: 'management.videos', query: { ...route.query } });
}
</script>

<template>
    <section class="space-y-6">
        <header class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <h1 class="text-2xl font-bold text-secondary-900">Yeni Video</h1>
            <p class="mt-2 text-sm text-neutral-500">
                YouTube videosu ekleyin, başlık ve açıklama bilgilerini düzenleyin.
            </p>
        </header>

        <VideoForm mode="create" @saved="handleSaved" @cancel="handleSaved" />
    </section>
</template>
