import { createApp } from 'vue';
import { useI18n } from '../../../shared/i18n/i18n';
import RolesGrid from './RolesGrid.vue';

const mountPoint: HTMLElement | null = document.getElementById('app-roles-grid');

if (mountPoint) {
    const app = createApp(RolesGrid, {
        fetchUrl: mountPoint.dataset.fetchUrl ?? '',
        createUrl: mountPoint.dataset.createUrl ?? '',
        canUpdate: mountPoint.dataset.canUpdate === 'true',
        canShareTeam: mountPoint.dataset.canShareTeam === 'true',
        canShareGlobal: mountPoint.dataset.canShareGlobal === 'true',
        shareableTeams: JSON.parse(mountPoint.dataset.shareableTeams ?? '[]') as Array<{ id: string; name: string }>,
    });

    useI18n(app);
    app.mount(mountPoint);
}
