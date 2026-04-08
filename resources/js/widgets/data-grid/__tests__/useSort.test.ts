import { beforeEach, describe, expect, it, vi } from 'vitest';
import { readParam } from '../composables/useUrlState';

vi.mock('../composables/useUrlState', () => ({
    readParam: vi.fn(() => ''),
}));

import { useSort } from '../composables/useSort';

beforeEach(() => {
    vi.restoreAllMocks();
});

describe('useSort', () => {
    it('starts with null sort when no default', () => {
        const { sortBy } = useSort();

        expect(sortBy.value).toBeNull();
    });

    it('starts with default sort when provided', () => {
        const { sortBy } = useSort({ key: 'name', order: 'asc' });

        expect(sortBy.value).toEqual({ key: 'name', order: 'asc' });
    });

    it('toggleSort sets ascending on new column', () => {
        const { sortBy, toggleSort } = useSort();

        toggleSort('name');

        expect(sortBy.value).toEqual({ key: 'name', order: 'asc' });
    });

    it('toggleSort flips direction on same column', () => {
        const { sortBy, toggleSort } = useSort();

        toggleSort('name');
        toggleSort('name');

        expect(sortBy.value).toEqual({ key: 'name', order: 'desc' });
    });

    it('toggleSort resets to asc on different column', () => {
        const { sortBy, toggleSort } = useSort();

        toggleSort('name');
        toggleSort('name');
        toggleSort('email');

        expect(sortBy.value).toEqual({ key: 'email', order: 'asc' });
    });

    it('isSorted returns true for active column', () => {
        const { isSorted, toggleSort } = useSort();

        toggleSort('name');

        expect(isSorted('name')).toBe(true);
        expect(isSorted('email')).toBe(false);
    });

    it('getSortDirection returns direction for active column', () => {
        const { getSortDirection, toggleSort } = useSort();

        toggleSort('name');

        expect(getSortDirection('name')).toBe('asc');
        expect(getSortDirection('email')).toBeNull();
    });

    it('clearSort resets to default', () => {
        const { sortBy, toggleSort, clearSort } = useSort({ key: 'name', order: 'asc' });

        toggleSort('email');
        clearSort();

        expect(sortBy.value).toEqual({ key: 'name', order: 'asc' });
    });

    it('clearSort resets to null when no default', () => {
        const { sortBy, toggleSort, clearSort } = useSort();

        toggleSort('email');
        clearSort();

        expect(sortBy.value).toBeNull();
    });

    it('initializes from URL sort params', () => {
        vi.mocked(readParam).mockImplementation((key: string) => {
            if (key === 'sort') return 'email';
            if (key === 'direction') return 'desc';

            return '';
        });

        const { sortBy } = useSort();

        expect(sortBy.value).toEqual({ key: 'email', order: 'desc' });
    });

    it('initializes from URL sort param with default asc', () => {
        vi.mocked(readParam).mockImplementation((key: string) => {
            if (key === 'sort') return 'name';

            return '';
        });

        const { sortBy } = useSort();

        expect(sortBy.value).toEqual({ key: 'name', order: 'asc' });
    });

    it('toggleSort flips from desc back to asc', () => {
        vi.mocked(readParam).mockReturnValue('');

        const { sortBy, toggleSort } = useSort({ key: 'name', order: 'desc' });

        expect(sortBy.value).toEqual({ key: 'name', order: 'desc' });
        toggleSort('name');
        expect(sortBy.value).toEqual({ key: 'name', order: 'asc' });
    });
});
