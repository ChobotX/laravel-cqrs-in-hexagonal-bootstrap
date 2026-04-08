import { type Ref, reactive, ref } from 'vue';

export enum NotificationLevel {
    Info = 'info',
    Success = 'success',
    Warning = 'warning',
    Error = 'error',
}

export interface NotificationEntry {
    id: string;
    level: NotificationLevel;
    title: string;
    body: string;
    linkUrl: string | null;
    readAt: string | null;
    createdAt: string;
}

export interface NotificationPreference {
    level: NotificationLevel;
    inApp: boolean;
    email: boolean;
}

export interface NotificationListResponse {
    data: NotificationEntry[];
    meta: {
        current_page: number;
        per_page: number;
        total: number;
        total_pages: number;
    };
}

const MAX_BELL_NOTIFICATIONS = 10;

export const notifications: NotificationEntry[] = reactive([]);
export const unreadCount: Ref<number> = ref(0);

export function setNotifications(items: NotificationEntry[]): void {
    notifications.length = 0;
    notifications.push(...items);
}

export function prependNotification(item: NotificationEntry): void {
    notifications.unshift(item);

    if (notifications.length > MAX_BELL_NOTIFICATIONS) {
        notifications.pop();
    }
}

export function setUnreadCount(count: number): void {
    unreadCount.value = count;
}

export function markAsRead(id: string): void {
    const notification = notifications.find((n) => n.id === id);

    if (notification && notification.readAt === null) {
        notification.readAt = new Date().toISOString();
        unreadCount.value = Math.max(0, unreadCount.value - 1);
    }
}

export function markAllAsRead(): void {
    const now = new Date().toISOString();

    for (const notification of notifications) {
        if (notification.readAt === null) {
            notification.readAt = now;
        }
    }

    unreadCount.value = 0;
}

export function removeNotification(id: string): void {
    const index = notifications.findIndex((n) => n.id === id);

    if (index !== -1) {
        const wasUnread = notifications[index].readAt === null;
        notifications.splice(index, 1);

        if (wasUnread) {
            unreadCount.value = Math.max(0, unreadCount.value - 1);
        }
    }
}

export function resetStore(): void {
    notifications.length = 0;
    unreadCount.value = 0;
}
