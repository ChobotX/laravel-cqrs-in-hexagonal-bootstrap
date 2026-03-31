import type { NotificationListResponse } from './notification-store';

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

    return (await response.json()) as NotificationListResponse;
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
