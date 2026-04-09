import { createApp } from 'vue';
import { useI18n } from '../../shared/i18n/i18n';
import NotificationPreferences from './NotificationPreferences.vue';

for (const el of document.querySelectorAll<HTMLElement>('#app-notification-preferences')) {
    const preferences = JSON.parse(el.dataset.preferences ?? '[]');

    const app = createApp(NotificationPreferences, { initialPreferences: preferences });
    useI18n(app);
    app.mount(el);
}
