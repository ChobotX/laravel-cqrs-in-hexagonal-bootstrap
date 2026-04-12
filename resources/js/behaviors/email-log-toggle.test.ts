import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

function buildDom(): void {
    document.body.innerHTML = `
        <table>
            <tbody>
                <tr>
                    <td>
                        <button data-email-log-toggle aria-expanded="false" data-testid="toggle-btn">
                            Toggle
                        </button>
                    </td>
                </tr>
                <tr class="hidden" data-testid="detail-row">
                    <td>Details here</td>
                </tr>
            </tbody>
        </table>
    `;
}

describe('email-log-toggle', () => {
    beforeEach(() => {
        vi.restoreAllMocks();
    });

    afterEach(() => {
        document.body.innerHTML = '';
        vi.resetModules();
    });

    it('expands detail row on button click', async () => {
        buildDom();
        await import('./email-log-toggle');

        const button = document.querySelector<HTMLButtonElement>('[data-email-log-toggle]');
        expect(button).not.toBeNull();
        button?.click();

        expect(button?.getAttribute('aria-expanded')).toBe('true');
        expect(document.querySelector('[data-testid="detail-row"]')?.classList.contains('hidden')).toBe(false);
    });

    it('collapses detail row on second click', async () => {
        buildDom();
        await import('./email-log-toggle');

        const button = document.querySelector<HTMLButtonElement>('[data-email-log-toggle]');
        expect(button).not.toBeNull();
        button?.click();
        button?.click();

        expect(button?.getAttribute('aria-expanded')).toBe('false');
        expect(document.querySelector('[data-testid="detail-row"]')?.classList.contains('hidden')).toBe(true);
    });

    it('does nothing when clicking outside toggle buttons', async () => {
        buildDom();
        await import('./email-log-toggle');

        const detailRow = document.querySelector('[data-testid="detail-row"]');
        expect(detailRow).not.toBeNull();
        document.body.click();

        expect(detailRow?.classList.contains('hidden')).toBe(true);
    });

    it('does nothing when there is no next sibling row', async () => {
        document.body.innerHTML = `
            <table>
                <tbody>
                    <tr>
                        <td>
                            <button data-email-log-toggle aria-expanded="false">Toggle</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        `;
        await import('./email-log-toggle');

        const button = document.querySelector<HTMLButtonElement>('[data-email-log-toggle]');
        expect(button).not.toBeNull();
        button?.click();

        expect(button?.getAttribute('aria-expanded')).toBe('false');
    });

    it('ignores clicks whose target is not an HTMLElement', async () => {
        buildDom();
        await import('./email-log-toggle');

        const ev = new MouseEvent('click', { bubbles: true });
        Object.defineProperty(ev, 'target', { value: document.createTextNode(''), enumerable: true });
        document.dispatchEvent(ev);
    });

    it('ignores toggle attribute on non-button elements', async () => {
        document.body.innerHTML = `<div data-email-log-toggle>not a button</div>`;
        await import('./email-log-toggle');

        const div = document.querySelector('[data-email-log-toggle]');
        div?.dispatchEvent(new MouseEvent('click', { bubbles: true }));
    });

    it('does nothing when toggle is not inside a table row', async () => {
        document.body.innerHTML = '<button data-email-log-toggle aria-expanded="false">Orphan</button>';
        await import('./email-log-toggle');

        const button = document.querySelector<HTMLButtonElement>('[data-email-log-toggle]');
        button?.click();
        expect(button?.getAttribute('aria-expanded')).toBe('false');
    });
});
