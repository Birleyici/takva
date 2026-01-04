<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import { useSiteSettingStore } from '../../stores/siteSettingStore';

const siteSettingStore = useSiteSettingStore();

const form = reactive({
    header_pattern_url: '',
    header_pattern_opacity: 20,
    header_pattern_placement: 'repeat',
    footer_pattern_url: '',
    footer_pattern_opacity: 20,
    footer_pattern_placement: 'repeat',
    home_articles_pattern_url: '',
    home_articles_pattern_opacity: 20,
    home_articles_pattern_placement: 'repeat',
    home_issues_pattern_url: '',
    home_issues_pattern_opacity: 20,
    home_issues_pattern_placement: 'repeat',
    home_videos_pattern_url: '',
    home_videos_pattern_opacity: 20,
    home_videos_pattern_placement: 'repeat',
});

const loading = ref(true);
const saving = ref(false);
const loadError = ref('');
const successMessage = ref('');

const headerPatternFile = ref(null);
const footerPatternFile = ref(null);
const homeArticlesPatternFile = ref(null);
const homeIssuesPatternFile = ref(null);
const homeVideosPatternFile = ref(null);
const removeHeaderPattern = ref(false);
const removeFooterPattern = ref(false);
const removeHomeArticlesPattern = ref(false);
const removeHomeIssuesPattern = ref(false);
const removeHomeVideosPattern = ref(false);

const headerPatternPreview = ref('');
const footerPatternPreview = ref('');
const homeArticlesPatternPreview = ref('');
const homeIssuesPatternPreview = ref('');
const homeVideosPatternPreview = ref('');
const headerPatternObjectUrl = ref('');
const footerPatternObjectUrl = ref('');
const homeArticlesPatternObjectUrl = ref('');
const homeIssuesPatternObjectUrl = ref('');
const homeVideosPatternObjectUrl = ref('');
const headerPatternInputRef = ref(null);
const footerPatternInputRef = ref(null);
const homeArticlesPatternInputRef = ref(null);
const homeIssuesPatternInputRef = ref(null);
const homeVideosPatternInputRef = ref(null);

const canRemoveHeaderPattern = computed(
    () => removeHeaderPattern.value || !!form.header_pattern_url || !!headerPatternFile.value
);
const canRemoveFooterPattern = computed(
    () => removeFooterPattern.value || !!form.footer_pattern_url || !!footerPatternFile.value
);
const canRemoveHomeArticlesPattern = computed(
    () => removeHomeArticlesPattern.value || !!form.home_articles_pattern_url || !!homeArticlesPatternFile.value
);
const canRemoveHomeIssuesPattern = computed(
    () => removeHomeIssuesPattern.value || !!form.home_issues_pattern_url || !!homeIssuesPatternFile.value
);
const canRemoveHomeVideosPattern = computed(
    () => removeHomeVideosPattern.value || !!form.home_videos_pattern_url || !!homeVideosPatternFile.value
);

const clampOpacity = (value, fallback = 20) => {
    const parsed = Number(value);
    if (!Number.isFinite(parsed)) {
        return fallback;
    }

    return Math.max(0, Math.min(100, Math.round(parsed)));
};

const hydrateFromSettings = (data) => {
const theme = data?.theme_settings ?? {};
const header = theme.header ?? {};
const footer = theme.footer ?? {};
const homeArticles = theme.home_articles ?? {};
const homeIssues = theme.home_issues ?? {};
const homeVideos = theme.home_videos ?? {};

    form.header_pattern_url = header.pattern_url ?? '';
    form.header_pattern_opacity = clampOpacity(header.opacity);
    form.header_pattern_placement = header.placement ?? 'repeat';
    form.footer_pattern_url = footer.pattern_url ?? '';
    form.footer_pattern_opacity = clampOpacity(footer.opacity);
    form.footer_pattern_placement = footer.placement ?? 'repeat';
    form.home_articles_pattern_url = homeArticles.pattern_url ?? '';
    form.home_articles_pattern_opacity = clampOpacity(homeArticles.opacity);
    form.home_articles_pattern_placement = homeArticles.placement ?? 'repeat';
    form.home_issues_pattern_url = homeIssues.pattern_url ?? '';
    form.home_issues_pattern_opacity = clampOpacity(homeIssues.opacity);
    form.home_issues_pattern_placement = homeIssues.placement ?? 'repeat';
    form.home_videos_pattern_url = homeVideos.pattern_url ?? '';
    form.home_videos_pattern_opacity = clampOpacity(homeVideos.opacity);
    form.home_videos_pattern_placement = homeVideos.placement ?? 'repeat';
};

