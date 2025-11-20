import { defineStore } from 'pinia';
import axios from 'axios';

const defaultMeta = () => ({
    current_page: 1,
    last_page: 1,
    per_page: 12,
    total: 0,
});

export const useContactMessageStore = defineStore('contactMessageStore', {
    state: () => ({
        messages: [],
        meta: defaultMeta(),
        loading: false,
        detailLoading: false,
        error: null,
        selected: null,
    }),
    actions: {
        async fetchMessages(params = {}) {
            this.loading = true;
            this.error = null;

            try {
                const query = {
                    per_page: params.per_page ?? this.meta.per_page,
                    page: params.page ?? this.meta.current_page,
                    search: params.search ?? undefined,
                    status: params.status ?? undefined,
                };

                const response = await axios.get('/management/api/contact-messages', {
                    params: query,
                });

                this.messages = response.data.data ?? [];
                this.meta = {
                    ...this.meta,
                    ...response.data.meta,
                };

                return this.messages;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchMessage(id) {
            this.detailLoading = true;
            this.error = null;

            try {
                const response = await axios.get(`/management/api/contact-messages/${id}`);
                const message = response.data.data;
                this.updateMessageInList(message);
                this.selected = message;
                return message;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            } finally {
                this.detailLoading = false;
            }
        },

        async updateStatus(id, isRead) {
            this.error = null;

            try {
                const response = await axios.put(`/management/api/contact-messages/${id}`, {
                    is_read: isRead,
                });
                const message = response.data.data;
                this.updateMessageInList(message);
                if (this.selected?.id === message.id) {
                    this.selected = message;
                }
                return message;
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        async deleteMessage(id) {
            this.error = null;

            try {
                await axios.delete(`/management/api/contact-messages/${id}`);
                this.messages = this.messages.filter((message) => message.id !== id);
                this.meta.total = Math.max(0, (this.meta.total ?? 0) - 1);
                if (this.selected?.id === id) {
                    this.selected = null;
                }
            } catch (error) {
                this.error = this.extractErrorMessage(error);
                throw error;
            }
        },

        updateMessageInList(payload) {
            if (!payload) {
                return;
            }

            const index = this.messages.findIndex((message) => message.id === payload.id);
            if (index >= 0) {
                this.messages.splice(index, 1, payload);
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
