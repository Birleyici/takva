<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useVideoStore } from '../../stores/videoStore';
import VideoForm from '../../components/videos/VideoForm.vue';

const route = useRoute();
const router = useRouter();
const videoStore = useVideoStore();

const video = ref(null);
const loading = ref(true);
const loadError = ref('');

onMounted(async () => {
    const id = Number(route.params.video);

    if (!id) {
        loadError.value = 'Geçersiz video bilgisi.';
        loading.value = false;
        return;
    }

    try {
        const data = await videoStore.fetchVideo(id);
        video.value = data;
    } catch (error) {
        loadError.value = videoStore.error || 'Video bulunamadı.';
    } finally {
        loading.value = false;
    }
});

function handleSaved() {
    router.push({ name: 'management.videos', query: { ...route.query } });
}
</script>

<template>
    <section class="space-y-6">
        <header class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <h1 class="text-2xl font-bold text-secondary-900">Videoyu Düzenle</h1>
            <p class="mt-2 text-sm text-neutral-500">
                Video bilgilerini güncelleyin ve yayın durumunu düzenleyin.
            </p>
        </header>

        <div v-if="loading" class="rounded-3xl border border-neutral-200 bg-white px-6 py-10 text-center text-sm text-neutral-500">
            Video yükleniyor...
        </div>
        <div v-else-if="loadError" class="rounded-3xl border border-rose-200 bg-rose-50 px-6 py-6 text-rose-700">
            {{ loadError }}
        </div>
        <VideoForm
            v-else
            mode="edit"
            :video="video"
            @saved="handleSaved"
            @cancel="handleSaved"
        />
    </section>
</template>
