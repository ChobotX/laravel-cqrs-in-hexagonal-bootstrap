import { createApp } from 'vue';
import { useI18n } from '../i18n/i18n';
import ToastContainer from './ToastContainer.vue';
import { error, success } from './toast-queue';

const mountPoint: HTMLElement | null = document.getElementById('app-toast');
if (mountPoint) {
    const app = createApp(ToastContainer);
    useI18n(app);
    app.mount(mountPoint);
}

window.appToast = { success, error };
