<script setup>
import axios from 'axios';
import { reactive, ref } from 'vue';

const managementData = window.__MANAGEMENT_DATA__ ?? {};

const profileForm = reactive({
    name: managementData.user?.name ?? '',
    email: managementData.user?.email ?? '',
    processing: false,
    success: '',
    errors: {},
    timeout: null,
});

const passwordForm = reactive({
    current_password: '',
    password: '',
    password_confirmation: '',
    processing: false,
    success: '',
    errors: {},
    timeout: null,
});

const formatErrorMessage = (message) => {
    const overrides = {
        'validation.confirmed': 'Yeni şifre ve doğrulama alanı aynı olmalıdır.',
        'validation.current_password': 'Mevcut şifrenizi doğru girmelisiniz.',
        'validation.required': 'Bu alan zorunludur.',
        'validation.email': 'Lütfen geçerli bir e-posta adresi giriniz.',
        'validation.min.string': 'Girilen değer çok kısa görünüyor.',
        'validation.max.string': 'Girilen değer izin verilen uzunluğu aşıyor.',
        'validation.password': 'Daha güçlü bir şifre belirleyiniz.',
    };

    if (overrides[message]) {
        return overrides[message];
    }

    const lowerMessage = (message ?? '').toLowerCase();

    if (lowerMessage.includes('required')) {
        return 'Bu alan zorunludur.';
    }

    if (lowerMessage.includes('email')) {
        return 'Lütfen geçerli bir e-posta adresi giriniz.';
    }

    if (lowerMessage.includes('confirmed')) {
        return 'Yeni şifre ve doğrulama alanı aynı olmalıdır.';
    }

    if (lowerMessage.includes('current password')) {
        return 'Mevcut şifreniz yanlış.';
    }

    return message ?? 'Geçersiz form verisi.';
};

const getFieldError = (errors, field) => {
    if (!errors[field]?.length) {
        return '';
    }

    return formatErrorMessage(errors[field][0]);
};

const resetProfileAlerts = () => {
    if (profileForm.timeout) {
        clearTimeout(profileForm.timeout);
    }
    profileForm.success = '';
    profileForm.errors = {};
};

const resetPasswordAlerts = () => {
    if (passwordForm.timeout) {
        clearTimeout(passwordForm.timeout);
    }
    passwordForm.success = '';
    passwordForm.errors = {};
};

const handleProfileSubmit = async () => {
    resetProfileAlerts();
    profileForm.processing = true;

    try {
        await axios.patch('/profile', {
            name: profileForm.name,
            email: profileForm.email,
        });
        profileForm.success = 'Profil bilgileriniz güncellendi.';
        profileForm.timeout = setTimeout(() => {
            profileForm.success = '';
        }, 5000);
        if (window.__MANAGEMENT_DATA__?.user) {
            window.__MANAGEMENT_DATA__.user.name = profileForm.name;
            window.__MANAGEMENT_DATA__.user.email = profileForm.email;
        }
    } catch (error) {
        if (error.response?.status === 422) {
            profileForm.errors = error.response.data.errors ?? {};
        } else {
            profileForm.errors = { general: ['Beklenmedik bir hata oluştu.'] };
        }
    } finally {
        profileForm.processing = false;
    }
};

const handlePasswordSubmit = async () => {
    resetPasswordAlerts();
    passwordForm.processing = true;

    try {
        await axios.put('/password', {
            current_password: passwordForm.current_password,
            password: passwordForm.password,
            password_confirmation: passwordForm.password_confirmation,
        });
        passwordForm.success = 'Şifreniz başarıyla güncellendi.';
        passwordForm.timeout = setTimeout(() => {
            passwordForm.success = '';
        }, 5000);
        passwordForm.current_password = '';
        passwordForm.password = '';
        passwordForm.password_confirmation = '';
    } catch (error) {
        if (error.response?.status === 422) {
            passwordForm.errors = error.response.data.errors ?? {};
        } else {
            passwordForm.errors = { general: ['Şifre güncellenemedi.'] };
        }
    } finally {
        passwordForm.processing = false;
    }
};
</script>

