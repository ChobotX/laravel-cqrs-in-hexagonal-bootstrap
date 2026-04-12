/**
 * @vitest-environment node
 */
import { afterEach, describe, expect, it, vi } from 'vitest';

describe('app-bootstrap (node)', () => {
    afterEach((): void => {
        vi.resetModules();
        Reflect.deleteProperty(globalThis, 'window');
    });

    it('hydrates __APP__ when window exists but document does not', async (): Promise<void> => {
        const win: { __APP__?: { displayTimezone?: string; locale?: string } } = {};
        Object.assign(globalThis, { window: win });

        await import('./app-bootstrap');

        expect(win.__APP__).toEqual({
            displayTimezone: undefined,
            locale: undefined,
        });
    });

    it('uses existing window.__APP__ as base when defined', async (): Promise<void> => {
        const win: { __APP__?: { displayTimezone?: string; locale?: string } } = {
            __APP__: { displayTimezone: 'Europe/Prague', locale: 'cs' },
        };
        Object.assign(globalThis, { window: win });

        await import('./app-bootstrap');

        expect(win.__APP__).toEqual({
            displayTimezone: undefined,
            locale: undefined,
        });
    });
});
