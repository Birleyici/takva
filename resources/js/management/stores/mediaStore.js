import { defineStore } from 'pinia';
import axios from 'axios';

const defaultMeta = () => ({
    current_page: 1,
    last_page: 1,
    per_page: 24,
    total: 0,
});

export const useMediaStore = defineStore('mediaStore', {
    state: () => ({
        items: [],
        meta: defaultMeta(),
        loading: false,
        uploading: false,
        error: null,
    }),
    actions: {
        async fetchMedia(params = {}) {
            this.loading = true;
            this.error = null;

            try {
                const query = {
                    per_page: params.per_page ?? this.meta.per_page,
                    page: params.page ?? this.meta.current_page,
                };

                const append = params.append ?? false;

                const response = await axios.get('/management/api/media', {
                    params: query,
                });

                const data = response.data.data ?? [];

                if (append && query.page > 1) {
                    const existingIds = new Set(this.items.map((item) => item.id));
                    const merged = [...this.items];
                    data.forEach((item) => {
                        if (!existingIds.has(item.id)) {
                            merged.push(item);
                        }
                    });
                    this.items = merged;
                } else {
                    this.items = data;
                }

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

        async uploadMedia(file) {
            this.uploading = true;
            this.error = null;

            try {
                const formData = new FormData();
                formData.append('file', file);

                const response = await axios.post('/management/api/media', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                });

                const media = response.data.data;
                this.items.unshift(media);
                this.meta.total += 1;

                return media;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            } finally {
                this.uploading = false;
            }
        },

        async deleteMedia(id) {
            this.error = null;

            try {
                await axios.delete(`/management/api/media/${id}`);

                this.items = this.items.filter((item) => item.id !== id);

                if (this.meta.total > 0) {
                    this.meta.total -= 1;
                }
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
