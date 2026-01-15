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

    Alpine.data('genreSelector', (genres, initialPreferred, initialAvoided, wire) => ({
        genres: genres || [],
        preferred: initialPreferred || [],
        avoided: initialAvoided || [],
        syncTimeout: null,

        init() {
            // Watch for changes and sync to Livewire
            this.$watch('preferred', () => this.debouncedSync());
            this.$watch('avoided', () => this.debouncedSync());
        },

        togglePreferred(genreId) {
            genreId = parseInt(genreId, 10);

            // Validate genre ID
            if (!genreId || genreId <= 0) {
                return;
            }

            // Check if already preferred
            const index = this.preferred.indexOf(genreId);
            if (index !== -1) {
                // Remove from preferred
                this.preferred.splice(index, 1);
                return;
            }

            // Check limit
            if (this.preferred.length >= 3) {
                Alpine.store('toasts').push('⚠️ Pick up to 3 favorites.', 'warning');
                return;
            }

            // Remove from avoided if present
            const avoidedIndex = this.avoided.indexOf(genreId);
            if (avoidedIndex !== -1) {
                this.avoided.splice(avoidedIndex, 1);
            }

            // Add to preferred
            this.preferred.push(genreId);
        },

        toggleAvoided(genreId) {
            genreId = parseInt(genreId, 10);

            // Validate genre ID
            if (!genreId || genreId <= 0) {
                return;
            }

            // Check if already avoided
            const index = this.avoided.indexOf(genreId);
            if (index !== -1) {
                // Remove from avoided
                this.avoided.splice(index, 1);
                return;
            }

            // Check limit
            if (this.avoided.length >= 3) {
                Alpine.store('toasts').push('⚠️ Pick up to 3 passes.', 'warning');
                return;
            }

            // Remove from preferred if present
            const preferredIndex = this.preferred.indexOf(genreId);
            if (preferredIndex !== -1) {
                this.preferred.splice(preferredIndex, 1);
            }

            // Add to avoided
            this.avoided.push(genreId);
        },

        isPreferred(genreId) {
            return this.preferred.includes(parseInt(genreId, 10));
        },

        isAvoided(genreId) {
            return this.avoided.includes(parseInt(genreId, 10));
        },

        get preferredCount() {
            return this.preferred.length;
        },

        get avoidedCount() {
            return this.avoided.length;
        },

        get preferredAtLimit() {
            return this.preferred.length >= 3;
        },

        get avoidedAtLimit() {
            return this.avoided.length >= 3;
        },

        debouncedSync() {
            if (this.syncTimeout) {
                window.clearTimeout(this.syncTimeout);
            }

            this.syncTimeout = setTimeout(() => {
                this.syncToLivewire();
            }, 500);
        },

        async syncToLivewire() {
            if (wire?.$call) {
                try {
                    await wire.$call('updateGenrePreferences', this.preferred, this.avoided);
                } catch (error) {
                    window.console.error('Failed to sync genre preferences:', error);
                }
            }
        },
    }));
});
