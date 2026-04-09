import { createApp } from 'vue';
import { useI18n } from '../../shared/i18n/i18n';
import NotificationList from './NotificationList.vue';

const mountPoint: HTMLElement | null = document.getElementById('app-notification-list');

if (mountPoint) {
    const app = createApp(NotificationList);
    useI18n(app);
    app.mount(mountPoint);
}
