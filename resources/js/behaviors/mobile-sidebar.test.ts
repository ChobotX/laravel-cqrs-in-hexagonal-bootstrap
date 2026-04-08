import { afterEach, beforeEach, describe, expect, it } from 'vitest';

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
    return document.getElementById('mobile-sidebar-overlay') as HTMLElement;
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
        const openBtn: HTMLElement = document.querySelector('[data-sidebar-open]') as HTMLElement;
        clickElement(openBtn);

        expect(isSidebarOpen()).toBe(true);
        expect(document.body.style.overflow).toBe('hidden');
    });

    it('focuses close button on open', () => {
        const openBtn: HTMLElement = document.querySelector('[data-sidebar-open]') as HTMLElement;
        clickElement(openBtn);

        const closeBtn: HTMLElement = document.querySelector('[data-sidebar-close]') as HTMLElement;
        expect(document.activeElement).toBe(closeBtn);
    });

    it('closes sidebar on close button click', () => {
        const openBtn: HTMLElement = document.querySelector('[data-sidebar-open]') as HTMLElement;
        clickElement(openBtn);
        expect(isSidebarOpen()).toBe(true);

        const closeBtn: HTMLElement = document.querySelector('[data-sidebar-close]') as HTMLElement;
        clickElement(closeBtn);
        expect(isSidebarOpen()).toBe(false);
        expect(document.body.style.overflow).toBe('');
    });

    it('closes sidebar on backdrop click', () => {
        const openBtn: HTMLElement = document.querySelector('[data-sidebar-open]') as HTMLElement;
        clickElement(openBtn);

        const backdrop: HTMLElement = document.querySelector('[data-sidebar-backdrop]') as HTMLElement;
        clickElement(backdrop);
        expect(isSidebarOpen()).toBe(false);
    });

    it('closes sidebar on Escape key', () => {
        const openBtn: HTMLElement = document.querySelector('[data-sidebar-open]') as HTMLElement;
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
        const openBtn: HTMLElement = document.querySelector('[data-sidebar-open]') as HTMLElement;
        clickElement(openBtn);

        const link: HTMLElement = document.querySelector('#mobile-sidebar-overlay a') as HTMLElement;
        clickElement(link);
        expect(isSidebarOpen()).toBe(false);
    });

    it('focuses open button after close', () => {
        const openBtn: HTMLElement = document.querySelector('[data-sidebar-open]') as HTMLElement;
        clickElement(openBtn);

        const closeBtn: HTMLElement = document.querySelector('[data-sidebar-close]') as HTMLElement;
        clickElement(closeBtn);
        expect(document.activeElement).toBe(openBtn);
    });

    it('handles open when overlay is missing', () => {
        getOverlay().remove();
        const openBtn: HTMLElement = document.querySelector('[data-sidebar-open]') as HTMLElement;
        clickElement(openBtn);
    });

    it('handles close when overlay is missing', () => {
        const openBtn: HTMLElement = document.querySelector('[data-sidebar-open]') as HTMLElement;
        clickElement(openBtn);
        getOverlay().remove();

        const closeBtn: HTMLButtonElement = document.createElement('button');
        closeBtn.setAttribute('data-sidebar-close', '');
        document.body.appendChild(closeBtn);
        clickElement(closeBtn);
    });

    it('handles missing close button on open', () => {
        document.querySelector('[data-sidebar-close]')?.remove();
        const openBtn: HTMLElement = document.querySelector('[data-sidebar-open]') as HTMLElement;
        clickElement(openBtn);
        expect(isSidebarOpen()).toBe(true);
    });

    it('handles missing open button when closing', () => {
        const openBtn: HTMLElement = document.querySelector('[data-sidebar-open]') as HTMLElement;
        clickElement(openBtn);
        openBtn.remove();

        const closeBtn: HTMLElement = document.querySelector('[data-sidebar-close]') as HTMLElement;
        clickElement(closeBtn);
        expect(isSidebarOpen()).toBe(false);
    });

    it('ignores unrelated clicks when open', () => {
        const openBtn: HTMLElement = document.querySelector('[data-sidebar-open]') as HTMLElement;
        clickElement(openBtn);

        const aside: HTMLElement = document.querySelector('aside') as HTMLElement;
        clickElement(aside);
        expect(isSidebarOpen()).toBe(true);
    });
});
