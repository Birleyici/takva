import { createApp } from 'vue';
import { createPinia } from 'pinia';
import axios from 'axios';
import AdminApp from './AdminApp.vue';
import router from './router';

const rootElement = document.getElementById('management-root');

if (rootElement) {
    const app = createApp(AdminApp);
    const pinia = createPinia();

    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    if (csrfToken) {
        axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
    }

    app.use(pinia);
    app.use(router);

    app.provide('axios', axios);

    app.mount(rootElement);
}
