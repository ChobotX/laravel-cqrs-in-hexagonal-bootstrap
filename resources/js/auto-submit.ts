export {};

document.addEventListener('change', (event: Event): void => {
    const target = event.target as HTMLElement;

    if (!target.hasAttribute('data-auto-submit')) {
        return;
    }

    const form = target.closest('form');

    if (form !== null) {
        form.submit();
    }
});
