<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
    slides: {
        type: Array,
        default: () => [],
    },
    autoplay: {
        type: Boolean,
        default: true,
    },
    interval: {
        type: Number,
        default: 6500,
    },
});

const currentIndex = ref(0);
const timerId = ref(null);
const touchStartX = ref(0);
const touchStartY = ref(0);
const touchDeltaX = ref(0);
const touchDeltaY = ref(0);
const isTouching = ref(false);
const lastSwipeAt = ref(0);

const normalizedSlides = computed(() =>
    props.slides.filter((slide) => slide?.image_url),
);
const hasSlides = computed(() => normalizedSlides.value.length > 0);
const hasMultipleSlides = computed(() => normalizedSlides.value.length > 1);
const currentSlide = computed(() => normalizedSlides.value[currentIndex.value] || null);

const clampIndex = (index) => {
    const total = normalizedSlides.value.length;
    if (total === 0) {
        return 0;
    }

    if (index < 0) {
        return total - 1;
    }

    if (index >= total) {
        return 0;
    }

    return index;
};

const goTo = (index) => {
    currentIndex.value = clampIndex(index);
};

const next = () => {
    goTo(currentIndex.value + 1);
};

const prev = () => {
    goTo(currentIndex.value - 1);
};

const stopAutoplay = () => {
    if (timerId.value) {
        clearInterval(timerId.value);
        timerId.value = null;
    }
};

const startAutoplay = () => {
    stopAutoplay();

    if (!props.autoplay || !hasMultipleSlides.value) {
        return;
    }

    timerId.value = setInterval(() => {
        next();
    }, Math.max(2000, props.interval));
};

const handleTouchStart = (event) => {
    if (!hasMultipleSlides.value) {
        return;
    }

    const touch = event.touches?.[0];
    if (!touch) {
        return;
    }

    isTouching.value = true;
    touchStartX.value = touch.clientX;
    touchStartY.value = touch.clientY;
    touchDeltaX.value = 0;
    touchDeltaY.value = 0;
    stopAutoplay();
};

const handleTouchMove = (event) => {
    if (!isTouching.value) {
        return;
    }

    const touch = event.touches?.[0];
    if (!touch) {
        return;
    }

    touchDeltaX.value = touch.clientX - touchStartX.value;
    touchDeltaY.value = touch.clientY - touchStartY.value;
};

const handleTouchEnd = () => {
    if (!isTouching.value) {
        return;
    }

    const deltaX = touchDeltaX.value;
    const deltaY = touchDeltaY.value;
    const threshold = 50;
    let didSwipe = false;

    isTouching.value = false;

    if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > threshold) {
        if (deltaX > 0) {
            prev();
        } else {
            next();
        }

        didSwipe = true;
    }

    if (didSwipe) {
        lastSwipeAt.value = Date.now();
    }

    startAutoplay();
};

const handleLinkClick = (event) => {
    if (Date.now() - lastSwipeAt.value < 400) {
        event.preventDefault();
    }
};

watch(
    () => props.slides,
    () => {
        currentIndex.value = 0;
        startAutoplay();
    },
    { immediate: true },
);

onMounted(() => {
    startAutoplay();
});

onBeforeUnmount(() => {
    stopAutoplay();
});
</script>

<template>
    <div
        v-if="hasSlides"
        class="relative w-full overflow-hidden bg-neutral-900"
        @mouseenter="stopAutoplay"
        @mouseleave="startAutoplay"
        @touchstart.passive="handleTouchStart"
        @touchmove.passive="handleTouchMove"
        @touchend="handleTouchEnd"
        @touchcancel="handleTouchEnd"
    >
        <img
            v-if="currentSlide?.image_url"
            :src="currentSlide.image_url"
            :alt="currentSlide.date_label || 'Hero slide'"
            class="block w-full h-auto opacity-0 pointer-events-none select-none"
            aria-hidden="true"
            :loading="currentIndex === 0 ? 'eager' : 'lazy'"
        />
        <div class="absolute inset-0">
            <div
                v-for="(slide, index) in normalizedSlides"
                :key="slide.id ?? index"
                class="absolute inset-0 transition-opacity duration-700 ease-out"
                :class="index === currentIndex ? 'opacity-100' : 'opacity-0'"
            >
                <img
                    :src="slide.image_url"
                    :alt="slide.date_label || 'Hero slide'"
                    class="w-full h-auto"
                    :loading="index === 0 ? 'eager' : 'lazy'"
                />
                <a
                    v-if="slide.link_url"
                    :href="slide.link_url"
                    class="absolute inset-0 z-10"
                    :aria-label="slide.date_label ? `Slide ${slide.date_label}` : 'Slide link'"
                    @click="handleLinkClick"
                ></a>
                <div
                    v-if="slide.date_label"
                    class="absolute top-6 left-6 z-20 rounded-full bg-black/50 px-5 py-2 text-xs tracking-[0.3em] text-white backdrop-blur sm:left-12 sm:text-sm"
                >
                    {{ slide.date_label }}
                </div>
            </div>
        </div>

        <div
            v-if="hasMultipleSlides"
            class="absolute inset-x-0 bottom-6 z-20 flex items-center justify-between px-6 sm:px-12"
        >
            <button
                type="button"
                class="group inline-flex h-12 w-12 items-center justify-center rounded-full border border-white/40 bg-white/10 text-white transition hover:border-white hover:bg-white/20"
                aria-label="Previous slide"
                @click="prev"
            >
                <svg
                    class="h-5 w-5 transition group-hover:-translate-x-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <div class="flex items-center gap-2">
                <button
                    v-for="(slide, index) in normalizedSlides"
                    :key="slide.id ?? `dot-${index}`"
                    type="button"
                    class="h-2.5 w-2.5 rounded-full transition"
                    :class="index === currentIndex ? 'bg-white' : 'bg-white/40 hover:bg-white/70'"
                    :aria-label="`Go to slide ${index + 1}`"
                    @click="goTo(index)"
                ></button>
            </div>

            <button
                type="button"
                class="group inline-flex h-12 w-12 items-center justify-center rounded-full border border-white/40 bg-white/10 text-white transition hover:border-white hover:bg-white/20"
                aria-label="Next slide"
                @click="next"
            >
                <svg
                    class="h-5 w-5 transition group-hover:translate-x-0.5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>
</template>
