import './bootstrap';
import notificationManager from './notifications';
import chatManager from './chat';
import formLoader from './form-loader';
import { fetchFreshCsrfToken } from './session-csrf';
import { initCaseManagerPatientChatsInbox } from './case-manager-patient-chats-inbox';
import { initFinanceTeamChatsInbox } from './finance-team-chats-inbox';

document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            void fetchFreshCsrfToken();
        }
    });

    notificationManager.init();
    chatManager.init();
    formLoader.init();
    initCaseManagerPatientChatsInbox();
    initFinanceTeamChatsInbox();
});

