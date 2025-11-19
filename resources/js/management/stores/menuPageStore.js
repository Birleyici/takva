import { defineStore } from 'pinia';
import axios from 'axios';

const defaultMeta = () => ({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
});

export const useMenuPageStore = defineStore('menuPageStore', {
    state: () => ({
        pages: [],
        meta: defaultMeta(),
        loading: false,
        error: null,
    }),
    actions: {
        async fetchPages(params = {}) {
            this.loading = true;
            this.error = null;

            try {
                const query = {
                    per_page: params.per_page ?? this.meta.per_page ?? 10,
                    page: params.page ?? this.meta.current_page ?? 1,
                    search: params.search ?? '',
                    is_active: params.is_active,
                };

                if (!query.search) {
                    delete query.search;
                }
                if (query.is_active === undefined || query.is_active === null) {
                    delete query.is_active;
                }

                const response = await axios.get('/management/api/menu-pages', {
                    params: query,
                });

                this.pages = response.data.data ?? [];
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

        async fetchPage(id) {
            this.error = null;

            try {
                const response = await axios.get(`/management/api/menu-pages/${id}`);
                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async createPage(payload, params = {}) {
            this.error = null;

            try {
                const response = await axios.post('/management/api/menu-pages', payload);
                await this.fetchPages({
                    page: params.page ?? 1,
                    search: params.search,
                    is_active: params.is_active,
                });

                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async updatePage(id, payload, params = {}) {
            this.error = null;

            try {
                const response = await axios.put(`/management/api/menu-pages/${id}`, payload);
                await this.fetchPages({
                    page: params.page ?? this.meta.current_page,
                    search: params.search,
                    is_active: params.is_active,
                });

                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async deletePage(id, params = {}) {
            this.error = null;

            try {
                await axios.delete(`/management/api/menu-pages/${id}`);

                const isLastItemOnPage = this.pages.length === 1 && this.meta.current_page > 1;
                const targetPage = isLastItemOnPage ? this.meta.current_page - 1 : this.meta.current_page;

                await this.fetchPages({
                    page: params.page ?? targetPage,
                    search: params.search,
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

            return 'Bir hata oluştu. Lütfen tekrar deneyin.';
        },
    },
});
