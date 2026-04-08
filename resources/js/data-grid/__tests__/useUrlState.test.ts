import { beforeEach, describe, expect, it } from 'vitest';
import { effectScope, nextTick, ref } from 'vue';
import { readFilterParams, readParam, syncToUrl, writeParams } from '../composables/useUrlState';

beforeEach(() => {
    // Reset URL to clean state
    window.history.replaceState(null, '', '/test');
});

describe('readParam', () => {
    it('returns empty string for missing param', () => {
        expect(readParam('page')).toBe('');
    });

    it('reads existing param', () => {
        window.history.replaceState(null, '', '/test?page=3');

        expect(readParam('page')).toBe('3');
    });

    it('reads search param', () => {
        window.history.replaceState(null, '', '/test?search=hello');

        expect(readParam('search')).toBe('hello');
    });
});

describe('readFilterParams', () => {
    it('returns empty object when no filters', () => {
        expect(readFilterParams()).toEqual({});
    });

    it('reads filter params', () => {
        window.history.replaceState(null, '', '/test?filter%5Bstatus%5D=active&filter%5Brole%5D=admin');

        expect(readFilterParams()).toEqual({ status: 'active', role: 'admin' });
    });

    it('ignores empty filter values', () => {
        window.history.replaceState(null, '', '/test?filter%5Bstatus%5D=');

        expect(readFilterParams()).toEqual({});
    });

    it('ignores non-filter params', () => {
        window.history.replaceState(null, '', '/test?page=1&search=test');

        expect(readFilterParams()).toEqual({});
    });
});

describe('writeParams', () => {
    it('sets params on URL', () => {
        writeParams({ page: '2', sort: 'name', direction: 'asc' });

        const url = new URL(window.location.href);

        expect(url.searchParams.get('page')).toBe('2');
        expect(url.searchParams.get('sort')).toBe('name');
        expect(url.searchParams.get('direction')).toBe('asc');
    });

    it('omits empty params', () => {
        writeParams({ page: '', sort: 'name' });

        const url = new URL(window.location.href);

        expect(url.searchParams.has('page')).toBe(false);
        expect(url.searchParams.get('sort')).toBe('name');
    });

    it('clears previous grid params', () => {
        window.history.replaceState(null, '', '/test?page=5&sort=old&search=old');

        writeParams({ page: '1' });

        const url = new URL(window.location.href);

        expect(url.searchParams.get('page')).toBe('1');
        expect(url.searchParams.has('sort')).toBe(false);
        expect(url.searchParams.has('search')).toBe(false);
    });

    it('preserves non-grid params', () => {
        window.history.replaceState(null, '', '/test?custom=keep');

        writeParams({ page: '2' });

        const url = new URL(window.location.href);

        expect(url.searchParams.get('custom')).toBe('keep');
        expect(url.searchParams.get('page')).toBe('2');
    });

    it('clears filter and preset params', () => {
        window.history.replaceState(null, '', '/test?filter%5Bstatus%5D=active&preset=abc&per_page=25');

        writeParams({});

        const url = new URL(window.location.href);

        expect(url.searchParams.has('filter[status]')).toBe(false);
        expect(url.searchParams.has('preset')).toBe(false);
        expect(url.searchParams.has('per_page')).toBe(false);
    });
});

describe('syncToUrl', () => {
    it('writes page and sort to URL when state changes', async () => {
        const scope = effectScope();

        scope.run(() => {
            const page = ref(1);
            const perPage = ref(15);
            const sort = ref<{ key: string; order: 'asc' | 'desc' } | null>({ key: 'name', order: 'asc' });
            const search = ref('');
            const filters = ref<Record<string, string>>({});

            syncToUrl(page, perPage, sort, search, filters);

            page.value = 3;
        });

        await nextTick();

        const url = new URL(window.location.href);

        expect(url.searchParams.get('page')).toBe('3');
        expect(url.searchParams.get('sort')).toBe('name');
        expect(url.searchParams.get('direction')).toBe('asc');

        scope.stop();
    });

    it('omits page 1 and default perPage from URL', async () => {
        const scope = effectScope();

        scope.run(() => {
            const page = ref(1);
            const perPage = ref(15);
            const sort = ref(null);
            const search = ref('');
            const filters = ref<Record<string, string>>({});

            syncToUrl(page, perPage, sort, search, filters);

            search.value = 'test';
        });

        await nextTick();

        const url = new URL(window.location.href);

        expect(url.searchParams.has('page')).toBe(false);
        expect(url.searchParams.has('per_page')).toBe(false);
        expect(url.searchParams.get('search')).toBe('test');

        scope.stop();
    });

    it('writes non-default perPage to URL', async () => {
        const scope = effectScope();

        scope.run(() => {
            const page = ref(1);
            const perPage = ref(25);
            const sort = ref(null);
            const search = ref('');
            const filters = ref<Record<string, string>>({});

            syncToUrl(page, perPage, sort, search, filters);

            page.value = 2;
        });

        await nextTick();

        const url = new URL(window.location.href);

        expect(url.searchParams.get('per_page')).toBe('25');

        scope.stop();
    });

    it('writes filters to URL', async () => {
        const scope = effectScope();

        scope.run(() => {
            const page = ref(1);
            const perPage = ref(15);
            const sort = ref(null);
            const search = ref('');
            const filters = ref<Record<string, string>>({});

            syncToUrl(page, perPage, sort, search, filters);

            filters.value = { status: 'active', role: 'admin' };
        });

        await nextTick();

        const url = new URL(window.location.href);

        expect(url.searchParams.get('filter[status]')).toBe('active');
        expect(url.searchParams.get('filter[role]')).toBe('admin');

        scope.stop();
    });

    it('omits empty filter values', async () => {
        const scope = effectScope();

        scope.run(() => {
            const page = ref(1);
            const perPage = ref(15);
            const sort = ref(null);
            const search = ref('');
            const filters = ref<Record<string, string>>({ status: '' });

            syncToUrl(page, perPage, sort, search, filters);

            page.value = 2;
        });

        await nextTick();

        const url = new URL(window.location.href);

        expect(url.searchParams.has('filter[status]')).toBe(false);

        scope.stop();
    });
});
