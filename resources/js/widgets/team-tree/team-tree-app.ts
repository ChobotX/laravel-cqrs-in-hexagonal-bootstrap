import { createApp } from 'vue';
import TeamTree from './TeamTree.vue';

for (const el of document.querySelectorAll<HTMLElement>('[data-team-tree]')) {
    const app = createApp(TeamTree, {
        fetchUrl: el.dataset.fetchUrl ?? '',
    });
    app.mount(el);
}
