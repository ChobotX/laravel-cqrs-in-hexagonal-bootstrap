import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import {
    requireChildElement,
    requireDocumentButton,
    requireDocumentElement,
    requireNthDocumentButton,
    requireSvgPathElement,
} from '../../test-utils/dom';

const SIMPLE_HTML = `
<button data-tooltip="Hello tooltip">Hover me</button>
`;

const HTML_CONTENT_HTML = `
<span data-tooltip>
    +3
    <template data-tooltip-content><strong>Label A</strong>, <strong>Label B</strong></template>
</span>
`;

const POSITIONED_HTML = `
<button data-tooltip="Bottom tip" data-tooltip-position="bottom">Hover me</button>
`;

const NO_TOOLTIP_HTML = `
<button>No tooltip</button>
`;

const EMPTY_ATTR_HTML = `
<button data-tooltip="">Empty</button>
`;

function mockRect(el: HTMLElement, rect: Partial<DOMRect>): void {
    vi.spyOn(el, 'getBoundingClientRect').mockReturnValue({
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        width: 0,
        height: 0,
        x: 0,
        y: 0,
        toJSON: () => ({}),
        ...rect,
    });
}

function getTooltip(): HTMLElement | null {
    return document.getElementById('app-tooltip');
}

function isVisible(): boolean {
    const tooltip = getTooltip();
    return tooltip?.classList.contains('tooltip--visible') ?? false;
}

function mouseOver(el: Element): void {
    el.dispatchEvent(new MouseEvent('mouseover', { bubbles: true }));
}

function mouseOut(el: Element, relatedTarget?: Element | null): void {
    el.dispatchEvent(new MouseEvent('mouseout', { bubbles: true, relatedTarget: relatedTarget ?? null }));
}

function focusIn(el: Element): void {
    el.dispatchEvent(new FocusEvent('focusin', { bubbles: true }));
}

function focusOut(el: Element, relatedTarget?: Element | null): void {
    el.dispatchEvent(new FocusEvent('focusout', { bubbles: true, relatedTarget: relatedTarget ?? null }));
}

function pressKey(key: string): void {
    document.dispatchEvent(new KeyboardEvent('keydown', { key, bubbles: true }));
}

function triggerScroll(): void {
    document.dispatchEvent(new Event('scroll', { bubbles: false }));
}

beforeEach(async () => {
    document.body.innerHTML = SIMPLE_HTML;
    vi.stubGlobal('innerWidth', 1024);
    vi.stubGlobal('innerHeight', 768);
    await import('./tooltip');
});

afterEach(() => {
    document.body.innerHTML = '';
    vi.restoreAllMocks();
});

