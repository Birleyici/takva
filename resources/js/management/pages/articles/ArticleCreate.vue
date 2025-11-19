<script setup>
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import ArticleForm from '../../components/articles/ArticleForm.vue';

const route = useRoute();
const router = useRouter();

const fetchParams = computed(() => ({
    page: route.query.page,
    search: route.query.search,
    category_id: route.query.category_id,
    author_id: route.query.author_id,
}));

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
            <h1 class="text-2xl font-bold text-secondary-900">Yeni Makale Oluştur</h1>
            <p class="mt-2 text-sm text-neutral-500">
                Başlık, içerik ve ilişkili bilgileri doldurarak yeni bir makale ekleyin.
            </p>
        </header>

        <div class="rounded-3xl border border-neutral-200 bg-white px-4 py-6 shadow-sm">
            <ArticleForm
                mode="create"
                :fetch-params="fetchParams"
                @saved="handleSaved"
                @cancel="handleCancel"
            />
        </div>
    </section>
</template>
