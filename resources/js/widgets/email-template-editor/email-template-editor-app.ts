import { createApp } from 'vue';
import { useI18n } from '../../shared/i18n/i18n';
import EmailTemplateEditor from './EmailTemplateEditor.vue';
import { parseTemplateVariablesJson } from './parse-template-variables';

for (const el of document.querySelectorAll<HTMLElement>('[data-email-template-editor]')) {
    const rawVariables = parseTemplateVariablesJson(el.dataset.variables ?? '{}');
    const variables = Object.entries(rawVariables).map(([name, config]) => ({
        name,
        ...config,
    }));

    const app = createApp(EmailTemplateEditor, {
        subjectTemplate: el.dataset.subjectTemplate ?? '',
        bodyTemplate: el.dataset.bodyTemplate ?? '',
        variables,
        previewUrl: el.dataset.previewUrl ?? '',
        templateType: el.dataset.type ?? '',
        locale: el.dataset.locale ?? '',
    });

    useI18n(app);
    app.mount(el);
}
