import { createApp } from 'vue';
import { useI18n } from '../../../shared/i18n/i18n';
import { parseFeatureFlagGroupsJson } from '../parse-feature-flag-groups';
import { parseShareableTeamsJson } from '../parse-shareable-teams';
import FeatureFlagsGrid from './FeatureFlagsGrid.vue';

const mountPoint: HTMLElement | null = document.getElementById('app-feature-flags-grid');

if (mountPoint) {
    const app = createApp(FeatureFlagsGrid, {
        fetchUrl: mountPoint.dataset.fetchUrl ?? '',
        canUpdate: mountPoint.dataset.canUpdate === 'true',
        canShareTeam: mountPoint.dataset.canShareTeam === 'true',
        canShareGlobal: mountPoint.dataset.canShareGlobal === 'true',
        shareableTeams: parseShareableTeamsJson(mountPoint.dataset.shareableTeams ?? '[]'),
        groups: parseFeatureFlagGroupsJson(mountPoint.dataset.groups ?? '[]'),
    });

    useI18n(app);
    app.mount(mountPoint);
}
