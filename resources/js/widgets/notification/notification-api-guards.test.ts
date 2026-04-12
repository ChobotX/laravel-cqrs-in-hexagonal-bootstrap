import { describe, expect, it } from 'vitest';
import { parseNotificationListResponse, parseUnreadCountJson } from './notification-api-guards';
import { NotificationLevel } from './notification-store';

const meta = { current_page: 1, per_page: 15, total: 1, total_pages: 1 };

function rawNotification(overrides: Partial<Record<string, unknown>> = {}): Record<string, unknown> {
    return {
        id: '1',
        level: 'info',
        title: 'T',
        body: 'B',
        link_url: null,
        read_at: null,
        created_at: '2026-01-01T00:00:00Z',
        ...overrides,
    };
}

describe('parseNotificationListResponse', () => {
    it('throws when envelope is invalid', () => {
        expect(() => parseNotificationListResponse(null)).toThrow('Invalid notifications list JSON');
        expect(() => parseNotificationListResponse({ data: [], meta: {} })).toThrow('Invalid notifications list JSON');
        expect(() => parseNotificationListResponse({ data: [], meta: 1 })).toThrow('Invalid notifications list JSON');
    });

    it('drops rows that are not raw notification objects', () => {
        const res = parseNotificationListResponse({
            data: [null, rawNotification(), { id: 'x' }],
            meta,
        });
        expect(res.data).toHaveLength(1);
        expect(res.data[0]?.id).toBe('1');
    });

    it('maps unknown level to Info', () => {
        const res = parseNotificationListResponse({
            data: [rawNotification({ level: 'unknown-level' })],
            meta,
        });
        expect(res.data[0]?.level).toBe(NotificationLevel.Info);
    });

    it('maps known levels', () => {
        const res = parseNotificationListResponse({
            data: [rawNotification({ level: 'warning' })],
            meta,
        });
        expect(res.data[0]?.level).toBe(NotificationLevel.Warning);
    });
});

describe('parseUnreadCountJson', () => {
    it('throws when shape is invalid', () => {
        expect(() => parseUnreadCountJson(null)).toThrow('Invalid unread count JSON');
        expect(() => parseUnreadCountJson({ count: '1' })).toThrow('Invalid unread count JSON');
    });

    it('returns numeric count', () => {
        expect(parseUnreadCountJson({ count: 5 })).toBe(5);
    });
});
