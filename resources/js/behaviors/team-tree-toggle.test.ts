import { afterEach, describe, expect, it, vi } from 'vitest';

function buildDom(): void {
    document.body.innerHTML = `
        <button id="teams-view-list-btn" class="bg-indigo-100 text-indigo-700">List</button>
        <button id="teams-view-tree-btn" class="text-gray-400 hover:text-gray-600">Tree</button>
        <div id="teams-list-view">List content</div>
        <div id="teams-tree-view" class="hidden">Tree content</div>
    `;
}

describe('team-tree-toggle', () => {
    afterEach(() => {
        document.body.innerHTML = '';
        vi.resetModules();
        // Reset URL
        window.history.replaceState(null, '', '/');
    });

    it('switches to tree view on tree button click', async () => {
        buildDom();
        await import('./team-tree-toggle');

        document.getElementById('teams-view-tree-btn')?.click();

        expect(document.getElementById('teams-tree-view')?.classList.contains('hidden')).toBe(false);
        expect(document.getElementById('teams-list-view')?.classList.contains('hidden')).toBe(true);
        expect(new URL(window.location.href).searchParams.get('view')).toBe('tree');
    });

    it('switches to list view on list button click', async () => {
        buildDom();
        await import('./team-tree-toggle');

        document.getElementById('teams-view-tree-btn')?.click();
        document.getElementById('teams-view-list-btn')?.click();

        expect(document.getElementById('teams-list-view')?.classList.contains('hidden')).toBe(false);
        expect(document.getElementById('teams-tree-view')?.classList.contains('hidden')).toBe(true);
        expect(new URL(window.location.href).searchParams.get('view')).toBe('list');
    });

    it('activates tree view on load when URL has view=tree', async () => {
        window.history.replaceState(null, '', '/?view=tree');
        buildDom();
        await import('./team-tree-toggle');

        expect(document.getElementById('teams-tree-view')?.classList.contains('hidden')).toBe(false);
        expect(document.getElementById('teams-list-view')?.classList.contains('hidden')).toBe(true);
    });

    it('dispatches tree-view:shown event on tree button click', async () => {
        buildDom();
        await import('./team-tree-toggle');

        const handler = vi.fn();
        document.getElementById('teams-tree-view')?.addEventListener('tree-view:shown', handler);

        document.getElementById('teams-view-tree-btn')?.click();

        expect(handler).toHaveBeenCalledOnce();
    });

    it('toggles active/inactive CSS classes on buttons', async () => {
        buildDom();
        await import('./team-tree-toggle');

        const listBtn = document.getElementById('teams-view-list-btn') as HTMLElement;
        const treeBtn = document.getElementById('teams-view-tree-btn') as HTMLElement;

        treeBtn.click();

        expect(treeBtn.classList.contains('bg-indigo-100')).toBe(true);
        expect(treeBtn.classList.contains('text-indigo-700')).toBe(true);
        expect(treeBtn.classList.contains('text-gray-400')).toBe(false);

        expect(listBtn.classList.contains('text-gray-400')).toBe(true);
        expect(listBtn.classList.contains('bg-indigo-100')).toBe(false);
    });

    it('does nothing when DOM elements are missing', async () => {
        document.body.innerHTML = '<div>No toggle elements</div>';
        await import('./team-tree-toggle');

        expect(document.body.innerHTML).toBe('<div>No toggle elements</div>');
    });
});
