import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

describe('app-bootstrap', () => {
    beforeEach((): void => {
        vi.resetModules();
        document.head.innerHTML = '';
        document.documentElement.removeAttribute('lang');
    });

    afterEach((): void => {
        document.head.innerHTML = '';
        document.documentElement.removeAttribute('lang');
        window.__APP__ = undefined;
        vi.resetModules();
    });

    it('hydrates window.__APP__ from meta tags', async (): Promise<void> => {
        document.head.innerHTML = '<meta name="app-display-timezone" content="America/New_York">';

        await import('./app-bootstrap');

        expect(window.__APP__?.displayTimezone).toBe('America/New_York');
    });

    it('treats empty meta content as missing timezone', async (): Promise<void> => {
        document.head.innerHTML = '<meta name="app-display-timezone" content="">';

        await import('./app-bootstrap');

        expect(window.__APP__?.displayTimezone).toBeUndefined();
    });

    it('treats whitespace-only meta content as missing timezone', async (): Promise<void> => {
        document.head.innerHTML = '<meta name="app-display-timezone" content="   ">';

        await import('./app-bootstrap');

        expect(window.__APP__?.displayTimezone).toBeUndefined();
    });

    it('treats meta without content attribute as missing timezone', async (): Promise<void> => {
        document.head.innerHTML = '<meta name="app-display-timezone">';

        await import('./app-bootstrap');

        expect(window.__APP__?.displayTimezone).toBeUndefined();
    });

    it('reads locale from document.documentElement.lang', async (): Promise<void> => {
        document.documentElement.lang = 'cs-CZ';

        await import('./app-bootstrap');

        expect(window.__APP__?.locale).toBe('cs-CZ');
    });

    it('treats blank lang as missing locale', async (): Promise<void> => {
        document.documentElement.lang = '   ';

        await import('./app-bootstrap');

        expect(window.__APP__?.locale).toBeUndefined();
    });

    it('initializes from existing window.__APP__ before overwriting known fields', async (): Promise<void> => {
        window.__APP__ = { displayTimezone: 'Europe/Prague', locale: 'sk' };
        document.head.innerHTML = '<meta name="app-display-timezone" content="UTC">';
        document.documentElement.lang = 'en';

        await import('./app-bootstrap');

        expect(window.__APP__?.displayTimezone).toBe('UTC');
        expect(window.__APP__?.locale).toBe('en');
    });
});
