/**
 * Stale browser tabs often keep an old CSRF meta / X-CSRF-TOKEN while the session
 * is still valid. Refreshing the token fixes POSTs without a full reload.
 */

function appUrlPrefix() {
    return (document.querySelector('meta[name="app-url"]')?.getAttribute('content') ?? '').replace(
        /\/+$/,
        ''
    );
}

export function sessionCsrfUrl() {
    const base = appUrlPrefix();
    return base ? `${base}/session/csrf-token` : '/session/csrf-token';
}

export function readCsrfFromMeta() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

export function applyCsrfToken(token) {
    if (!token) {
        return;
    }

    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) {
        meta.setAttribute('content', token);
    }

    document.querySelectorAll('input[name="_token"]').forEach((input) => {
        input.value = token;
    });

    if (typeof window !== 'undefined' && window.axios) {
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    }

    document.dispatchEvent(new CustomEvent('pinkme:csrf-refreshed', { detail: { token } }));
}

export async function fetchFreshCsrfToken() {
    try {
        const res = await fetch(sessionCsrfUrl(), {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        if (!res.ok) {
            return null;
        }
        const data = await res.json();
        const token = data?.token ?? null;
        if (token) {
            applyCsrfToken(token);
        }
        return token;
    } catch {
        return null;
    }
}

/** Login URL from layouts (staff vs patient/sponsor); falls back to /login. */
export function loginUrlForSessionExpired() {
    const fromMeta = document.querySelector('meta[name="session-expired-redirect"]')?.getAttribute('content');
    if (fromMeta) {
        return fromMeta;
    }
    const base = appUrlPrefix();
    return base ? `${base}/login` : '/login';
}

export function redirectSessionExpiredToLogin() {
    if (window.__pinkme419Reloading) {
        return;
    }
    window.__pinkme419Reloading = true;
    window.location.assign(loginUrlForSessionExpired());
}
