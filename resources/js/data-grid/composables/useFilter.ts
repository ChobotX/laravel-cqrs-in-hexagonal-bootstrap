import { type ComputedRef, computed, type Ref, ref } from 'vue';
import { readFilterParams } from './useUrlState';

export interface FilterReturn {
    filters: Ref<Record<string, string>>;
    activeCount: ComputedRef<number>;
    setFilter: (key: string, value: string) => void;
    clearFilter: (key: string) => void;
    clearAll: () => void;
}

export function useFilter(): FilterReturn {
    const filters = ref<Record<string, string>>(readFilterParams());

    const activeCount = computed(() => Object.values(filters.value).filter((v) => v !== '').length);

    function setFilter(key: string, value: string): void {
        filters.value = { ...filters.value, [key]: value };
    }

    function clearFilter(key: string): void {
        const next = { ...filters.value };
        delete next[key];
        filters.value = next;
    }

    function clearAll(): void {
        filters.value = {};
    }

    return { filters, activeCount, setFilter, clearFilter, clearAll };
}
