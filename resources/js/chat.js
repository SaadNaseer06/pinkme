import axios from "axios";

const DEFAULT_TIME_OPTIONS = { hour: '2-digit', minute: '2-digit' };

class ChatConversation {
    constructor(root) {
        this.root = root;
        this.thread = root.querySelector('[data-chat-thread]');
        this.form = root.querySelector('[data-chat-form]');
        this.input = this.form ? this.form.querySelector('input[name="content"]') : null;
        this.fileInput = this.form ? this.form.querySelector('[data-chat-upload]') : null;
        this.filePreview = this.form ? this.form.querySelector('[data-chat-file-preview]') : null;
        this.fileNameSpan = this.form ? this.form.querySelector('[data-chat-file-name]') : null;
        this.fileClearBtn = this.form ? this.form.querySelector('[data-chat-file-clear]') : null;
        this.submitButton = this.form ? this.form.querySelector('button[type="submit"]') : null;
        this.currentUserId = Number(root.dataset.currentUserId);
        this.activeContactId = Number(root.dataset.activeContactId);
        this.fetchUrl = root.dataset.fetchUrl;
        this.sendUrl = root.dataset.sendUrl;
        this.defaultAvatar = root.dataset.defaultAvatar || '';
        this.selfAvatar = root.dataset.selfAvatar || '';
        this.contactItems = document.querySelectorAll('[data-contact-item]');
        this.contactMap = new Map();
        this.isSending = false;

        this.contactItems.forEach((item) => {
            const id = Number(item.dataset.contactId);
            if (!Number.isNaN(id)) {
                this.contactMap.set(id, item);
            }
        });

        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleIncoming = this.handleIncoming.bind(this);
        this.handleFileChange = this.handleFileChange.bind(this);
        this.handleFileClear = this.handleFileClear.bind(this);

        this.initialize();
    }

    initialize() {
        if (this.form && this.input) {
            this.form.addEventListener('submit', this.handleSubmit);
        }

        if (this.fileInput) {
            this.fileInput.addEventListener('change', this.handleFileChange);
        }
        if (this.fileClearBtn) {
            this.fileClearBtn.addEventListener('click', this.handleFileClear);
        }

        this.scrollToBottom();
        this.subscribeToUpdates();
        this.startActivityPing();
    }

    handleFileChange() {
        const file = this.fileInput?.files?.[0];
        if (this.filePreview && this.fileNameSpan) {
            if (file) {
                this.fileNameSpan.textContent = file.name;
                this.filePreview.classList.remove('hidden');
            } else {
                this.filePreview.classList.add('hidden');
                this.fileNameSpan.textContent = '';
            }
        }
    }

    handleFileClear(event) {
        event.preventDefault();
        if (this.fileInput) {
            this.fileInput.value = '';
        }
        if (this.filePreview && this.fileNameSpan) {
            this.filePreview.classList.add('hidden');
            this.fileNameSpan.textContent = '';
        }
    }

    /** Ping so we're marked "in chat" and don't get new-message emails while viewing chat. */
    startActivityPing() {
        const activityUrl = this.root?.dataset?.activityUrl || '/chat/activity';
        const ping = () => {
            axios.post(activityUrl).catch(() => {});
        };
        ping();
        this._activityInterval = setInterval(ping, 60000);
    }