<template>
    <div class="space-y-8">
        <section class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
                <div
                    v-if="profileForm.success"
                    class="mb-5 flex items-center gap-2 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ profileForm.success }}</span>
                </div>
            </transition>

            <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
                <div
                    v-if="profileForm.errors.general"
                    class="mb-5 flex items-center gap-2 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856a2 2 0 0 0 1.732-3l-6.928-12a2 2 0 0 0-3.464 0l-6.928 12a2 2 0 0 0 1.732 3z" />
                    </svg>
                    <span>{{ profileForm.errors.general[0] }}</span>
                </div>
            </transition>

            <header class="mb-6">
                <h2 class="text-xl font-semibold text-secondary-900">Profil Bilgileri</h2>
                <p class="text-sm text-neutral-500">
                    İsim ve e-posta adresinizi güncelleyebilirsiniz.
                </p>
            </header>

            <form class="space-y-5" @submit.prevent="handleProfileSubmit">
                <div class="grid gap-6 sm:grid-cols-2 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700">İsim</label>
                        <input
                            v-model="profileForm.name"
                            type="text"
                            class="mt-2 w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100"
                            placeholder="Takva Dergisi"
                        />
                        <p v-if="getFieldError(profileForm.errors, 'name')" class="mt-1 text-xs text-rose-600">
                            {{ getFieldError(profileForm.errors, 'name') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700">E-posta</label>
                        <input
                            v-model="profileForm.email"
                            type="email"
                            class="mt-2 w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100"
                            placeholder="info@takvadergisi.org"
                        />
                        <p v-if="getFieldError(profileForm.errors, 'email')" class="mt-1 text-xs text-rose-600">
                            {{ getFieldError(profileForm.errors, 'email') }}
                        </p>
                    </div>
                </div>

                <button
                    type="submit"
                    class="inline-flex mt-4 items-center justify-center rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-500 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="profileForm.processing"
                >
                    Kaydet
                </button>
            </form>
        </section>

        <section class="rounded-3xl border border-neutral-200 bg-white px-6 py-6 shadow-sm">
            <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
                <div
                    v-if="passwordForm.success"
                    class="mb-5 flex items-center gap-2 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ passwordForm.success }}</span>
                </div>
            </Transition>

            <transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
                <div
                    v-if="passwordForm.errors.general"
                    class="mb-5 flex items-center gap-2 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856a2 2 0 0 0 1.732-3l-6.928-12a2 2 0 0 0-3.464 0l-6.928 12a2 2 0 0 0 1.732 3z" />
                    </svg>
                    <span>{{ passwordForm.errors.general[0] }}</span>
                </div>
            </transition>

            <header class="mb-6">
                <h2 class="text-xl font-semibold text-secondary-900">Şifre Güncelle</h2>
                <p class="text-sm text-neutral-500">
                    Güncel şifrenizi girip yeni şifrenizi belirleyin.
                </p>
            </header>

            <form class="space-y-5" @submit.prevent="handlePasswordSubmit">
                <div class="grid gap-6 sm:grid-cols-3 mb-6">
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-neutral-700">Mevcut Şifre</label>
                        <input
                            v-model="passwordForm.current_password"
                            type="password"
                            class="mt-2 w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100"
                            placeholder="********"
                        />
                        <p v-if="getFieldError(passwordForm.errors, 'current_password')" class="mt-1 text-xs text-rose-600">
                            {{ getFieldError(passwordForm.errors, 'current_password') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700">Yeni Şifre</label>
                        <input
                            v-model="passwordForm.password"
                            type="password"
                            class="mt-2 w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100"
                            placeholder="********"
                        />
                        <p v-if="getFieldError(passwordForm.errors, 'password')" class="mt-1 text-xs text-rose-600">
                            {{ getFieldError(passwordForm.errors, 'password') }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700">Yeni Şifre (Tekrar)</label>
                        <input
                            v-model="passwordForm.password_confirmation"
                            type="password"
                            class="mt-2 w-full rounded-xl border border-neutral-200 px-4 py-3 text-sm shadow-sm focus:border-primary-300 focus:outline-none focus:ring-2 focus:ring-primary-100"
                            placeholder="********"
                        />
                    </div>
                </div>

                <button
                    type="submit"
                    class="inline-flex mt-4 items-center justify-center rounded-xl bg-secondary-900 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-secondary-800 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="passwordForm.processing"
                >
                    Şifreyi Güncelle
                </button>
            </form>
        </section>
    </div>
</template>
