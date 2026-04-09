import { createApp } from 'vue';
import { useI18n } from '../../../shared/i18n/i18n';
import TeamsGrid from './TeamsGrid.vue';

const mountPoint: HTMLElement | null = document.getElementById('app-teams-grid');

if (mountPoint) {
    const app = createApp(TeamsGrid, {
        fetchUrl: mountPoint.dataset.fetchUrl ?? '',
        createUrl: mountPoint.dataset.createUrl ?? '',
        canCreate: mountPoint.dataset.canCreate === 'true',
        canRead: mountPoint.dataset.canRead === 'true',
        canUpdate: mountPoint.dataset.canUpdate === 'true',
        canDelete: mountPoint.dataset.canDelete === 'true',
        treeViewUrl: mountPoint.dataset.treeViewUrl ?? '',
        canShareTeam: mountPoint.dataset.canShareTeam === 'true',
        canShareGlobal: mountPoint.dataset.canShareGlobal === 'true',
        shareableTeams: JSON.parse(mountPoint.dataset.shareableTeams ?? '[]') as Array<{ id: string; name: string }>,
    });

    useI18n(app);
    app.mount(mountPoint);
}
