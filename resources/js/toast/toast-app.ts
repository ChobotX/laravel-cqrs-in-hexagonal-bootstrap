import { i18nVue } from 'laravel-vue-i18n';
import { createApp } from 'vue';
import ToastContainer from './ToastContainer.vue';
import { error, success } from './toast-queue';

const mountPoint: HTMLElement | null = document.getElementById('app-toast');
if (mountPoint) {
    const app = createApp(ToastContainer);
    app.use(i18nVue, {
        resolve: async (lang: string) => {
            const langs = import.meta.glob('../../../lang/*.json');
            return await langs[`../../../lang/${lang}.json`]();
        },
    });
    app.mount(mountPoint);
}

window.appToast = { success, error };
