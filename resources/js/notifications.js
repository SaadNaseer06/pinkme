const MAX_NOTIFICATIONS = 20;
const API_ROUTES = {
    index: '/notifications',
    readAll: '/notifications/read-all',
    read: (id) => `/notifications/${id}/read`,
};

class NotificationManager {
    constructor() {
        this.initialized = false;
        this.userId = null;
        this.csrfToken = null;
        this.centers = [];
        this.openDropdown = null;
        this.notifications = [];
        this.notificationMap = new Map();
        this.unreadCount = 0;
        this.importantQueue = [];
        this.shownImportant = new Set();
        this.modal = null;
        this.modalElements = {};
        this.boundDocumentHandler = this.handleDocumentClick.bind(this);
        this.boundKeyHandler = this.handleKey.bind(this);
    }

    init() {
        if (this.initialized) {
            return;
        }

        this.collectMeta();
        this.collectCenters();
        this.collectModal();

        if (!this.userId || (this.centers.length === 0 && !this.modal)) {
            return;
        }

        this.initialized = true;
        this.bindCenterEvents();
        this.fetchInitial();
        this.subscribeToRealtime();

        document.addEventListener('click', this.boundDocumentHandler);
        document.addEventListener('keydown', this.boundKeyHandler);
    }

    collectMeta() {
        this.csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content') ?? null;

        this.userId = document
            .querySelector('meta[name="current-user-id"]')
            ?.getAttribute('content') ?? null;
    }

    collectCenters() {
        this.centers = Array.from(document.querySelectorAll('[data-notification-center]')).map((root) => ({
            root,
            toggle: root.querySelector('[data-notification-toggle]'),
            dropdown: root.querySelector('[data-notification-dropdown]'),
            list: root.querySelector('[data-notification-list]'),
            empty: root.querySelector('[data-notification-empty]'),
            count: root.querySelector('[data-notification-count]'),
            markAll: root.querySelector('[data-notification-mark-all]'),
        }));
    }

