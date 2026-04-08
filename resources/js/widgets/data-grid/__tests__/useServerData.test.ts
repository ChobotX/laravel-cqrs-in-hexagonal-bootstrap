import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { type EffectScope, effectScope, nextTick, ref } from 'vue';
import type { FetchResult } from '../composables/types';
import { useServerData } from '../composables/useServerData';

function runInScope<T>(effectScopeInstance: EffectScope, fn: () => T): T {
    const result = effectScopeInstance.run(fn);
    if (result === undefined) throw new Error('scope.run returned undefined');
    return result;
}

vi.mock('../data-grid-api', () => ({
    fetchGridData: vi.fn(),
}));

import { fetchGridData } from '../data-grid-api';

const COALESCE_MS = 50;

function createDeps(overrides: Partial<Parameters<typeof useServerData>[0]> = {}) {
    return {
        page: ref(1),
        perPage: ref(15),
        sortBy: ref(null),
        debouncedSearch: ref(''),
        filters: ref<Record<string, string>>({}),
        fetchUrl: '/api/test',
        setTotal: vi.fn(),
        ...overrides,
    };
}

function makeFetchResult<T>(data: T[], total: number): FetchResult<T> {
    return {
        data,
        meta: { current_page: 1, per_page: 15, total, total_pages: Math.ceil(total / 15) },
    };
}

let scope: ReturnType<typeof effectScope>;

beforeEach(() => {
    vi.useFakeTimers();
    vi.mocked(fetchGridData).mockReset();
    scope = effectScope();
});

afterEach(() => {
    scope.stop();
    vi.useRealTimers();
});

