import { i18nVue } from 'laravel-vue-i18n';
import { createApp } from 'vue';
import SchemaForm from './SchemaForm.vue';

for (const el of document.querySelectorAll<HTMLElement>('[data-schema-form]')) {
    const app = createApp(SchemaForm, {
        schema: el.dataset.schema ?? '{}',
        values: el.dataset.values ?? '{}',
        errors: el.dataset.errors ?? '{}',
        fieldPrefix: el.dataset.fieldPrefix ?? 'data',
    });

    app.use(i18nVue, {
        resolve: async (lang: string) => {
            const langs = import.meta.glob<{ default: Record<string, string> }>('../../../lang/*.json');

            return await langs[`../../../lang/${lang}.json`]();
        },
    });

    app.mount(el);
}
