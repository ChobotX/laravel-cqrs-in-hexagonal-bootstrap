export {};

const SELECTOR = 'time[data-local-datetime]';

function formatElement(timeElement: HTMLTimeElement): void {
    const iso = timeElement.dateTime;

    if (iso === '') {
        return;
    }

    const parsed = new Date(iso);

    if (Number.isNaN(parsed.getTime())) {
        return;
    }

    timeElement.textContent = new Intl.DateTimeFormat(navigator.language, {
        dateStyle: 'medium',
        timeStyle: 'medium',
    }).format(parsed);
}

function formatAll(root: ParentNode): void {
    root.querySelectorAll<HTMLTimeElement>(SELECTOR).forEach(formatElement);
}

document.addEventListener('DOMContentLoaded', (): void => {
    formatAll(document);
});
