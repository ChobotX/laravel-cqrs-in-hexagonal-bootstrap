import { afterEach, describe, expect, it, vi } from 'vitest';

describe('avatar-remove', () => {
    afterEach(() => {
        document.body.innerHTML = '';
        vi.resetModules();
    });

    it('removes preview and sets hidden input on click', async () => {
        document.body.innerHTML = `
            <div data-avatar-field>
                <input name="remove_avatar" type="hidden" value="0" data-avatar-remove>
                <div data-avatar-preview>
                    <img src="/files/123" alt="Avatar">
                    <button type="button" data-avatar-remove-btn>X</button>
                </div>
            </div>
        `;

        await import('./avatar-remove');

        const btn = document.querySelector<HTMLButtonElement>('[data-avatar-remove-btn]');
        btn?.click();

        expect(document.querySelector('[data-avatar-preview]')).toBeNull();
        expect(document.querySelector<HTMLInputElement>('[data-avatar-remove]')?.value).toBe('1');
    });

    it('does nothing without remove button', async () => {
        document.body.innerHTML = `
            <div data-avatar-field>
                <input name="remove_avatar" type="hidden" value="0" data-avatar-remove>
                <div data-avatar-preview>
                    <img src="/files/123" alt="Avatar">
                </div>
            </div>
        `;

        await import('./avatar-remove');

        expect(document.querySelector('[data-avatar-preview]')).not.toBeNull();
        expect(document.querySelector<HTMLInputElement>('[data-avatar-remove]')?.value).toBe('0');
    });
});
