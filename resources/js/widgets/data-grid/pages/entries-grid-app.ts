import { createApp } from 'vue';
import { useI18n } from '../../../shared/i18n/i18n';
import { parseShareableTeamsJson } from '../parse-shareable-teams';
import EntriesGrid from './EntriesGrid.vue';

const mountPoint: HTMLElement | null = document.getElementById('app-entries-grid');

if (mountPoint) {
    const app = createApp(EntriesGrid, {
        fetchUrl: mountPoint.dataset.fetchUrl ?? '',
        createUrl: mountPoint.dataset.createUrl ?? '',
        definitionName: mountPoint.dataset.definitionName ?? '',
        canShareTeam: mountPoint.dataset.canShareTeam === 'true',
        canShareGlobal: mountPoint.dataset.canShareGlobal === 'true',
        shareableTeams: parseShareableTeamsJson(mountPoint.dataset.shareableTeams ?? '[]'),
    });

    useI18n(app);
    app.mount(mountPoint);
}
