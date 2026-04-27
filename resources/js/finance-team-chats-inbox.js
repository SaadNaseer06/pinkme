/**
 * Realtime refresh of the shared “Finance queue” strip on Finance → Team Chats.
 */
export function initFinanceTeamChatsInbox() {
    const page = document.querySelector('[data-finance-team-chats-page]');
    if (!page || !window.Echo) {
        return;
    }

    const url = page.getAttribute('data-inbox-fragment-url');
    const inbox = document.querySelector('[data-finance-claimable-inbox]');
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

    window.Echo.private('finance.team-chats').listen('.finance.team-chats.inbox.updated', () => {
        scheduleRefresh();
    });
}
