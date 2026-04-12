import { createApp } from 'vue';
import { useI18n } from '../../../shared/i18n/i18n';
import { parseShareableTeamsJson } from '../parse-shareable-teams';
import UsersGrid from './UsersGrid.vue';

const mountPoint: HTMLElement | null = document.getElementById('app-users-grid');

if (mountPoint) {
    const app = createApp(UsersGrid, {
        fetchUrl: mountPoint.dataset.fetchUrl ?? '',
        createUrl: mountPoint.dataset.createUrl ?? '',
        canCreate: mountPoint.dataset.canCreate === 'true',
        canShareTeam: mountPoint.dataset.canShareTeam === 'true',
        canShareGlobal: mountPoint.dataset.canShareGlobal === 'true',
        shareableTeams: parseShareableTeamsJson(mountPoint.dataset.shareableTeams ?? '[]'),
    });

    useI18n(app);
    app.mount(mountPoint);
}
