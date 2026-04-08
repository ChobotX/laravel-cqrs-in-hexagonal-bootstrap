import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

describe('toast-bridge', () => {
    beforeEach(() => {
        window.appToast = {
            success: vi.fn(),
            error: vi.fn(),
        };
    });

    afterEach(() => {
        document.body.innerHTML = '';
        vi.resetModules();
    });

    it('reads flash data and fires toasts', async () => {
        document.body.innerHTML = '<div id="app-flash-data" hidden data-success="User created." data-error=""></div>';

        await import('./toast-bridge');

        expect(window.appToast.success).toHaveBeenCalledWith('User created.');
        expect(window.appToast.error).not.toHaveBeenCalled();
    });

    it('fires error toast when error attribute is set', async () => {
        document.body.innerHTML =
            '<div id="app-flash-data" hidden data-success="" data-error="Something failed."></div>';

        await import('./toast-bridge');

        expect(window.appToast.success).not.toHaveBeenCalled();
        expect(window.appToast.error).toHaveBeenCalledWith('Something failed.');
    });

    it('is no-op when flash element is missing', async () => {
        await import('./toast-bridge');

        expect(window.appToast.success).not.toHaveBeenCalled();
        expect(window.appToast.error).not.toHaveBeenCalled();
    });
});
