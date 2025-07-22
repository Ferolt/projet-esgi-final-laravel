class OfflineManager {
    constructor() {
        this.isOnline = navigator.onLine;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updateConnectionStatus();
        this.showInstallPrompt();
    }

    setupEventListeners() {
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.updateConnectionStatus();
            this.showNotification('Connexion rétablie !', 'success');
            this.syncOfflineData();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.updateConnectionStatus();
            this.showNotification('Mode hors ligne activé', 'warning');
        });

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.addEventListener('controllerchange', () => {
                this.showNotification('Application mise à jour !', 'success');
            });
        }
    }

    updateConnectionStatus() {
        const statusElement = document.getElementById('connection-status');
        if (!statusElement) return;

        statusElement.className = 'connection-status';

        if (this.isOnline) {
            statusElement.innerHTML = '<i class="fas fa-wifi text-green-500"></i>';
            statusElement.classList.add('online');
            statusElement.title = 'En ligne';
        } else {
            statusElement.innerHTML = '<i class="fas fa-wifi-slash text-red-500"></i>';
            statusElement.classList.add('offline');
            statusElement.title = 'Hors ligne';
        }
    }

    showNotification(message, type = 'info', duration = 3000) {
        const existingNotifications = document.querySelectorAll('.pwa-notification');
        existingNotifications.forEach(notification => notification.remove());

        const notification = document.createElement('div');
        notification.className = `pwa-notification ${type}`;
        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fas ${this.getNotificationIcon(type)}"></i>
                <div>
                    <div style="font-weight: 600; margin-bottom: 4px;">${message}</div>
                    ${this.getNotificationSubtext(type)}
                </div>
                <button onclick="this.parentElement.parentElement.remove()"
                        style="background: none; border: none; color: inherit; cursor: pointer; font-size: 18px; margin-left: auto;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => notification.classList.add('show'), 100);

        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }

    getNotificationIcon(type) {
        const icons = {
            'success': 'fa-check-circle',
            'warning': 'fa-exclamation-triangle',
            'error': 'fa-times-circle',
            'info': 'fa-info-circle'
        };
        return icons[type] || icons.info;
    }

    getNotificationSubtext(type) {
        const subtexts = {
            'success': '<small style="opacity: 0.8;">Toutes les fonctionnalités sont disponibles</small>',
            'warning': '<small style="opacity: 0.8;">Fonctionnalités limitées en mode hors ligne</small>',
            'error': '<small style="opacity: 0.8;">Une erreur est survenue</small>',
            'info': '<small style="opacity: 0.8;">Information importante</small>'
        };
        return subtexts[type] || subtexts.info;
    }

    async syncOfflineData() {
        const pendingData = this.getStoredOfflineData();

        if (pendingData.length > 0) {
            this.showNotification('Synchronisation en cours...', 'info');

            try {
                await this.sendDataToServer(pendingData);
                this.clearStoredOfflineData();
                this.showNotification('Données synchronisées avec succès !', 'success');
            } catch (error) {
                console.error('Erreur lors de la synchronisation:', error);
                this.showNotification('Erreur lors de la synchronisation', 'error');
            }
        }
    }

    getStoredOfflineData() {
        const data = localStorage.getItem('kanboard_offline_data');
        return data ? JSON.parse(data) : [];
    }

    clearStoredOfflineData() {
        localStorage.removeItem('kanboard_offline_data');
    }

    async sendDataToServer(data) {
        const response = await fetch('/api/sync-offline-data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            body: JSON.stringify({ data })
        });

        if (!response.ok) {
            throw new Error('Erreur lors de la synchronisation');
        }

        return response.json();
    }

    storeOfflineAction(action) {
        if (!this.isOnline) {
            const offlineData = this.getStoredOfflineData();
            offlineData.push({
                action,
                timestamp: Date.now(),
                id: Math.random().toString(36).substr(2, 9)
            });
            localStorage.setItem('kanboard_offline_data', JSON.stringify(offlineData));
        }
    }

    showInstallPrompt() {
        let deferredPrompt = null;

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;

            this.showCustomInstallPrompt(deferredPrompt);
        });

        window.addEventListener('appinstalled', () => {
            this.showNotification('Application installée avec succès !', 'success');
            this.hideCustomInstallPrompt();
        });
    }

    showCustomInstallPrompt(deferredPrompt) {
        if (window.matchMedia('(display-mode: standalone)').matches) {
            return;
        }

        const installPrompt = document.createElement('div');
        installPrompt.id = 'install-prompt';
        installPrompt.className = 'install-prompt';
        installPrompt.innerHTML = `
            <i class="fas fa-download"></i>
            <span>Installer Kanboard</span>
        `;

        installPrompt.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;

                if (outcome === 'accepted') {
                    this.showNotification('Installation en cours...', 'info');
                }

                deferredPrompt = null;
                this.hideCustomInstallPrompt();
            }
        });

        document.body.appendChild(installPrompt);

        setTimeout(() => {
            this.hideCustomInstallPrompt();
        }, 30000);
    }

    hideCustomInstallPrompt() {
        const installPrompt = document.getElementById('install-prompt');
        if (installPrompt) {
            installPrompt.remove();
        }
    }

    disableElementsOffline() {
        if (!this.isOnline) {
            const onlineOnlyElements = document.querySelectorAll('[data-requires-online]');
            onlineOnlyElements.forEach(element => {
                element.classList.add('offline-disabled');
            });

            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                if (!form.dataset.offlineReady) {
                    form.classList.add('offline-disabled');
                }
            });
        }
    }

    enableElementsOnline() {
        if (this.isOnline) {
            const disabledElements = document.querySelectorAll('.offline-disabled');
            disabledElements.forEach(element => {
                element.classList.remove('offline-disabled');
            });
        }
    }

    static storeOfflineAction(action) {
        if (window.offlineManager) {
            window.offlineManager.storeOfflineAction(action);
        }
    }

    static isOnline() {
        return window.offlineManager ? window.offlineManager.isOnline : navigator.onLine;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.offlineManager = new OfflineManager();

    window.storeOfflineAction = OfflineManager.storeOfflineAction;
    window.isOnline = OfflineManager.isOnline;
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = OfflineManager;
}
