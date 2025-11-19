<script setup>
import { computed, ref } from 'vue';
import { RouterView, useRoute } from 'vue-router';
import AdminSidebar from './components/AdminSidebar.vue';
import AdminTopbar from './components/AdminTopbar.vue';

const isSidebarOpen = ref(false);
const route = useRoute();

const pageTitle = computed(() => route.meta.title ?? 'Kontrol Paneli');
const breadcrumb = computed(() => route.meta.breadcrumb ?? pageTitle.value);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const closeSidebar = () => {
    isSidebarOpen.value = false;
};
</script>

<template>
    <div class="min-h-screen bg-neutral-100 text-secondary-900">
        <AdminSidebar :open="isSidebarOpen" @close="closeSidebar" />

        <div class="relative min-h-screen lg:pl-72 transition-[padding] duration-300 ease-out">
            <AdminTopbar
                :page-title="pageTitle"
                :breadcrumb="breadcrumb"
                @toggle-sidebar="toggleSidebar"
            />

            <main class="px-4 py-6 sm:px-6 lg:px-10">
                <RouterView />
            </main>
        </div>
    </div>
</template>
