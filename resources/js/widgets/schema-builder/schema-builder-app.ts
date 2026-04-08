import { i18nVue } from 'laravel-vue-i18n';
import { createApp } from 'vue';
import SchemaBuilder from './SchemaBuilder.vue';

for (const el of document.querySelectorAll<HTMLElement>('[data-schema-builder]')) {
    const app = createApp(SchemaBuilder, {
        action: el.dataset.action ?? '',
        csrf: el.dataset.csrf ?? '',
    });

    app.use(i18nVue, {
        resolve: async (lang: string) => {
            const langs = import.meta.glob<{ default: Record<string, string> }>('../../../lang/*.json');

            return await langs[`../../../lang/${lang}.json`]();
        },
    });

    app.mount(el);
}
