import { defineStore } from 'pinia';
import axios from 'axios';

const defaultMeta = () => ({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
});

export const useIssueStore = defineStore('issueStore', {
    state: () => ({
        issues: [],
        meta: defaultMeta(),
        loading: false,
        error: null,
    }),
    actions: {
        async fetchIssues(params = {}) {
            this.loading = true;
            this.error = null;

            try {
                const query = {
                    per_page: params.per_page ?? this.meta.per_page ?? 10,
                    page: params.page ?? this.meta.current_page ?? 1,
                    search: params.search ?? '',
                    year: params.year ?? undefined,
                    month: params.month ?? undefined,
                };

                if (!query.search) {
                    delete query.search;
                }
                if (query.year === undefined || query.year === '') {
                    delete query.year;
                }
                if (query.month === undefined || query.month === '') {
                    delete query.month;
                }

                const response = await axios.get('/management/api/issues', {
                    params: query,
                });

                this.issues = response.data.data ?? [];
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

        async fetchIssue(id) {
            this.error = null;

            try {
                const response = await axios.get(`/management/api/issues/${id}`);
                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async createIssue(payload, params = {}, options = {}) {
            this.error = null;

            try {
                const response = await axios.post('/management/api/issues', payload, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                    onUploadProgress: options.onUploadProgress,
                });

                await this.fetchIssues({
                    page: params.page ?? 1,
                    search: params.search,
                    year: params.year,
                    month: params.month,
                });

                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async updateIssue(id, payload, params = {}, options = {}) {
            this.error = null;

            try {
                const response = await axios.post(`/management/api/issues/${id}?_method=PUT`, payload, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                    onUploadProgress: options.onUploadProgress,
                });

                await this.fetchIssues({
                    page: params.page ?? this.meta.current_page,
                    search: params.search,
                    year: params.year,
                    month: params.month,
                });

                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async deleteIssue(id, params = {}) {
            this.error = null;

            try {
                await axios.delete(`/management/api/issues/${id}`);

                const isLastItemOnPage =
                    this.issues.length === 1 && this.meta.current_page > 1;

                const targetPage = isLastItemOnPage
                    ? this.meta.current_page - 1
                    : this.meta.current_page;

                await this.fetchIssues({
                    page: params.page ?? targetPage,
                    search: params.search,
                    year: params.year,
                    month: params.month,
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
