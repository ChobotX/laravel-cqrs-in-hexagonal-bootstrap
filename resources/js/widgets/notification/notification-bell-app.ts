import { createApp } from 'vue';
import { useI18n } from '../../shared/i18n/i18n';
import NotificationBell from './NotificationBell.vue';
import { subscribeToNotifications } from './notification-echo';
import { prependNotification, setUnreadCount } from './notification-store';

const mountPoint: HTMLElement | null = document.getElementById('app-notification-bell');

if (mountPoint) {
    const app = createApp(NotificationBell);
    useI18n(app);
    app.mount(mountPoint);

    const userId = mountPoint.dataset.userId;

    if (userId) {
        subscribeToNotifications(userId, prependNotification, setUnreadCount);
    }
}
