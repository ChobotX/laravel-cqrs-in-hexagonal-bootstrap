import { mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { defineComponent, nextTick, ref } from 'vue';
import type { FetchResult, Preset } from '../composables/types';

// Mock useUrlState so composables don't read from real URL
vi.mock('../composables/useUrlState', () => ({
    readParam: vi.fn(() => ''),
    readFilterParams: vi.fn(() => ({})),
    syncToUrl: vi.fn(),
    writeParams: vi.fn(),
}));

// Mock data-grid-api so real fetch is never called
vi.mock('../data-grid-api', () => ({
    fetchGridData: vi.fn(),
    postGridAction: vi.fn(),
    deleteGridAction: vi.fn(),
    getCsrfToken: vi.fn().mockReturnValue('test-token'),
}));

import { useDataGrid } from '../composables/useDataGrid';
import { readParam } from '../composables/useUrlState';
import { fetchGridData } from '../data-grid-api';

function makeFetchResult<T>(data: T[], total: number): FetchResult<T> {
    return {
        data,
        meta: { current_page: 1, per_page: 15, total, total_pages: Math.ceil(total / 15) },
    };
}

function makePreset(overrides: Partial<Preset> = {}): Preset {
    return {
        id: 'preset-1',
        name: 'Default',
        filters: '{}',
        sorting: '{}',
        search: '',
        is_default: false,
        position: 0,
        scope: 'personal',
        team_id: null,
        is_owner: true,
        ...overrides,
    };
}

type DataGridReturn = ReturnType<typeof useDataGrid>;

function mountWithDataGrid(options: Parameters<typeof useDataGrid>[0]) {
    let grid!: DataGridReturn;

    const wrapper = mount(
        defineComponent({
            setup() {
                grid = useDataGrid(options);

                return () => null;
            },
        }),
    );

    return { wrapper, grid: () => grid };
}

beforeEach(() => {
    vi.useFakeTimers();
    vi.mocked(readParam).mockReturnValue('');
    vi.mocked(fetchGridData).mockResolvedValue(makeFetchResult([], 0));

    Object.defineProperty(document, 'cookie', {
        writable: true,
        value: 'XSRF-TOKEN=test-token',
    });

    // Default: no presets
    vi.stubGlobal(
        'fetch',
        vi.fn().mockResolvedValue({
            ok: true,
            json: vi.fn().mockResolvedValue({ data: [] }),
        }),
    );
});

afterEach(() => {
    vi.useRealTimers();
    vi.unstubAllGlobals();
});

describe('useDataGrid', () => {
    it('returns all expected sub-composables and columns', () => {
        const columns = ref([{ key: 'name', title: 'Name', sortable: true }]);

        const { grid } = mountWithDataGrid({
            fetchUrl: '/api/test',
            gridName: 'test',
            columns,
        });

        expect(grid().columns.value).toEqual([{ key: 'name', title: 'Name', sortable: true }]);
        expect(grid().pagination).toBeDefined();
        expect(grid().sort).toBeDefined();
        expect(grid().search).toBeDefined();
        expect(grid().filter).toBeDefined();
        expect(grid().serverData).toBeDefined();
        expect(grid().presets).toBeDefined();
        expect(typeof grid().clearAll).toBe('function');
    });

    it('applies default sort and per page', () => {
        const { grid } = mountWithDataGrid({
            fetchUrl: '/api/test',
            gridName: 'test',
            columns: ref([]),
            defaultSort: { key: 'created_at', order: 'desc' },
            defaultPerPage: 25,
        });

        expect(grid().sort.sortBy.value).toEqual({ key: 'created_at', order: 'desc' });
        expect(grid().pagination.perPage.value).toBe(25);
    });

    it('resets page to 1 when sort changes', async () => {
        const { grid } = mountWithDataGrid({
            fetchUrl: '/api/test',
            gridName: 'test',
            columns: ref([]),
        });

        grid().pagination.page.value = 3;
        grid().pagination.setTotal(100);

        grid().sort.toggleSort('name');
        await nextTick();

        expect(grid().pagination.page.value).toBe(1);
    });

    it('resets page to 1 when filter changes', async () => {
        const { grid } = mountWithDataGrid({
            fetchUrl: '/api/test',
            gridName: 'test',
            columns: ref([]),
        });

        grid().pagination.page.value = 5;
        grid().pagination.setTotal(200);

        grid().filter.setFilter('status', 'active');
        await nextTick();

        expect(grid().pagination.page.value).toBe(1);
    });

    it('resets page to 1 when debounced search changes', async () => {
        const { grid } = mountWithDataGrid({
            fetchUrl: '/api/test',
            gridName: 'test',
            columns: ref([]),
        });

        grid().pagination.page.value = 4;
        grid().pagination.setTotal(100);

        grid().search.searchTerm.value = 'hello';
        await nextTick();

        // Wait for the search debounce (300ms default)
        vi.advanceTimersByTime(300);
        await nextTick();

        expect(grid().pagination.page.value).toBe(1);
    });

    it('clearAll resets all state', () => {
        const { grid } = mountWithDataGrid({
            fetchUrl: '/api/test',
            gridName: 'test',
            columns: ref([]),
            defaultSort: { key: 'name', order: 'asc' },
        });

        // Modify state
        grid().search.searchTerm.value = 'query';
        grid().filter.setFilter('role', 'admin');
        grid().sort.toggleSort('email');
        grid().pagination.page.value = 3;
        grid().pagination.setTotal(100);
        grid().presets.activePresetId.value = 'p1';

        grid().clearAll();

        expect(grid().search.searchTerm.value).toBe('');
        expect(grid().filter.filters.value).toEqual({});
        expect(grid().sort.sortBy.value).toEqual({ key: 'name', order: 'asc' });
        expect(grid().presets.activePresetId.value).toBeNull();
        expect(grid().pagination.page.value).toBe(1);
    });

    it('uses custom fetchFn when provided', () => {
        const customFetch = vi.fn().mockResolvedValue(makeFetchResult([], 0));

        const { grid } = mountWithDataGrid({
            fetchUrl: '/api/test',
            gridName: 'test',
            columns: ref([]),
            fetchFn: customFetch,
        });

        grid().serverData.refresh();

        expect(customFetch).toHaveBeenCalled();
    });

    describe('onMounted', () => {
        it('loads presets and calls refresh when no default preset', async () => {
            vi.stubGlobal(
                'fetch',
                vi.fn().mockResolvedValue({
                    ok: true,
                    json: vi.fn().mockResolvedValue({ data: [makePreset({ is_default: false })] }),
                }),
            );
            vi.mocked(fetchGridData).mockResolvedValue(makeFetchResult([{ id: 1 }], 1));

            const { grid } = mountWithDataGrid({
                fetchUrl: '/api/test',
                gridName: 'test',
                columns: ref([]),
            });

            await vi.runAllTimersAsync();
            await nextTick();
            await vi.runAllTimersAsync();

            expect(grid().presets.presets.value).toHaveLength(1);
            expect(vi.mocked(fetchGridData)).toHaveBeenCalled();
        });

        it('applies default preset instead of calling refresh when found', async () => {
            const defaultPreset = makePreset({
                id: 'dp1',
                is_default: true,
                sorting: JSON.stringify({ sort: 'name', direction: 'asc' }),
                filters: JSON.stringify({ status: 'active' }),
                search: 'preset-search',
            });

            vi.stubGlobal(
                'fetch',
                vi.fn().mockResolvedValue({
                    ok: true,
                    json: vi.fn().mockResolvedValue({ data: [defaultPreset] }),
                }),
            );

            vi.mocked(fetchGridData).mockClear();

            const { grid } = mountWithDataGrid({
                fetchUrl: '/api/test',
                gridName: 'test',
                columns: ref([]),
            });

            await vi.runAllTimersAsync();
            await nextTick();

            expect(grid().presets.activePresetId.value).toBe('dp1');
            expect(grid().sort.sortBy.value).toEqual({ key: 'name', order: 'asc' });
            expect(grid().filter.filters.value).toEqual({ status: 'active' });
            expect(grid().search.searchTerm.value).toBe('preset-search');
        });

        it('calls refresh when URL state exists even with default preset', async () => {
            vi.mocked(readParam).mockImplementation((key: string) => {
                if (key === 'search') return 'url-search';

                return '';
            });

            const defaultPreset = makePreset({ id: 'dp1', is_default: true });

            vi.stubGlobal(
                'fetch',
                vi.fn().mockResolvedValue({
                    ok: true,
                    json: vi.fn().mockResolvedValue({ data: [defaultPreset] }),
                }),
            );
            vi.mocked(fetchGridData).mockResolvedValue(makeFetchResult([], 0));

            const { grid } = mountWithDataGrid({
                fetchUrl: '/api/test',
                gridName: 'test',
                columns: ref([]),
            });

            await vi.runAllTimersAsync();
            await nextTick();
            await vi.runAllTimersAsync();

            expect(grid().search.searchTerm.value).toBe('url-search');
            expect(vi.mocked(fetchGridData)).toHaveBeenCalled();
        });
    });
});
