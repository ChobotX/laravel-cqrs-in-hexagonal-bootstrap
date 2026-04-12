import { formatInstant } from '../core/datetime/format-instant';

const SELECTOR = 'time[data-local-datetime]';

function formatElement(timeElement: HTMLTimeElement): void {
    const iso = timeElement.dateTime;

    if (iso === '') {
        return;
    }

    const formatted = formatInstant(iso);

    if (formatted === '') {
        return;
    }

    timeElement.textContent = formatted;
}

function formatAll(root: ParentNode): void {
    root.querySelectorAll<HTMLTimeElement>(SELECTOR).forEach(formatElement);
}

document.addEventListener('DOMContentLoaded', (): void => {
    formatAll(document);
});
