import { type Ref, ref, watch } from 'vue';
import { readParam } from './useUrlState';

const DEFAULT_DEBOUNCE_MS = 300;

export interface SearchReturn {
    searchTerm: Ref<string>;
    debouncedTerm: Ref<string>;
    clear: () => void;
    setImmediate: (value: string) => void;
}

export function useSearch(debounceMs: number = DEFAULT_DEBOUNCE_MS): SearchReturn {
    const searchTerm = ref(readParam('search'));
    const debouncedTerm = ref(searchTerm.value);
    let timeout: ReturnType<typeof setTimeout> | undefined;

    watch(searchTerm, (newValue: string) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            debouncedTerm.value = newValue;
        }, debounceMs);
    });

    function clear(): void {
        clearTimeout(timeout);
        searchTerm.value = '';
        debouncedTerm.value = '';
    }

    function setImmediate(value: string): void {
        clearTimeout(timeout);
        searchTerm.value = value;
        debouncedTerm.value = value;
    }

    return { searchTerm, debouncedTerm, clear, setImmediate };
}
