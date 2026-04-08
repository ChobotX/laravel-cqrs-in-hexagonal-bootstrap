import { type ComputedRef, computed, type Ref, ref } from 'vue';
import { readParam } from './useUrlState';

const DEFAULT_PER_PAGE = 15;

export interface PaginationReturn {
    page: Ref<number>;
    perPage: Ref<number>;
    total: Ref<number>;
    totalPages: ComputedRef<number>;
    goToPage: (newPage: number) => void;
    nextPage: () => void;
    prevPage: () => void;
    setTotal: (newTotal: number) => void;
    resetPage: () => void;
}

export function usePagination(defaultPerPage: number = DEFAULT_PER_PAGE): PaginationReturn {
    const page = ref(Math.max(1, Number(readParam('page')) || 1));
    const perPage = ref(Number(readParam('per_page')) || defaultPerPage);
    const total = ref(0);
    const totalPages = computed(() => Math.max(1, Math.ceil(total.value / perPage.value)));

    function goToPage(newPage: number): void {
        page.value = Math.max(1, Math.min(newPage, totalPages.value));
    }

    function nextPage(): void {
        if (page.value < totalPages.value) {
            page.value++;
        }
    }

    function prevPage(): void {
        if (page.value > 1) {
            page.value--;
        }
    }

    function setTotal(newTotal: number): void {
        total.value = newTotal;

        if (page.value > totalPages.value && totalPages.value > 0) {
            page.value = 1;
        }
    }

    function resetPage(): void {
        page.value = 1;
    }

    return {
        page,
        perPage,
        total,
        totalPages,
        goToPage,
        nextPage,
        prevPage,
        setTotal,
        resetPage,
    };
}
