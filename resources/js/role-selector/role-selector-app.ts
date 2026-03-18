import { i18nVue } from 'laravel-vue-i18n';
import { createApp } from 'vue';
import RoleSelector from './RoleSelector.vue';

const el: HTMLElement | null = document.getElementById('role-selector');
if (el) {
    const app = createApp(RoleSelector, {
        availableRoles: JSON.parse(el.dataset.availableRoles ?? '[]'),
        selectedRoleIds: JSON.parse(el.dataset.selectedRoleIds ?? '[]'),
    });
    app.use(i18nVue, {
        resolve: async (lang: string) => {
            const langs = import.meta.glob('../../../lang/*.json');
            return await langs[`../../../lang/${lang}.json`]();
        },
    });
    app.mount(el);
}
