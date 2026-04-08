import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { nextTick } from 'vue';

vi.mock('../composables/useUrlState', () => ({
    readParam: vi.fn(() => ''),
}));

import { useSearch } from '../composables/useSearch';
import { readParam } from '../composables/useUrlState';

beforeEach(() => {
    vi.useFakeTimers();
    vi.mocked(readParam).mockReturnValue('');
});

afterEach(() => {
    vi.useRealTimers();
});

describe('useSearch', () => {
    it('starts with empty search term when URL has no search param', () => {
        const { searchTerm, debouncedTerm } = useSearch();

        expect(searchTerm.value).toBe('');
        expect(debouncedTerm.value).toBe('');
    });

    it('initializes search term from URL param', () => {
        vi.mocked(readParam).mockReturnValue('hello');

        const { searchTerm, debouncedTerm } = useSearch();

        expect(searchTerm.value).toBe('hello');
        expect(debouncedTerm.value).toBe('hello');
    });

    it('debounces search term changes with default delay', async () => {
        const { searchTerm, debouncedTerm } = useSearch();

        searchTerm.value = 'test';
        await nextTick();

        // Before debounce expires, debouncedTerm should still be empty
        expect(debouncedTerm.value).toBe('');

        vi.advanceTimersByTime(299);
        expect(debouncedTerm.value).toBe('');

        vi.advanceTimersByTime(1);
        expect(debouncedTerm.value).toBe('test');
    });

    it('debounces with custom delay', async () => {
        const { searchTerm, debouncedTerm } = useSearch(500);

        searchTerm.value = 'custom';
        await nextTick();

        vi.advanceTimersByTime(499);
        expect(debouncedTerm.value).toBe('');

        vi.advanceTimersByTime(1);
        expect(debouncedTerm.value).toBe('custom');
    });

    it('resets debounce timer on rapid input', async () => {
        const { searchTerm, debouncedTerm } = useSearch();

        searchTerm.value = 'a';
        await nextTick();

        vi.advanceTimersByTime(200);
        expect(debouncedTerm.value).toBe('');

        // Change again before debounce completes — resets the timer
        searchTerm.value = 'ab';
        await nextTick();

        vi.advanceTimersByTime(200);
        expect(debouncedTerm.value).toBe('');

        vi.advanceTimersByTime(100);
        expect(debouncedTerm.value).toBe('ab');
    });

    it('clear resets both searchTerm and debouncedTerm immediately', async () => {
        const { searchTerm, debouncedTerm, clear } = useSearch();

        searchTerm.value = 'query';
        await nextTick();
        vi.advanceTimersByTime(300);

        expect(debouncedTerm.value).toBe('query');

        clear();

        expect(searchTerm.value).toBe('');
        expect(debouncedTerm.value).toBe('');
    });

    it('clear cancels pending debounce', async () => {
        const { searchTerm, debouncedTerm, clear } = useSearch();

        searchTerm.value = 'typing';
        await nextTick();

        vi.advanceTimersByTime(100);
        clear();
        vi.advanceTimersByTime(300);

        expect(searchTerm.value).toBe('');
        expect(debouncedTerm.value).toBe('');
    });

    it('setImmediate updates both refs without debounce', () => {
        const { searchTerm, debouncedTerm, setImmediate } = useSearch();

        setImmediate('instant');

        expect(searchTerm.value).toBe('instant');
        expect(debouncedTerm.value).toBe('instant');
    });

    it('setImmediate cancels pending debounce', async () => {
        const { searchTerm, debouncedTerm, setImmediate } = useSearch();

        searchTerm.value = 'slow';
        await nextTick();
        vi.advanceTimersByTime(100);

        setImmediate('fast');
        vi.advanceTimersByTime(300);

        expect(searchTerm.value).toBe('fast');
        expect(debouncedTerm.value).toBe('fast');
    });
});
