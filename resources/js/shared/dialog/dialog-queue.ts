import { type ComputedRef, computed, reactive } from 'vue';

export enum DialogType {
    Confirm = 'confirm',
    Alert = 'alert',
}

export interface DialogEntry {
    id: number;
    type: DialogType;
    title: string;
    message: string;
    resolve: (result: boolean) => void;
}

let nextId = 0;

export const dialogs: DialogEntry[] = reactive([]);

export const activeDialog: ComputedRef<DialogEntry | undefined> = computed(() => dialogs[0]);

export function confirm(options: { title: string; message: string }): Promise<boolean> {
    const { promise, resolve } = Promise.withResolvers<boolean>();
    dialogs.push({ id: ++nextId, type: DialogType.Confirm, ...options, resolve });
    return promise;
}

export function alert(options: { title: string; message: string }): Promise<void> {
    const { promise, resolve } = Promise.withResolvers<boolean>();
    dialogs.push({ id: ++nextId, type: DialogType.Alert, ...options, resolve });
    return promise.then((): undefined => undefined);
}

export function resolveDialog(id: number, result: boolean): void {
    const index: number = dialogs.findIndex((d) => d.id === id);
    if (index === -1) {
        return;
    }
    dialogs[index].resolve(result);
    dialogs.splice(index, 1);
}

export function clearDialogs(): void {
    while (dialogs.length > 0) {
        dialogs.pop();
    }
}

export function resetNextId(): void {
    nextId = 0;
}
