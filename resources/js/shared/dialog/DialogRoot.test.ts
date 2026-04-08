import { mount } from '@vue/test-utils';
import { afterEach, describe, expect, it } from 'vitest';
import DialogRoot from './DialogRoot.vue';
import { clearDialogs, DialogType, dialogs, resetNextId } from './dialog-queue';

const i18nMock = { $t: (key: string): string => key };

afterEach(() => {
    clearDialogs();
    resetNextId();
    document.body.innerHTML = '';
});

function pushDialog(type: DialogType, title: string, message: string): void {
    dialogs.push({
        id: dialogs.length + 1,
        type,
        title,
        message,
        // noop resolver for test setup
        resolve: (): void => undefined,
    });
}

describe('DialogRoot', () => {
    it('renders nothing when queue is empty', () => {
        const wrapper = mount(DialogRoot, { global: { mocks: i18nMock } });
        expect(document.querySelector('[role="dialog"]')).toBeNull();
        wrapper.unmount();
    });

    it('renders confirm dialog when confirm entry is active', async () => {
        pushDialog(DialogType.Confirm, 'Delete?', 'Are you sure?');
        const wrapper = mount(DialogRoot, { global: { mocks: i18nMock } });
        await wrapper.vm.$nextTick();

        const dialog: Element | null = document.querySelector('[role="dialog"]');
        expect(dialog).not.toBeNull();
        expect(dialog?.textContent).toContain('Delete?');
        expect(dialog?.textContent).toContain('Are you sure?');
        wrapper.unmount();
    });

    it('renders alert dialog when alert entry is active', async () => {
        pushDialog(DialogType.Alert, 'Info', 'Done');
        const wrapper = mount(DialogRoot, { global: { mocks: i18nMock } });
        await wrapper.vm.$nextTick();

        const dialog: Element | null = document.querySelector('[role="dialog"]');
        expect(dialog).not.toBeNull();
        expect(dialog?.textContent).toContain('Info');
        expect(dialog?.textContent).toContain('Done');
        wrapper.unmount();
    });

    it('resolves dialog on confirm click', async () => {
        let resolved = false;
        dialogs.push({
            id: 1,
            type: DialogType.Confirm,
            title: 'Delete?',
            message: 'Sure?',
            resolve: (): void => {
                resolved = true;
            },
        });
        const wrapper = mount(DialogRoot, { global: { mocks: i18nMock } });
        await wrapper.vm.$nextTick();

        const buttons: NodeListOf<HTMLButtonElement> = document.querySelectorAll('button');
        const confirmBtn: HTMLButtonElement | undefined = [...buttons].find(
            (b) => b.textContent?.trim() === 'messages.dialog.confirm',
        );
        expect(confirmBtn).toBeDefined();
        confirmBtn?.click();

        await wrapper.vm.$nextTick();
        expect(resolved).toBe(true);
        expect(dialogs).toHaveLength(0);
        wrapper.unmount();
    });

    it('resolves alert dialog on acknowledge click', async () => {
        let resolved = false;
        dialogs.push({
            id: 1,
            type: DialogType.Alert,
            title: 'Info',
            message: 'Done',
            resolve: (): void => {
                resolved = true;
            },
        });
        const wrapper = mount(DialogRoot, { global: { mocks: i18nMock } });
        await wrapper.vm.$nextTick();

        const buttons: NodeListOf<HTMLButtonElement> = document.querySelectorAll('button');
        const okBtn: HTMLButtonElement | undefined = [...buttons].find(
            (b) => b.textContent?.trim() === 'messages.dialog.ok',
        );
        expect(okBtn).toBeDefined();
        okBtn?.click();

        await wrapper.vm.$nextTick();
        expect(resolved).toBe(true);
        expect(dialogs).toHaveLength(0);
        wrapper.unmount();
    });

    it('resolves dialog on cancel click', async () => {
        let resolvedWith: boolean | undefined;
        dialogs.push({
            id: 1,
            type: DialogType.Confirm,
            title: 'Delete?',
            message: 'Sure?',
            resolve: (result: boolean): void => {
                resolvedWith = result;
            },
        });
        const wrapper = mount(DialogRoot, { global: { mocks: i18nMock } });
        await wrapper.vm.$nextTick();

        const buttons: NodeListOf<HTMLButtonElement> = document.querySelectorAll('button');
        const cancelBtn: HTMLButtonElement | undefined = [...buttons].find(
            (b) => b.textContent?.trim() === 'messages.dialog.cancel',
        );
        expect(cancelBtn).toBeDefined();
        cancelBtn?.click();

        await wrapper.vm.$nextTick();
        expect(resolvedWith).toBe(false);
        expect(dialogs).toHaveLength(0);
        wrapper.unmount();
    });
});
