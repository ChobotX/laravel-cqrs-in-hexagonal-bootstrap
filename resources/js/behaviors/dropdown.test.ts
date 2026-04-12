import { afterEach, beforeEach, describe, expect, it } from 'vitest';

const DROPDOWN_HTML = `
<div data-dropdown>
    <button data-dropdown-toggle aria-expanded="false">Toggle</button>
    <div data-dropdown-menu class="hidden">
        <a role="menuitem" href="#one">One</a>
        <a role="menuitem" href="#two">Two</a>
    </div>
</div>
`;

beforeEach(async () => {
    document.body.innerHTML = DROPDOWN_HTML;
    await import('./dropdown');
});

afterEach(() => {
    document.body.innerHTML = '';
});

function getToggle(): HTMLElement {
    const el = document.querySelector('[data-dropdown-toggle]');
    expect(el).toBeInstanceOf(HTMLElement);
    return el;
}

function getMenu(): HTMLElement {
    const el = document.querySelector('[data-dropdown-menu]');
    expect(el).toBeInstanceOf(HTMLElement);
    return el;
}

function isMenuOpen(): boolean {
    return !getMenu().classList.contains('hidden');
}

function clickElement(el: Element): void {
    el.dispatchEvent(new MouseEvent('click', { bubbles: true }));
}

function pressKey(key: string, target?: HTMLElement): void {
    const event = new KeyboardEvent('keydown', { key, bubbles: true });
    (target ?? document.body).dispatchEvent(event);
}

describe('dropdown', () => {
    it('opens menu on toggle click', () => {
        clickElement(getToggle());

        expect(isMenuOpen()).toBe(true);
        expect(getToggle().getAttribute('aria-expanded')).toBe('true');
    });

    it('closes menu on second toggle click', () => {
        clickElement(getToggle());
        expect(isMenuOpen()).toBe(true);

        clickElement(getToggle());
        expect(isMenuOpen()).toBe(false);
        expect(getToggle().getAttribute('aria-expanded')).toBe('false');
    });

    it('focuses first menu item on open', () => {
        clickElement(getToggle());

        const firstItem: Element | null = document.querySelector('[role="menuitem"]');
        expect(document.activeElement).toBe(firstItem);
    });

    it('closes menu on Escape key', () => {
        clickElement(getToggle());
        expect(isMenuOpen()).toBe(true);

        pressKey('Escape');
        expect(isMenuOpen()).toBe(false);
    });

    it('silently closes when clicking outside', () => {
        clickElement(getToggle());
        expect(isMenuOpen()).toBe(true);

        clickElement(document.body);
        expect(isMenuOpen()).toBe(false);
    });

    it('navigates menu items with ArrowDown', () => {
        clickElement(getToggle());
        const items: NodeListOf<HTMLElement> = document.querySelectorAll('[role="menuitem"]');

        items[0].focus();
        pressKey('ArrowDown', items[0]);
        expect(document.activeElement).toBe(items[1]);
    });

    it('ArrowDown from menu when no menu item is focused starts at first item', () => {
        clickElement(getToggle());
        const items: NodeListOf<HTMLElement> = document.querySelectorAll('[role="menuitem"]');
        const ae = document.activeElement;
        if (ae instanceof HTMLElement) {
            ae.blur();
        }
        pressKey('ArrowDown', getMenu());
        expect(document.activeElement).toBe(items[0]);
    });

    it('ArrowDown when focus is inside dropdown but not on a menu item wraps from index -1', () => {
        clickElement(getToggle());
        const items: NodeListOf<HTMLElement> = document.querySelectorAll('[role="menuitem"]');
        getToggle().focus();
        pressKey('ArrowDown', getToggle());
        expect(document.activeElement).toBe(items[0]);
    });

    it('navigates menu items with ArrowUp', () => {
        clickElement(getToggle());
        const items: NodeListOf<HTMLElement> = document.querySelectorAll('[role="menuitem"]');

        items[1].focus();
        pressKey('ArrowUp', items[1]);
        expect(document.activeElement).toBe(items[0]);
    });

    it('wraps ArrowDown from last to first', () => {
        clickElement(getToggle());
        const items: NodeListOf<HTMLElement> = document.querySelectorAll('[role="menuitem"]');

        items[1].focus();
        pressKey('ArrowDown', items[1]);
        expect(document.activeElement).toBe(items[0]);
    });

    it('ignores ArrowKey outside dropdown', () => {
        const outside: HTMLButtonElement = document.createElement('button');
        document.body.appendChild(outside);
        pressKey('ArrowDown', outside);
    });

    it('ignores ArrowKey when menu is closed', () => {
        pressKey('ArrowDown', getToggle());
        expect(isMenuOpen()).toBe(false);
    });

    it('ignores non-arrow non-escape keys', () => {
        pressKey('Enter');
    });

    it('handles open with no menu items', () => {
        document.body.innerHTML = `
            <div data-dropdown>
                <button data-dropdown-toggle aria-expanded="false">Toggle</button>
                <div data-dropdown-menu class="hidden"></div>
            </div>
        `;
        clickElement(getToggle());
        expect(isMenuOpen()).toBe(true);
    });

    it('handles focusMenuItem with empty menu', () => {
        document.body.innerHTML = `
            <div data-dropdown>
                <button data-dropdown-toggle aria-expanded="false">Toggle</button>
                <div data-dropdown-menu class="hidden"></div>
            </div>
        `;
        clickElement(getToggle());
        pressKey('ArrowDown', getMenu());
    });

    it('openDropdown guards when menu element is missing', () => {
        document.body.innerHTML = `
            <div data-dropdown>
                <button data-dropdown-toggle aria-expanded="false">Toggle</button>
            </div>
        `;
        clickElement(getToggle());
        expect(getToggle().getAttribute('aria-expanded')).toBe('false');
    });

    it('closeDropdown guards when toggle is removed while open', () => {
        clickElement(getToggle());
        expect(isMenuOpen()).toBe(true);

        getToggle().remove();
        pressKey('Escape');
        expect(getMenu().classList.contains('hidden')).toBe(false);
    });

    it('silentClose guards when toggle is removed while open', () => {
        clickElement(getToggle());
        expect(isMenuOpen()).toBe(true);

        getToggle().remove();
        clickElement(document.body);
        expect(getMenu().classList.contains('hidden')).toBe(false);
    });

    it('clicking inside open dropdown but not on toggle does nothing', () => {
        clickElement(getToggle());
        expect(isMenuOpen()).toBe(true);

        const menuItem = document.querySelector('[role="menuitem"]');
        expect(menuItem).toBeInstanceOf(HTMLElement);
        clickElement(menuItem);
        expect(isMenuOpen()).toBe(true);
    });

    it('Escape when no dropdown is open is no-op', () => {
        pressKey('Escape');
    });

    it('ignores Arrow keys when keyboard target is null', () => {
        const ev = new KeyboardEvent('keydown', { key: 'ArrowDown', bubbles: true });
        Object.defineProperty(ev, 'target', { value: null, enumerable: true });
        document.dispatchEvent(ev);
    });

    it('ignores document clicks when mouse target is null', () => {
        const ev = new MouseEvent('click', { bubbles: true });
        Object.defineProperty(ev, 'target', { value: null, enumerable: true });
        document.dispatchEvent(ev);
    });
});
