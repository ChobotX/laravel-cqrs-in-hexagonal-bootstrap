import { i18nVue } from 'laravel-vue-i18n';
import { createApp } from 'vue';
import ChipSelector from './ChipSelector.vue';
import LazyChipSelector from './LazyChipSelector.vue';

for (const el of document.querySelectorAll<HTMLElement>('[data-chip-selector]')) {
    const searchUrl = el.dataset.searchUrl;
    const isLazy = !!searchUrl;

    const component = isLazy ? LazyChipSelector : ChipSelector;
    const props = isLazy
        ? {
              searchUrl,
              selectedItems: JSON.parse(el.dataset.selectedItems ?? '[]'),
              inputName: el.dataset.inputName ?? 'items[]',
              placeholder: el.dataset.placeholder,
              noResultsText: el.dataset.noResultsText,
          }
        : {
              options: JSON.parse(el.dataset.options ?? '[]'),
              modelValue: JSON.parse(el.dataset.selectedIds ?? '[]'),
              inputName: el.dataset.inputName ?? 'items[]',
              placeholder: el.dataset.placeholder,
              noResultsText: el.dataset.noResultsText,
          };

    const app = createApp(component, props);
    app.use(i18nVue, {
        resolve: async (lang: string) => {
            const langs = import.meta.glob('../../../lang/*.json');
            return await langs[`../../../lang/${lang}.json`]();
        },
    });
    app.mount(el);
}
