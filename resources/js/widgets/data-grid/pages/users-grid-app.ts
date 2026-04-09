import { createApp } from 'vue';
import { useI18n } from '../../../shared/i18n/i18n';
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

    useI18n(app);
    app.mount(mountPoint);
}
