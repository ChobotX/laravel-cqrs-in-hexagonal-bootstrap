import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

describe('confirm-delete', () => {
    beforeEach(() => {
        window.appDialog = {
            confirm: vi.fn(),
            alert: vi.fn(),
        };
    });

    afterEach(() => {
        document.body.innerHTML = '';
        vi.resetModules();
    });

    it('intercepts form submission and waits for confirm', async () => {
        document.body.innerHTML = `
            <form data-confirm-delete action="/users/1" method="POST">
                <button type="submit" data-confirm-title="Delete User" data-confirm-message="Delete John?">Delete</button>
            </form>
        `;

        (window.appDialog.confirm as ReturnType<typeof vi.fn>).mockResolvedValue(false);

        await import('./confirm-delete');

        const form: HTMLFormElement | null = document.querySelector('form');
        expect(form).not.toBeNull();
        const event: Event = new Event('submit', { bubbles: true, cancelable: true });
        form?.dispatchEvent(event);

        expect(window.appDialog.confirm).toHaveBeenCalledWith({
            title: 'Delete User',
            message: 'Delete John?',
        });
    });

    it('submits form when confirmed', async () => {
        document.body.innerHTML = `
            <form data-confirm-delete action="/users/1" method="POST">
                <button type="submit" data-confirm-title="Delete" data-confirm-message="Sure?">Delete</button>
            </form>
        `;

        (window.appDialog.confirm as ReturnType<typeof vi.fn>).mockResolvedValue(true);

        await import('./confirm-delete');

        const form: HTMLFormElement | null = document.querySelector('form');
        expect(form).not.toBeNull();
        const submitSpy = vi.spyOn(form as HTMLFormElement, 'submit').mockImplementation((): void => {
            // noop: prevent actual form submission in test
        });
        const event: Event = new Event('submit', { bubbles: true, cancelable: true });
        form?.dispatchEvent(event);

        await vi.waitFor(() => {
            expect(submitSpy).toHaveBeenCalled();
        });
    });

    it('uses fallback title and message when button has no data attributes', async () => {
        document.body.innerHTML = `
            <form data-confirm-delete action="/users/1" method="POST">
                <button type="submit">Delete</button>
            </form>
        `;

        (window.appDialog.confirm as ReturnType<typeof vi.fn>).mockResolvedValue(false);

        await import('./confirm-delete');

        const form: HTMLFormElement | null = document.querySelector('form');
        expect(form).not.toBeNull();
        const event: Event = new Event('submit', { bubbles: true, cancelable: true });
        form?.dispatchEvent(event);

        expect(window.appDialog.confirm).toHaveBeenCalledWith({
            title: 'Confirm',
            message: 'Are you sure?',
        });
    });

    it('does not intercept forms without data-confirm-delete', async () => {
        document.body.innerHTML = `
            <form action="/other" method="POST">
                <button type="submit">Submit</button>
            </form>
        `;

        await import('./confirm-delete');

        const form: HTMLFormElement | null = document.querySelector('form');
        expect(form).not.toBeNull();
        const event: Event = new Event('submit', { bubbles: true, cancelable: true });
        form?.dispatchEvent(event);

        expect(window.appDialog.confirm).not.toHaveBeenCalled();
    });
});
