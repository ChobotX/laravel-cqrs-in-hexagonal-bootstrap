import { beforeEach, describe, expect, it, vi } from 'vitest';

// Mock useUrlState before importing
vi.mock('../composables/useUrlState', () => ({
    readParam: vi.fn(() => ''),
}));

import { usePagination } from '../composables/usePagination';

beforeEach(() => {
    vi.restoreAllMocks();
});

describe('usePagination', () => {
    it('starts at page 1 with default per page', () => {
        const { page, perPage, total, totalPages } = usePagination();

        expect(page.value).toBe(1);
        expect(perPage.value).toBe(15);
        expect(total.value).toBe(0);
        expect(totalPages.value).toBe(1);
    });

    it('accepts custom default per page', () => {
        const { perPage } = usePagination(25);

        expect(perPage.value).toBe(25);
    });

    it('calculates total pages from total and per page', () => {
        const { total, totalPages, setTotal, perPage } = usePagination();
        perPage.value = 10;
        setTotal(45);

        expect(total.value).toBe(45);
        expect(totalPages.value).toBe(5);
    });

    it('goToPage navigates to valid page', () => {
        const { page, setTotal, goToPage } = usePagination();
        setTotal(50);

        goToPage(3);

        expect(page.value).toBe(3);
    });

    it('goToPage clamps to minimum 1', () => {
        const { page, goToPage } = usePagination();

        goToPage(0);

        expect(page.value).toBe(1);
    });

    it('goToPage clamps to max total pages', () => {
        const { page, setTotal, goToPage } = usePagination();
        setTotal(30);

        goToPage(100);

        expect(page.value).toBe(2);
    });

    it('nextPage increments page', () => {
        const { page, setTotal, nextPage } = usePagination();
        setTotal(30);

        nextPage();

        expect(page.value).toBe(2);
    });

    it('nextPage does not exceed total pages', () => {
        const { page, setTotal, nextPage } = usePagination();
        setTotal(10);

        nextPage();

        expect(page.value).toBe(1);
    });

    it('prevPage decrements page', () => {
        const { page, setTotal, prevPage } = usePagination();
        setTotal(30);
        page.value = 2;

        prevPage();

        expect(page.value).toBe(1);
    });

    it('prevPage does not go below 1', () => {
        const { page, prevPage } = usePagination();

        prevPage();

        expect(page.value).toBe(1);
    });

    it('setTotal resets page when current page exceeds new total pages', () => {
        const { page, setTotal } = usePagination();
        page.value = 5;

        setTotal(10);

        expect(page.value).toBe(1);
    });

    it('resetPage sets page to 1', () => {
        const { page, setTotal, resetPage } = usePagination();
        setTotal(50);
        page.value = 3;

        resetPage();

        expect(page.value).toBe(1);
    });
});
