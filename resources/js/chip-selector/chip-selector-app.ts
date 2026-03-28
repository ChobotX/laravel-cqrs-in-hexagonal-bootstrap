import { i18nVue } from 'laravel-vue-i18n';
import { type Component, createApp, defineComponent, h, ref } from 'vue';
import type { ChipOption } from './ChipSelector.vue';
import ChipSelector from './ChipSelector.vue';
import LazyChipSelector from './LazyChipSelector.vue';

function createStaticWrapper(
    options: ChipOption[],
    initialIds: string[],
    inputName: string,
    placeholder: string | undefined,
    noResultsText: string | undefined,
): Component {
    return defineComponent({
        setup(): () => ReturnType<typeof h> {
            const selectedIds = ref<string[]>(initialIds);
            return () =>
                h(ChipSelector, {
                    options,
                    modelValue: selectedIds.value,
                    'onUpdate:modelValue': (ids: string[]) => {
                        selectedIds.value = ids;
                    },
                    inputName,
                    placeholder,
                    noResultsText,
                });
        },
    });
}

for (const el of document.querySelectorAll<HTMLElement>('[data-chip-selector]')) {
    const searchUrl = el.dataset.searchUrl;
    const isLazy = !!searchUrl;

    let component: Component;
    let props: Record<string, unknown>;

    if (isLazy) {
        component = LazyChipSelector;
        props = {
            searchUrl,
            selectedItems: JSON.parse(el.dataset.selectedItems ?? '[]'),
            inputName: el.dataset.inputName ?? 'items[]',
            placeholder: el.dataset.placeholder,
            noResultsText: el.dataset.noResultsText,
        };
    } else {
        component = createStaticWrapper(
            JSON.parse(el.dataset.options ?? '[]') as ChipOption[],
            JSON.parse(el.dataset.selectedIds ?? '[]') as string[],
            el.dataset.inputName ?? 'items[]',
            el.dataset.placeholder,
            el.dataset.noResultsText,
        );
        props = {};
    }

    const app = createApp(component, props);
    app.use(i18nVue, {
        resolve: async (lang: string) => {
            const langs = import.meta.glob('../../../lang/*.json');
            return await langs[`../../../lang/${lang}.json`]();
        },
    });
    app.mount(el);
}
