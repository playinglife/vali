import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.axios.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error?.code === 'ERR_CANCELED') {
            return Promise.reject(error);
        }

        const status = error?.response?.status ?? null;
        const data = error?.response?.data ?? null;

        const messages = [];

        if (status === 422 && data?.errors && typeof data.errors === 'object') {
            Object.values(data.errors).forEach((fieldErrors) => {
                if (Array.isArray(fieldErrors)) {
                    messages.push(...fieldErrors.filter(Boolean));
                }
            });
        }

        if (messages.length === 0 && data?.message) {
            messages.push(data.message);
        }

        const fallbackByStatus = {
            401: 'You are not authorized. Please log in again.',
            403: 'You do not have permission to perform this action.',
            404: 'Requested resource was not found.',
            419: 'Your session has expired. Please refresh the page.',
            422: 'Please fix the highlighted validation errors.',
            429: 'Too many requests. Please try again in a moment.',
            500: 'Server error. Please try again.',
            503: 'Service is temporarily unavailable. Please try again later.',
        };

        if (messages.length === 0) {
            messages.push(fallbackByStatus[status] || 'Unexpected error occurred.');
        }

        if (status !== 200 && messages.length > 0) {
            window.alert(messages.join('\n'));
        }

        window.dispatchEvent(
            new CustomEvent('http-error', {
                detail: {
                    status,
                    message: messages.join('\n'),
                    messages,
                    data,
                    error,
                },
            })
        );

        return Promise.reject(error);
    }
);