function revokeHeaderPatternObjectUrl() {
    if (headerPatternObjectUrl.value) {
        URL.revokeObjectURL(headerPatternObjectUrl.value);
        headerPatternObjectUrl.value = '';
    }
}

function refreshHeaderPatternPreview() {
    revokeHeaderPatternObjectUrl();

    if (headerPatternFile.value) {
        headerPatternObjectUrl.value = URL.createObjectURL(headerPatternFile.value);
        headerPatternPreview.value = headerPatternObjectUrl.value;
        return;
    }

    if (!removeHeaderPattern.value && form.header_pattern_url) {
        headerPatternPreview.value = form.header_pattern_url;
        return;
    }

    headerPatternPreview.value = '';
}

function handleHeaderPatternChange(event) {
    const [file] = event.target.files ?? [];
    headerPatternFile.value = file || null;
    removeHeaderPattern.value = false;
    refreshHeaderPatternPreview();
}

function toggleRemoveHeaderPattern() {
    if (!canRemoveHeaderPattern.value && !removeHeaderPattern.value) {
        return;
    }

    if (removeHeaderPattern.value) {
        removeHeaderPattern.value = false;
    } else {
        removeHeaderPattern.value = true;
        headerPatternFile.value = null;
        if (headerPatternInputRef.value) {
            headerPatternInputRef.value.value = '';
        }
    }

    refreshHeaderPatternPreview();
}

function revokeFooterPatternObjectUrl() {
    if (footerPatternObjectUrl.value) {
        URL.revokeObjectURL(footerPatternObjectUrl.value);
        footerPatternObjectUrl.value = '';
    }
}

function refreshFooterPatternPreview() {
    revokeFooterPatternObjectUrl();

    if (footerPatternFile.value) {
        footerPatternObjectUrl.value = URL.createObjectURL(footerPatternFile.value);
        footerPatternPreview.value = footerPatternObjectUrl.value;
        return;
    }

    if (!removeFooterPattern.value && form.footer_pattern_url) {
        footerPatternPreview.value = form.footer_pattern_url;
        return;
    }

    footerPatternPreview.value = '';
}

function handleFooterPatternChange(event) {
    const [file] = event.target.files ?? [];
    footerPatternFile.value = file || null;
    removeFooterPattern.value = false;
    refreshFooterPatternPreview();
}

function toggleRemoveFooterPattern() {
    if (!canRemoveFooterPattern.value && !removeFooterPattern.value) {
        return;
    }

    if (removeFooterPattern.value) {
        removeFooterPattern.value = false;
    } else {
        removeFooterPattern.value = true;
        footerPatternFile.value = null;
        if (footerPatternInputRef.value) {
            footerPatternInputRef.value.value = '';
        }
    }

    refreshFooterPatternPreview();
}

function revokeHomeArticlesPatternObjectUrl() {
    if (homeArticlesPatternObjectUrl.value) {
        URL.revokeObjectURL(homeArticlesPatternObjectUrl.value);
        homeArticlesPatternObjectUrl.value = '';
    }
}

function refreshHomeArticlesPatternPreview() {
    revokeHomeArticlesPatternObjectUrl();

    if (homeArticlesPatternFile.value) {
        homeArticlesPatternObjectUrl.value = URL.createObjectURL(homeArticlesPatternFile.value);
        homeArticlesPatternPreview.value = homeArticlesPatternObjectUrl.value;
        return;
    }

    if (!removeHomeArticlesPattern.value && form.home_articles_pattern_url) {
        homeArticlesPatternPreview.value = form.home_articles_pattern_url;
        return;
    }

    homeArticlesPatternPreview.value = '';
}

