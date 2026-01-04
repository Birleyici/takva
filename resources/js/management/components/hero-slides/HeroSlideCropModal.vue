<script setup>
import { computed, onBeforeUnmount, reactive, ref, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useMediaStore } from '../../stores/mediaStore';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    aspectRatio: {
        type: Number,
        default: 16 / 9,
    },
    ratioLabel: {
        type: String,
        default: '16:9',
    },
    outputWidth: {
        type: Number,
        default: 1920,
    },
});

const emit = defineEmits(['close', 'select']);

const mediaStore = useMediaStore();
const { uploading } = storeToRefs(mediaStore);

const canvasRef = ref(null);
const fileInputRef = ref(null);
const imageElement = ref(null);
const imageUrl = ref('');
const localError = ref('');
const isDragging = ref(false);
const pointerId = ref(null);
const zoomValue = ref(100);
const startPoint = reactive({
    x: 0,
    y: 0,
    offsetX: 0,
    offsetY: 0,
});

const state = reactive({
    scale: 1,
    minScale: 1,
    offsetX: 0,
    offsetY: 0,
});

const outputWidth = computed(() => props.outputWidth);
const outputHeight = computed(() =>
    Math.round(props.outputWidth / props.aspectRatio),
);

const hasImage = computed(() => Boolean(imageElement.value));
const canCrop = computed(() => hasImage.value && !uploading.value);

const cleanupImageUrl = () => {
    if (imageUrl.value) {
        URL.revokeObjectURL(imageUrl.value);
        imageUrl.value = '';
    }
};

const resetState = () => {
    localError.value = '';
    imageElement.value = null;
    zoomValue.value = 100;
    state.scale = 1;
    state.minScale = 1;
    state.offsetX = 0;
    state.offsetY = 0;
    isDragging.value = false;
    pointerId.value = null;
};

const closeModal = () => {
    emit('close');
};

const drawCanvas = () => {
    const canvas = canvasRef.value;
    const image = imageElement.value;

    if (!canvas || !image) {
        return;
    }

    const ctx = canvas.getContext('2d');
    if (!ctx) {
        return;
    }

    canvas.width = props.outputWidth;
    canvas.height = outputHeight.value;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.imageSmoothingEnabled = true;
    ctx.imageSmoothingQuality = 'high';

    const drawWidth = image.naturalWidth * state.scale;
    const drawHeight = image.naturalHeight * state.scale;
    ctx.drawImage(
        image,
        state.offsetX,
        state.offsetY,
        drawWidth,
        drawHeight,
    );
};

const clampOffsets = () => {
    const canvas = canvasRef.value;
    const image = imageElement.value;

    if (!canvas || !image) {
        return;
    }

    const maxOffsetX = 0;
    const maxOffsetY = 0;
    const minOffsetX = canvas.width - image.naturalWidth * state.scale;
    const minOffsetY = canvas.height - image.naturalHeight * state.scale;

    state.offsetX = Math.min(maxOffsetX, Math.max(state.offsetX, minOffsetX));
    state.offsetY = Math.min(maxOffsetY, Math.max(state.offsetY, minOffsetY));
};

const setScale = (nextScale) => {
    const image = imageElement.value;
    const canvas = canvasRef.value;

    if (!image || !canvas) {
        return;
    }

    const prevScale = state.scale;
    state.scale = Math.max(state.minScale, nextScale);

    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;

    const imageCenterX = (centerX - state.offsetX) / prevScale;
    const imageCenterY = (centerY - state.offsetY) / prevScale;

    state.offsetX = centerX - imageCenterX * state.scale;
    state.offsetY = centerY - imageCenterY * state.scale;

    clampOffsets();
    drawCanvas();
};

const initCrop = () => {
    const image = imageElement.value;
    if (!image) {
        return;
    }

    const canvas = canvasRef.value;
    if (!canvas) {
        return;
    }

    canvas.width = props.outputWidth;
    canvas.height = outputHeight.value;

    const scaleX = canvas.width / image.naturalWidth;
    const scaleY = canvas.height / image.naturalHeight;
    state.minScale = Math.max(scaleX, scaleY);
    state.scale = state.minScale;

    state.offsetX = (canvas.width - image.naturalWidth * state.scale) / 2;
    state.offsetY = (canvas.height - image.naturalHeight * state.scale) / 2;
    zoomValue.value = 100;

    clampOffsets();
    drawCanvas();
};

const handleFileSelect = (event) => {
    const [file] = event.target.files || [];
    if (!file) {
        return;
    }

    if (!file.type.startsWith('image/')) {
        localError.value = 'Sadece gorsel dosyalari yuklenebilir.';
        if (fileInputRef.value) {
            fileInputRef.value.value = '';
        }
        return;
    }

    localError.value = '';
    cleanupImageUrl();

    const url = URL.createObjectURL(file);
    imageUrl.value = url;

    const img = new Image();
    img.onload = () => {
        imageElement.value = img;
        initCrop();
    };
    img.onerror = () => {
        localError.value = 'Gorsel yuklenemedi.';
        cleanupImageUrl();
    };
    img.src = url;

    if (fileInputRef.value) {
        fileInputRef.value.value = '';
    }
};

watch(zoomValue, (value) => {
    if (!hasImage.value) {
        return;
    }

    const nextScale = state.minScale * (value / 100);
    setScale(nextScale);
});

const handlePointerDown = (event) => {
    if (!hasImage.value) {
        return;
    }

    if (event.pointerType === 'mouse' && event.button !== 0) {
        return;
    }

    isDragging.value = true;
    pointerId.value = event.pointerId;
    startPoint.x = event.clientX;
    startPoint.y = event.clientY;
    startPoint.offsetX = state.offsetX;
    startPoint.offsetY = state.offsetY;

    event.currentTarget.setPointerCapture(pointerId.value);
};

