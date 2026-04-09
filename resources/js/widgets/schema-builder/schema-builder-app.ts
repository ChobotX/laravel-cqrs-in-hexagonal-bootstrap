import { createApp } from 'vue';
import { useI18n } from '../../shared/i18n/i18n';
import SchemaBuilder from './SchemaBuilder.vue';

for (const el of document.querySelectorAll<HTMLElement>('[data-schema-builder]')) {
    const app = createApp(SchemaBuilder, {
        action: el.dataset.action ?? '',
        csrf: el.dataset.csrf ?? '',
    });

    useI18n(app);
    app.mount(el);
}
