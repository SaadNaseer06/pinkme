/**
 * Realtime refresh of the shared “Open program applications” strip on Case Manager → Patient Chats.
 */
export function initCaseManagerPatientChatsInbox() {
    const page = document.querySelector('[data-cm-patient-chats-page]');
    if (!page || !window.Echo) {
        return;
    }

    const url = page.getAttribute('data-inbox-fragment-url');
    const inbox = document.querySelector('[data-cm-claimable-inbox]');
    if (!url || !inbox) {
        return;
    }

    let refreshTimer = null;
    const refresh = () => {
        if (!window.axios) {
            return;
        }
        window.axios
            .get(url, { headers: { Accept: 'application/json' } })
            .then((res) => {
                const html = res.data?.html;
                if (typeof html === 'string') {
                    inbox.innerHTML = html;
                }
            })
            .catch(() => {});
    };

    const scheduleRefresh = () => {
        if (refreshTimer) {
            clearTimeout(refreshTimer);
        }
        refreshTimer = window.setTimeout(() => {
            refreshTimer = null;
            refresh();
        }, 150);
    };

    window.Echo.private('case-managers.patient-chats').listen('.patient-chats.inbox.updated', () => {
        scheduleRefresh();
    });
}
