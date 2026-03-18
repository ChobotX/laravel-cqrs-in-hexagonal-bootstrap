import { afterEach, describe, expect, it } from 'vitest';
import * as dialogQueue from './dialog-queue';
import { activeDialog, clearDialogs, confirm, DialogType, dialogs, resetNextId, resolveDialog } from './dialog-queue';

const QUEUE_SIZE_THREE = 3;
const UNKNOWN_ID = 999;

afterEach(() => {
    clearDialogs();
    resetNextId();
});

describe('confirm', () => {
    it('resolves true when confirmed', async () => {
        const promise: Promise<boolean> = confirm({ title: 'Delete?', message: 'Are you sure?' });

        expect(dialogs).toHaveLength(1);
        expect(dialogs[0].type).toBe(DialogType.Confirm);

        resolveDialog(dialogs[0].id, true);
        await expect(promise).resolves.toBe(true);
    });

    it('resolves false when cancelled', async () => {
        const promise: Promise<boolean> = confirm({ title: 'Delete?', message: 'Are you sure?' });

        resolveDialog(dialogs[0].id, false);
        await expect(promise).resolves.toBe(false);
    });
});

describe('alert', () => {
    it('resolves void when acknowledged', async () => {
        const promise: Promise<void> = dialogQueue.alert({ title: 'Info', message: 'Done' });

        expect(dialogs).toHaveLength(1);
        expect(dialogs[0].type).toBe(DialogType.Alert);

        resolveDialog(dialogs[0].id, true);
        await expect(promise).resolves.toBeUndefined();
    });
});

describe('queue behavior', () => {
    it('queues multiple dialogs and activeDialog returns the first', () => {
        void confirm({ title: 'First', message: 'msg1' });
        void confirm({ title: 'Second', message: 'msg2' });
        void dialogQueue.alert({ title: 'Third', message: 'msg3' });

        expect(dialogs).toHaveLength(QUEUE_SIZE_THREE);
        expect(activeDialog.value?.title).toBe('First');
    });

    it('advances queue when dialog is resolved', () => {
        void confirm({ title: 'First', message: 'msg1' });
        void confirm({ title: 'Second', message: 'msg2' });

        resolveDialog(dialogs[0].id, true);

        expect(dialogs).toHaveLength(1);
        expect(activeDialog.value?.title).toBe('Second');
    });

    it('returns undefined activeDialog when queue is empty', () => {
        expect(activeDialog.value).toBeUndefined();
    });

    it('resolveDialog is no-op for unknown id', () => {
        void confirm({ title: 'Test', message: 'msg' });
        resolveDialog(UNKNOWN_ID, true);
        expect(dialogs).toHaveLength(1);
    });
});
