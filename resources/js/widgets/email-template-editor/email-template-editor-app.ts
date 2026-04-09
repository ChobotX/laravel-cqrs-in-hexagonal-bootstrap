import { createApp } from 'vue';
import EmailTemplateEditor from './EmailTemplateEditor.vue';

for (const el of document.querySelectorAll<HTMLElement>('[data-email-template-editor]')) {
    const rawVariables = JSON.parse(el.dataset.variables ?? '{}') as Record<
        string,
        { description: string; sensitive: boolean; sample: string }
    >;
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

    app.mount(el);
}
