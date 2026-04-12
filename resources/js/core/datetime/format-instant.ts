const UTC_ANCHOR_HOUR = 12;

function readBootstrapDisplayTimezone(): string | undefined {
    if (typeof window === 'undefined') {
        return undefined;
    }

    const fromWindow = window.__APP__?.displayTimezone;

    if (typeof fromWindow === 'string' && fromWindow !== '') {
        return fromWindow;
    }

    const meta = document.querySelector('meta[name="app-display-timezone"]');
    const fromMeta = meta?.getAttribute('content')?.trim();

    return fromMeta !== undefined && fromMeta !== '' ? fromMeta : undefined;
}

export function getDisplayTimeZone(): string | undefined {
    return readBootstrapDisplayTimezone();
}

export function formatInstant(iso: string, options?: Intl.DateTimeFormatOptions): string {
    const parsed = new Date(iso);

    if (Number.isNaN(parsed.getTime())) {
        return '';
    }

    const timeZone = getDisplayTimeZone();

    return new Intl.DateTimeFormat(navigator.language, {
        dateStyle: 'medium',
        timeStyle: 'medium',
        ...(timeZone === undefined ? {} : { timeZone }),
        ...options,
    }).format(parsed);
}

export function formatDateOnly(yyyyMmDd: string, options?: Intl.DateTimeFormatOptions): string {
    const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(yyyyMmDd);

    if (match === null) {
        return yyyyMmDd;
    }

    const year = Number(match[1]);
    const month = Number(match[2]);
    const day = Number(match[3]);
    const utcNoon = new Date(Date.UTC(year, month - 1, day, UTC_ANCHOR_HOUR));

    return new Intl.DateTimeFormat(navigator.language, {
        dateStyle: 'medium',
        timeZone: 'UTC',
        ...options,
    }).format(utcNoon);
}
