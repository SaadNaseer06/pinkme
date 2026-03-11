import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.Echo = null;
window.Pusher = Pusher;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content');

if (csrfToken) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

// Subdirectory: set axios base URL so /notifications etc resolve correctly
const appUrl = document.querySelector('meta[name="app-url"]')?.getAttribute('content');
if (appUrl) {
    window.axios.defaults.baseURL = appUrl;
}

// Use runtime config from server (for subdirectory auth URL) or fall back to build-time env
const runtimeConfig = window.PUSHER_CONFIG;
const pusherKey = runtimeConfig?.key ?? import.meta.env.VITE_PUSHER_APP_KEY;

if (pusherKey) {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: runtimeConfig?.cluster ?? import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
        wsHost: runtimeConfig?.wsHost ?? import.meta.env.VITE_PUSHER_HOST ?? `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1'}.pusher.com`,
        wsPort: runtimeConfig?.wsPort ?? Number(import.meta.env.VITE_PUSHER_PORT ?? 80),
        wssPort: runtimeConfig?.wssPort ?? Number(import.meta.env.VITE_PUSHER_PORT ?? 443),
        forceTLS: runtimeConfig?.forceTLS ?? (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: runtimeConfig?.authEndpoint ?? '/broadcasting/auth',
    });
}
