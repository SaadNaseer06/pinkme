import './bootstrap';
import formLoader from './form-loader';
import { fetchFreshCsrfToken } from './session-csrf';

document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            void fetchFreshCsrfToken();
        }
    });

    formLoader.init();

    if (document.querySelector('[data-notification-center], [data-notification-modal]')) {
        import('./notifications').then(({ default: notificationManager }) => notificationManager.init());
    }

    if (document.querySelector('[data-chat-app]')) {
        import('./chat').then(({ default: chatManager }) => chatManager.init());
    }

    if (document.querySelector('[data-cm-patient-chats-page]')) {
        import('./case-manager-patient-chats-inbox').then(({ initCaseManagerPatientChatsInbox }) => {
            initCaseManagerPatientChatsInbox();
        });
    }

    if (document.querySelector('[data-finance-team-chats-page]')) {
        import('./finance-team-chats-inbox').then(({ initFinanceTeamChatsInbox }) => {
            initFinanceTeamChatsInbox();
        });
    }
});

