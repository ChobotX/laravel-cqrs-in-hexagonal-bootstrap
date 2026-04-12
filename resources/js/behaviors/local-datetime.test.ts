import { afterEach, beforeEach, describe, expect, it } from 'vitest';
import { formatInstant } from '../core/datetime/format-instant';
import { queryHTMLTimeElementById } from '../test-utils/dom';

const HTML = `
<time id="tagged" datetime="2026-04-12T12:00:00Z" data-local-datetime>2026-04-12 12:00:00</time>
<time id="untagged" datetime="2026-04-12T12:00:00Z">fallback</time>
<time id="no-datetime" data-local-datetime></time>
<time id="bad-datetime" datetime="not a date" data-local-datetime>original</time>
`;

beforeEach(async () => {
    window.__APP__ = {};
    document.body.innerHTML = HTML;
    await import('./local-datetime');
    document.dispatchEvent(new Event('DOMContentLoaded'));
});

afterEach(() => {
    document.body.innerHTML = '';
    window.__APP__ = undefined;
});

describe('local-datetime', () => {
    it('rewrites text of elements with data-local-datetime using shared formatter', () => {
        const el = queryHTMLTimeElementById('tagged');
        expect(el).not.toBeNull();

        expect(el.textContent).toBe(formatInstant('2026-04-12T12:00:00Z'));
    });

    it('leaves elements without the marker attribute untouched', () => {
        const el = queryHTMLTimeElementById('untagged');
        expect(el).not.toBeNull();

        expect(el.textContent).toBe('fallback');
    });

    it('ignores elements with an empty datetime attribute', () => {
        const el = queryHTMLTimeElementById('no-datetime');
        expect(el).not.toBeNull();

        expect(el.textContent).toBe('');
    });

    it('leaves text unchanged when datetime is unparseable', () => {
        const el = queryHTMLTimeElementById('bad-datetime');
        expect(el).not.toBeNull();

        expect(el.textContent).toBe('original');
    });
});
