import { defineStore } from 'pinia';
import axios from 'axios';

const defaultMeta = () => ({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
});

export const useHeroSlideStore = defineStore('heroSlideStore', {
    state: () => ({
        slides: [],
        meta: defaultMeta(),
        loading: false,
        error: null,
    }),
    actions: {
        async fetchSlides(params = {}) {
            this.loading = true;
            this.error = null;

            try {
                const query = {
                    per_page: params.per_page ?? this.meta.per_page ?? 10,
                    page: params.page ?? this.meta.current_page ?? 1,
                };

                if (typeof params.is_active === 'boolean') {
                    query.is_active = params.is_active;
                }

                const response = await axios.get('/management/api/hero-slides', {
                    params: query,
                });

                this.slides = response.data.data ?? [];
                this.meta = {
                    ...this.meta,
                    ...response.data.meta,
                };
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async createSlide(payload, params = {}) {
            this.error = null;

            try {
                const response = await axios.post('/management/api/hero-slides', payload);

                await this.fetchSlides({
                    page: params.page ?? 1,
                    per_page: params.per_page,
                });

                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async updateSlide(id, payload, params = {}) {
            this.error = null;

            try {
                const response = await axios.put(`/management/api/hero-slides/${id}`, payload);

                await this.fetchSlides({
                    page: params.page ?? this.meta.current_page,
                    per_page: params.per_page,
                });

                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async deleteSlide(id, params = {}) {
            this.error = null;

            try {
                await axios.delete(`/management/api/hero-slides/${id}`);

                const isLastItemOnPage =
                    this.slides.length === 1 && this.meta.current_page > 1;

                const targetPage = isLastItemOnPage
                    ? this.meta.current_page - 1
                    : this.meta.current_page;

                await this.fetchSlides({
                    page: params.page ?? targetPage,
                    per_page: params.per_page,
                });
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        extractErrorMessage(error) {
            if (error.response?.data?.message) {
                return error.response.data.message;
            }

            return 'Bir hata olustu. Lutfen tekrar deneyin.';
        },
    },
});
