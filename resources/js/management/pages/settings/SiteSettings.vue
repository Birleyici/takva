<script setup>
import { computed, onBeforeUnmount, onMounted, reactive, ref } from 'vue';
import { useSiteSettingStore } from '../../stores/siteSettingStore';

const siteSettingStore = useSiteSettingStore();

const form = reactive({
    contact_email: '',
    contact_phone: '',
    contact_address: '',
    contact_map_embed: '',
    contact_hero_text: '',
    logo_url: '',
    social_twitter: '',
    social_instagram: '',
    social_youtube: '',
    social_facebook: '',
    social_whatsapp: '',
});

const loading = ref(true);
const saving = ref(false);
const loadError = ref('');
const successMessage = ref('');
const logoFile = ref(null);
const removeLogo = ref(false);
const logoPreview = ref('');
const logoObjectUrl = ref('');
const logoInputRef = ref(null);

const canRemoveLogo = computed(() => removeLogo.value || !!form.logo_url || !!logoFile.value);

function revokeLogoObjectUrl() {
    if (logoObjectUrl.value) {
        URL.revokeObjectURL(logoObjectUrl.value);
        logoObjectUrl.value = '';
    }
}

function refreshLogoPreview() {
    revokeLogoObjectUrl();

    if (logoFile.value) {
        logoObjectUrl.value = URL.createObjectURL(logoFile.value);
        logoPreview.value = logoObjectUrl.value;
        return;
    }

    if (!removeLogo.value && form.logo_url) {
        logoPreview.value = form.logo_url;
        return;
    }

    logoPreview.value = '';
}

function handleLogoChange(event) {
    const [file] = event.target.files ?? [];
    logoFile.value = file || null;
    removeLogo.value = false;
    refreshLogoPreview();
}

function toggleRemoveLogo() {
    if (!canRemoveLogo.value && !removeLogo.value) {
        return;
    }

    if (removeLogo.value) {
        removeLogo.value = false;
    } else {
        removeLogo.value = true;
        logoFile.value = null;
        if (logoInputRef.value) {
            logoInputRef.value.value = '';
        }
    }

    refreshLogoPreview();
}

onMounted(async () => {
    try {
        const data = await siteSettingStore.fetchSettings();
        Object.assign(form, data ?? {});
        removeLogo.value = false;
        logoFile.value = null;
        refreshLogoPreview();
    } catch (error) {
        loadError.value = siteSettingStore.error || 'Ayarlar yüklenemedi.';
    } finally {
        loading.value = false;
    }
});

onBeforeUnmount(() => {
    revokeLogoObjectUrl();
});

