import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { mockedFetchFirstUrl } from '../../test-utils/vitest-fetch';
import { deleteNotification, fetchNotifications, fetchUnreadCount, markAllRead, markRead } from './notification-api';

const HTTP_OK = 200;
const HTTP_ERROR = 500;

function mockFetch(response: unknown, ok = true): void {
    global.fetch = vi.fn().mockResolvedValue({
        ok,
        status: ok ? HTTP_OK : HTTP_ERROR,
        json: () => Promise.resolve(response),
    });
}

function setCsrfCookie(token: string): void {
    Object.defineProperty(document, 'cookie', {
        writable: true,
        value: `XSRF-TOKEN=${encodeURIComponent(token)}`,
    });
}

beforeEach(() => {
    setCsrfCookie('test-csrf-token');
});

afterEach(() => {
    vi.restoreAllMocks();
    Object.defineProperty(document, 'cookie', {
        writable: true,
        value: '',
    });
});

describe('fetchNotifications', () => {
    it('fetches and maps snake_case to camelCase', async () => {
        const serverResponse = {
            data: [
                {
                    id: '1',
                    level: 'info',
                    title: 'Test',
                    body: 'Body',
                    link_url: null,
                    read_at: null,
                    created_at: '2026-01-15T10:00:00Z',
                },
            ],
            meta: { current_page: 1, per_page: 15, total: 1, total_pages: 1 },
        };
        mockFetch(serverResponse);

        const result = await fetchNotifications('all', 1, 15);

        expect(global.fetch).toHaveBeenCalledWith(
            expect.stringContaining('/internal-api/notifications?'),
            expect.objectContaining({ headers: { Accept: 'application/json' } }),
        );
        expect(result.data[0]).toEqual({
            id: '1',
            level: 'info',
            title: 'Test',
            body: 'Body',
            linkUrl: null,
            readAt: null,
            createdAt: '2026-01-15T10:00:00Z',
        });
        expect(result.meta).toEqual(serverResponse.meta);
    });

    it('passes filter, page, and per_page params', async () => {
        mockFetch({ data: [], meta: { current_page: 2, per_page: 5, total: 0, total_pages: 0 } });

        await fetchNotifications('unread', 2, 5);

        const callUrl = mockedFetchFirstUrl(global.fetch);
        expect(callUrl).toContain('filter=unread');
        expect(callUrl).toContain('page=2');
        expect(callUrl).toContain('per_page=5');
    });

    it('maps link_url and read_at correctly', async () => {
        mockFetch({
            data: [
                {
                    id: '2',
                    level: 'warning',
                    title: 'T',
                    body: 'B',
                    link_url: '/dashboard',
                    read_at: '2026-01-15T12:00:00Z',
                    created_at: '2026-01-15T10:00:00Z',
                },
            ],
            meta: { current_page: 1, per_page: 15, total: 1, total_pages: 1 },
        });

        const result = await fetchNotifications('all', 1, 15);

        expect(result.data[0].linkUrl).toBe('/dashboard');
        expect(result.data[0].readAt).toBe('2026-01-15T12:00:00Z');
    });
});

describe('fetchUnreadCount', () => {
    it('returns the count', async () => {
        mockFetch({ count: 3 });

        const result = await fetchUnreadCount();

        expect(result).toBe(3);
        expect(global.fetch).toHaveBeenCalledWith(
            '/internal-api/notifications/unread-count',
            expect.objectContaining({ headers: { Accept: 'application/json' } }),
        );
    });
});

describe('markRead', () => {
    it('sends POST with CSRF token', async () => {
        mockFetch({});

        await markRead('abc-123');

        expect(global.fetch).toHaveBeenCalledWith(
            '/internal-api/notifications/abc-123/mark-read',
            expect.objectContaining({
                method: 'POST',
                headers: expect.objectContaining({
                    'X-XSRF-TOKEN': 'test-csrf-token',
                }),
            }),
        );
    });
});

describe('markAllRead', () => {
    it('sends POST with CSRF token', async () => {
        mockFetch({});

        await markAllRead();

        expect(global.fetch).toHaveBeenCalledWith(
            '/internal-api/notifications/mark-all-read',
            expect.objectContaining({
                method: 'POST',
                headers: expect.objectContaining({
                    'X-XSRF-TOKEN': 'test-csrf-token',
                }),
            }),
        );
    });
});

describe('deleteNotification', () => {
    it('sends DELETE with CSRF token', async () => {
        mockFetch({});

        await deleteNotification('abc-123');

        expect(global.fetch).toHaveBeenCalledWith(
            '/internal-api/notifications/abc-123',
            expect.objectContaining({
                method: 'DELETE',
                headers: expect.objectContaining({
                    'X-XSRF-TOKEN': 'test-csrf-token',
                }),
            }),
        );
    });
});

describe('error handling', () => {
    it('throws on non-ok response for fetchNotifications', async () => {
        mockFetch({}, false);

        await expect(fetchNotifications('all', 1, 15)).rejects.toThrow('Failed to fetch notifications: 500');
    });

    it('throws on non-ok response for fetchUnreadCount', async () => {
        mockFetch({}, false);

        await expect(fetchUnreadCount()).rejects.toThrow('Failed to fetch unread count: 500');
    });

    it('throws on non-ok response for markRead', async () => {
        mockFetch({}, false);

        await expect(markRead('abc')).rejects.toThrow('Failed to mark notification as read: 500');
    });

    it('throws on non-ok response for markAllRead', async () => {
        mockFetch({}, false);

        await expect(markAllRead()).rejects.toThrow('Failed to mark all notifications as read: 500');
    });

    it('throws on non-ok response for deleteNotification', async () => {
        mockFetch({}, false);

        await expect(deleteNotification('abc')).rejects.toThrow('Failed to delete notification: 500');
    });
});

describe('CSRF token extraction', () => {
    it('returns empty string when no cookie', async () => {
        Object.defineProperty(document, 'cookie', { writable: true, value: '' });
        mockFetch({});

        await markRead('test');

        expect(global.fetch).toHaveBeenCalledWith(
            expect.any(String),
            expect.objectContaining({
                headers: expect.objectContaining({ 'X-XSRF-TOKEN': '' }),
            }),
        );
    });

    it('decodes URI-encoded token', async () => {
        Object.defineProperty(document, 'cookie', {
            writable: true,
            value: 'XSRF-TOKEN=token%20with%20spaces',
        });
        mockFetch({});

        await markRead('test');

        expect(global.fetch).toHaveBeenCalledWith(
            expect.any(String),
            expect.objectContaining({
                headers: expect.objectContaining({ 'X-XSRF-TOKEN': 'token with spaces' }),
            }),
        );
    });
});
