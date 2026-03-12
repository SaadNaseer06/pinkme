import './bootstrap';
import notificationManager from './notifications';
import chatManager from './chat';
import formLoader from './form-loader';

document.addEventListener('DOMContentLoaded', () => {
    notificationManager.init();
    chatManager.init();
    formLoader.init();
});

