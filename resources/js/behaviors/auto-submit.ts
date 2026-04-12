export {};

document.addEventListener('change', (event: Event): void => {
    const t = event.target;
    if (!(t instanceof HTMLElement)) {
        return;
    }
    const target = t;

    if (!target.hasAttribute('data-auto-submit')) {
        return;
    }

    const form = target.closest('form');

    if (form !== null) {
        form.submit();
    }
});
