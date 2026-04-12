import { type Component, createApp, defineComponent, h, ref } from 'vue';
import { useI18n } from '../../shared/i18n/i18n';
import type { ChipOption } from './ChipSelector.vue';
import ChipSelector from './ChipSelector.vue';
import LazyChipSelector from './LazyChipSelector.vue';
import { parseChipOptionsJson, parseStringArrayJson } from './parse-chip-mount-data';

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
            allowCreate: el.dataset.allowCreate === 'true',
            createUrl: el.dataset.createUrl,
            createNamespace: el.dataset.createNamespace,
            createText: el.dataset.createText,
        };
    } else {
        component = createStaticWrapper(
            parseChipOptionsJson(el.dataset.options ?? '[]'),
            parseStringArrayJson(el.dataset.selectedIds ?? '[]'),
            el.dataset.inputName ?? 'items[]',
            el.dataset.placeholder,
            el.dataset.noResultsText,
        );
        props = {};
    }

    const app = createApp(component, props);
    useI18n(app);
    app.mount(el);
}
