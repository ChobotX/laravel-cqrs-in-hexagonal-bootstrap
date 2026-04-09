import { i18nVue } from 'laravel-vue-i18n';
import { createApp } from 'vue';
import FeatureFlagsGrid from './FeatureFlagsGrid.vue';

const mountPoint: HTMLElement | null = document.getElementById('app-feature-flags-grid');

if (mountPoint) {
    const app = createApp(FeatureFlagsGrid, {
        fetchUrl: mountPoint.dataset.fetchUrl ?? '',
        canUpdate: mountPoint.dataset.canUpdate === 'true',
        canShareTeam: mountPoint.dataset.canShareTeam === 'true',
        canShareGlobal: mountPoint.dataset.canShareGlobal === 'true',
        shareableTeams: JSON.parse(mountPoint.dataset.shareableTeams ?? '[]') as { id: string; name: string }[],
        groups: JSON.parse(mountPoint.dataset.groups ?? '[]') as { key: string; label: string }[],
    });

    app.use(i18nVue, {
        resolve: async (lang: string) => {
            const langs = import.meta.glob<{ default: Record<string, string> }>('../../../../lang/*.json');

            return await langs[`../../../../lang/${lang}.json`]();
        },
    });

    app.mount(mountPoint);
}
