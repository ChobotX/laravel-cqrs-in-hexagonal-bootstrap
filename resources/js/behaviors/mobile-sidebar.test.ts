import { afterEach, beforeEach, describe, expect, it } from 'vitest';
import { queryHTMLElementById } from '../test-utils/dom';

const SIDEBAR_HTML = `
<button data-sidebar-open>Open</button>
<div id="mobile-sidebar-overlay" class="hidden">
    <div data-sidebar-backdrop></div>
    <aside>
        <button data-sidebar-close>Close</button>
        <a href="/dashboard">Dashboard</a>
    </aside>
</div>
`;

beforeEach(async () => {
    document.body.innerHTML = SIDEBAR_HTML;
    await import('./mobile-sidebar');
});

afterEach(() => {
    document.body.innerHTML = '';
    document.body.style.overflow = '';
});

function getOverlay(): HTMLElement {
    const el = queryHTMLElementById('mobile-sidebar-overlay');
    expect(el).not.toBeNull();
    return el;
}

function requireHTMLElement(selector: string): HTMLElement {
    const el = document.querySelector(selector);
    expect(el).toBeInstanceOf(HTMLElement);
    return el;
}

function isSidebarOpen(): boolean {
    return !getOverlay().classList.contains('hidden');
}

function clickElement(el: Element): void {
    el.dispatchEvent(new MouseEvent('click', { bubbles: true }));
}

function pressKey(key: string): void {
    document.dispatchEvent(new KeyboardEvent('keydown', { key, bubbles: true }));
}

describe('mobile-sidebar', () => {
    it('opens sidebar on open button click', () => {
        const openBtn = requireHTMLElement('[data-sidebar-open]');
        clickElement(openBtn);

        expect(isSidebarOpen()).toBe(true);
        expect(document.body.style.overflow).toBe('hidden');
    });

    it('focuses close button on open', () => {
        const openBtn = requireHTMLElement('[data-sidebar-open]');
        clickElement(openBtn);

        const closeBtn = requireHTMLElement('[data-sidebar-close]');
        expect(document.activeElement).toBe(closeBtn);
    });

    it('closes sidebar on close button click', () => {
        const openBtn = requireHTMLElement('[data-sidebar-open]');
        clickElement(openBtn);
        expect(isSidebarOpen()).toBe(true);

        const closeBtn = requireHTMLElement('[data-sidebar-close]');
        clickElement(closeBtn);
        expect(isSidebarOpen()).toBe(false);
        expect(document.body.style.overflow).toBe('');
    });

    it('closes sidebar on backdrop click', () => {
        const openBtn = requireHTMLElement('[data-sidebar-open]');
        clickElement(openBtn);

        const backdrop = requireHTMLElement('[data-sidebar-backdrop]');
        clickElement(backdrop);
        expect(isSidebarOpen()).toBe(false);
    });

    it('closes sidebar on Escape key', () => {
        const openBtn = requireHTMLElement('[data-sidebar-open]');
        clickElement(openBtn);
        expect(isSidebarOpen()).toBe(true);

        pressKey('Escape');
        expect(isSidebarOpen()).toBe(false);
    });

    it('does not close on Escape when already closed', () => {
        pressKey('Escape');
        expect(isSidebarOpen()).toBe(false);
    });

    it('closes sidebar on nav link click', () => {
        const openBtn = requireHTMLElement('[data-sidebar-open]');
        clickElement(openBtn);

        const link = requireHTMLElement('#mobile-sidebar-overlay a');
        clickElement(link);
        expect(isSidebarOpen()).toBe(false);
    });

    it('focuses open button after close', () => {
        const openBtn = requireHTMLElement('[data-sidebar-open]');
        clickElement(openBtn);

        const closeBtn = requireHTMLElement('[data-sidebar-close]');
        clickElement(closeBtn);
        expect(document.activeElement).toBe(openBtn);
    });

    it('handles open when overlay is missing', () => {
        getOverlay().remove();
        const openBtn = requireHTMLElement('[data-sidebar-open]');
        clickElement(openBtn);
    });

    it('handles close when overlay is missing', () => {
        const openBtn = requireHTMLElement('[data-sidebar-open]');
        clickElement(openBtn);
        getOverlay().remove();

        const closeBtn: HTMLButtonElement = document.createElement('button');
        closeBtn.setAttribute('data-sidebar-close', '');
        document.body.appendChild(closeBtn);
        clickElement(closeBtn);
    });

    it('handles missing close button on open', () => {
        document.querySelector('[data-sidebar-close]')?.remove();
        const openBtn = requireHTMLElement('[data-sidebar-open]');
        clickElement(openBtn);
        expect(isSidebarOpen()).toBe(true);
    });

    it('handles missing open button when closing', () => {
        const openBtn = requireHTMLElement('[data-sidebar-open]');
        clickElement(openBtn);
        openBtn.remove();

        const closeBtn = requireHTMLElement('[data-sidebar-close]');
        clickElement(closeBtn);
        expect(isSidebarOpen()).toBe(false);
    });

    it('ignores unrelated clicks when open', () => {
        const openBtn = requireHTMLElement('[data-sidebar-open]');
        clickElement(openBtn);

        const aside = requireHTMLElement('aside');
        clickElement(aside);
        expect(isSidebarOpen()).toBe(true);
    });

    it('ignores clicks when event target is null', () => {
        const ev = new MouseEvent('click', { bubbles: true });
        Object.defineProperty(ev, 'target', { value: null, enumerable: true });
        document.dispatchEvent(ev);
    });
});
