import { createApp } from 'vue';
import { useI18n } from '../../shared/i18n/i18n';
import SharePanel from './SharePanel.vue';

for (const el of document.querySelectorAll<HTMLElement>('[data-share-panel]')) {
    const resourceType = el.dataset.resourceType;
    const resourceId = el.dataset.resourceId;
    const sharesUrl = el.dataset.sharesUrl;
    const searchUrl = el.dataset.searchUrl;

    if (!resourceType || !resourceId || !sharesUrl || !searchUrl) {
        continue;
    }

    const app = createApp(SharePanel, {
        resourceType,
        resourceId,
        sharesUrl,
        searchUrl,
    });
    useI18n(app);
    app.mount(el);
}
