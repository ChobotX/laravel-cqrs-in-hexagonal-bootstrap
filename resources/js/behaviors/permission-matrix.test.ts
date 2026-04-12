import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

describe('permission-matrix', () => {
    beforeEach(() => {
        vi.resetModules();
    });

    afterEach(() => {
        document.body.innerHTML = '';
    });

    it('module toggle checks all child checkboxes', async () => {
        document.body.innerHTML = `
            <input type="checkbox" data-module-toggle="users">
            <input type="checkbox" data-module="users" data-permission="users.list.read">
            <input type="checkbox" data-module="users" data-permission="users.list.create">
        `;

        await import('./permission-matrix');
        document.dispatchEvent(new Event('DOMContentLoaded'));

        const toggleEl = document.querySelector('[data-module-toggle="users"]');
        expect(toggleEl).toBeInstanceOf(HTMLInputElement);
        const toggle = toggleEl;
        toggle.checked = true;
        toggle.dispatchEvent(new Event('change'));

        const checkboxes = document.querySelectorAll<HTMLInputElement>('input[data-module="users"]');
        for (const cb of checkboxes) {
            expect(cb.checked).toBe(true);
        }
    });

    it('module toggle unchecks all child checkboxes', async () => {
        document.body.innerHTML = `
            <input type="checkbox" data-module-toggle="users">
            <input type="checkbox" data-module="users" data-permission="users.list.read" checked>
            <input type="checkbox" data-module="users" data-permission="users.list.create" checked>
        `;

        await import('./permission-matrix');
        document.dispatchEvent(new Event('DOMContentLoaded'));

        const toggleEl = document.querySelector('[data-module-toggle="users"]');
        expect(toggleEl).toBeInstanceOf(HTMLInputElement);
        const toggle = toggleEl;
        toggle.checked = false;
        toggle.dispatchEvent(new Event('change'));

        const checkboxes = document.querySelectorAll<HTMLInputElement>('input[data-module="users"]');
        for (const cb of checkboxes) {
            expect(cb.checked).toBe(false);
        }
    });

    it('sets toggle to checked when all children are checked', async () => {
        document.body.innerHTML = `
            <input type="checkbox" data-module-toggle="users">
            <input type="checkbox" data-module="users" data-permission="users.list.read" checked>
            <input type="checkbox" data-module="users" data-permission="users.list.create" checked>
        `;

        await import('./permission-matrix');
        document.dispatchEvent(new Event('DOMContentLoaded'));

        const toggleEl = document.querySelector('[data-module-toggle="users"]');
        expect(toggleEl).toBeInstanceOf(HTMLInputElement);
        const toggle = toggleEl;
        expect(toggle.checked).toBe(true);
        expect(toggle.indeterminate).toBe(false);
    });

    it('sets toggle to indeterminate when some children are checked', async () => {
        document.body.innerHTML = `
            <input type="checkbox" data-module-toggle="users">
            <input type="checkbox" data-module="users" data-permission="users.list.read" checked>
            <input type="checkbox" data-module="users" data-permission="users.list.create">
        `;

        await import('./permission-matrix');
        document.dispatchEvent(new Event('DOMContentLoaded'));

        const toggleEl = document.querySelector('[data-module-toggle="users"]');
        expect(toggleEl).toBeInstanceOf(HTMLInputElement);
        const toggle = toggleEl;
        expect(toggle.checked).toBe(false);
        expect(toggle.indeterminate).toBe(true);
    });

    it('updates toggle state when child checkbox changes', async () => {
        document.body.innerHTML = `
            <input type="checkbox" data-module-toggle="users">
            <input type="checkbox" data-module="users" data-permission="users.list.read">
            <input type="checkbox" data-module="users" data-permission="users.list.create">
        `;

        await import('./permission-matrix');
        document.dispatchEvent(new Event('DOMContentLoaded'));

        const toggleEl = document.querySelector('[data-module-toggle="users"]');
        expect(toggleEl).toBeInstanceOf(HTMLInputElement);
        const toggle = toggleEl;
        expect(toggle.checked).toBe(false);
        expect(toggle.indeterminate).toBe(false);

        const firstCbEl = document.querySelector('[data-permission="users.list.read"]');
        expect(firstCbEl).toBeInstanceOf(HTMLInputElement);
        const firstCb = firstCbEl;
        firstCb.checked = true;
        firstCb.dispatchEvent(new Event('change'));

        expect(toggle.indeterminate).toBe(true);
    });
});
