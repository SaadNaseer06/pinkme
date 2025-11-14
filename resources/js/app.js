import './bootstrap';
import notificationManager from './notifications';
import chatManager from './chat';

document.addEventListener('DOMContentLoaded', () => {
    notificationManager.init();
    chatManager.init();
});

