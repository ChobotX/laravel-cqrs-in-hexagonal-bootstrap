import { expect } from 'vitest';

/** Test helper: `getElementById` narrowed to `HTMLElement`. */
export function queryHTMLElementById(id: string): HTMLElement | null {
    const el = document.getElementById(id);
    return el instanceof HTMLElement ? el : null;
}

/** Test helper: `getElementById` narrowed to `HTMLTimeElement`. */
export function queryHTMLTimeElementById(id: string): HTMLTimeElement | null {
    const el = document.getElementById(id);
    return el instanceof HTMLTimeElement ? el : null;
}

/** Asserts `document.querySelector('button')` is an `HTMLElement`. */
export function requireDocumentButton(): HTMLElement {
    const el = document.querySelector('button');
    expect(el).toBeInstanceOf(HTMLElement);
    return el;
}

/** Asserts `document.querySelector(selector)` is an `HTMLElement`. */
export function requireDocumentElement(selector: string): HTMLElement {
    const el = document.querySelector(selector);
    expect(el).toBeInstanceOf(HTMLElement);
    return el;
}

/** Asserts `parent.querySelector(selector)` is an `HTMLElement`. */
export function requireChildElement(parent: Element, selector: string): HTMLElement {
    const el = parent.querySelector(selector);
    expect(el).toBeInstanceOf(HTMLElement);
    return el;
}

/** Asserts `document.querySelectorAll('button')[index]` is an `HTMLElement`. */
export function requireNthDocumentButton(index: number): HTMLElement {
    const el = document.querySelectorAll('button')[index];
    expect(el).toBeInstanceOf(HTMLElement);
    return el;
}

/** Asserts `document.querySelector('path')` exists (SVG path). */
export function requireSvgPathElement(): SVGElement {
    const el = document.querySelector('path');
    expect(el).toBeInstanceOf(SVGElement);
    return el;
}
