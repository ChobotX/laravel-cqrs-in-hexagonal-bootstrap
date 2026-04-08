import { type Ref, ref } from 'vue';
import type { SortItem } from './types';
import { readParam } from './useUrlState';

export interface SortReturn {
    sortBy: Ref<SortItem | null>;
    toggleSort: (key: string) => void;
    isSorted: (key: string) => boolean;
    getSortDirection: (key: string) => 'asc' | 'desc' | null;
    clearSort: () => void;
}

export function useSort(defaultSort?: SortItem): SortReturn {
    const sortKey = readParam('sort');
    const initialDirection = readParam('direction');

    const sortBy = ref<SortItem | null>(
        sortKey === '' ? (defaultSort ?? null) : { key: sortKey, order: initialDirection === 'desc' ? 'desc' : 'asc' },
    );

    function toggleSort(key: string): void {
        if (sortBy.value !== null && sortBy.value.key === key) {
            sortBy.value = {
                key,
                order: sortBy.value.order === 'asc' ? 'desc' : 'asc',
            };
        } else {
            sortBy.value = { key, order: 'asc' };
        }
    }

    function isSorted(key: string): boolean {
        return sortBy.value !== null && sortBy.value.key === key;
    }

    function getSortDirection(key: string): 'asc' | 'desc' | null {
        if (sortBy.value !== null && sortBy.value.key === key) {
            return sortBy.value.order;
        }

        return null;
    }

    function clearSort(): void {
        sortBy.value = defaultSort ?? null;
    }

    return { sortBy, toggleSort, isSorted, getSortDirection, clearSort };
}