describe('tooltip bridge', () => {
    it('shows tooltip on mouseover', () => {
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(btn);

        expect(isVisible()).toBe(true);
        expect(getTooltip()?.textContent).toBe('Hello tooltip');
    });

    it('hides tooltip on mouseout', () => {
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(btn);
        expect(isVisible()).toBe(true);

        mouseOut(btn, document.body);
        expect(isVisible()).toBe(false);
    });

    it('does not hide when moving between children of same trigger', () => {
        document.body.innerHTML = '<div data-tooltip="Parent"><span>A</span><span>B</span></div>';
        const trigger = requireDocumentElement('[data-tooltip]');
        const spanA = requireChildElement(trigger, 'span:first-child');
        const spanB = requireChildElement(trigger, 'span:last-child');
        mockRect(trigger, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(spanA);
        expect(isVisible()).toBe(true);

        mouseOut(spanA, spanB);
        expect(isVisible()).toBe(true);
    });

    it('shows tooltip on focusin', () => {
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        focusIn(btn);
        expect(isVisible()).toBe(true);
    });

    it('hides tooltip on focusout', () => {
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        focusIn(btn);
        expect(isVisible()).toBe(true);

        focusOut(btn, document.body);
        expect(isVisible()).toBe(false);
    });

    it('does not hide on focusout when focus stays in trigger', () => {
        document.body.innerHTML = '<div data-tooltip="Parent"><button>A</button><button>B</button></div>';
        const trigger = requireDocumentElement('[data-tooltip]');
        const btnA = requireChildElement(trigger, 'button:first-child');
        const btnB = requireChildElement(trigger, 'button:last-child');
        mockRect(trigger, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        focusIn(btnA);
        expect(isVisible()).toBe(true);

        focusOut(btnA, btnB);
        expect(isVisible()).toBe(true);
    });

    it('hides tooltip on Escape key', () => {
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(btn);
        expect(isVisible()).toBe(true);

        pressKey('Escape');
        expect(isVisible()).toBe(false);
    });

    it('hides tooltip on scroll', () => {
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(btn);
        expect(isVisible()).toBe(true);

        triggerScroll();
        expect(isVisible()).toBe(false);
    });

    it('uses template content when present', () => {
        document.body.innerHTML = HTML_CONTENT_HTML;
        const trigger = requireDocumentElement('[data-tooltip]');
        mockRect(trigger, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(trigger);

        expect(isVisible()).toBe(true);
        expect(getTooltip()?.innerHTML).toContain('<strong>Label A</strong>');
    });

    it('prefers template over attribute text', () => {
        document.body.innerHTML = `
            <span data-tooltip="Plain text">
                <template data-tooltip-content><em>HTML content</em></template>
            </span>
        `;
        const trigger = requireDocumentElement('[data-tooltip]');
        mockRect(trigger, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(trigger);

        expect(getTooltip()?.innerHTML).toContain('<em>HTML content</em>');
    });

    it('ignores elements without data-tooltip', () => {
        document.body.innerHTML = NO_TOOLTIP_HTML;
        const btn = requireDocumentButton();

        mouseOver(btn);
        expect(isVisible()).toBe(false);
    });

    it('does not show tooltip for empty data-tooltip without template', () => {
        document.body.innerHTML = EMPTY_ATTR_HTML;
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(btn);
        expect(isVisible()).toBe(false);
    });

    it('does not re-show for same trigger', () => {
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(btn);
        const tooltip = getTooltip();
        mouseOver(btn);

        expect(getTooltip()).toBe(tooltip);
        expect(isVisible()).toBe(true);
    });

    it('reuses singleton tooltip element', () => {
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(btn);
        const firstTooltip = getTooltip();

        mouseOut(btn, document.body);
        mouseOver(btn);
        const secondTooltip = getTooltip();

        expect(firstTooltip).toBe(secondTooltip);
    });

    it('flips to bottom when not enough space above', () => {
        const btn = requireDocumentButton();
        mockRect(btn, { top: 20, left: 200, bottom: 52, right: 300, width: 100, height: 32 });

        mouseOver(btn);

        expect(getTooltip()?.classList.contains('tooltip--bottom')).toBe(true);
    });

    it('flips to top when not enough space below', () => {
        document.body.innerHTML = '<button data-tooltip="Tip" data-tooltip-position="bottom">Hover</button>';
        const btn = requireDocumentButton();
        mockRect(btn, { top: 700, left: 200, bottom: 732, right: 300, width: 100, height: 32 });

        mouseOver(btn);

        expect(getTooltip()?.classList.contains('tooltip--top')).toBe(true);
    });

    it('flips to right when not enough space on left', () => {
        document.body.innerHTML = '<button data-tooltip="Tip" data-tooltip-position="left">Hover</button>';
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 20, bottom: 232, right: 120, width: 100, height: 32 });

        mouseOver(btn);

        expect(getTooltip()?.classList.contains('tooltip--right')).toBe(true);
    });

    it('flips to left when not enough space on right', () => {
        document.body.innerHTML = '<button data-tooltip="Tip" data-tooltip-position="right">Hover</button>';
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 900, bottom: 232, right: 1000, width: 100, height: 32 });

        mouseOver(btn);

        expect(getTooltip()?.classList.contains('tooltip--left')).toBe(true);
    });

    it('respects data-tooltip-position attribute', () => {
        document.body.innerHTML = POSITIONED_HTML;
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(btn);

        expect(getTooltip()?.classList.contains('tooltip--bottom')).toBe(true);
    });

    it('defaults to top position for invalid position attribute', () => {
        document.body.innerHTML = '<button data-tooltip="Tip" data-tooltip-position="invalid">Hover</button>';
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(btn);

        expect(getTooltip()?.classList.contains('tooltip--top')).toBe(true);
    });

    it('removes position classes on hide', () => {
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(btn);
        expect(getTooltip()?.classList.contains('tooltip--top')).toBe(true);

        mouseOut(btn, document.body);
        const tooltip = getTooltip();
        expect(tooltip?.classList.contains('tooltip--top')).toBe(false);
        expect(tooltip?.classList.contains('tooltip--visible')).toBe(false);
    });

    it('handles mouseover on non-HTMLElement target', () => {
        document.dispatchEvent(new MouseEvent('mouseover', { bubbles: true }));
        expect(isVisible()).toBe(false);
    });

    it('shows tooltip when hovering SVG child inside data-tooltip button', () => {
        document.body.innerHTML = `
            <button data-tooltip="Delete">
                <svg class="h-5 w-5" viewBox="0 0 24 24"><path d="M6 18L18 6" /></svg>
            </button>
        `;
        const btn = requireDocumentButton();
        const path = requireSvgPathElement();
        mockRect(btn, { top: 200, left: 400, bottom: 236, right: 436, width: 36, height: 36 });

        mouseOver(path);

        expect(isVisible()).toBe(true);
        expect(getTooltip()?.textContent).toBe('Delete');
    });

    it('handles mouseout on non-trigger element', () => {
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(btn);
        expect(isVisible()).toBe(true);

        document.body.dispatchEvent(new MouseEvent('mouseout', { bubbles: true, relatedTarget: null }));
        expect(isVisible()).toBe(true);
    });

    it('handles focusin on non-trigger element', () => {
        document.body.innerHTML = NO_TOOLTIP_HTML;
        const btn = requireDocumentButton();

        focusIn(btn);
        expect(isVisible()).toBe(false);
    });

    it('handles focusout on non-trigger element', () => {
        document.body.innerHTML = NO_TOOLTIP_HTML;
        const btn = requireDocumentButton();

        focusOut(btn);
        expect(isVisible()).toBe(false);
    });

    it('hideTooltip is no-op when no tooltip is active', () => {
        // First escape clears any stale module state from prior tests
        pressKey('Escape');
        // Second escape hits the early return (currentTrigger is null)
        pressKey('Escape');
        expect(isVisible()).toBe(false);
    });

    it('ignores non-Escape keys', () => {
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(btn);
        pressKey('Enter');
        expect(isVisible()).toBe(true);
    });

    it('switches tooltip content when moving to different trigger', () => {
        document.body.innerHTML = `
            <button data-tooltip="First">One</button>
            <button data-tooltip="Second">Two</button>
        `;
        const btn1 = requireNthDocumentButton(0);
        const btn2 = requireNthDocumentButton(1);
        mockRect(btn1, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });
        mockRect(btn2, { top: 200, left: 400, bottom: 232, right: 500, width: 100, height: 32 });

        mouseOver(btn1);
        expect(getTooltip()?.textContent).toBe('First');

        mouseOut(btn1, btn2);
        mouseOver(btn2);
        expect(getTooltip()?.textContent).toBe('Second');
    });

    it('sets role=tooltip on the popup element', () => {
        const btn = requireDocumentButton();
        mockRect(btn, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });

        mouseOver(btn);
        expect(getTooltip()?.getAttribute('role')).toBe('tooltip');
    });

    it('handles scroll hide when no tooltip is active', () => {
        triggerScroll();
        expect(isVisible()).toBe(false);
    });

    it('cleans up position classes when switching triggers', () => {
        document.body.innerHTML = `
            <button data-tooltip="Top" data-tooltip-position="top">Top</button>
            <button data-tooltip="Bottom" data-tooltip-position="bottom">Bottom</button>
        `;
        const btn1 = requireNthDocumentButton(0);
        const btn2 = requireNthDocumentButton(1);
        mockRect(btn1, { top: 200, left: 200, bottom: 232, right: 300, width: 100, height: 32 });
        mockRect(btn2, { top: 200, left: 400, bottom: 232, right: 500, width: 100, height: 32 });

        mouseOver(btn1);
        expect(getTooltip()?.classList.contains('tooltip--top')).toBe(true);

        mouseOut(btn1, btn2);
        mouseOver(btn2);
        expect(getTooltip()?.classList.contains('tooltip--bottom')).toBe(true);
        expect(getTooltip()?.classList.contains('tooltip--top')).toBe(false);
    });
});
