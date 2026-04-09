export {};

document.addEventListener('click', (event: MouseEvent): void => {
    const button: HTMLButtonElement | null = (event.target as HTMLElement).closest<HTMLButtonElement>(
        '[data-email-log-toggle]',
    );

    if (!button) return;

    const row: HTMLTableRowElement | null = button.closest<HTMLTableRowElement>('tr');
    const detailRow: HTMLTableRowElement | null = row?.nextElementSibling as HTMLTableRowElement | null;

    if (!detailRow) return;

    const isExpanded: boolean = button.getAttribute('aria-expanded') === 'true';
    button.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
    detailRow.classList.toggle('hidden');
});
