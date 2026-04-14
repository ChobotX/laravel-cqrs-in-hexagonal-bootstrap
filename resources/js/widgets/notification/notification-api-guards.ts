import { isRecord } from '../../shared/type-guards/is-record';
import { type NotificationEntry, NotificationLevel, type NotificationListResponse } from './notification-store';

interface RawNotification {
    id: string;
    level: string;
    title: string;
    body: string;
    link_url: string | null;
    read_at: string | null;
    created_at: string;
}

function parseNotificationLevel(level: string): NotificationLevel {
    for (const candidate of Object.values(NotificationLevel)) {
        if (candidate === level) {
            return candidate;
        }
    }
    return NotificationLevel.Info;
}

function isRawNotification(value: unknown): value is RawNotification {
    if (!isRecord(value)) {
        return false;
    }
    return (
        typeof value.id === 'string' &&
        typeof value.level === 'string' &&
        typeof value.title === 'string' &&
        typeof value.body === 'string' &&
        (value.link_url === null || typeof value.link_url === 'string') &&
        (value.read_at === null || typeof value.read_at === 'string') &&
        typeof value.created_at === 'string'
    );
}

function isListMeta(value: unknown): value is NotificationListResponse['meta'] {
    if (!isRecord(value)) {
        return false;
    }
    return (
        typeof value.current_page === 'number' &&
        typeof value.per_page === 'number' &&
        typeof value.total === 'number' &&
        typeof value.total_pages === 'number'
    );
}

export function parseNotificationListResponse(value: unknown): NotificationListResponse {
    if (!isRecord(value) || !Array.isArray(value.data) || !isListMeta(value.meta)) {
        throw new Error('Invalid notifications list JSON');
    }
    const rows = value.data.filter(isRawNotification);
    return {
        data: rows.map(
            (raw): NotificationEntry => ({
                id: raw.id,
                level: parseNotificationLevel(raw.level),
                title: raw.title,
                body: raw.body,
                linkUrl: raw.link_url,
                readAt: raw.read_at,
                createdAt: raw.created_at,
            }),
        ),
        meta: value.meta,
    };
}

export function parseUnreadCountJson(value: unknown): number {
    if (!isRecord(value) || typeof value.count !== 'number') {
        throw new Error('Invalid unread count JSON');
    }
    return value.count;
}
