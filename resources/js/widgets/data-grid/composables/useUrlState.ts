import { type Ref, watch } from 'vue';

const DEFAULT_PER_PAGE = 15;

export function readParam(key: string): string {
    return new URLSearchParams(window.location.search).get(key) ?? '';
}

export function readFilterParams(): Record<string, string> {
    const params = new URLSearchParams(window.location.search);
    const filters: Record<string, string> = {};

    for (const [key, value] of params.entries()) {
        const match = key.match(/^filter\[(\w+)]$/);

        if (match && value !== '') {
            filters[match[1]] = value;
        }
    }

    return filters;
}

export function writeParams(params: Record<string, string>): void {
    const url = new URL(window.location.href);
    const searchParams = url.searchParams;

    // Clear all existing grid params
    for (const key of [...searchParams.keys()]) {
        if (
            key === 'page' ||
            key === 'per_page' ||
            key === 'sort' ||
            key === 'direction' ||
            key === 'search' ||
            key === 'preset' ||
            key.startsWith('filter[')
        ) {
            searchParams.delete(key);
        }
    }

    // Set new params, omitting empty/default values
    for (const [key, value] of Object.entries(params)) {
        if (value !== '') {
            searchParams.set(key, value);
        }
    }

    window.history.replaceState(null, '', url.toString());
}

function buildUrlParams(
    page: number,
    perPage: number,
    sort: { key: string; order: 'asc' | 'desc' } | null,
    search: string,
    filters: Record<string, string>,
): Record<string, string> {
    const params: Record<string, string> = {};

    if (page > 1) {
        params.page = String(page);
    }

    if (perPage !== DEFAULT_PER_PAGE) {
        params.per_page = String(perPage);
    }

    if (sort !== null) {
        params.sort = sort.key;
        params.direction = sort.order;
    }

    if (search !== '') {
        params.search = search;
    }

    for (const [key, value] of Object.entries(filters)) {
        if (value !== '') {
            params[`filter[${key}]`] = value;
        }
    }

    return params;
}

export function syncToUrl(
    page: Ref<number>,
    perPage: Ref<number>,
    sort: Ref<{ key: string; order: 'asc' | 'desc' } | null>,
    search: Ref<string>,
    filters: Ref<Record<string, string>>,
): void {
    watch(
        [page, perPage, sort, search, filters],
        () => {
            writeParams(buildUrlParams(page.value, perPage.value, sort.value, search.value, filters.value));
        },
        { deep: true },
    );
}