    async handleSubmit(event) {
        event.preventDefault();

        const content = this.input ? this.input.value.trim() : '';
        const attachment = this.fileInput?.files?.[0] ?? null;

        if ((!content && !attachment) || this.isSending) {
            return;
        }

        const payload = new FormData();
        if (content) {
            payload.append('content', content);
        }
        if (attachment) {
            payload.append('attachment', attachment);
        }

        this.isSending = true;
        this.toggleForm(true);

        try {
            const response = await axios.post(this.sendUrl, payload, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });

            const message = response?.data?.message;
            if (message) {
                this.appendMessage(message, true);
                this.updateContactPreview(message, { markRead: true });
            }

            if (this.input) {
                this.input.value = '';
            }
            if (this.fileInput) {
                this.fileInput.value = '';
            }
            this.handleFileChange();
        } catch (error) {
            console.error('[chat] unable to send message', error);
            window.alert('Unable to send message. Please try again.');
        } finally {
            this.toggleForm(false);
            this.isSending = false;
        }
    }

    toggleForm(disabled) {
        if (this.input) {
            this.input.disabled = disabled;
        }
        if (this.submitButton) {
            this.submitButton.disabled = disabled;
            this.submitButton.classList.toggle('opacity-60', disabled);
        }
        if (this.fileInput) {
            this.fileInput.disabled = disabled;
        }
    }

    subscribeToUpdates() {
        if (!window.Echo || !this.currentUserId) {
            return;
        }

        window.Echo.private(`users.${this.currentUserId}.messages`).listen('.chat.message', this.handleIncoming);
    }

    handleIncoming(payload) {
        const message = payload?.message ?? payload;
        if (!message) {
            return;
        }

        const otherUserId = message.sender_id === this.currentUserId
            ? message.receiver_id
            : message.sender_id;

        if (otherUserId === this.activeContactId) {
            this.appendMessage(message, false);
            this.updateContactPreview(message, { markRead: true });
        } else {
            this.updateContactPreview(message, { incrementUnread: true });
        }
    }

    appendMessage(message, scroll = true) {
        if (!this.thread || !message) {
            return;
        }

        const isOwn = message.sender_id === this.currentUserId;
        const container = document.createElement('div');
        container.className = `flex ${isOwn ? 'justify-end' : ''}`;
        container.dataset.messageId = String(message.id);

        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-end gap-3 max-w-[70%]';

        if (!isOwn) {
            wrapper.appendChild(this.buildAvatarColumn(message.sender, message.sent_at_display));
            wrapper.appendChild(this.buildMessageBubble(message, false));
        } else {
            wrapper.appendChild(this.buildMessageBubble(message, true));
            wrapper.appendChild(this.buildAvatarColumn({ avatar_url: this.selfAvatar, name: 'You' }, message.sent_at_display));
        }

        container.appendChild(wrapper);
        this.thread.appendChild(container);

        if (scroll) {
            this.scrollToBottom();
        }
    }

    buildAvatarColumn(user = {}, timeText = '') {
        const column = document.createElement('div');
        column.className = 'flex flex-col items-center';

        const avatar = document.createElement('img');
        avatar.src = user?.avatar_url || this.defaultAvatar;
        avatar.alt = user?.name || 'User';
        avatar.className = 'w-10 h-10 rounded-full object-cover';
        column.appendChild(avatar);

        if (timeText) {
            const time = document.createElement('span');
            time.className = 'text-[10px] text-[#B1A4AD] mt-1';
            time.textContent = timeText;
            column.appendChild(time);
        }

        return column;
    }

    buildMessageBubble(message, isOwn) {
        const { content, attachment } = message ?? {};
        const bubble = document.createElement('div');
        bubble.className = `${isOwn ? 'bg-[#9E2469] text-white rounded-br-sm text-left' : 'bg-white text-[#4C4047] border border-[#E5D2DE] rounded-bl-sm'} text-sm leading-relaxed px-4 py-3 rounded-2xl shadow-sm`;

        if (content && content.trim().length > 0) {
            const textBlock = document.createElement('div');
            textBlock.className = 'whitespace-pre-line';
            textBlock.textContent = content;
            bubble.appendChild(textBlock);
        }

        if (attachment) {
            const container = document.createElement('div');
            container.className = 'mt-3';

            if (attachment.is_image) {
                const link = document.createElement('a');
                link.href = attachment.url;
                link.target = '_blank';
                link.className = 'block';

                const image = document.createElement('img');
                image.src = attachment.url;
                image.alt = attachment.name || 'Attachment';
                image.className = 'max-w-[220px] rounded-lg shadow-sm';

                link.appendChild(image);
                container.appendChild(link);
            } else {
                const link = document.createElement('a');
                link.href = attachment.url;
                link.target = '_blank';
                link.className = `inline-flex items-center gap-2 text-sm ${isOwn ? 'text-white' : 'text-[#9E2469]'} hover:underline`;

                const icon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                icon.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
                icon.setAttribute('viewBox', '0 0 24 24');
                icon.setAttribute('fill', 'none');
                icon.setAttribute('stroke', 'currentColor');
                icon.setAttribute('stroke-width', '1.5');
                icon.classList.add('h-4', 'w-4');

                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                path.setAttribute('stroke-linecap', 'round');
                path.setAttribute('stroke-linejoin', 'round');
                path.setAttribute('d', 'M8.25 15.75L15.75 8.25M9 9l6 6M6 12l6 6 6-6');
                icon.appendChild(path);

                const name = document.createElement('span');
                name.textContent = attachment.name || 'Download attachment';

                link.appendChild(icon);
                link.appendChild(name);

                if (attachment.size) {
                    const size = document.createElement('span');
                    size.className = `text-xs ${isOwn ? 'text-[#F7D7EB]' : 'text-[#91848C]'}`;
                    size.textContent = `(${this.formatFileSize(attachment.size)})`;
                    link.appendChild(size);
                }

                container.appendChild(link);
            }

            bubble.appendChild(container);
        }

        return bubble;
    }

    scrollToBottom() {
        if (!this.thread) {
            return;
        }

        requestAnimationFrame(() => {
            this.thread.scrollTop = this.thread.scrollHeight;
        });
    }

    updateContactPreview(message, { incrementUnread = false, markRead = false } = {}) {
        const otherUserId = message.sender_id === this.currentUserId
            ? message.receiver_id
            : message.sender_id;

        const item = this.contactMap.get(otherUserId);
        if (!item) {
            return;
        }

        const preview = item.querySelector('[data-contact-last]');
        const time = item.querySelector('[data-contact-time]');
        const badge = item.querySelector('[data-contact-unread]');

        if (preview) {
            let previewText = '';
            if (message.content && message.content.trim().length > 0) {
                previewText = message.content;
            } else if (message.attachment) {
                previewText = message.attachment.name || 'Attachment';
            }
            preview.textContent = previewText;
        }

        if (time) {
            time.textContent = this.formatDisplayTime(message.sent_at, message.sent_at_display);
        }

        if (badge) {
            if (markRead) {
                badge.classList.add('hidden');
                badge.textContent = '0';
            } else if (incrementUnread) {
                const current = parseInt(badge.textContent || '0', 10) || 0;
                badge.textContent = String(current + 1);
                badge.classList.remove('hidden');
            }
        }
    }

    formatDisplayTime(sentAtIso, formatted) {
        if (formatted) {
            return formatted;
        }

        try {
            if (sentAtIso) {
                const parsed = new Date(sentAtIso);
                if (!Number.isNaN(parsed.getTime())) {
                    return parsed.toLocaleTimeString([], DEFAULT_TIME_OPTIONS);
                }
            }
        } catch (error) {
            // ignore formatting errors
        }

        return '';
    }

    formatFileSize(bytes) {
        if (!Number.isFinite(bytes)) {
            return '';
        }

        if (bytes < 1024) {
            return `${bytes} B`;
        }

        const units = ['KB', 'MB', 'GB'];
        let size = bytes / 1024;
        let unitIndex = 0;

        while (size >= 1024 && unitIndex < units.length - 1) {
            size /= 1024;
            unitIndex += 1;
        }

        return `${size.toFixed(size >= 10 ? 0 : 1)} ${units[unitIndex]}`;
    }
}

class ChatManager {
    init() {
        const roots = document.querySelectorAll('[data-chat-app]');
        roots.forEach((root) => {
            if (!root.__chatInstance) {
                root.__chatInstance = new ChatConversation(root);
            }
        });
    }
}

export default new ChatManager();

