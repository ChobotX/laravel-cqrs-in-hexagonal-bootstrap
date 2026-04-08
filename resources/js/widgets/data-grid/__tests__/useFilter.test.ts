import { beforeEach, describe, expect, it, vi } from 'vitest';

vi.mock('../composables/useUrlState', () => ({
    readFilterParams: vi.fn(() => ({})),
}));

import { useFilter } from '../composables/useFilter';

beforeEach(() => {
    vi.restoreAllMocks();
});

describe('useFilter', () => {
    it('starts with empty filters', () => {
        const { filters, activeCount } = useFilter();

        expect(filters.value).toEqual({});
        expect(activeCount.value).toBe(0);
    });

    it('setFilter adds a filter', () => {
        const { filters, activeCount, setFilter } = useFilter();

        setFilter('status', 'active');

        expect(filters.value).toEqual({ status: 'active' });
        expect(activeCount.value).toBe(1);
    });

    it('setFilter overwrites existing filter', () => {
        const { filters, setFilter } = useFilter();

        setFilter('status', 'active');
        setFilter('status', 'inactive');

        expect(filters.value).toEqual({ status: 'inactive' });
    });

    it('clearFilter removes a specific filter', () => {
        const { filters, activeCount, setFilter, clearFilter } = useFilter();

        setFilter('status', 'active');
        setFilter('role', 'admin');
        clearFilter('status');

        expect(filters.value).toEqual({ role: 'admin' });
        expect(activeCount.value).toBe(1);
    });

    it('clearAll removes all filters', () => {
        const { filters, activeCount, setFilter, clearAll } = useFilter();

        setFilter('status', 'active');
        setFilter('role', 'admin');
        clearAll();

        expect(filters.value).toEqual({});
        expect(activeCount.value).toBe(0);
    });

    it('activeCount ignores empty string values', () => {
        const { activeCount, setFilter } = useFilter();

        setFilter('status', '');
        setFilter('role', 'admin');

        expect(activeCount.value).toBe(1);
    });
});
