import { afterEach, describe, expect, it, vi } from 'vitest';

const DROPDOWN_HTML = `
<div data-dropdown>
    <button data-dropdown-toggle aria-expanded="false">Toggle</button>
    <div data-dropdown-menu class="hidden">
        <a role="menuitem" href="#one">One</a>
        <a role="menuitem" href="#two">Two</a>
    </div>
</div>
`;

afterEach(() => {
    vi.restoreAllMocks();
    vi.resetModules();
    document.body.innerHTML = '';
});

describe('dropdown focusMenuItem + activeHtmlElement', () => {
    it('uses index -1 when activeHtmlElement returns null (ternary left branch)', async () => {
        const guards = await import('../core/dom/event-target-guards');
        vi.spyOn(guards, 'activeHtmlElement').mockReturnValue(null);

        document.body.innerHTML = DROPDOWN_HTML;
        await import('./dropdown');

        const toggle = document.querySelector<HTMLElement>('[data-dropdown-toggle]');
        expect(toggle).not.toBeNull();
        toggle?.dispatchEvent(new MouseEvent('click', { bubbles: true }));

        const items = document.querySelectorAll<HTMLElement>('[role="menuitem"]');
        items[0].focus();

        items[0].dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowDown', bubbles: true }));

        expect(document.activeElement).toBe(items[0]);
    });
});
