import { afterEach, beforeEach, describe, expect, it } from 'vitest';
import { formatDateOnly, formatInstant, getDisplayTimeZone } from './format-instant';

describe('format-instant', () => {
    beforeEach((): void => {
        document.documentElement.innerHTML = '';
        window.__APP__ = {};
    });

    afterEach((): void => {
        window.__APP__ = undefined;
    });

    it('formats using tenant display timezone from meta when set', (): void => {
        document.head.innerHTML = '<meta name="app-display-timezone" content="UTC">';

        const out = formatInstant('2026-04-12T15:30:00.000000+00:00');

        expect(out.length).toBeGreaterThan(0);
        expect(getDisplayTimeZone()).toBe('UTC');
    });

    it('prefers window bootstrap over meta', (): void => {
        document.head.innerHTML = '<meta name="app-display-timezone" content="UTC">';
        window.__APP__ = { displayTimezone: 'Europe/Prague' };

        expect(getDisplayTimeZone()).toBe('Europe/Prague');
    });

    it('formats date-only in UTC', (): void => {
        const out = formatDateOnly('2026-01-05');

        expect(out.length).toBeGreaterThan(0);
    });

    it('returns input unchanged for invalid date-only strings', (): void => {
        expect(formatDateOnly('not-a-date')).toBe('not-a-date');
    });

    it('returns empty string for unparseable instant', (): void => {
        expect(formatInstant('invalid')).toBe('');
    });
});