function handleHomeArticlesPatternChange(event) {
    const [file] = event.target.files ?? [];
    homeArticlesPatternFile.value = file || null;
    removeHomeArticlesPattern.value = false;
    refreshHomeArticlesPatternPreview();
}

function toggleRemoveHomeArticlesPattern() {
    if (!canRemoveHomeArticlesPattern.value && !removeHomeArticlesPattern.value) {
        return;
    }

    if (removeHomeArticlesPattern.value) {
        removeHomeArticlesPattern.value = false;
    } else {
        removeHomeArticlesPattern.value = true;
        homeArticlesPatternFile.value = null;
        if (homeArticlesPatternInputRef.value) {
            homeArticlesPatternInputRef.value.value = '';
        }
    }

    refreshHomeArticlesPatternPreview();
}

function revokeHomeIssuesPatternObjectUrl() {
    if (homeIssuesPatternObjectUrl.value) {
        URL.revokeObjectURL(homeIssuesPatternObjectUrl.value);
        homeIssuesPatternObjectUrl.value = '';
    }
}

function refreshHomeIssuesPatternPreview() {
    revokeHomeIssuesPatternObjectUrl();

    if (homeIssuesPatternFile.value) {
        homeIssuesPatternObjectUrl.value = URL.createObjectURL(homeIssuesPatternFile.value);
        homeIssuesPatternPreview.value = homeIssuesPatternObjectUrl.value;
        return;
    }

    if (!removeHomeIssuesPattern.value && form.home_issues_pattern_url) {
        homeIssuesPatternPreview.value = form.home_issues_pattern_url;
        return;
    }

    homeIssuesPatternPreview.value = '';
}

function handleHomeIssuesPatternChange(event) {
    const [file] = event.target.files ?? [];
    homeIssuesPatternFile.value = file || null;
    removeHomeIssuesPattern.value = false;
    refreshHomeIssuesPatternPreview();
}

function toggleRemoveHomeIssuesPattern() {
    if (!canRemoveHomeIssuesPattern.value && !removeHomeIssuesPattern.value) {
        return;
    }

    if (removeHomeIssuesPattern.value) {
        removeHomeIssuesPattern.value = false;
    } else {
        removeHomeIssuesPattern.value = true;
        homeIssuesPatternFile.value = null;
        if (homeIssuesPatternInputRef.value) {
            homeIssuesPatternInputRef.value.value = '';
        }
    }

    refreshHomeIssuesPatternPreview();
}

function revokeHomeVideosPatternObjectUrl() {
    if (homeVideosPatternObjectUrl.value) {
        URL.revokeObjectURL(homeVideosPatternObjectUrl.value);
        homeVideosPatternObjectUrl.value = '';
    }
}

function refreshHomeVideosPatternPreview() {
    revokeHomeVideosPatternObjectUrl();

    if (homeVideosPatternFile.value) {
        homeVideosPatternObjectUrl.value = URL.createObjectURL(homeVideosPatternFile.value);
        homeVideosPatternPreview.value = homeVideosPatternObjectUrl.value;
        return;
    }

    if (!removeHomeVideosPattern.value && form.home_videos_pattern_url) {
        homeVideosPatternPreview.value = form.home_videos_pattern_url;
        return;
    }

    homeVideosPatternPreview.value = '';
}

function handleHomeVideosPatternChange(event) {
    const [file] = event.target.files ?? [];
    homeVideosPatternFile.value = file || null;
    removeHomeVideosPattern.value = false;
    refreshHomeVideosPatternPreview();
}

function toggleRemoveHomeVideosPattern() {
    if (!canRemoveHomeVideosPattern.value && !removeHomeVideosPattern.value) {
        return;
    }

    if (removeHomeVideosPattern.value) {
        removeHomeVideosPattern.value = false;
    } else {
        removeHomeVideosPattern.value = true;
        homeVideosPatternFile.value = null;
        if (homeVideosPatternInputRef.value) {
            homeVideosPatternInputRef.value.value = '';
        }
    }

    refreshHomeVideosPatternPreview();
}

