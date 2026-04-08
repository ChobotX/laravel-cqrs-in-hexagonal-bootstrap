import type { FetchParams, FetchResult } from './composables/types';

export function getCsrfToken(): string {
    return decodeURIComponent(
        document.cookie
            .split('; ')
            .find((row) => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1] ?? '',
    );
}

function buildQueryString(params: FetchParams): string {
    const query = new URLSearchParams();

    query.set('page', String(params.page));
    query.set('per_page', String(params.perPage));

    if (params.sort !== null) {
        query.set('sort', params.sort.key);
        query.set('direction', params.sort.order);
    }

    if (params.search !== '') {
        query.set('search', params.search);
    }

    for (const [key, value] of Object.entries(params.filters)) {
        if (value !== '') {
            query.set(`filter[${key}]`, value);
        }
    }

    return query.toString();
}

export async function fetchGridData<T>(url: string, params: FetchParams): Promise<FetchResult<T>> {
    const fullUrl = `${url}?${buildQueryString(params)}`;

    const response = await fetch(fullUrl, {
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-XSRF-TOKEN': getCsrfToken(),
        },
    });

    if (!response.ok) {
        throw new Error(`Grid fetch failed: ${response.status}`);
    }

    return (await response.json()) as FetchResult<T>;
}

export async function postGridAction(url: string, body?: Record<string, unknown>): Promise<void> {
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-XSRF-TOKEN': getCsrfToken(),
    };

    const init: RequestInit = { method: 'POST', headers };

    if (body !== undefined) {
        headers['Content-Type'] = 'application/json';
        init.body = JSON.stringify(body);
    }

    const response = await fetch(url, init);

    if (!response.ok) {
        throw new Error(`Grid action failed: ${response.status}`);
    }
}

export async function deleteGridAction(url: string): Promise<void> {
    const response = await fetch(url, {
        method: 'DELETE',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-XSRF-TOKEN': getCsrfToken(),
        },
    });

    if (!response.ok) {
        throw new Error(`Grid delete failed: ${response.status}`);
    }
}
