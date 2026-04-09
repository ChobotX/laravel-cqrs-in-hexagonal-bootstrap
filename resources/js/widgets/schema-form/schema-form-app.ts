import { createApp } from 'vue';
import { useI18n } from '../../shared/i18n/i18n';
import SchemaForm from './SchemaForm.vue';

for (const el of document.querySelectorAll<HTMLElement>('[data-schema-form]')) {
    const app = createApp(SchemaForm, {
        schema: el.dataset.schema ?? '{}',
        values: el.dataset.values ?? '{}',
        errors: el.dataset.errors ?? '{}',
        fieldPrefix: el.dataset.fieldPrefix ?? 'data',
    });

    useI18n(app);
    app.mount(el);
}
