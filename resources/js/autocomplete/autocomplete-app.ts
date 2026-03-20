import { createApp } from 'vue';
import Autocomplete from './Autocomplete.vue';

for (const el of document.querySelectorAll<HTMLElement>('[data-autocomplete]')) {
    const app = createApp(Autocomplete, {
        searchUrl: el.dataset.searchUrl ?? '',
        excludeIds: JSON.parse(el.dataset.excludeIds ?? '[]'),
        inputName: el.dataset.inputName ?? 'id',
        placeholder: el.dataset.placeholder ?? '',
        noResultsText: el.dataset.noResultsText ?? 'No results found.',
    });
    app.mount(el);
}
