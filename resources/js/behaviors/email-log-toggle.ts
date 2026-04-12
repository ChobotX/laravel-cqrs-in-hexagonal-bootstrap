export {};

document.addEventListener('click', (event: MouseEvent): void => {
    const t = event.target;
    if (!(t instanceof HTMLElement)) {
        return;
    }
    const toggleEl = t.closest('[data-email-log-toggle]');
    if (!(toggleEl instanceof HTMLButtonElement)) {
        return;
    }
    const button = toggleEl;

    const rowEl = button.closest('tr');
    if (!(rowEl instanceof HTMLTableRowElement)) {
        return;
    }
    const row = rowEl;
    const next = row.nextElementSibling;
    if (!(next instanceof HTMLTableRowElement)) {
        return;
    }
    const detailRow = next;

    const isExpanded: boolean = button.getAttribute('aria-expanded') === 'true';
    button.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
    detailRow.classList.toggle('hidden');
});
