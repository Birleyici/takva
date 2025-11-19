<script setup>
import { computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: 'Onay',
    },
    message: {
        type: String,
        default: '',
    },
    confirmLabel: {
        type: String,
        default: 'Onayla',
    },
    cancelLabel: {
        type: String,
        default: 'Vazgeç',
    },
    kind: {
        type: String,
        default: 'danger', // danger | primary | neutral
    },
    processing: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['confirm', 'cancel']);

const confirmButtonClass = computed(() => {
    switch (props.kind) {
        case 'primary':
            return 'bg-primary-500 hover:bg-primary-600 text-white border border-primary-500';
        case 'neutral':
            return 'bg-white border border-neutral-200 text-neutral-700 hover:border-neutral-300 hover:text-neutral-900';
        default:
            return 'bg-rose-500 hover:bg-rose-600 text-white border border-rose-500';
    }
});

const onKeydown = (event) => {
    if (!props.open) return;

    if (event.key === 'Escape') {
        emit('cancel');
    }
};

watch(
    () => props.open,
    (value) => {
        document.body.style.overflow = value ? 'hidden' : '';
    },
    { immediate: true },
);

onMounted(() => {
    window.addEventListener('keydown', onKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', onKeydown);
    document.body.style.overflow = '';
});
</script>

<template>
    <transition enter-active-class="duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-neutral-900/60 backdrop-blur-sm"
            @click.self="emit('cancel')"
        >
            <transition enter-active-class="duration-200 ease-out"
                enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                enter-to-class="opacity-100 translate-y-0 sm:scale-100" leave-active-class="duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div
                    class="mx-4 w-full max-w-lg rounded-3xl border border-neutral-200 bg-white shadow-2xl ring-1 ring-black/5">
                    <div class="flex items-center justify-between border-b border-neutral-100 px-6 py-5">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-neutral-400">
                                Onay Gerekiyor
                            </p>
                            <h2 class="mt-1 text-xl font-semibold text-secondary-900">
                                {{ title }}
                            </h2>
                        </div>
                        <button type="button"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-neutral-200 text-neutral-400 transition hover:border-neutral-300 hover:text-neutral-600"
                            @click="emit('cancel')">
                            <span class="sr-only">Kapat</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-6">
                        <p class="text-sm leading-relaxed text-neutral-600" v-html="message"></p>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-neutral-100 px-6 py-4">
                        <button type="button"
                            class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 text-sm font-semibold text-neutral-600 transition hover:border-neutral-300 hover:text-neutral-800 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="processing"
                            @click="emit('cancel')">
                            {{ cancelLabel }}
                        </button>
                        <button type="button"
                            :class="['inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white disabled:cursor-not-allowed disabled:opacity-60', confirmButtonClass]"
                            :disabled="processing"
                            @click="emit('confirm')">
                            {{ confirmLabel }}
                        </button>
                    </div>
                </div>
            </transition>
        </div>
    </transition>
</template>
