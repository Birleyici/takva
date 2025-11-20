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

const initMobileMenu = () => {
    const triggers = document.querySelectorAll('[data-mobile-menu-trigger]');
    const overlay = document.querySelector('[data-mobile-menu-overlay]');
    const panel = document.querySelector('[data-mobile-menu-panel]');
    const closers = document.querySelectorAll('[data-mobile-menu-close]');

    if (!triggers.length || !overlay || !panel) {
        return;
    }

    const openMenu = () => {
        overlay.classList.remove('hidden');
        panel.classList.remove('translate-x-full');
        document.body.classList.add('overflow-hidden');
    };

    const closeMenu = () => {
        overlay.classList.add('hidden');
        panel.classList.add('translate-x-full');
        document.body.classList.remove('overflow-hidden');
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', openMenu);
    });

    overlay.addEventListener('click', closeMenu);
    closers.forEach((button) => button.addEventListener('click', closeMenu));

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            closeMenu();
        }
    });
};

const initializeSite = () => {
    mountSearchComponents();
    initMobileMenu();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSite);
} else {
    initializeSite();
}
