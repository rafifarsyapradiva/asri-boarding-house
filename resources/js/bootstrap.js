import axios from 'axios';

/**
 * Mengonfigurasi dan mengembalikan instansi Axios secara modular.
 * 
 * @param {import('axios').AxiosRequestConfig} customConfig - Konfigurasi tambahan Axios
 * @returns {import('axios').AxiosInstance}
 */
export function configureAxios(customConfig = {}) {
    // Separasi headers dari customConfig agar tidak tertimpa oleh object spread
    const { headers: customHeaders, ...otherConfig } = customConfig;

    const defaultHeaders = {
        'X-Requested-With': 'XMLHttpRequest',
    };

    const instance = axios.create({
        ...otherConfig,
        headers: {
            ...defaultHeaders,
            ...(customHeaders || {}),
        },
    });

    // Ekspos ke window global hanya di lingkungan browser
    if (typeof window !== 'undefined') {
        window.axios = instance;
    }

    return instance;
}

