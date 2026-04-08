import { i18nVue } from 'laravel-vue-i18n';
import { createApp } from 'vue';
import DialogRoot from './DialogRoot.vue';
import { alert, confirm } from './dialog-queue';

const mountPoint: HTMLElement | null = document.getElementById('app-dialog');
if (mountPoint) {
    const app = createApp(DialogRoot);
    app.use(i18nVue, {
        resolve: async (lang: string) => {
            const langs = import.meta.glob('../../../lang/*.json');
            return await langs[`../../../lang/${lang}.json`]();
        },
    });
    app.mount(mountPoint);
}

window.appDialog = { confirm, alert };
