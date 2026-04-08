import { i18nVue } from 'laravel-vue-i18n';
import { createApp } from 'vue';
import UsersGrid from './UsersGrid.vue';

const mountPoint: HTMLElement | null = document.getElementById('app-users-grid');

if (mountPoint) {
    const app = createApp(UsersGrid, {
        fetchUrl: mountPoint.dataset.fetchUrl ?? '',
        createUrl: mountPoint.dataset.createUrl ?? '',
        canCreate: mountPoint.dataset.canCreate === 'true',
        canShareTeam: mountPoint.dataset.canShareTeam === 'true',
        canShareGlobal: mountPoint.dataset.canShareGlobal === 'true',
        shareableTeams: JSON.parse(mountPoint.dataset.shareableTeams ?? '[]') as Array<{ id: string; name: string }>,
    });

    app.use(i18nVue, {
        resolve: async (lang: string) => {
            const langs = import.meta.glob<{ default: Record<string, string> }>('../../../../lang/*.json');

            return await langs[`../../../../lang/${lang}.json`]();
        },
    });

    app.mount(mountPoint);
}
