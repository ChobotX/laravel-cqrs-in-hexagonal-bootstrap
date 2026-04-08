import { i18nVue } from 'laravel-vue-i18n';
import { createApp } from 'vue';
import NotificationPreferences from './NotificationPreferences.vue';

for (const el of document.querySelectorAll<HTMLElement>('#app-notification-preferences')) {
    const preferences = JSON.parse(el.dataset.preferences ?? '[]');

    const app = createApp(NotificationPreferences, { initialPreferences: preferences });

    app.use(i18nVue, {
        resolve: async (lang: string) => {
            const langs = import.meta.glob<{ default: Record<string, string> }>('../../../lang/*.json');

            return await langs[`../../../lang/${lang}.json`]();
        },
    });

    app.mount(el);
}