const handlePointerMove = (event) => {
    if (!isDragging.value || pointerId.value !== event.pointerId) {
        return;
    }

    const canvas = canvasRef.value;
    if (!canvas) {
        return;
    }

    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;

    const deltaX = (event.clientX - startPoint.x) * scaleX;
    const deltaY = (event.clientY - startPoint.y) * scaleY;

    state.offsetX = startPoint.offsetX + deltaX;
    state.offsetY = startPoint.offsetY + deltaY;

    clampOffsets();
    drawCanvas();
};

const handlePointerUp = (event) => {
    if (pointerId.value !== event.pointerId) {
        return;
    }

    isDragging.value = false;
    event.currentTarget.releasePointerCapture(pointerId.value);
    pointerId.value = null;
};

const handleCrop = async () => {
    const canvas = canvasRef.value;
    if (!canvas) {
        localError.value = 'Gorsel kirpilamadi.';
        return;
    }

    localError.value = '';

    try {
        const blob = await new Promise((resolve) => {
            canvas.toBlob((result) => resolve(result), 'image/jpeg', 0.9);
        });

        if (!blob) {
            localError.value = 'Gorsel kirpilamadi.';
            return;
        }

        const file = new File([blob], `hero-slide-${Date.now()}.jpg`, {
            type: 'image/jpeg',
        });

        const media = await mediaStore.uploadMedia(file);
        emit('select', media);
        emit('close');
    } catch (error) {
        localError.value = mediaStore.error || 'Gorsel yuklenemedi.';
    }
};

watch(
    () => props.open,
    (open) => {
        if (open) {
            resetState();
            localError.value = '';
            const canvas = canvasRef.value;
            if (canvas) {
                canvas.width = props.outputWidth;
                canvas.height = outputHeight.value;
            }
        } else {
            cleanupImageUrl();
            resetState();
        }
    },
);

onBeforeUnmount(() => {
    cleanupImageUrl();
});
</script>

<template>
    <transition enter-active-class="duration-200 ease-out" enter-from-class="opacity-0"
        enter-to-class="opacity-100" leave-active-class="duration-150 ease-in" leave-from-class="opacity-100"
        leave-to-class="opacity-0">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-neutral-900/70 backdrop-blur-sm px-4 py-8"
            @click.self="closeModal"
        >
            <div
                class="relative flex h-full w-full max-w-6xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl max-h-[calc(100vh-2rem)] sm:max-h-[calc(100vh-4rem)]"
            >
                <header class="flex items-center justify-between border-b border-neutral-100 px-6 py-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-neutral-400">
                            Slider Gorsel Kirpma
                        </p>
                        <h2 class="mt-1 text-xl font-semibold text-secondary-900">
                            Zorunlu Oran: {{ ratioLabel }}
                        </h2>
                    </div>
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-neutral-200 text-neutral-400 transition hover:border-neutral-300 hover:text-neutral-600"
                        @click="closeModal"
                    >
                        <span class="sr-only">Kapat</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </header>

                <div class="flex flex-1 flex-col gap-6 overflow-hidden px-6 py-6 lg:flex-row">
                    <div class="flex-1 rounded-3xl border border-neutral-200 bg-neutral-900/5 p-4">
                        <div class="relative flex items-center justify-center">
                            <canvas
                                ref="canvasRef"
                                class="w-full max-w-4xl rounded-2xl bg-neutral-900/10 touch-none select-none"
                                :class="hasImage ? 'cursor-grab active:cursor-grabbing' : ''"
                                @pointerdown="handlePointerDown"
                                @pointermove="handlePointerMove"
                                @pointerup="handlePointerUp"
                                @pointercancel="handlePointerUp"
                            ></canvas>
                            <div
                                v-if="!hasImage"
                                class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-sm text-neutral-400"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 16v-4m0 0V8m0 4h4m-4 0H8M4 19V5a2 2 0 0 1 2-2h6.586a2 2 0 0 1 1.414.586l5.414 5.414A2 2 0 0 1 20 10.414V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z" />
                                </svg>
                                Gorsel yukleyin ve kirpma alanini ayarlayin.
                            </div>
                        </div>
                    </div>

                    <div class="w-full lg:w-80 space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-neutral-700">
                                Gorsel Sec
                            </label>
                            <label
                                class="mt-2 inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-primary-200 bg-primary-50 px-4 py-3 text-sm font-semibold text-primary-700 transition hover:border-primary-300 hover:bg-primary-100"
                            >
                                Dosya Yukle
                                <input
                                    ref="fileInputRef"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    :disabled="uploading"
                                    @change="handleFileSelect"
                                />
                            </label>
                            <p class="mt-2 text-xs text-neutral-400">
                                Onerilen boyut: {{ outputWidth }}x{{ outputHeight }} ({{ ratioLabel }}).
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-neutral-700">
                                Yakinlastirma
                            </label>
                            <input
                                type="range"
                                min="100"
                                max="240"
                                step="1"
                                class="mt-2 w-full accent-primary-500"
                                :disabled="!hasImage"
                                v-model="zoomValue"
                            />
                        </div>

                        <div class="rounded-2xl border border-neutral-200 bg-neutral-50 px-4 py-3 text-xs text-neutral-500">
                            Kirpma alani sabit oranlidir. Gorseli surukleyerek konumlandirin.
                        </div>

                        <p v-if="localError" class="text-sm text-rose-600">
                            {{ localError }}
                        </p>

                        <button
                            type="button"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-primary-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600 disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="!canCrop"
                            @click="handleCrop"
                        >
                            Kirp ve Kullan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>
