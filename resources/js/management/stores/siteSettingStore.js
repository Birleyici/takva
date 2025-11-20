import { defineStore } from 'pinia';
import axios from 'axios';

export const useSiteSettingStore = defineStore('siteSettingStore', {
    state: () => ({
        settings: null,
        loading: false,
        error: null,
    }),
    actions: {
        async fetchSettings() {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get('/management/api/site-settings');
                this.settings = response.data.data;
                return this.settings;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updateSettings(payload) {
            this.error = null;

            try {
                const formData = new FormData();

                Object.entries(payload).forEach(([key, value]) => {
                    if (value === undefined) {
                        return;
                    }

                    if (typeof File !== 'undefined' && value instanceof File) {
                        formData.append(key, value);
                        return;
                    }

                    formData.append(key, value ?? '');
                });

                formData.append('_method', 'PUT');

                const response = await axios.post('/management/api/site-settings', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });
                this.settings = response.data.data;
                return this.settings;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        extractErrorMessage(error) {
            if (error.response?.data?.message) {
                return error.response.data.message;
            }

            return 'Bir hata oluştu. Lütfen tekrar deneyin.';
        },
    },
});
