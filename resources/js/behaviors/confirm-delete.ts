export {};

document.addEventListener('submit', (event: SubmitEvent): void => {
    const form: HTMLFormElement = event.target as HTMLFormElement;
    if (!form.hasAttribute('data-confirm-delete')) {
        return;
    }

    event.preventDefault();

    const button: HTMLElement | null = form.querySelector<HTMLElement>('[data-confirm-title]');
    const title: string = button?.getAttribute('data-confirm-title') ?? 'Confirm';
    const message: string = button?.getAttribute('data-confirm-message') ?? 'Are you sure?';

    void window.appDialog.confirm({ title, message }).then((confirmed: boolean): void => {
        if (confirmed) {
            form.submit();
        }
    });
});
