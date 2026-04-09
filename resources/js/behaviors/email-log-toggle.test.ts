import { afterEach, describe, expect, it, vi } from 'vitest';

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
    afterEach(() => {
        document.body.innerHTML = '';
        vi.resetModules();
    });

    it('expands detail row on button click', async () => {
        buildDom();
        await import('./email-log-toggle');

        const button = document.querySelector<HTMLButtonElement>('[data-email-log-toggle]')!;
        button.click();

        expect(button.getAttribute('aria-expanded')).toBe('true');
        expect(document.querySelector('[data-testid="detail-row"]')?.classList.contains('hidden')).toBe(false);
    });

    it('collapses detail row on second click', async () => {
        buildDom();
        await import('./email-log-toggle');

        const button = document.querySelector<HTMLButtonElement>('[data-email-log-toggle]')!;
        button.click();
        button.click();

        expect(button.getAttribute('aria-expanded')).toBe('false');
        expect(document.querySelector('[data-testid="detail-row"]')?.classList.contains('hidden')).toBe(true);
    });

    it('does nothing when clicking outside toggle buttons', async () => {
        buildDom();
        await import('./email-log-toggle');

        const detailRow = document.querySelector('[data-testid="detail-row"]')!;
        document.body.click();

        expect(detailRow.classList.contains('hidden')).toBe(true);
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

        const button = document.querySelector<HTMLButtonElement>('[data-email-log-toggle]')!;
        button.click();

        expect(button.getAttribute('aria-expanded')).toBe('false');
    });
});
