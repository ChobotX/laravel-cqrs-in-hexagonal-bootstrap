import { type Ref, ref, watch } from 'vue';
import { error as logError } from '../../../core/logger/logger';
import { fetchGridData } from '../data-grid-api';
import type { FetchParams, FetchResult, SortItem } from './types';

interface ServerDataDeps {
    page: Ref<number>;
    perPage: Ref<number>;
    sortBy: Ref<SortItem | null>;
    debouncedSearch: Ref<string>;
    filters: Ref<Record<string, string>>;
    fetchUrl: string;
    setTotal: (total: number) => void;
    fetchFn?: (url: string, params: FetchParams) => Promise<FetchResult<unknown>>;
}

export interface ServerDataReturn<T> {
    items: Ref<T[]>;
    loading: Ref<boolean>;
    error: Ref<string | null>;
    extra: Ref<Record<string, unknown>>;
    refresh: () => void;
}

const COALESCE_MS = 50;

function buildFetchParams(deps: ServerDataDeps): FetchParams {
    return {
        page: deps.page.value,
        perPage: deps.perPage.value,
        sort: deps.sortBy.value,
        filters: deps.filters.value,
        search: deps.debouncedSearch.value,
    };
}

function applyFetchResult<T>(
    result: FetchResult<T> & Record<string, unknown>,
    items: Ref<T[]>,
    extra: Ref<Record<string, unknown>>,
    setTotal: (total: number) => void,
): void {
    items.value = result.data;
    setTotal(result.meta.total);

    const { data: _data, meta: _meta, ...rest } = result;
    extra.value = rest;
}

function handleFetchError(err: unknown, errorRef: Ref<string | null>): void {
    errorRef.value = err instanceof Error ? err.message : 'Unknown error';
}

async function executeFetch<T>(
    deps: ServerDataDeps,
    state: { items: Ref<T[]>; loading: Ref<boolean>; error: Ref<string | null>; extra: Ref<Record<string, unknown>> },
    isStale: () => boolean,
): Promise<void> {
    try {
        const fetcher = deps.fetchFn ?? fetchGridData;
        const result = (await fetcher(deps.fetchUrl, buildFetchParams(deps))) as FetchResult<T> &
            Record<string, unknown>;

        if (isStale()) {
            return;
        }

        applyFetchResult(result, state.items, state.extra, deps.setTotal);
    } catch (err) {
        if (isStale()) {
            return;
        }

        handleFetchError(err, state.error);
        logError('Grid data fetch failed', err);
    } finally {
        if (!isStale()) {
            state.loading.value = false;
        }
    }
}

export function useServerData<T>(deps: ServerDataDeps): ServerDataReturn<T> {
    const items = ref<T[]>([]) as Ref<T[]>;
    const loading = ref(false);
    const error = ref<string | null>(null);
    const extra = ref<Record<string, unknown>>({});
    let fetchId = 0;

    async function load(): Promise<void> {
        const currentFetchId = ++fetchId;
        loading.value = true;
        error.value = null;

        await executeFetch<T>(deps, { items, loading, error, extra }, () => currentFetchId !== fetchId);
    }

    let timeout: ReturnType<typeof setTimeout> | undefined;

    watch(
        [deps.page, deps.perPage, deps.sortBy, deps.debouncedSearch, deps.filters],
        () => {
            clearTimeout(timeout);
            loading.value = true;
            timeout = setTimeout(() => {
                void load();
            }, COALESCE_MS);
        },
        { deep: true },
    );

    function refresh(): void {
        void load();
    }

    return { items, loading, error, extra, refresh };
}
