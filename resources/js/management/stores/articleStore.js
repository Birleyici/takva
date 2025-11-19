import { defineStore } from 'pinia';
import axios from 'axios';

const defaultMeta = () => ({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
});

export const useArticleStore = defineStore('articleStore', {
    state: () => ({
        articles: [],
        meta: defaultMeta(),
        loading: false,
        error: null,
    }),
    actions: {
        async fetchArticles(params = {}) {
            this.loading = true;
            this.error = null;

            try {
                const query = {
                    per_page: params.per_page ?? this.meta.per_page ?? 10,
                    page: params.page ?? this.meta.current_page ?? 1,
                    search: params.search ?? '',
                    category_id: params.category_id ?? undefined,
                    author_id: params.author_id ?? undefined,
                    is_published: params.is_published,
                };

                if (!query.search) {
                    delete query.search;
                }
                if (query.category_id === undefined) {
                    delete query.category_id;
                }
                if (query.author_id === undefined) {
                    delete query.author_id;
                }
                if (query.is_published === undefined || query.is_published === null) {
                    delete query.is_published;
                }

                const response = await axios.get('/management/api/articles', {
                    params: query,
                });

                this.articles = response.data.data ?? [];
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

        async fetchArticle(id) {
            this.error = null;

            try {
                const response = await axios.get(`/management/api/articles/${id}`);
                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async createArticle(payload, params = {}) {
            this.error = null;

            try {
                const response = await axios.post('/management/api/articles', payload);

                await this.fetchArticles({
                    page: params.page ?? 1,
                    search: params.search,
                    category_id: params.category_id,
                    author_id: params.author_id,
                    is_published: params.is_published,
                });

                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async updateArticle(id, payload, params = {}) {
            this.error = null;

            try {
                const response = await axios.put(`/management/api/articles/${id}`, payload);

                await this.fetchArticles({
                    page: params.page ?? this.meta.current_page,
                    search: params.search,
                    category_id: params.category_id,
                    author_id: params.author_id,
                    is_published: params.is_published,
                });

                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async deleteArticle(id, params = {}) {
            this.error = null;

            try {
                await axios.delete(`/management/api/articles/${id}`);

                const isLastItemOnPage =
                    this.articles.length === 1 && this.meta.current_page > 1;

                const targetPage = isLastItemOnPage
                    ? this.meta.current_page - 1
                    : this.meta.current_page;

                await this.fetchArticles({
                    page: params.page ?? targetPage,
                    search: params.search,
                    category_id: params.category_id,
                    author_id: params.author_id,
                    is_published: params.is_published,
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
