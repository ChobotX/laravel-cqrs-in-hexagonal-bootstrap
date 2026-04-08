import { mount } from '@vue/test-utils';
import { beforeAll, describe, expect, it } from 'vitest';
import ChipSelector from './ChipSelector.vue';

beforeAll(() => {
    Element.prototype.scrollIntoView = (): void => undefined;
});

const i18nMock = { $t: (key: string): string => key };

interface ChipOption {
    id: string;
    name: string;
    badge?: string;
}

const options: ChipOption[] = [
    { id: 'opt-1', name: 'Admin' },
    { id: 'opt-2', name: 'Editor' },
    { id: 'opt-3', name: 'Super Admin', badge: 'System' },
];

function mountSelector(modelValue: string[] = [], extraProps: Record<string, unknown> = {}): ReturnType<typeof mount> {
    return mount(ChipSelector, {
        props: { options, modelValue, ...extraProps },
        global: { mocks: i18nMock },
    });
}

describe('ChipSelector', () => {
    it('renders input and chip tags for pre-selected options', () => {
        const wrapper = mountSelector(['opt-1']);

        expect(wrapper.find('input[role="combobox"]').exists()).toBe(true);
        expect(wrapper.text()).toContain('Admin');
    });

    it('typing filters available options in dropdown', async () => {
        const wrapper = mountSelector();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await input.setValue('Edit');

        const items = wrapper.findAll('[role="option"]');
        expect(items).toHaveLength(1);
        expect(items[0].text()).toContain('Editor');
    });

    it('selecting an option emits update:modelValue with new id', async () => {
        const wrapper = mountSelector();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');

        const dropdownOptions = wrapper.findAll('[role="option"]');
        await dropdownOptions[0].trigger('mousedown');

        expect(wrapper.emitted('update:modelValue')).toBeTruthy();
        expect(wrapper.emitted('update:modelValue')?.[0]).toEqual([['opt-1']]);
    });

    it('renders hidden inputs for modelValue ids', () => {
        const wrapper = mountSelector(['opt-1']);

        expect(wrapper.find('input[type="hidden"][value="opt-1"]').exists()).toBe(true);
    });

    it('removing a chip emits update:modelValue without removed id', async () => {
        const wrapper = mountSelector(['opt-1']);

        const removeButton = wrapper.find('button[aria-label="Remove Admin"]');
        await removeButton.trigger('click');

        expect(wrapper.emitted('update:modelValue')?.[0]).toEqual([[]]);
    });

    it('options with badge show badge text', () => {
        const wrapper = mountSelector(['opt-3']);
        expect(wrapper.text()).toContain('(System)');
    });

    it('already-selected options are hidden from dropdown', async () => {
        const wrapper = mountSelector(['opt-1']);
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');

        const dropdownOptions = wrapper.findAll('[role="option"]');
        const texts = dropdownOptions.map((o) => o.text());
        expect(texts).not.toContain(expect.stringContaining('Admin'));
        expect(texts.some((t) => t.includes('Editor'))).toBe(true);
    });

    it('keyboard navigation works', async () => {
        const wrapper = mountSelector();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');

        await input.trigger('keydown', { key: 'ArrowDown' });
        let activeOptions = wrapper.findAll('[data-active="true"]');
        expect(activeOptions).toHaveLength(1);
        expect(activeOptions[0].text()).toContain('Admin');

        await input.trigger('keydown', { key: 'ArrowDown' });
        activeOptions = wrapper.findAll('[data-active="true"]');
        expect(activeOptions[0].text()).toContain('Editor');

        await input.trigger('keydown', { key: 'ArrowUp' });
        activeOptions = wrapper.findAll('[data-active="true"]');
        expect(activeOptions[0].text()).toContain('Admin');

        await input.trigger('keydown', { key: 'Enter' });
        expect(wrapper.emitted('update:modelValue')?.[0]).toEqual([['opt-1']]);
    });

    it('escape closes dropdown', async () => {
        const wrapper = mountSelector();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        expect(wrapper.findAll('[role="option"]').length).toBeGreaterThan(0);

        await input.trigger('keydown', { key: 'Escape' });
        expect(wrapper.findAll('[role="option"]')).toHaveLength(0);
    });

    it('backspace removes last chip when input is empty', async () => {
        const wrapper = mountSelector(['opt-1', 'opt-2']);
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('keydown', { key: 'Backspace' });
        expect(wrapper.emitted('update:modelValue')?.[0]).toEqual([['opt-1']]);
    });

    it('arrow keys are ignored when dropdown is closed', async () => {
        const wrapper = mountSelector();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('keydown', { key: 'ArrowDown' });

        expect(wrapper.findAll('[data-active="true"]')).toHaveLength(0);
    });

    it('unrelated key does not affect state when dropdown is open', async () => {
        const wrapper = mountSelector();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await input.trigger('keydown', { key: 'Tab' });

        expect(wrapper.findAll('[data-active="true"]')).toHaveLength(0);
    });

    it('uses custom inputName for hidden inputs', () => {
        const wrapper = mount(ChipSelector, {
            props: { options, modelValue: ['opt-1'], inputName: 'roles[]' },
            global: { mocks: i18nMock },
        });

        const hidden = wrapper.find('input[type="hidden"][value="opt-1"]');
        expect(hidden.attributes('name')).toBe('roles[]');
    });

    it('options without badge do not show badge text', () => {
        const wrapper = mountSelector(['opt-1']);
        expect(wrapper.text()).not.toContain('(System)');
    });

    it('emits focus event when input is focused', async () => {
        const wrapper = mountSelector();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');

        expect(wrapper.emitted('focus')).toBeTruthy();
    });

    it('emits search event on input', async () => {
        const wrapper = mountSelector();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await input.setValue('test');
        await input.trigger('input');

        expect(wrapper.emitted('search')).toBeTruthy();
        expect(wrapper.emitted('search')?.[0]).toEqual(['test']);
    });

    it('shows noResultsText when filter matches nothing', async () => {
        const wrapper = mountSelector([], { noResultsText: 'No matches found' });
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await input.setValue('zzzzz');

        expect(wrapper.text()).toContain('No matches found');
    });

    it('does not show empty state when noResultsText is not set', async () => {
        const wrapper = mountSelector();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await input.setValue('zzzzz');

        expect(wrapper.findAll('p')).toHaveLength(0);
    });

    it('uses placeholder prop for input', () => {
        const wrapper = mountSelector([], { placeholder: 'Custom placeholder' });
        const input = wrapper.find<HTMLInputElement>('input[role="combobox"]');

        expect(input.attributes('placeholder')).toBe('Custom placeholder');
    });

    it('falls back to empty string for placeholder when not provided', () => {
        const wrapper = mountSelector();
        const input = wrapper.find<HTMLInputElement>('input[role="combobox"]');

        expect(input.attributes('placeholder')).toBe('');
    });

    it('shows create option when allowCreate is true and no matches', async () => {
        const wrapper = mountSelector([], { allowCreate: true, createText: 'Create "%s"' });
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await input.setValue('newlabel');

        const listboxes = wrapper.findAll('[role="listbox"]');
        expect(listboxes.length).toBeGreaterThan(0);
        const lastListbox = listboxes[listboxes.length - 1];
        expect(lastListbox.text()).toContain('Create "newlabel"');
    });

    it('does not show create option when allowCreate is false', async () => {
        const wrapper = mountSelector([], { allowCreate: false });
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await input.setValue('zzzzz');

        const allText = wrapper.text();
        expect(allText).not.toContain('Create');
    });

    it('does not show create option when search is empty', async () => {
        const wrapper = mountSelector([], { allowCreate: true });
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');

        const allText = wrapper.text();
        expect(allText).not.toContain('Create');
    });

    it('emits create event when create option is clicked', async () => {
        const wrapper = mountSelector([], { allowCreate: true, createText: 'Create "%s"' });
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await input.setValue('newlabel');

        const createOption = wrapper.findAll('[role="option"]').find((o) => o.text().includes('Create'));
        expect(createOption).toBeDefined();
        await createOption?.trigger('mousedown');

        expect(wrapper.emitted('create')).toBeTruthy();
        expect(wrapper.emitted('create')?.[0]).toEqual(['newlabel']);
    });

    it('emits create event via keyboard Enter on create option', async () => {
        const wrapper = mountSelector([], { allowCreate: true, createText: 'Create "%s"' });
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await input.setValue('newlabel');

        await input.trigger('keydown', { key: 'ArrowDown' });
        await input.trigger('keydown', { key: 'Enter' });

        expect(wrapper.emitted('create')).toBeTruthy();
        expect(wrapper.emitted('create')?.[0]).toEqual(['newlabel']);
    });

    it('does not show create option when there are matching results', async () => {
        const wrapper = mountSelector([], { allowCreate: true });
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await input.setValue('Admin');

        const dropdownItems = wrapper.findAll('[role="option"]');
        expect(dropdownItems.some((o) => o.text().includes('Create'))).toBe(false);
    });
});
