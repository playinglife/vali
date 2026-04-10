/**
 * Wraps an axios instance with keyed requests: each key has at most one in-flight
 * request; starting a new one or calling cancelRequest aborts the previous.
 * Aborted requests reject with axios CanceledError (message "canceled").
 *
 * @param {import('axios').AxiosInstance} axiosInstance
 */
export function useAdvancedAxios(axiosInstance) {
    /** @type {Map<string, AbortController>} */
    const controllers = new Map();

    function cancelRequest(key) {
        const existing = controllers.get(key);
        if (!existing) {
            return;
        }
        existing.abort();
        controllers.delete(key);
    }

    function sendRequest(key, config) {
        cancelRequest(key);

        const controller = new AbortController();
        controllers.set(key, controller);

        const requestConfig = {
            ...config,
            signal: controller.signal,
        };

        return axiosInstance.request(requestConfig).finally(() => {
            if (controllers.get(key) === controller) {
                controllers.delete(key);
            }
        });
    }

    return {
        sendRequest,
        cancelRequest,
    };
}
