import { reactive } from 'vue';

export enum ToastType {
    Success = 'success',
    Error = 'error',
}

export interface ToastEntry {
    id: number;
    type: ToastType;
    message: string;
    removing: boolean;
}

const SUCCESS_TIMEOUT = 5000;
const ERROR_TIMEOUT = 8000;
const REMOVE_ANIMATION_DURATION = 300;

let nextId = 0;

export const toasts: ToastEntry[] = reactive([]);

export function removeToast(id: number): void {
    const toast: ToastEntry | undefined = toasts.find((t) => t.id === id);
    if (!toast) {
        return;
    }
    toast.removing = true;
    setTimeout((): void => {
        const index: number = toasts.findIndex((t) => t.id === id);
        if (index !== -1) {
            toasts.splice(index, 1);
        }
    }, REMOVE_ANIMATION_DURATION);
}

function addToast(type: ToastType, message: string): void {
    const id: number = ++nextId;
    toasts.push({ id, type, message, removing: false });

    const timeout: number = type === ToastType.Success ? SUCCESS_TIMEOUT : ERROR_TIMEOUT;
    setTimeout((): void => {
        removeToast(id);
    }, timeout);
}

export function success(message: string): void {
    addToast(ToastType.Success, message);
}

export function error(message: string): void {
    addToast(ToastType.Error, message);
}

export function clearToasts(): void {
    while (toasts.length > 0) {
        toasts.pop();
    }
}

export function resetNextId(): void {
    nextId = 0;
}
