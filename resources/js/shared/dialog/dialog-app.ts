import { createApp } from 'vue';
import { useI18n } from '../i18n/i18n';
import DialogRoot from './DialogRoot.vue';
import { alert, confirm } from './dialog-queue';

const mountPoint: HTMLElement | null = document.getElementById('app-dialog');
if (mountPoint) {
    const app = createApp(DialogRoot);
    useI18n(app);
    app.mount(mountPoint);
}

window.appDialog = { confirm, alert };