onMounted(async () => {
    try {
        const data = await siteSettingStore.fetchSettings();
        hydrateFromSettings(data);
        removeHeaderPattern.value = false;
        headerPatternFile.value = null;
        refreshHeaderPatternPreview();
        removeFooterPattern.value = false;
        footerPatternFile.value = null;
        refreshFooterPatternPreview();
        removeHomeArticlesPattern.value = false;
        homeArticlesPatternFile.value = null;
        refreshHomeArticlesPatternPreview();
        removeHomeIssuesPattern.value = false;
        homeIssuesPatternFile.value = null;
        refreshHomeIssuesPatternPreview();
        removeHomeVideosPattern.value = false;
        homeVideosPatternFile.value = null;
        refreshHomeVideosPatternPreview();
    } catch (error) {
        loadError.value = siteSettingStore.error || 'Ayarlar yüklenemedi.';
    } finally {
        loading.value = false;
    }
});

onBeforeUnmount(() => {
    revokeHeaderPatternObjectUrl();
    revokeFooterPatternObjectUrl();
    revokeHomeArticlesPatternObjectUrl();
    revokeHomeIssuesPatternObjectUrl();
    revokeHomeVideosPatternObjectUrl();
});

async function handleSubmit() {
    saving.value = true;
    successMessage.value = '';
    loadError.value = '';

    try {
        const payload = {
            header_pattern_opacity: form.header_pattern_opacity ?? 0,
            header_pattern_placement: form.header_pattern_placement ?? 'repeat',
            footer_pattern_opacity: form.footer_pattern_opacity ?? 0,
            footer_pattern_placement: form.footer_pattern_placement ?? 'repeat',
            remove_header_pattern: removeHeaderPattern.value ? 1 : 0,
            remove_footer_pattern: removeFooterPattern.value ? 1 : 0,
            home_articles_pattern_opacity: form.home_articles_pattern_opacity ?? 0,
            home_articles_pattern_placement: form.home_articles_pattern_placement ?? 'repeat',
            home_issues_pattern_opacity: form.home_issues_pattern_opacity ?? 0,
            home_issues_pattern_placement: form.home_issues_pattern_placement ?? 'repeat',
            home_videos_pattern_opacity: form.home_videos_pattern_opacity ?? 0,
            home_videos_pattern_placement: form.home_videos_pattern_placement ?? 'repeat',
            remove_home_articles_pattern: removeHomeArticlesPattern.value ? 1 : 0,
            remove_home_issues_pattern: removeHomeIssuesPattern.value ? 1 : 0,
            remove_home_videos_pattern: removeHomeVideosPattern.value ? 1 : 0,
        };

        if (headerPatternFile.value) {
            payload.header_pattern = headerPatternFile.value;
        }

        if (footerPatternFile.value) {
            payload.footer_pattern = footerPatternFile.value;
        }

        if (homeArticlesPatternFile.value) {
            payload.home_articles_pattern = homeArticlesPatternFile.value;
        }

        if (homeIssuesPatternFile.value) {
            payload.home_issues_pattern = homeIssuesPatternFile.value;
        }

        if (homeVideosPatternFile.value) {
            payload.home_videos_pattern = homeVideosPatternFile.value;
        }

        const updated = await siteSettingStore.updateSettings(payload);
        if (updated) {
            hydrateFromSettings(updated);
        }

        successMessage.value = 'Tema ayarlari kaydedildi.';
        removeHeaderPattern.value = false;
        headerPatternFile.value = null;
        if (headerPatternInputRef.value) {
            headerPatternInputRef.value.value = '';
        }
        refreshHeaderPatternPreview();
        removeFooterPattern.value = false;
        footerPatternFile.value = null;
        if (footerPatternInputRef.value) {
            footerPatternInputRef.value.value = '';
        }
        refreshFooterPatternPreview();
        removeHomeArticlesPattern.value = false;
        homeArticlesPatternFile.value = null;
        if (homeArticlesPatternInputRef.value) {
            homeArticlesPatternInputRef.value.value = '';
        }
        refreshHomeArticlesPatternPreview();
        removeHomeIssuesPattern.value = false;
        homeIssuesPatternFile.value = null;
        if (homeIssuesPatternInputRef.value) {
            homeIssuesPatternInputRef.value.value = '';
        }
        refreshHomeIssuesPatternPreview();
        removeHomeVideosPattern.value = false;
        homeVideosPatternFile.value = null;
        if (homeVideosPatternInputRef.value) {
            homeVideosPatternInputRef.value.value = '';
        }
        refreshHomeVideosPatternPreview();
    } catch (error) {
        loadError.value = siteSettingStore.error || 'Tema ayarlari guncellenemedi.';
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <section class="space-y-6">
        <header class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <h1 class="text-2xl font-bold text-secondary-900">Tema Ayarlari</h1>
            <p class="mt-2 text-sm text-neutral-500">
                Header, footer ve ana sayfa baslik desenlerini buradan yonetebilirsiniz.
            </p>
        </header>

        <div v-if="loading" class="rounded-3xl border border-neutral-200 bg-white px-6 py-10 text-center text-sm text-neutral-500">
            Ayarlar yukleniyor...
        </div>
        <div v-else class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <div v-if="loadError" class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ loadError }}
            </div>
            <div v-if="successMessage" class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ successMessage }}
            </div>

            <form class="space-y-6" @submit.prevent="handleSubmit">
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-3xl border border-neutral-200 bg-neutral-50/40 p-5">
                        <h2 class="text-lg font-semibold text-secondary-900">Header Pattern Yukle</h2>
                        <p class="mt-1 text-xs text-neutral-500">
                            Ust menude gorunen desen gorselini buradan yukleyebilirsiniz.
                        </p>

                        <div class="mt-4 flex flex-col gap-4">
                            <div class="flex h-28 w-full items-center justify-center rounded-2xl border border-dashed border-neutral-200 bg-white p-3">
                                <img
                                    v-if="headerPatternPreview"
                                    :src="headerPatternPreview"
                                    alt="Header pattern on izlemesi"
                                    class="max-h-full max-w-full object-contain"
                                />
                                <span v-else class="text-center text-xs font-medium text-neutral-400">
                                    Header pattern yuklenmemis
                                </span>
                            </div>

                            <div class="flex flex-col gap-3 text-sm text-neutral-500">
                                <label
                                    class="inline-flex cursor-pointer items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 font-semibold text-secondary-900 shadow-sm transition hover:border-primary-300 hover:text-primary-600"
                                >
                                    <input
                                        ref="headerPatternInputRef"
                                        type="file"
                                        accept="image/*"
                                        class="sr-only"
                                        @change="handleHeaderPatternChange"
                                    />
                                    Yeni Pattern Yukle
                                </label>
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 font-semibold text-secondary-700 transition hover:border-rose-200 hover:text-rose-600 disabled:opacity-50"
                                    :disabled="!canRemoveHeaderPattern && !removeHeaderPattern"
                                    @click="toggleRemoveHeaderPattern"
                                >
                                    {{ removeHeaderPattern ? 'Kaldirmayi Iptal Et' : 'Patterni Kaldir' }}
                                </button>
                                <p class="text-xs text-neutral-400">PNG, JPG veya SVG formatinda maksimum 4MB.</p>
                                <p v-if="removeHeaderPattern" class="text-xs text-rose-500">
                                    Kaydettiginizde header pattern kaldirilacak.
                                </p>
                            </div>

                            <div class="mt-2">
                                <label class="block text-sm font-semibold text-neutral-700">Opacity</label>
                                <div class="mt-2 flex items-center gap-4">
                                    <input
                                        v-model.number="form.header_pattern_opacity"
                                        type="range"
                                        min="0"
                                        max="100"
                                        class="w-full accent-primary-500"
                                    />
                                    <span class="text-xs text-neutral-500 w-10 text-right">
                                        {{ clampOpacity(form.header_pattern_opacity) }}%
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-neutral-700">Yerlesim</label>
                                <select
                                    v-model="form.header_pattern_placement"
                                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                                >
                                    <option value="repeat">Repeat</option>
                                    <option value="cover">Cover</option>
                                    <option value="contain">Contain</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-neutral-200 bg-neutral-50/40 p-5">
                        <h2 class="text-lg font-semibold text-secondary-900">Footer Pattern Yukle</h2>
                        <p class="mt-1 text-xs text-neutral-500">
                            Footer arka planinda gorunen desen gorselini buradan yukleyebilirsiniz.
                        </p>

                        <div class="mt-4 flex flex-col gap-4">
                            <div class="flex h-28 w-full items-center justify-center rounded-2xl border border-dashed border-neutral-200 bg-white p-3">
                                <img
                                    v-if="footerPatternPreview"
                                    :src="footerPatternPreview"
                                    alt="Footer pattern on izlemesi"
                                    class="max-h-full max-w-full object-contain"
                                />
                                <span v-else class="text-center text-xs font-medium text-neutral-400">
                                    Footer pattern yuklenmemis
                                </span>
                            </div>

                            <div class="flex flex-col gap-3 text-sm text-neutral-500">
                                <label
                                    class="inline-flex cursor-pointer items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 font-semibold text-secondary-900 shadow-sm transition hover:border-primary-300 hover:text-primary-600"
                                >
                                    <input
                                        ref="footerPatternInputRef"
                                        type="file"
                                        accept="image/*"
                                        class="sr-only"
                                        @change="handleFooterPatternChange"
                                    />
                                    Yeni Pattern Yukle
                                </label>
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 font-semibold text-secondary-700 transition hover:border-rose-200 hover:text-rose-600 disabled:opacity-50"
                                    :disabled="!canRemoveFooterPattern && !removeFooterPattern"
                                    @click="toggleRemoveFooterPattern"
                                >
                                    {{ removeFooterPattern ? 'Kaldirmayi Iptal Et' : 'Patterni Kaldir' }}
                                </button>
                                <p class="text-xs text-neutral-400">PNG, JPG veya SVG formatinda maksimum 4MB.</p>
                                <p v-if="removeFooterPattern" class="text-xs text-rose-500">
                                    Kaydettiginizde footer pattern kaldirilacak.
                                </p>
                            </div>

                            <div class="mt-2">
                                <label class="block text-sm font-semibold text-neutral-700">Opacity</label>
                                <div class="mt-2 flex items-center gap-4">
                                    <input
                                        v-model.number="form.footer_pattern_opacity"
                                        type="range"
                                        min="0"
                                        max="100"
                                        class="w-full accent-primary-500"
                                    />
                                    <span class="text-xs text-neutral-500 w-10 text-right">
                                        {{ clampOpacity(form.footer_pattern_opacity) }}%
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-neutral-700">Yerlesim</label>
                                <select
                                    v-model="form.footer_pattern_placement"
                                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                                >
                                    <option value="repeat">Repeat</option>
                                    <option value="cover">Cover</option>
                                    <option value="contain">Contain</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-3xl border border-neutral-200 bg-neutral-50/40 p-5">
                        <h2 class="text-lg font-semibold text-secondary-900">Makaleler Baslik Patterni</h2>
                        <p class="mt-1 text-xs text-neutral-500">
                            Ana sayfa makaleler bolumundeki baslik ve aciklama alaninda gorunur.
                        </p>

                        <div class="mt-4 flex flex-col gap-4">
                            <div class="flex h-28 w-full items-center justify-center rounded-2xl border border-dashed border-neutral-200 bg-white p-3">
                                <img
                                    v-if="homeArticlesPatternPreview"
                                    :src="homeArticlesPatternPreview"
                                    alt="Makaleler pattern on izlemesi"
                                    class="max-h-full max-w-full object-contain"
                                />
                                <span v-else class="text-center text-xs font-medium text-neutral-400">
                                    Makaleler pattern yuklenmemis
                                </span>
                            </div>

                            <div class="flex flex-col gap-3 text-sm text-neutral-500">
                                <label
                                    class="inline-flex cursor-pointer items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 font-semibold text-secondary-900 shadow-sm transition hover:border-primary-300 hover:text-primary-600"
                                >
                                    <input
                                        ref="homeArticlesPatternInputRef"
                                        type="file"
                                        accept="image/*"
                                        class="sr-only"
                                        @change="handleHomeArticlesPatternChange"
                                    />
                                    Yeni Pattern Yukle
                                </label>
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 font-semibold text-secondary-700 transition hover:border-rose-200 hover:text-rose-600 disabled:opacity-50"
                                    :disabled="!canRemoveHomeArticlesPattern && !removeHomeArticlesPattern"
                                    @click="toggleRemoveHomeArticlesPattern"
                                >
                                    {{ removeHomeArticlesPattern ? 'Kaldirmayi Iptal Et' : 'Patterni Kaldir' }}
                                </button>
                                <p class="text-xs text-neutral-400">PNG, JPG veya SVG formatinda maksimum 4MB.</p>
                                <p v-if="removeHomeArticlesPattern" class="text-xs text-rose-500">
                                    Kaydettiginizde makaleler pattern kaldirilacak.
                                </p>
                            </div>

                            <div class="mt-2">
                                <label class="block text-sm font-semibold text-neutral-700">Opacity</label>
                                <div class="mt-2 flex items-center gap-4">
                                    <input
                                        v-model.number="form.home_articles_pattern_opacity"
                                        type="range"
                                        min="0"
                                        max="100"
                                        class="w-full accent-primary-500"
                                    />
                                    <span class="text-xs text-neutral-500 w-10 text-right">
                                        {{ clampOpacity(form.home_articles_pattern_opacity) }}%
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-neutral-700">Yerlesim</label>
                                <select
                                    v-model="form.home_articles_pattern_placement"
                                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                                >
                                    <option value="repeat">Repeat</option>
                                    <option value="cover">Cover</option>
                                    <option value="contain">Contain</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-neutral-200 bg-neutral-50/40 p-5">
                        <h2 class="text-lg font-semibold text-secondary-900">Son Sayilar Baslik Patterni</h2>
                        <p class="mt-1 text-xs text-neutral-500">
                            Ana sayfa son sayilar bolumundeki baslik ve aciklama alaninda gorunur.
                        </p>

                        <div class="mt-4 flex flex-col gap-4">
                            <div class="flex h-28 w-full items-center justify-center rounded-2xl border border-dashed border-neutral-200 bg-white p-3">
                                <img
                                    v-if="homeIssuesPatternPreview"
                                    :src="homeIssuesPatternPreview"
                                    alt="Son sayilar pattern on izlemesi"
                                    class="max-h-full max-w-full object-contain"
                                />
                                <span v-else class="text-center text-xs font-medium text-neutral-400">
                                    Son sayilar pattern yuklenmemis
                                </span>
                            </div>

                            <div class="flex flex-col gap-3 text-sm text-neutral-500">
                                <label
                                    class="inline-flex cursor-pointer items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 font-semibold text-secondary-900 shadow-sm transition hover:border-primary-300 hover:text-primary-600"
                                >
                                    <input
                                        ref="homeIssuesPatternInputRef"
                                        type="file"
                                        accept="image/*"
                                        class="sr-only"
                                        @change="handleHomeIssuesPatternChange"
                                    />
                                    Yeni Pattern Yukle
                                </label>
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 font-semibold text-secondary-700 transition hover:border-rose-200 hover:text-rose-600 disabled:opacity-50"
                                    :disabled="!canRemoveHomeIssuesPattern && !removeHomeIssuesPattern"
                                    @click="toggleRemoveHomeIssuesPattern"
                                >
                                    {{ removeHomeIssuesPattern ? 'Kaldirmayi Iptal Et' : 'Patterni Kaldir' }}
                                </button>
                                <p class="text-xs text-neutral-400">PNG, JPG veya SVG formatinda maksimum 4MB.</p>
                                <p v-if="removeHomeIssuesPattern" class="text-xs text-rose-500">
                                    Kaydettiginizde son sayilar pattern kaldirilacak.
                                </p>
                            </div>

                            <div class="mt-2">
                                <label class="block text-sm font-semibold text-neutral-700">Opacity</label>
                                <div class="mt-2 flex items-center gap-4">
                                    <input
                                        v-model.number="form.home_issues_pattern_opacity"
                                        type="range"
                                        min="0"
                                        max="100"
                                        class="w-full accent-primary-500"
                                    />
                                    <span class="text-xs text-neutral-500 w-10 text-right">
                                        {{ clampOpacity(form.home_issues_pattern_opacity) }}%
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-neutral-700">Yerlesim</label>
                                <select
                                    v-model="form.home_issues_pattern_placement"
                                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                                >
                                    <option value="repeat">Repeat</option>
                                    <option value="cover">Cover</option>
                                    <option value="contain">Contain</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-3xl border border-neutral-200 bg-neutral-50/40 p-5">
                        <h2 class="text-lg font-semibold text-secondary-900">Son Videolar Baslik Patterni</h2>
                        <p class="mt-1 text-xs text-neutral-500">
                            Ana sayfa son videolar bolumundeki baslik ve aciklama alaninda gorunur.
                        </p>

                        <div class="mt-4 flex flex-col gap-4">
                            <div class="flex h-28 w-full items-center justify-center rounded-2xl border border-dashed border-neutral-200 bg-white p-3">
                                <img
                                    v-if="homeVideosPatternPreview"
                                    :src="homeVideosPatternPreview"
                                    alt="Son videolar pattern on izlemesi"
                                    class="max-h-full max-w-full object-contain"
                                />
                                <span v-else class="text-center text-xs font-medium text-neutral-400">
                                    Son videolar pattern yuklenmemis
                                </span>
                            </div>

                            <div class="flex flex-col gap-3 text-sm text-neutral-500">
                                <label
                                    class="inline-flex cursor-pointer items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 font-semibold text-secondary-900 shadow-sm transition hover:border-primary-300 hover:text-primary-600"
                                >
                                    <input
                                        ref="homeVideosPatternInputRef"
                                        type="file"
                                        accept="image/*"
                                        class="sr-only"
                                        @change="handleHomeVideosPatternChange"
                                    />
                                    Yeni Pattern Yukle
                                </label>
                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 font-semibold text-secondary-700 transition hover:border-rose-200 hover:text-rose-600 disabled:opacity-50"
                                    :disabled="!canRemoveHomeVideosPattern && !removeHomeVideosPattern"
                                    @click="toggleRemoveHomeVideosPattern"
                                >
                                    {{ removeHomeVideosPattern ? 'Kaldirmayi Iptal Et' : 'Patterni Kaldir' }}
                                </button>
                                <p class="text-xs text-neutral-400">PNG, JPG veya SVG formatinda maksimum 4MB.</p>
                                <p v-if="removeHomeVideosPattern" class="text-xs text-rose-500">
                                    Kaydettiginizde son videolar pattern kaldirilacak.
                                </p>
                            </div>

                            <div class="mt-2">
                                <label class="block text-sm font-semibold text-neutral-700">Opacity</label>
                                <div class="mt-2 flex items-center gap-4">
                                    <input
                                        v-model.number="form.home_videos_pattern_opacity"
                                        type="range"
                                        min="0"
                                        max="100"
                                        class="w-full accent-primary-500"
                                    />
                                    <span class="text-xs text-neutral-500 w-10 text-right">
                                        {{ clampOpacity(form.home_videos_pattern_opacity) }}%
                                    </span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-neutral-700">Yerlesim</label>
                                <select
                                    v-model="form.home_videos_pattern_placement"
                                    class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                                >
                                    <option value="repeat">Repeat</option>
                                    <option value="cover">Cover</option>
                                    <option value="contain">Contain</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-primary-500 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-600 disabled:cursor-not-allowed disabled:opacity-60"
                        :disabled="saving"
                    >
                        Kaydet
                    </button>
                </div>
            </form>
        </div>
    </section>
</template>
