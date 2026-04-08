import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import type { FetchParams } from '../composables/types';
import { deleteGridAction, fetchGridData, postGridAction } from '../data-grid-api';

beforeEach(() => {
    // Set XSRF-TOKEN cookie
    Object.defineProperty(document, 'cookie', {
        writable: true,
        value: 'XSRF-TOKEN=test-csrf-token',
    });
});

afterEach(() => {
    vi.restoreAllMocks();
});

function mockFetch(body: unknown, status = 200, ok = true) {
    const fn = vi.fn().mockResolvedValue({
        ok,
        status,
        json: vi.fn().mockResolvedValue(body),
    });
    vi.stubGlobal('fetch', fn);

    return fn;
}

describe('fetchGridData', () => {
    it('builds query string with all params and returns data', async () => {
        const responseBody = {
            data: [{ id: 1, name: 'Alice' }],
            meta: { current_page: 1, per_page: 15, total: 1, total_pages: 1 },
        };
        const fetchMock = mockFetch(responseBody);

        const params: FetchParams = {
            page: 2,
            perPage: 25,
            sort: { key: 'name', order: 'desc' },
            filters: { status: 'active', role: 'admin' },
            search: 'test',
        };

        const result = await fetchGridData('/api/users', params);

        expect(result).toEqual(responseBody);
        expect(fetchMock).toHaveBeenCalledOnce();

        const calledUrl = fetchMock.mock.calls[0][0] as string;
        const url = new URL(calledUrl, 'http://localhost');

        expect(url.pathname).toBe('/api/users');
        expect(url.searchParams.get('page')).toBe('2');
        expect(url.searchParams.get('per_page')).toBe('25');
        expect(url.searchParams.get('sort')).toBe('name');
        expect(url.searchParams.get('direction')).toBe('desc');
        expect(url.searchParams.get('search')).toBe('test');
        expect(url.searchParams.get('filter[status]')).toBe('active');
        expect(url.searchParams.get('filter[role]')).toBe('admin');

        const headers = fetchMock.mock.calls[0][1].headers;

        expect(headers.Accept).toBe('application/json');
        expect(headers['X-Requested-With']).toBe('XMLHttpRequest');
        expect(headers['X-XSRF-TOKEN']).toBe('test-csrf-token');
    });

    it('omits sort params when sort is null', async () => {
        const fetchMock = mockFetch({ data: [], meta: { current_page: 1, per_page: 15, total: 0, total_pages: 1 } });

        const params: FetchParams = {
            page: 1,
            perPage: 15,
            sort: null,
            filters: {},
            search: '',
        };

        await fetchGridData('/api/items', params);

        const calledUrl = fetchMock.mock.calls[0][0] as string;
        const url = new URL(calledUrl, 'http://localhost');

        expect(url.searchParams.has('sort')).toBe(false);
        expect(url.searchParams.has('direction')).toBe(false);
    });

    it('omits search param when empty', async () => {
        const fetchMock = mockFetch({ data: [], meta: { current_page: 1, per_page: 15, total: 0, total_pages: 1 } });

        const params: FetchParams = {
            page: 1,
            perPage: 15,
            sort: null,
            filters: {},
            search: '',
        };

        await fetchGridData('/api/items', params);

        const calledUrl = fetchMock.mock.calls[0][0] as string;
        const url = new URL(calledUrl, 'http://localhost');

        expect(url.searchParams.has('search')).toBe(false);
    });

    it('omits filter entries with empty values', async () => {
        const fetchMock = mockFetch({ data: [], meta: { current_page: 1, per_page: 15, total: 0, total_pages: 1 } });

        const params: FetchParams = {
            page: 1,
            perPage: 15,
            sort: null,
            filters: { status: 'active', role: '' },
            search: '',
        };

        await fetchGridData('/api/items', params);

        const calledUrl = fetchMock.mock.calls[0][0] as string;
        const url = new URL(calledUrl, 'http://localhost');

        expect(url.searchParams.get('filter[status]')).toBe('active');
        expect(url.searchParams.has('filter[role]')).toBe(false);
    });

    it('throws on non-ok response', async () => {
        mockFetch(null, 500, false);

        const params: FetchParams = {
            page: 1,
            perPage: 15,
            sort: null,
            filters: {},
            search: '',
        };

        await expect(fetchGridData('/api/items', params)).rejects.toThrow('Grid fetch failed: 500');
    });

    it('decodes URL-encoded CSRF token', async () => {
        Object.defineProperty(document, 'cookie', {
            writable: true,
            value: 'XSRF-TOKEN=abc%3Ddef',
        });

        const fetchMock = mockFetch({ data: [], meta: { current_page: 1, per_page: 15, total: 0, total_pages: 1 } });

        const params: FetchParams = {
            page: 1,
            perPage: 15,
            sort: null,
            filters: {},
            search: '',
        };

        await fetchGridData('/api/items', params);

        expect(fetchMock.mock.calls[0][1].headers['X-XSRF-TOKEN']).toBe('abc=def');
    });

    it('uses empty string when CSRF cookie is missing', async () => {
        Object.defineProperty(document, 'cookie', {
            writable: true,
            value: 'other=value',
        });

        const fetchMock = mockFetch({ data: [], meta: { current_page: 1, per_page: 15, total: 0, total_pages: 1 } });

        const params: FetchParams = {
            page: 1,
            perPage: 15,
            sort: null,
            filters: {},
            search: '',
        };

        await fetchGridData('/api/items', params);

        expect(fetchMock.mock.calls[0][1].headers['X-XSRF-TOKEN']).toBe('');
    });
});

describe('postGridAction', () => {
    it('sends POST request with CSRF headers', async () => {
        const fetchMock = mockFetch(null, 200, true);

        await postGridAction('/grid-presets/123/default');

        expect(fetchMock).toHaveBeenCalledOnce();
        expect(fetchMock.mock.calls[0][0]).toBe('/grid-presets/123/default');
        expect(fetchMock.mock.calls[0][1].method).toBe('POST');
        expect(fetchMock.mock.calls[0][1].headers['X-XSRF-TOKEN']).toBe('test-csrf-token');
    });

    it('throws on non-ok response', async () => {
        mockFetch(null, 403, false);

        await expect(postGridAction('/grid-presets/123/default')).rejects.toThrow('Grid action failed: 403');
    });

    it('sends JSON body when body parameter is provided', async () => {
        const fetchMock = mockFetch(null, 201, true);

        await postGridAction('/grid-presets', { grid_name: 'users', name: 'My Preset' });

        expect(fetchMock).toHaveBeenCalledOnce();
        expect(fetchMock.mock.calls[0][1].headers['Content-Type']).toBe('application/json');
        expect(JSON.parse(fetchMock.mock.calls[0][1].body as string)).toEqual({
            grid_name: 'users',
            name: 'My Preset',
        });
    });
});

describe('deleteGridAction', () => {
    it('sends DELETE request with CSRF headers', async () => {
        const fetchMock = mockFetch(null, 200, true);

        await deleteGridAction('/grid-presets/abc');

        expect(fetchMock).toHaveBeenCalledOnce();
        expect(fetchMock.mock.calls[0][0]).toBe('/grid-presets/abc');
        expect(fetchMock.mock.calls[0][1].method).toBe('DELETE');
        expect(fetchMock.mock.calls[0][1].headers['X-XSRF-TOKEN']).toBe('test-csrf-token');
    });

    it('throws on non-ok response', async () => {
        mockFetch(null, 404, false);

        await expect(deleteGridAction('/grid-presets/abc')).rejects.toThrow('Grid delete failed: 404');
    });
});
