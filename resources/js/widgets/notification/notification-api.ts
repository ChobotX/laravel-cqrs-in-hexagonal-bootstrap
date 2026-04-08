import type { NotificationEntry, NotificationListResponse } from './notification-store';

interface RawNotification {
    id: string;
    level: string;
    title: string;
    body: string;
    link_url: string | null;
    read_at: string | null;
    created_at: string;
}

interface RawListResponse {
    data: RawNotification[];
    meta: {
        current_page: number;
        per_page: number;
        total: number;
        total_pages: number;
    };
}

function mapNotification(raw: RawNotification): NotificationEntry {
    return {
        id: raw.id,
        level: raw.level as NotificationEntry['level'],
        title: raw.title,
        body: raw.body,
        linkUrl: raw.link_url,
        readAt: raw.read_at,
        createdAt: raw.created_at,
    };
}

export async function fetchNotifications(
    filter: string,
    page: number,
    perPage: number,
): Promise<NotificationListResponse> {
    const params = new URLSearchParams({
        filter,
        page: String(page),
        per_page: String(perPage),
    });

    const response = await fetch(`/internal-api/notifications?${params.toString()}`, {
        headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
        throw new Error(`Failed to fetch notifications: ${response.status}`);
    }

    const raw = (await response.json()) as RawListResponse;

    return {
        data: raw.data.map(mapNotification),
        meta: raw.meta,
    };
}

export async function fetchUnreadCount(): Promise<number> {
    const response = await fetch('/internal-api/notifications/unread-count', {
        headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
        throw new Error(`Failed to fetch unread count: ${response.status}`);
    }

    const data = (await response.json()) as { count: number };

    return data.count;
}

export async function markRead(notificationId: string): Promise<void> {
    const response = await fetch(`/internal-api/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'X-XSRF-TOKEN': getCsrfToken(),
        },
    });

    if (!response.ok) {
        throw new Error(`Failed to mark notification as read: ${response.status}`);
    }
}

export async function markAllRead(): Promise<void> {
    const response = await fetch('/internal-api/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'X-XSRF-TOKEN': getCsrfToken(),
        },
    });

    if (!response.ok) {
        throw new Error(`Failed to mark all notifications as read: ${response.status}`);
    }
}

export async function deleteNotification(notificationId: string): Promise<void> {
    const response = await fetch(`/internal-api/notifications/${notificationId}`, {
        method: 'DELETE',
        headers: {
            Accept: 'application/json',
            'X-XSRF-TOKEN': getCsrfToken(),
        },
    });

    if (!response.ok) {
        throw new Error(`Failed to delete notification: ${response.status}`);
    }
}

function getCsrfToken(): string {
    return decodeURIComponent(
        document.cookie
            .split('; ')
            .find((row) => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1] ?? '',
    );
}
