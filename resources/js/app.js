import { configureAxios } from './bootstrap';
import Alpine from 'alpinejs';

/**
 * Helper aman untuk membaca nilai dari localStorage.
 * Mencegah crash jika browser memblokir penyimpanan lokal (Incognito/Security Restriction).
 * 
 * @param {string} key 
 * @param {any} defaultValue 
 * @returns {any}
 */
export function getSafeStorage(key, defaultValue = null) {
    try {
        if (typeof window === 'undefined' || !window.localStorage) return defaultValue;
        return localStorage.getItem(key) ?? defaultValue;
    } catch {
        return defaultValue;
    }
}

/**
 * Helper aman untuk menyimpan nilai ke localStorage.
 * 
 * @param {string} key 
 * @param {string} value 
 */
export function setSafeStorage(key, value) {
    try {
        if (typeof window !== 'undefined' && window.localStorage) {
            localStorage.setItem(key, value);
        }
    } catch (e) {
        console.warn(`[Storage] Gagal menyimpan key "${key}":`, e);
    }
}

/**
 * Komponen Alpine.js untuk penanganan Mode Gelap / Terang (Theme Toggle).
 */
export function createThemeToggleComponent() {
    return {
        theme: 'light',
        init() {
            const savedTheme = getSafeStorage('theme');
            if (savedTheme) {
                this.theme = savedTheme;
            } else if (typeof window !== 'undefined' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                this.theme = 'dark';
            }
        },
        toggle() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            setSafeStorage('theme', this.theme);

            if (typeof document !== 'undefined') {
                document.documentElement.classList.toggle('dark', this.theme === 'dark');
            }
        }
    };
}

/**
 * Mencegah klik ganda pada tautan autentikasi Google OAuth.
 * 
 * @param {Document|HTMLElement} targetContext
 */
export function initOAuthProtection(targetContext = typeof document !== 'undefined' ? document : null) {
    if (!targetContext) return;

    const googleAuthLinks = targetContext.querySelectorAll('a[href*="/auth/google"]');
    googleAuthLinks.forEach(link => {
        // Idempotency check: cegah pembacaan & penambahan event listener ganda
        if (link.dataset.oauthProtected === 'true') return;
        link.dataset.oauthProtected = 'true';

        const span = link.querySelector('span');
        if (span && !link.hasAttribute('data-original-text')) {
            link.setAttribute('data-original-text', span.textContent);
        }

        link.addEventListener('click', function (e) {
            if (link.getAttribute('data-clicked') === 'true') {
                e.preventDefault();
                return false;
            }
            link.setAttribute('data-clicked', 'true');
            link.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
            if (span) {
                span.textContent = 'Menghubungkan ke Google...';
            }
        });
    });
}

/**
 * Menginisialisasi seluruh pustaka frontend aplikasi secara aman.
 */
export function initializeApp() {
    try {
        // 1. Inisialisasi Axios Instance
        const axiosInstance = configureAxios();

        // 2. Inisialisasi AlpineJS & DOM Bindings hanya di Browser
        if (typeof window !== 'undefined') {
            window.Alpine = Alpine;

            // Registrasi Alpine Data Component
            Alpine.data('themeToggle', createThemeToggleComponent);

            // Inisialisasi DOM OAuth Protection
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => initOAuthProtection(document));
            } else {
                initOAuthProtection(document);
            }

            // Jalankan Alpine hanya jika window ada
            Alpine.start();
        }

        return { success: true, Alpine, axios: axiosInstance };
    } catch (error) {
        console.error('Gagal menginisialisasi aplikasi frontend:', error);
        return { success: false, error };
    }
}

// Auto-run pada lingkungan browser
if (typeof window !== 'undefined') {
    initializeApp();
}

