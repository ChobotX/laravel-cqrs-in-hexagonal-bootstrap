import { onMounted, type Ref, watch } from 'vue';
import type { ColumnDef, DataGridOptions } from './types';
import { type FilterReturn, useFilter } from './useFilter';
import { type PaginationReturn, usePagination } from './usePagination';
import { type PresetReturn, usePresets } from './usePresets';
import { type SearchReturn, useSearch } from './useSearch';
import { type ServerDataReturn, useServerData } from './useServerData';
import { type SortReturn, useSort } from './useSort';
import { syncToUrl } from './useUrlState';

interface DataGridReturn<T> {
    columns: Ref<ColumnDef[]>;
    pagination: PaginationReturn;
    sort: SortReturn;
    search: SearchReturn;
    filter: FilterReturn;
    serverData: ServerDataReturn<T>;
    presets: PresetReturn;
    clearAll: () => void;
}

export function useDataGrid<T>(options: DataGridOptions<T>): DataGridReturn<T> {
    const pagination = usePagination(options.defaultPerPage);
    const sort = useSort(options.defaultSort);
    const search = useSearch();
    const filter = useFilter();

    // Reset page to 1 when sort/filter/search changes
    watch([sort.sortBy, filter.filters, search.debouncedTerm], () => {
        pagination.resetPage();
    });

    // Sync state to URL
    syncToUrl(pagination.page, pagination.perPage, sort.sortBy, search.debouncedTerm, filter.filters);

    const serverData = useServerData<T>({
        page: pagination.page,
        perPage: pagination.perPage,
        sortBy: sort.sortBy,
        debouncedSearch: search.debouncedTerm,
        filters: filter.filters,
        fetchUrl: options.fetchUrl,
        setTotal: pagination.setTotal,
        fetchFn: options.fetchFn,
    });

    const presets = usePresets({
        gridName: options.gridName,
        sortBy: sort.sortBy,
        filters: filter.filters,
        searchTerm: search.searchTerm,
        setSearchImmediate: search.setImmediate,
        resetPage: pagination.resetPage,
    });

    function clearAll(): void {
        search.clear();
        filter.clearAll();
        sort.clearSort();
        presets.clearPreset();
        pagination.resetPage();
    }

    // Initial data load + presets
    onMounted(async () => {
        await presets.loadPresets();

        // If no URL params are set, apply default preset
        const hasUrlState =
            pagination.page.value > 1 ||
            sort.sortBy.value !== (options.defaultSort ?? null) ||
            search.searchTerm.value !== '' ||
            Object.keys(filter.filters.value).length > 0;

        if (!hasUrlState) {
            const defaultPreset = presets.presets.value.find((p) => p.is_default);

            if (defaultPreset) {
                presets.applyPreset(defaultPreset);

                return;
            }
        }

        serverData.refresh();
    });

    return {
        columns: options.columns,
        pagination,
        sort,
        search,
        filter,
        serverData,
        presets,
        clearAll,
    };
}
