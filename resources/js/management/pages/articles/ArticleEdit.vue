<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ArticleForm from '../../components/articles/ArticleForm.vue';
import { useArticleStore } from '../../stores/articleStore';

const route = useRoute();
const router = useRouter();
const articleStore = useArticleStore();

const article = ref(null);
const loading = ref(true);
const loadError = ref('');

const fetchParams = computed(() => ({
    page: route.query.page,
    search: route.query.search,
    category_id: route.query.category_id,
    author_id: route.query.author_id,
}));

const loadArticle = async () => {
    const id = Number(route.params.article);

    if (!id) {
        loadError.value = 'Makaleye erişilemedi.';
        loading.value = false;
        return;
    }

    try {
        const data = await articleStore.fetchArticle(id);
        article.value = data;
    } catch (error) {
        loadError.value = articleStore.error || 'Makale bulunamadı.';
    } finally {
        loading.value = false;
    }
};

onMounted(loadArticle);

const redirectToList = () => {
    router.push({ name: 'management.articles', query: { ...route.query } });
};

const handleSaved = () => {
    redirectToList();
};

const handleCancel = () => {
    redirectToList();
};
</script>

<template>
    <section class="space-y-6">
        <header class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <h1 class="text-2xl font-bold text-secondary-900">Makaleyi Düzenle</h1>
            <p class="mt-2 text-sm text-neutral-500">
                Seçtiğiniz makalenin içeriğini ve ilişkili bilgilerini güncelleyin.
            </p>
        </header>

        <div class="rounded-3xl border border-neutral-200 bg-white px-4 py-6 shadow-sm">
            <div v-if="loading" class="py-20 text-center text-sm text-neutral-500">
                Makale yükleniyor...
            </div>
            <div v-else-if="loadError" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-600">
                {{ loadError }}
            </div>
            <ArticleForm
                v-else
                mode="edit"
                :article="article"
                :fetch-params="fetchParams"
                @saved="handleSaved"
                @cancel="handleCancel"
            />
        </div>
    </section>
</template>
