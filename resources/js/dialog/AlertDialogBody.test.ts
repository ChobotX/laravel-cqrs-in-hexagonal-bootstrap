import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import AlertDialogBody from './AlertDialogBody.vue';

const i18nMock = { $t: (key: string): string => key };

describe('AlertDialogBody', () => {
    it('renders title and message', () => {
        const wrapper = mount(AlertDialogBody, {
            props: { title: 'Information', message: 'Operation completed.' },
            global: { mocks: i18nMock },
        });

        expect(wrapper.text()).toContain('Information');
        expect(wrapper.text()).toContain('Operation completed.');
    });

    it('emits acknowledge when OK button is clicked', async () => {
        const wrapper = mount(AlertDialogBody, {
            props: { title: 'Info', message: 'Done' },
            global: { mocks: i18nMock },
        });

        const okButton = wrapper.find('button');
        await okButton.trigger('click');

        expect(wrapper.emitted('acknowledge')).toHaveLength(1);
    });
});
