import { afterEach, beforeEach, describe, expect, it } from 'vitest';

const HTML = `
<time id="tagged" datetime="2026-04-12T12:00:00Z" data-local-datetime>2026-04-12 12:00:00</time>
<time id="untagged" datetime="2026-04-12T12:00:00Z">fallback</time>
<time id="no-datetime" data-local-datetime></time>
<time id="bad-datetime" datetime="not a date" data-local-datetime>original</time>
`;

beforeEach(async () => {
    document.body.innerHTML = HTML;
    await import('./local-datetime');
    document.dispatchEvent(new Event('DOMContentLoaded'));
});

afterEach(() => {
    document.body.innerHTML = '';
});

describe('local-datetime', () => {
    it('rewrites text of elements with data-local-datetime using browser locale', () => {
        const el = document.getElementById('tagged') as HTMLTimeElement;
        const expected = new Intl.DateTimeFormat(navigator.language, {
            dateStyle: 'medium',
            timeStyle: 'medium',
        }).format(new Date('2026-04-12T12:00:00Z'));

        expect(el.textContent).toBe(expected);
    });

    it('leaves elements without the marker attribute untouched', () => {
        const el = document.getElementById('untagged') as HTMLTimeElement;

        expect(el.textContent).toBe('fallback');
    });

    it('ignores elements with an empty datetime attribute', () => {
        const el = document.getElementById('no-datetime') as HTMLTimeElement;

        expect(el.textContent).toBe('');
    });

    it('leaves text unchanged when datetime is unparseable', () => {
        const el = document.getElementById('bad-datetime') as HTMLTimeElement;

        expect(el.textContent).toBe('original');
    });
});
