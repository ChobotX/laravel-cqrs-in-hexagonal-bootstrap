type AppBootstrapPayload = {
    displayTimezone?: string;
    locale?: string;
};

function readMetaContent(name: string): string | undefined {
    if (typeof document === 'undefined') {
        return undefined;
    }

    const el = document.querySelector(`meta[name="${name}"]`);
    const value = el?.getAttribute('content')?.trim();

    return value !== undefined && value !== '' ? value : undefined;
}

const existing: AppBootstrapPayload =
    typeof window !== 'undefined' && window.__APP__ !== undefined ? window.__APP__ : {};

window.__APP__ = {
    ...existing,
    displayTimezone: readMetaContent('app-display-timezone'),
    locale: typeof document === 'undefined' ? undefined : document.documentElement.lang?.trim() || undefined,
};
