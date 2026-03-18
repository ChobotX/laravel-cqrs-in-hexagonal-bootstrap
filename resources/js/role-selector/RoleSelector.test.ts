import { mount } from '@vue/test-utils';
import { beforeAll, describe, expect, it } from 'vitest';
import RoleSelector from './RoleSelector.vue';

beforeAll(() => {
    Element.prototype.scrollIntoView = (): void => undefined;
});

const i18nMock = { $t: (key: string): string => key };

interface RoleOption {
    id: string;
    name: string;
    isSystem: boolean;
}

const roles: RoleOption[] = [
    { id: 'role-1', name: 'Admin', isSystem: false },
    { id: 'role-2', name: 'Editor', isSystem: false },
    { id: 'role-3', name: 'Super Admin', isSystem: true },
];

function mountSelector(selectedRoleIds: string[] = []): ReturnType<typeof mount> {
    return mount(RoleSelector, {
        props: { availableRoles: roles, selectedRoleIds },
        global: { mocks: i18nMock },
    });
}

describe('RoleSelector', () => {
    it('renders input and chip tags for pre-selected roles', () => {
        const wrapper = mountSelector(['role-1']);

        expect(wrapper.find('input[role="combobox"]').exists()).toBe(true);
        expect(wrapper.text()).toContain('Admin');
    });

    it('typing filters available roles in dropdown', async () => {
        const wrapper = mountSelector();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');
        await input.setValue('Edit');

        const items = wrapper.findAll('[role="option"]');
        expect(items).toHaveLength(1);
        expect(items[0].text()).toContain('Editor');
    });

    it('selecting a role adds chip and hidden input', async () => {
        const wrapper = mountSelector();
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');

        const options = wrapper.findAll('[role="option"]');
        await options[0].trigger('mousedown');

        expect(wrapper.text()).toContain('Admin');
        expect(wrapper.find('input[type="hidden"][value="role-1"]').exists()).toBe(true);
    });

    it('removing a chip removes hidden input', async () => {
        const wrapper = mountSelector(['role-1']);

        expect(wrapper.find('input[type="hidden"][value="role-1"]').exists()).toBe(true);

        const removeButton = wrapper.find('button[aria-label="Remove Admin"]');
        await removeButton.trigger('click');

        expect(wrapper.find('input[type="hidden"][value="role-1"]').exists()).toBe(false);
    });

    it('system roles show badge', () => {
        const wrapper = mountSelector(['role-3']);
        expect(wrapper.text()).toContain('(System)');
    });

    it('already-selected roles are hidden from dropdown', async () => {
        const wrapper = mountSelector(['role-1']);
        const input = wrapper.find('input[role="combobox"]');

        await input.trigger('focus');

        const options = wrapper.findAll('[role="option"]');
        const texts = options.map((o) => o.text());
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
        expect(wrapper.text()).toContain('Admin');
        expect(wrapper.find('input[type="hidden"][value="role-1"]').exists()).toBe(true);
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
        const wrapper = mountSelector(['role-1', 'role-2']);
        const input = wrapper.find('input[role="combobox"]');

        expect(wrapper.find('input[type="hidden"][value="role-2"]').exists()).toBe(true);

        await input.trigger('keydown', { key: 'Backspace' });
        expect(wrapper.find('input[type="hidden"][value="role-2"]').exists()).toBe(false);
        expect(wrapper.find('input[type="hidden"][value="role-1"]').exists()).toBe(true);
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
});
