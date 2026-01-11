document.addEventListener('alpine:init', () => {
    const copyToClipboard = async (text) => {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return true;
        }

        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', '');
        textarea.style.position = 'absolute';
        textarea.style.left = '-9999px';
        document.body.appendChild(textarea);
        textarea.select();
        const successful = document.execCommand('copy');
        document.body.removeChild(textarea);
        return successful;
    };

    Alpine.store('toasts', {
        list: [],
        push(message, type = 'info') {
            const id = `${Date.now()}-${Math.random().toString(16).slice(2)}`;
            const toast = Alpine.reactive({ id, message, type, state: 'entering' });
            this.list.push(toast);

            // Trigger enter animation
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    toast.state = 'visible';
                });
            });

            // Auto-hide after 3 seconds
            setTimeout(() => this.hide(id), 3000);
        },
        hide(id) {
            const toast = this.list.find((item) => item.id === id);
            if (!toast) {
                return;
            }
            toast.state = 'leaving';
            setTimeout(() => this.remove(id), 400);
        },
        remove(id) {
            this.list = this.list.filter((toast) => toast.id !== id);
        },
    });

    Alpine.store('roomReveal', {
        show: false,
    });

    Alpine.data('roomCodePanel', (successMessage = 'Room code copied') => ({
        async copy(text) {
            const successful = await copyToClipboard(text);
            if (successful) {
                Alpine.store('toasts').push(successMessage, 'success');
            }
        },
    }));

    Alpine.data('clipboardHelper', (successMessage = 'Copied') => ({
        async copy(text) {
            const successful = await copyToClipboard(text);
            if (successful) {
                Alpine.store('toasts').push(successMessage, 'success');
            }
        },
    }));

    Alpine.data('kickModal', (wire) => ({
        state: 'closed',
        targetId: null,
        targetName: '',
        openKick(id, name) {
            this.targetId = id;
            this.targetName = name || 'Guest';
            this.state = 'entering';

            // Trigger enter animation
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    this.state = 'open';
                });
            });
        },
        close() {
            this.state = 'leaving';
            setTimeout(() => {
                this.state = 'closed';
                this.targetId = null;
                this.targetName = '';
            }, 300);
        },
        confirmKick() {
            if (!this.targetId) {
                this.close();
                return;
            }
            if (wire?.kickParticipant) {
                wire.kickParticipant(this.targetId);
            }
            this.close();
        },
        get isVisible() {
            return this.state !== 'closed';
        },
    }));
});
