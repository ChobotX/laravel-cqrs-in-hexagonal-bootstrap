import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import ConfirmDialogBody from './ConfirmDialogBody.vue';

const i18nMock = { $t: (key: string): string => key };

describe('ConfirmDialogBody', () => {
    it('renders title and message', () => {
        const wrapper = mount(ConfirmDialogBody, {
            props: { title: 'Delete User', message: 'Are you sure?' },
            global: { mocks: i18nMock },
        });

        expect(wrapper.text()).toContain('Delete User');
        expect(wrapper.text()).toContain('Are you sure?');
    });

    it('emits confirm when confirm button is clicked', async () => {
        const wrapper = mount(ConfirmDialogBody, {
            props: { title: 'Title', message: 'Message' },
            global: { mocks: i18nMock },
        });

        const buttons = wrapper.findAll('button');
        const confirmButton = buttons.find((b) => b.text() !== 'messages.dialog.cancel');
        expect(confirmButton).toBeDefined();
        await confirmButton?.trigger('click');

        expect(wrapper.emitted('confirm')).toHaveLength(1);
    });

    it('emits cancel when cancel button is clicked', async () => {
        const wrapper = mount(ConfirmDialogBody, {
            props: { title: 'Title', message: 'Message' },
            global: { mocks: i18nMock },
        });

        const cancelButton = wrapper.findAll('button').find((b) => b.text() === 'messages.dialog.cancel');
        expect(cancelButton).toBeDefined();
        await cancelButton?.trigger('click');

        expect(wrapper.emitted('cancel')).toHaveLength(1);
    });
});
