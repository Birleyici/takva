import './bootstrap';
import { createApp } from 'vue';
import HeroSlider from './Components/HeroSlider.vue';
import SearchModal from './Components/SearchModal.vue';

const mountHeroSliders = () => {
    const nodes = document.querySelectorAll('[data-hero-slider]');

    nodes.forEach((node) => {
        if (node.__vue_app__) {
            return;
        }

        const slidesRaw = node.getAttribute('data-hero-slides') || '[]';
        let slides = [];

        try {
            slides = JSON.parse(slidesRaw);
        } catch (error) {
            slides = [];
        }

        if (!Array.isArray(slides) || slides.length === 0) {
            return;
        }

        const autoplayAttr = node.getAttribute('data-hero-slider-autoplay');
        const autoplay = autoplayAttr !== 'false';
        const intervalAttr = Number.parseInt(
            node.getAttribute('data-hero-slider-interval') || '6500',
            10,
        );
        const interval = Number.isNaN(intervalAttr) ? 6500 : intervalAttr;

        const app = createApp(HeroSlider, {
            slides,
            autoplay,
            interval,
        });
        node.__vue_app__ = app;
        app.mount(node);
    });
};

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
    mountHeroSliders();
    mountSearchComponents();
    initMobileMenu();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSite);
} else {
    initializeSite();
}
