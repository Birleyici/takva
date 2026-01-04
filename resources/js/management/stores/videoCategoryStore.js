import { defineStore } from 'pinia';
import axios from 'axios';

const defaultMeta = () => ({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
});

export const useVideoCategoryStore = defineStore('videoCategoryStore', {
    state: () => ({
        categories: [],
        meta: defaultMeta(),
        loading: false,
        error: null,
    }),
    actions: {
        async fetchCategories(params = {}) {
            this.loading = true;
            this.error = null;

            try {
                const query = {
                    per_page: params.per_page ?? this.meta.per_page ?? 10,
                    page: params.page ?? this.meta.current_page ?? 1,
                    search: params.search ?? '',
                    is_active:
                        params.is_active !== undefined ? params.is_active : undefined,
                };

                if (!query.search) {
                    delete query.search;
                }
                if (query.is_active === undefined) {
                    delete query.is_active;
                }

                const response = await axios.get('/management/api/video-categories', {
                    params: query,
                });

                this.categories = response.data.data ?? [];
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

        async createCategory(payload, params = {}) {
            this.error = null;

            try {
                const response = await axios.post('/management/api/video-categories', payload);

                await this.fetchCategories({
                    page: params.page ?? 1,
                    search: params.search,
                    per_page: params.per_page,
                    is_active: params.is_active,
                });

                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async updateCategory(id, payload, params = {}) {
            this.error = null;

            try {
                const response = await axios.put(`/management/api/video-categories/${id}`, payload);

                await this.fetchCategories({
                    page: params.page ?? this.meta.current_page,
                    search: params.search,
                    per_page: params.per_page,
                    is_active: params.is_active,
                });

                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async deleteCategory(id, params = {}) {
            this.error = null;

            try {
                await axios.delete(`/management/api/video-categories/${id}`);

                const isLastItemOnPage =
                    this.categories.length === 1 && this.meta.current_page > 1;

                const targetPage = isLastItemOnPage
                    ? this.meta.current_page - 1
                    : this.meta.current_page;

                await this.fetchCategories({
                    page: params.page ?? targetPage,
                    search: params.search,
                    per_page: params.per_page,
                    is_active: params.is_active,
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

            return 'Beklenmeyen bir hata oluştu. Lütfen tekrar deneyin.';
        },
    },
});
