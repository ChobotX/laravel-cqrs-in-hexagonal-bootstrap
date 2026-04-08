import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { clearToasts, error, removeToast, resetNextId, success, ToastType, toasts } from './toast-queue';

const SUCCESS_TIMEOUT = 5000;
const ERROR_TIMEOUT = 8000;
const REMOVE_ANIMATION_DURATION = 300;
const UNKNOWN_ID = 999;

beforeEach(() => {
    vi.useFakeTimers();
});

afterEach(() => {
    clearToasts();
    resetNextId();
    vi.useRealTimers();
});

describe('success', () => {
    it('adds a success toast', () => {
        success('User created.');

        expect(toasts).toHaveLength(1);
        expect(toasts[0].type).toBe(ToastType.Success);
        expect(toasts[0].message).toBe('User created.');
        expect(toasts[0].removing).toBe(false);
    });

    it('auto-dismisses after 5 seconds', () => {
        success('Done');

        vi.advanceTimersByTime(SUCCESS_TIMEOUT);
        expect(toasts[0].removing).toBe(true);

        vi.advanceTimersByTime(REMOVE_ANIMATION_DURATION);
        expect(toasts).toHaveLength(0);
    });
});

describe('error', () => {
    it('adds an error toast', () => {
        error('Something failed.');

        expect(toasts).toHaveLength(1);
        expect(toasts[0].type).toBe(ToastType.Error);
        expect(toasts[0].message).toBe('Something failed.');
    });

    it('auto-dismisses after 8 seconds', () => {
        error('Failed');

        vi.advanceTimersByTime(ERROR_TIMEOUT);
        expect(toasts[0].removing).toBe(true);

        vi.advanceTimersByTime(REMOVE_ANIMATION_DURATION);
        expect(toasts).toHaveLength(0);
    });
});

describe('removeToast', () => {
    it('sets removing flag and removes after animation', () => {
        success('Test');
        const { id } = toasts[0];

        removeToast(id);
        expect(toasts[0].removing).toBe(true);

        vi.advanceTimersByTime(REMOVE_ANIMATION_DURATION);
        expect(toasts).toHaveLength(0);
    });

    it('is no-op for unknown id', () => {
        success('Test');
        removeToast(UNKNOWN_ID);
        expect(toasts).toHaveLength(1);
        expect(toasts[0].removing).toBe(false);
    });

    it('handles already-removed toast when animation timeout fires', () => {
        success('Test');
        const { id } = toasts[0];

        removeToast(id);
        expect(toasts[0].removing).toBe(true);

        clearToasts();
        expect(toasts).toHaveLength(0);

        vi.advanceTimersByTime(REMOVE_ANIMATION_DURATION);
        expect(toasts).toHaveLength(0);
    });
});

describe('clearToasts', () => {
    it('removes all toasts', () => {
        success('One');
        error('Two');
        success('Three');

        clearToasts();
        expect(toasts).toHaveLength(0);
    });
});
