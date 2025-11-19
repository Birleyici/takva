import { defineStore } from 'pinia';
import axios from 'axios';

const defaultMeta = () => ({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
});

export const useAuthorStore = defineStore('authorStore', {
    state: () => ({
        authors: [],
        meta: defaultMeta(),
        loading: false,
        error: null,
    }),
    actions: {
        async fetchAuthors(params = {}) {
            this.loading = true;
            this.error = null;

            try {
                const query = {
                    per_page: params.per_page ?? this.meta.per_page ?? 10,
                    page: params.page ?? this.meta.current_page ?? 1,
                    search: params.search ?? '',
                };

                if (!query.search) {
                    delete query.search;
                }

                const response = await axios.get('/management/api/authors', {
                    params: query,
                });

                this.authors = response.data.data ?? [];
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

        async createAuthor(payload, params = {}) {
            this.error = null;

            try {
                const response = await axios.post('/management/api/authors', payload);

                await this.fetchAuthors({
                    page: params.page ?? 1,
                    search: params.search,
                    per_page: params.per_page,
                });

                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async updateAuthor(id, payload, params = {}) {
            this.error = null;

            try {
                const response = await axios.put(`/management/api/authors/${id}`, payload);

                await this.fetchAuthors({
                    page: params.page ?? this.meta.current_page,
                    search: params.search,
                    per_page: params.per_page,
                });

                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async deleteAuthor(id, params = {}) {
            this.error = null;

            try {
                await axios.delete(`/management/api/authors/${id}`);

                const isLastItemOnPage =
                    this.authors.length === 1 && this.meta.current_page > 1;

                const targetPage = isLastItemOnPage
                    ? this.meta.current_page - 1
                    : this.meta.current_page;

                await this.fetchAuthors({
                    page: params.page ?? targetPage,
                    search: params.search,
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

            return 'Bir hata oluştu. Lütfen tekrar deneyin.';
        },
    },
});
