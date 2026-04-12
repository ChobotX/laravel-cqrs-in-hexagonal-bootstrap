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

        vi.mocked(window.appDialog.confirm).mockResolvedValue(false);

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

        vi.mocked(window.appDialog.confirm).mockResolvedValue(true);

        await import('./confirm-delete');

        const form = document.querySelector('form');
        expect(form).toBeInstanceOf(HTMLFormElement);
        const submitSpy = vi.spyOn(form, 'submit').mockImplementation((): void => {
            // noop: prevent actual form submission in test
        });
        const event: Event = new Event('submit', { bubbles: true, cancelable: true });
        form.dispatchEvent(event);

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

        vi.mocked(window.appDialog.confirm).mockResolvedValue(false);

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

    it('ignores submit when event target is not an HTMLFormElement', async () => {
        document.body.innerHTML = `
            <form data-confirm-delete action="/users/1" method="POST">
                <button type="submit">Delete</button>
            </form>
        `;

        await import('./confirm-delete');

        const button = document.querySelector('button');
        expect(button).not.toBeNull();
        const event = new SubmitEvent('submit', { bubbles: true, cancelable: true });
        button?.dispatchEvent(event);

        expect(window.appDialog.confirm).not.toHaveBeenCalled();
    });
});
