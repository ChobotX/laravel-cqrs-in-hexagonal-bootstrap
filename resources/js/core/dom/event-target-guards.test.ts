import { afterEach, describe, expect, it, vi } from 'vitest';
import { activeHtmlElement, htmlElementFromEventTarget, nodeFromEventTarget } from './event-target-guards';

describe('event-target-guards', () => {
    afterEach(() => {
        vi.restoreAllMocks();
    });

    it('htmlElementFromEventTarget returns null for non-HTMLElement targets', () => {
        expect(htmlElementFromEventTarget(null)).toBeNull();
        expect(htmlElementFromEventTarget(document.createTextNode(''))).toBeNull();
    });

    it('htmlElementFromEventTarget returns element for HTMLElement', () => {
        const el = document.createElement('div');
        expect(htmlElementFromEventTarget(el)).toBe(el);
    });

    it('nodeFromEventTarget returns null when target is not a Node', () => {
        expect(nodeFromEventTarget(null)).toBeNull();
        expect(nodeFromEventTarget(window)).toBeNull();
    });

    it('nodeFromEventTarget accepts Node instances', () => {
        const text = document.createTextNode('x');
        expect(nodeFromEventTarget(text)).toBe(text);
        const el = document.createElement('div');
        expect(nodeFromEventTarget(el)).toBe(el);
    });

    it('activeHtmlElement returns null when activeElement is not HTMLElement', () => {
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        vi.spyOn(document, 'activeElement', 'get').mockReturnValue(svg);
        expect(activeHtmlElement()).toBeNull();
    });

    it('activeHtmlElement returns element when focus is HTMLElement', () => {
        const button = document.createElement('button');
        document.body.append(button);
        button.focus();
        expect(activeHtmlElement()).toBe(button);
        button.remove();
    });
});
