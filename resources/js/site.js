import './bootstrap';
import { createApp } from 'vue';
import SearchModal from './Components/SearchModal.vue';

const mountSearchComponents = () => {
    const nodes = document.querySelectorAll('[data-search-component]');

    nodes.forEach((node) => {
        if (node.__vue_app__) {
            return;
        }

        const endpoint = node.getAttribute('data-search-endpoint') || '/api/search';
        const pageUrl = node.getAttribute('data-search-page') || '/ara';
        const app = createApp(SearchModal, { endpoint, pageUrl });
        node.__vue_app__ = app;
        app.mount(node);
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mountSearchComponents);
} else {
    mountSearchComponents();
}