async function handleSubmit() {
    saving.value = true;
    successMessage.value = '';
    loadError.value = '';

    try {
        const payload = {
            contact_email: form.contact_email ?? '',
            contact_phone: form.contact_phone ?? '',
            contact_address: form.contact_address ?? '',
            contact_map_embed: form.contact_map_embed ?? '',
            contact_hero_text: form.contact_hero_text ?? '',
            social_twitter: form.social_twitter ?? '',
            social_instagram: form.social_instagram ?? '',
            social_youtube: form.social_youtube ?? '',
            social_facebook: form.social_facebook ?? '',
            social_whatsapp: form.social_whatsapp ?? '',
            remove_logo: removeLogo.value ? 1 : 0,
        };

        if (logoFile.value) {
            payload.logo = logoFile.value;
        }

        const updated = await siteSettingStore.updateSettings(payload);
        if (updated) {
            Object.assign(form, updated);
        }

        successMessage.value = 'Ayarlar kaydedildi.';
        removeLogo.value = false;
        logoFile.value = null;
        if (logoInputRef.value) {
            logoInputRef.value.value = '';
        }
        refreshLogoPreview();
    } catch (error) {
        loadError.value = siteSettingStore.error || 'Ayarlar güncellenemedi.';
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <section class="space-y-6">
        <header class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <h1 class="text-2xl font-bold text-secondary-900">Site Ayarları</h1>
            <p class="mt-2 text-sm text-neutral-500">
                İletişim bilgileri ve harita bağlantısını buradan güncelleyebilirsiniz.
            </p>
        </header>

        <div v-if="loading" class="rounded-3xl border border-neutral-200 bg-white px-6 py-10 text-center text-sm text-neutral-500">
            Ayarlar yükleniyor...
        </div>
        <div v-else class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <div v-if="loadError" class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ loadError }}
            </div>
            <div v-if="successMessage" class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ successMessage }}
            </div>
            <form class="space-y-6" @submit.prevent="handleSubmit">
                <div>
                    <label class="block text-sm font-semibold text-neutral-700">Site Logosu</label>
                    <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="flex h-28 w-28 items-center justify-center rounded-2xl border border-dashed border-neutral-200 bg-neutral-50 p-3">
                            <img
                                v-if="logoPreview"
                                :src="logoPreview"
                                alt="Logo ön izlemesi"
                                class="max-h-full max-w-full object-contain"
                            />
                            <span v-else class="text-center text-xs font-medium text-neutral-400">Logo yüklenmemiş</span>
                        </div>
                        <div class="flex flex-col gap-3 text-sm text-neutral-500">
                            <label
                                class="inline-flex cursor-pointer items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 font-semibold text-secondary-900 shadow-sm transition hover:border-primary-300 hover:text-primary-600"
                            >
                                <input
                                    ref="logoInputRef"
                                    type="file"
                                    accept="image/*"
                                    class="sr-only"
                                    @change="handleLogoChange"
                                />
                                Yeni Logo Yükle
                            </label>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl border border-neutral-200 px-4 py-2 font-semibold text-secondary-700 transition hover:border-rose-200 hover:text-rose-600 disabled:opacity-50"
                                :disabled="!canRemoveLogo && !removeLogo"
                                @click="toggleRemoveLogo"
                            >
                                {{ removeLogo ? 'Kaldırmayı İptal Et' : 'Logoyu Kaldır' }}
                            </button>
                            <p class="text-xs text-neutral-400">PNG, JPG veya SVG formatında maksimum 5MB.</p>
                            <p v-if="removeLogo" class="text-xs text-rose-500">
                                Kaydettiğinizde mevcut logo kaldırılacak.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-neutral-700">E-posta</label>
                        <input
                            v-model="form.contact_email"
                            type="email"
                            class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                            placeholder="info@takvadergisi.org"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-neutral-700">Telefon</label>
                        <input
                            v-model="form.contact_phone"
                            type="text"
                            class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                            placeholder="0552 822 74 42"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-neutral-700">Adres</label>
                    <textarea
                        v-model="form.contact_address"
                        rows="2"
                        class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                        placeholder="Tatlıcak Mh. Uluyatır Sk. No:42/A Karatay / KONYA"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-neutral-700">Hero Açıklaması</label>
                    <textarea
                        v-model="form.contact_hero_text"
                        rows="2"
                        class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                        placeholder="Görüş, öneri ve katkılarınızı memnuniyetle dinliyoruz..."
                    ></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-neutral-700">Google Maps Embed Kodu</label>
                    <textarea
                        v-model="form.contact_map_embed"
                        rows="3"
                        class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                        placeholder="&lt;iframe src='https://www.google.com/maps/embed?...' width='600' height='450' style='border:0;' allowfullscreen loading='lazy'&gt;&lt;/iframe&gt;"
                    ></textarea>
                    <p class="mt-2 text-xs text-neutral-400">
                        Google Maps &gt; Paylaş &gt; Embed adımlarından aldığınız iframe kodunu (veya sadece src bağlantısını) buraya yapıştırabilirsiniz.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-neutral-700">Twitter</label>
                        <input
                            v-model="form.social_twitter"
                            type="url"
                            class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                            placeholder="https://x.com/takvadergisi1"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-neutral-700">Instagram</label>
                        <input
                            v-model="form.social_instagram"
                            type="url"
                            class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                            placeholder="https://www.instagram.com/..."
                        />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-neutral-700">YouTube</label>
                        <input
                            v-model="form.social_youtube"
                            type="url"
                            class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                            placeholder="https://www.youtube.com/@takvadergisi"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-neutral-700">Facebook</label>
                        <input
                            v-model="form.social_facebook"
                            type="url"
                            class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                            placeholder="https://www.facebook.com/..."
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-neutral-700">WhatsApp</label>
                        <input
                            v-model="form.social_whatsapp"
                            type="text"
                            class="mt-2 w-full rounded-xl border border-neutral-200 bg-white px-4 py-3 text-sm text-secondary-900 shadow-sm transition focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-200"
                            placeholder="+90 552 822 74 42"
                        />
                        <p class="mt-2 text-xs text-neutral-400">
                            Sadece numarayı yazın, bağlantı otomatik oluşturulur.
                        </p>
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
