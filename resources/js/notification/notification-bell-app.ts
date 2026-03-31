import { i18nVue } from 'laravel-vue-i18n';
import { createApp } from 'vue';
import NotificationBell from './NotificationBell.vue';
import { subscribeToNotifications } from './notification-echo';
import { prependNotification, setUnreadCount } from './notification-store';

const mountPoint: HTMLElement | null = document.getElementById('app-notification-bell');

if (mountPoint) {
    const app = createApp(NotificationBell);

    app.use(i18nVue, {
        resolve: async (lang: string) => {
            const langs = import.meta.glob<{ default: Record<string, string> }>('../../../lang/*.json');

            return await langs[`../../../lang/${lang}.json`]();
        },
    });

    app.mount(mountPoint);

    const userId = mountPoint.dataset.userId;

    if (userId) {
        subscribeToNotifications(userId, prependNotification, setUnreadCount);
    }
}
