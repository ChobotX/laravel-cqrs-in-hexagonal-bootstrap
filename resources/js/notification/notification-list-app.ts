import { i18nVue } from 'laravel-vue-i18n';
import { createApp } from 'vue';
import NotificationList from './NotificationList.vue';

const mountPoint: HTMLElement | null = document.getElementById('app-notification-list');

if (mountPoint) {
    const app = createApp(NotificationList);

    app.use(i18nVue, {
        resolve: async (lang: string) => {
            const langs = import.meta.glob<{ default: Record<string, string> }>('../../../lang/*.json');

            return await langs[`../../../lang/${lang}.json`]();
        },
    });

    app.mount(mountPoint);
}