describe('useServerData', () => {
    it('initializes with empty state', () => {
        const deps = createDeps();
        const { items, loading, error, extra } = runInScope(scope, () => useServerData(deps));

        expect(items.value).toEqual([]);
        expect(loading.value).toBe(false);
        expect(error.value).toBeNull();
        expect(extra.value).toEqual({});
    });

    it('refresh triggers a fetch and populates items', async () => {
        const result = makeFetchResult([{ id: 1 }, { id: 2 }], 2);
        vi.mocked(fetchGridData).mockResolvedValue(result);

        const deps = createDeps();
        const { items, loading, error, refresh } = runInScope(scope, () => useServerData(deps));

        refresh();

        expect(loading.value).toBe(true);
        await vi.runAllTimersAsync();

        expect(items.value).toEqual([{ id: 1 }, { id: 2 }]);
        expect(loading.value).toBe(false);
        expect(error.value).toBeNull();
        expect(deps.setTotal).toHaveBeenCalledWith(2);
    });

    it('uses custom fetchFn when provided', async () => {
        const customFetch = vi.fn().mockResolvedValue(makeFetchResult([{ id: 99 }], 1));
        const deps = createDeps({ fetchFn: customFetch });
        const { items, refresh } = runInScope(scope, () => useServerData(deps));

        refresh();
        await vi.runAllTimersAsync();

        expect(customFetch).toHaveBeenCalledWith('/api/test', expect.any(Object));
        expect(vi.mocked(fetchGridData)).not.toHaveBeenCalled();
        expect(items.value).toEqual([{ id: 99 }]);
    });

    it('passes correct params to fetch', async () => {
        vi.mocked(fetchGridData).mockResolvedValue(makeFetchResult([], 0));

        const deps = createDeps({
            page: ref(3),
            perPage: ref(25),
            sortBy: ref({ key: 'name', order: 'asc' as const }),
            debouncedSearch: ref('query'),
            filters: ref({ status: 'active' }),
        });
        const { refresh } = runInScope(scope, () => useServerData(deps));

        refresh();
        await vi.runAllTimersAsync();

        expect(vi.mocked(fetchGridData)).toHaveBeenCalledWith('/api/test', {
            page: 3,
            perPage: 25,
            sort: { key: 'name', order: 'asc' },
            filters: { status: 'active' },
            search: 'query',
        });
    });

    it('sets error on fetch failure with Error instance', async () => {
        vi.mocked(fetchGridData).mockRejectedValue(new Error('Network error'));

        const deps = createDeps();
        const { error, loading, refresh } = runInScope(scope, () => useServerData(deps));

        refresh();
        await vi.runAllTimersAsync();

        expect(error.value).toBe('Network error');
        expect(loading.value).toBe(false);
    });

    it('sets error on fetch failure with non-Error value', async () => {
        vi.mocked(fetchGridData).mockRejectedValue('string error');

        const deps = createDeps();
        const { error, refresh } = runInScope(scope, () => useServerData(deps));

        refresh();
        await vi.runAllTimersAsync();

        expect(error.value).toBe('Unknown error');
    });

    it('extracts extra fields from response', async () => {
        const response = {
            ...makeFetchResult([{ id: 1 }], 1),
            summary: { totalAmount: 500 },
            flags: ['flagA'],
        };
        vi.mocked(fetchGridData).mockResolvedValue(response as FetchResult<unknown>);

        const deps = createDeps();
        const { extra, refresh } = runInScope(scope, () => useServerData(deps));

        refresh();
        await vi.runAllTimersAsync();

        expect(extra.value).toEqual({ summary: { totalAmount: 500 }, flags: ['flagA'] });
    });

    it('watches deps and reloads after coalesce delay', async () => {
        vi.mocked(fetchGridData).mockResolvedValue(makeFetchResult([], 0));

        const deps = createDeps();
        scope.run(() => useServerData(deps));

        deps.page.value = 2;
        await nextTick();

        expect(vi.mocked(fetchGridData)).not.toHaveBeenCalled();

        vi.advanceTimersByTime(COALESCE_MS);
        await vi.runAllTimersAsync();

        expect(vi.mocked(fetchGridData)).toHaveBeenCalledOnce();
    });

    it('coalesces multiple rapid dep changes into single fetch', async () => {
        vi.mocked(fetchGridData).mockResolvedValue(makeFetchResult([], 0));

        const deps = createDeps();
        scope.run(() => useServerData(deps));

        deps.page.value = 2;
        await nextTick();
        vi.advanceTimersByTime(20);

        deps.page.value = 3;
        await nextTick();
        vi.advanceTimersByTime(20);

        deps.page.value = 4;
        await nextTick();

        vi.advanceTimersByTime(COALESCE_MS);
        await vi.runAllTimersAsync();

        expect(vi.mocked(fetchGridData)).toHaveBeenCalledOnce();
        expect(vi.mocked(fetchGridData)).toHaveBeenCalledWith('/api/test', expect.objectContaining({ page: 4 }));
    });

    it('discards stale response when a newer fetch is in flight', async () => {
        const setTotal = vi.fn();
        const deps = createDeps({ setTotal });

        let resolveFirst!: (value: FetchResult<unknown>) => void;
        let resolveSecond!: (value: FetchResult<unknown>) => void;

        vi.mocked(fetchGridData)
            .mockReturnValueOnce(
                new Promise((r) => {
                    resolveFirst = r;
                }),
            )
            .mockReturnValueOnce(
                new Promise((r) => {
                    resolveSecond = r;
                }),
            );

        const { items, refresh } = runInScope(scope, () => useServerData(deps));

        refresh();
        await nextTick();

        refresh();
        await nextTick();

        resolveFirst(makeFetchResult([{ id: 'stale' }], 1));
        await vi.runAllTimersAsync();

        expect(items.value).toEqual([]);
        expect(setTotal).not.toHaveBeenCalled();

        resolveSecond(makeFetchResult([{ id: 'current' }], 5));
        await vi.runAllTimersAsync();

        expect(items.value).toEqual([{ id: 'current' }]);
        expect(setTotal).toHaveBeenCalledWith(5);
    });

    it('discards stale error when a newer fetch is in flight', async () => {
        const deps = createDeps();

        let rejectFirst!: (reason: Error) => void;
        let resolveSecond!: (value: FetchResult<unknown>) => void;

        vi.mocked(fetchGridData)
            .mockReturnValueOnce(
                new Promise((_, rej) => {
                    rejectFirst = rej;
                }),
            )
            .mockReturnValueOnce(
                new Promise((r) => {
                    resolveSecond = r;
                }),
            );

        const { error, refresh } = runInScope(scope, () => useServerData(deps));

        refresh();
        await nextTick();
        refresh();
        await nextTick();

        rejectFirst(new Error('stale error'));
        await vi.runAllTimersAsync();

        expect(error.value).toBeNull();

        resolveSecond(makeFetchResult([], 0));
        await vi.runAllTimersAsync();

        expect(error.value).toBeNull();
    });

    it('does not clear loading when stale fetch completes', async () => {
        const deps = createDeps();

        let resolveFirst!: (value: FetchResult<unknown>) => void;

        vi.mocked(fetchGridData)
            .mockReturnValueOnce(
                new Promise((r) => {
                    resolveFirst = r;
                }),
            )
            .mockReturnValueOnce(
                new Promise(() => {
                    /* intentionally never resolved */
                }),
            );

        const { loading, refresh } = runInScope(scope, () => useServerData(deps));

        refresh();
        await nextTick();

        refresh();
        await nextTick();

        expect(loading.value).toBe(true);

        resolveFirst(makeFetchResult([], 0));
        await vi.runAllTimersAsync();

        expect(loading.value).toBe(true);
    });
});
