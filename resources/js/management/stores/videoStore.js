import { defineStore } from 'pinia';
import axios from 'axios';

const defaultMeta = () => ({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
});

export const useVideoStore = defineStore('videoStore', {
    state: () => ({
        videos: [],
        meta: defaultMeta(),
        loading: false,
        error: null,
    }),
    actions: {
        async fetchVideos(params = {}) {
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
                if (typeof query.is_active !== 'boolean') {
                    delete query.is_active;
                }

                const response = await axios.get('/management/api/videos', {
                    params: query,
                });

                this.videos = response.data.data ?? [];
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

        async fetchVideo(id) {
            this.error = null;

            try {
                const response = await axios.get(`/management/api/videos/${id}`);
                return response.data.data;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async createVideo(payload, params = {}) {
            this.error = null;

            try {
                const response = await axios.post('/management/api/videos', payload);

                await this.fetchVideos({
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

        async updateVideo(id, payload, params = {}) {
            this.error = null;

            try {
                const response = await axios.put(`/management/api/videos/${id}`, payload);

                await this.fetchVideos({
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

        async deleteVideo(id, params = {}) {
            this.error = null;

            try {
                await axios.delete(`/management/api/videos/${id}`);

                const isLastItemOnPage =
                    this.videos.length === 1 && this.meta.current_page > 1;

                const targetPage = isLastItemOnPage
                    ? this.meta.current_page - 1
                    : this.meta.current_page;

                await this.fetchVideos({
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
