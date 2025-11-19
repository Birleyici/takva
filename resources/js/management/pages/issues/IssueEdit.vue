<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useIssueStore } from '../../stores/issueStore';
import IssueForm from '../../components/issues/IssueForm.vue';

const route = useRoute();
const router = useRouter();
const issueStore = useIssueStore();

const issue = ref(null);
const loading = ref(true);
const loadError = ref('');

onMounted(async () => {
    const id = Number(route.params.issue);

    if (!id) {
        loadError.value = 'Geçersiz sayı bilgisi.';
        loading.value = false;
        return;
    }

    try {
        const data = await issueStore.fetchIssue(id);
        issue.value = data;
    } catch (error) {
        loadError.value = issueStore.error || 'Sayı bulunamadı.';
    } finally {
        loading.value = false;
    }
});

function handleSaved() {
    router.push({ name: 'management.issues', query: { ...route.query } });
}
</script>

<template>
    <section class="space-y-6">
        <header class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <h1 class="text-2xl font-bold text-secondary-900">Sayıyı Düzenle</h1>
            <p class="mt-2 text-sm text-neutral-500">
                Sayı bilgilerini güncelleyin, açıklama ve PDF dosyasını yönetin.
            </p>
        </header>

        <div v-if="loading" class="rounded-3xl border border-neutral-200 bg-white px-6 py-10 text-center text-sm text-neutral-500">
            Sayı yükleniyor...
        </div>
        <div v-else-if="loadError" class="rounded-3xl border border-rose-200 bg-rose-50 px-6 py-6 text-rose-700">
            {{ loadError }}
        </div>
        <IssueForm
            v-else
            mode="edit"
            :issue="issue"
            @saved="handleSaved"
            @cancel="handleSaved"
        />
    </section>
</template>
