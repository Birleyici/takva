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
                const response = await axios.put('/management/api/site-settings', payload);
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