    collectModal() {
        const modal = document.querySelector('[data-notification-modal]');

        if (!modal) {
            return;
        }

        this.modal = modal;
        this.modalElements = {
            title: modal.querySelector('[data-modal-title]'),
            message: modal.querySelector('[data-modal-message]'),
            time: modal.querySelector('[data-modal-time]'),
            dismiss: modal.querySelector('[data-modal-dismiss]'),
            view: modal.querySelector('[data-modal-view]'),
            close: modal.querySelector('[data-modal-close]'),
        };

        const handlers = [
            [this.modalElements.dismiss, () => this.dismissModal()],
            [this.modalElements.close, () => this.dismissModal()],
        ];

        handlers.forEach(([element, handler]) => {
            if (element) {
                element.addEventListener('click', handler);
            }
        });

        if (this.modalElements.view) {
            this.modalElements.view.addEventListener('click', (event) => {
                const destination = event.currentTarget.getAttribute('href');
                const notificationId = event.currentTarget.getAttribute('data-notification-id');

                if (!destination || destination === '#') {
                    event.preventDefault();
                    this.dismissModal();
                    return;
                }

                event.preventDefault();
                this.markNotificationAsRead(Number(notificationId)).finally(() => {
                    window.location.href = destination;
                });
            });
        }

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                this.dismissModal();
            }
        });
    }

    bindCenterEvents() {
        this.centers.forEach((center) => {
            center.toggle?.addEventListener('click', (event) => {
                event.stopPropagation();
                this.toggleDropdown(center);
            });

            center.markAll?.addEventListener('click', (event) => {
                event.preventDefault();
                this.markAllAsRead();
            });

            center.list?.addEventListener('click', (event) => {
                const item = event.target.closest('[data-notification-item]');
                if (!item) {
                    return;
                }

                const id = Number(item.getAttribute('data-notification-id'));
                const link = item.getAttribute('data-notification-link');
                this.handleNotificationClick(id, link);
            });
        });
    }

    handleDocumentClick(event) {
        if (!this.openDropdown) {
            return;
        }

        const isInside = this.openDropdown.root.contains(event.target);
        if (!isInside) {
            this.closeDropdown();
        }
    }

    handleKey(event) {
        if (event.key === 'Escape') {
            const dismissed = this.dismissModalIfVisible();
            if (!dismissed) {
                this.closeDropdown();
            }
        }
    }

    toggleDropdown(center) {
        if (this.openDropdown && this.openDropdown.root === center.root) {
            this.closeDropdown();
            return;
        }

        this.openDropdown = center;
        this.centers.forEach((ctx) => {
            if (ctx.dropdown) {
                if (ctx === center) {
                    ctx.dropdown.classList.remove('hidden');
                } else {
                    ctx.dropdown.classList.add('hidden');
                }
            }
        });
    }

    closeDropdown() {
        this.centers.forEach((center) => {
            center.dropdown?.classList.add('hidden');
        });
        this.openDropdown = null;
    }

    async fetchInitial() {
        try {
            const response = await fetch(API_ROUTES.index, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`Failed to load notifications (${response.status})`);
            }

            const data = await response.json();
            this.applySnapshot(data);
        } catch (error) {
            console.warn('[notifications] unable to fetch initial notifications', error);
        }
    }

    applySnapshot(data) {
        const notifications = Array.isArray(data.notifications) ? data.notifications : [];
        this.notifications = notifications.slice(0, MAX_NOTIFICATIONS);
        this.notificationMap.clear();
        this.notifications.forEach((notification) => {
            this.notificationMap.set(notification.id, notification);
        });

        this.unreadCount = Number.isFinite(data.unread_count) ? data.unread_count : this.countUnread(this.notifications);
        this.render();
        this.enqueueImportant(data.important);

        // Include any important notifications present in the snapshot list.
        this.notifications
            .filter((notification) => notification.priority === 'important' && !notification.read_at)
            .forEach((notification) => this.enqueueImportant(notification));
    }

    render() {
        this.centers.forEach((center) => {
            if (!center.list) {
                return;
            }

            center.list.innerHTML = '';

            const items = this.notifications.slice(0, 10);

            if (items.length === 0) {
                center.empty?.classList.remove('hidden');
            } else {
                center.empty?.classList.add('hidden');
            }

            if (center.markAll) {
                if (this.unreadCount === 0) {
                    center.markAll.classList.add('hidden');
                } else {
                    center.markAll.classList.remove('hidden');
                }
            }

            items.forEach((notification) => {
                const item = this.createListItem(notification);
                center.list.appendChild(item);
            });

            this.updateCountBadge(center.count);
        });
    }

    createListItem(notification) {
        const isUnread = !notification.read_at;
        const wrapper = document.createElement('button');
        wrapper.type = 'button';
        wrapper.className = [
            'flex',
            'w-full',
            'items-start',
            'gap-3',
            'p-3',
            'rounded-xl',
            'transition',
            'text-left',
            isUnread ? 'bg-white shadow-sm' : 'bg-transparent',
            'hover:bg-white/70',
        ].join(' ');

        wrapper.setAttribute('data-notification-item', 'true');
        wrapper.setAttribute('data-notification-id', String(notification.id));
        wrapper.setAttribute('data-notification-link', notification.link_url ?? '');

        const accent = document.createElement('div');
        accent.className = 'mt-1';
        accent.innerHTML = `<span class="inline-block h-2.5 w-2.5 rounded-full ${isUnread ? 'bg-[#DB69A2]' : 'bg-gray-300'}"></span>`;

        const body = document.createElement('div');
        body.className = 'flex-1 space-y-1';

        const title = document.createElement('p');
        title.className = 'text-sm font-semibold text-[#213430]';
        title.textContent = notification.title || 'Notification';

        const message = document.createElement('p');
        message.className = 'text-xs text-[#6B5F65] leading-relaxed';
        message.textContent = notification.message || '';

        const meta = document.createElement('p');
        meta.className = 'text-[11px] text-[#A9A9A9]';
        meta.textContent = notification.created_at_formatted || '';

        body.appendChild(title);
        body.appendChild(message);
        body.appendChild(meta);

        if (notification.priority === 'important') {
            const badge = document.createElement('span');
            badge.className = 'inline-flex items-center rounded-full bg-[#DB69A2] px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white';
            badge.textContent = 'Important';
            body.insertBefore(badge, message);
        }

        wrapper.appendChild(accent);
        wrapper.appendChild(body);
        return wrapper;
    }

    updateCountBadge(badge) {
        if (!badge) {
            return;
        }

        const count = Math.max(0, this.unreadCount);
        if (count === 0) {
            badge.classList.add('hidden');
            badge.textContent = '';
        } else {
            badge.classList.remove('hidden');
            badge.textContent = count > 9 ? '9+' : String(count);
        }
    }

    handleNotificationClick(id, link) {
        const notification = this.notificationMap.get(id);
        if (!notification) {
            return;
        }

        const navigate = () => {
            if (link) {
                window.location.href = link;
            }
        };

        if (!notification.read_at) {
            this.markNotificationAsRead(id).then(navigate);
        } else {
            navigate();
        }
    }

    async markNotificationAsRead(id) {
        this.removeFromImportantQueue(id);
        try {
            const response = await fetch(API_ROUTES.read(id), {
                method: 'POST',
                headers: this.requestHeaders(),
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error(`Failed to mark notification ${id} as read`);
            }

            const data = await response.json();
            this.applyUpdateFromServer(data);
        } catch (error) {
            console.warn('[notifications] unable to mark notification as read', error);
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch(API_ROUTES.readAll, {
                method: 'POST',
                headers: this.requestHeaders(),
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Failed to mark notifications as read');
            }

            const data = await response.json();
            this.notifications = this.notifications.map((notification) => ({
                ...notification,
                read_at: notification.read_at ?? new Date().toISOString(),
            }));
            this.notifications.forEach((notification) => {
                this.notificationMap.set(notification.id, notification);
            });
            this.importantQueue = [];
            this.unreadCount = Number.isFinite(data.unread_count) ? data.unread_count : 0;
            this.render();
        } catch (error) {
            console.warn('[notifications] unable to mark all notifications as read', error);
        }
    }

    applyUpdateFromServer(data) {
        if (data.notification) {
            this.notificationMap.set(data.notification.id, data.notification);
            const index = this.notifications.findIndex((item) => item.id === data.notification.id);
            if (index >= 0) {
                this.notifications[index] = data.notification;
            }
            if (data.notification.read_at) {
                this.removeFromImportantQueue(data.notification.id);
            }
        }

        if (Number.isFinite(data.unread_count)) {
            this.unreadCount = data.unread_count;
        } else {
            this.unreadCount = this.countUnread(this.notifications);
        }

        this.render();
    }

    removeFromImportantQueue(id) {
        if (!this.importantQueue.length) {
            return;
        }

        this.importantQueue = this.importantQueue.filter((item) => item.id !== id);
    }

    requestHeaders() {
        const headers = {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        };

        if (this.csrfToken) {
            headers['X-CSRF-TOKEN'] = this.csrfToken;
        }

        return headers;
    }

    countUnread(list) {
        return list.reduce((total, notification) => total + (notification.read_at ? 0 : 1), 0);
    }

    subscribeToRealtime() {
        if (!window.Echo || !this.userId) {
            return;
        }

        try {
            window.Echo.private(`users.${this.userId}.notifications`).listen('.user.notification.created', (event) => {
                this.handleRealtime(event);
            });
        } catch (error) {
            console.warn('[notifications] unable to subscribe to realtime channel', error);
        }
    }

    handleRealtime(event) {
        if (!event || !event.notification) {
            return;
        }

        const incoming = event.notification;
        const existingIndex = this.notifications.findIndex((item) => item.id === incoming.id);
        if (existingIndex >= 0) {
            this.notifications.splice(existingIndex, 1);
        }

        this.notifications.unshift(incoming);
        if (this.notifications.length > MAX_NOTIFICATIONS) {
            this.notifications.length = MAX_NOTIFICATIONS;
        }

        this.notificationMap.set(incoming.id, incoming);

        if (Number.isFinite(event.unread_count)) {
            this.unreadCount = event.unread_count;
        } else {
            this.unreadCount = this.countUnread(this.notifications);
        }

        this.render();
        this.enqueueImportant(incoming);
    }

    enqueueImportant(notification) {
        if (!notification || notification.read_at || notification.priority !== 'important') {
            return;
        }

        if (this.shownImportant.has(notification.id)) {
            return;
        }

        this.importantQueue.push(notification);
        this.processImportantQueue();
    }

    processImportantQueue() {
        if (!this.modal || this.modal.classList.contains('visible-important')) {
            return;
        }

        const next = this.importantQueue.shift();
        if (!next) {
            this.hideModal();
            return;
        }

        this.populateModal(next);
        this.shownImportant.add(next.id);
        this.modal.classList.remove('hidden');
        this.modal.classList.add('flex', 'visible-important');
    }

    populateModal(notification) {
        if (!this.modal) {
            return;
        }

        const { title, message, time, view } = this.modalElements;
        if (title) {
            title.textContent = notification.title || 'Notification';
        }
        if (message) {
            message.textContent = notification.message || '';
        }
        if (time) {
            time.textContent = notification.created_at_formatted ? `Received on ${notification.created_at_formatted}` : '';
        }
        if (view) {
            view.href = notification.link_url || '#';
            view.setAttribute('data-notification-id', String(notification.id));
            if (!notification.link_url) {
                view.classList.add('hidden');
            } else {
                view.classList.remove('hidden');
            }
        }

        this.modal.setAttribute('data-visible-notification-id', String(notification.id));
    }

    dismissModal() {
        const currentId = Number(this.modal?.getAttribute('data-visible-notification-id'));
        if (currentId) {
            this.markNotificationAsRead(currentId);
        }
        this.hideModal();
    }

    dismissModalIfVisible() {
        if (!this.modal || this.modal.classList.contains('hidden')) {
            return false;
        }

        this.dismissModal();
        return true;
    }

    hideModal() {
        if (!this.modal) {
            return;
        }

        this.modal.classList.add('hidden');
        this.modal.classList.remove('flex');
        this.modal.classList.remove('visible-important');
        this.modal.removeAttribute('data-visible-notification-id');

        if (this.importantQueue.length > 0) {
            setTimeout(() => this.processImportantQueue(), 150);
        }
    }
}

const notificationManager = new NotificationManager();
export default notificationManager;
