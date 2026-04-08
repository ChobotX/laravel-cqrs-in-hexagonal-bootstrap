import { afterEach, describe, expect, it } from 'vitest';
import {
    markAllAsRead,
    markAsRead,
    type NotificationEntry,
    NotificationLevel,
    notifications,
    prependNotification,
    removeNotification,
    resetStore,
    setNotifications,
    setUnreadCount,
    unreadCount,
} from './notification-store';

function createEntry(overrides: Partial<NotificationEntry> = {}): NotificationEntry {
    return {
        id: 'test-id-1',
        level: NotificationLevel.Info,
        title: 'Test Notification',
        body: 'Test body',
        linkUrl: null,
        readAt: null,
        createdAt: '2026-01-15T10:00:00Z',
        ...overrides,
    };
}

afterEach(() => {
    resetStore();
});

describe('setNotifications', () => {
    it('replaces all notifications', () => {
        const items = [createEntry({ id: 'a' }), createEntry({ id: 'b' })];
        setNotifications(items);

        expect(notifications).toHaveLength(2);
        expect(notifications[0].id).toBe('a');
        expect(notifications[1].id).toBe('b');
    });

    it('clears previous notifications', () => {
        setNotifications([createEntry({ id: 'a' })]);
        setNotifications([createEntry({ id: 'b' })]);

        expect(notifications).toHaveLength(1);
        expect(notifications[0].id).toBe('b');
    });
});

describe('prependNotification', () => {
    it('adds notification to the beginning', () => {
        setNotifications([createEntry({ id: 'a' })]);
        prependNotification(createEntry({ id: 'b' }));

        expect(notifications[0].id).toBe('b');
        expect(notifications[1].id).toBe('a');
    });

    it('caps at MAX_BELL_NOTIFICATIONS (10)', () => {
        const existing = Array.from({ length: 10 }, (_, i) => createEntry({ id: `item-${i}` }));
        setNotifications(existing);

        prependNotification(createEntry({ id: 'new' }));

        expect(notifications).toHaveLength(10);
        expect(notifications[0].id).toBe('new');
        expect(notifications[9].id).toBe('item-8');
    });
});

describe('setUnreadCount', () => {
    it('sets the unread count', () => {
        setUnreadCount(5);
        expect(unreadCount.value).toBe(5);
    });

    it('can set to zero', () => {
        setUnreadCount(3);
        setUnreadCount(0);
        expect(unreadCount.value).toBe(0);
    });
});

describe('markAsRead', () => {
    it('marks an unread notification as read', () => {
        setNotifications([createEntry({ id: 'a', readAt: null })]);
        setUnreadCount(1);

        markAsRead('a');

        expect(notifications[0].readAt).not.toBeNull();
        expect(unreadCount.value).toBe(0);
    });

    it('does nothing for already read notification', () => {
        setNotifications([createEntry({ id: 'a', readAt: '2026-01-15T11:00:00Z' })]);
        setUnreadCount(1);

        markAsRead('a');

        expect(unreadCount.value).toBe(1);
    });

    it('does nothing for non-existent notification', () => {
        setNotifications([createEntry({ id: 'a' })]);
        setUnreadCount(1);

        markAsRead('nonexistent');

        expect(unreadCount.value).toBe(1);
    });

    it('does not go below zero', () => {
        setNotifications([createEntry({ id: 'a', readAt: null })]);
        setUnreadCount(0);

        markAsRead('a');

        expect(unreadCount.value).toBe(0);
    });
});

describe('markAllAsRead', () => {
    it('marks all unread notifications as read and sets count to 0', () => {
        setNotifications([
            createEntry({ id: 'a', readAt: null }),
            createEntry({ id: 'b', readAt: null }),
            createEntry({ id: 'c', readAt: '2026-01-15T11:00:00Z' }),
        ]);
        setUnreadCount(2);

        markAllAsRead();

        expect(notifications[0].readAt).not.toBeNull();
        expect(notifications[1].readAt).not.toBeNull();
        expect(notifications[2].readAt).not.toBeNull();
        expect(unreadCount.value).toBe(0);
    });
});

describe('removeNotification', () => {
    it('removes notification by id', () => {
        setNotifications([createEntry({ id: 'a' }), createEntry({ id: 'b' })]);

        removeNotification('a');

        expect(notifications).toHaveLength(1);
        expect(notifications[0].id).toBe('b');
    });

    it('decrements unread count when removing unread notification', () => {
        setNotifications([createEntry({ id: 'a', readAt: null })]);
        setUnreadCount(1);

        removeNotification('a');

        expect(unreadCount.value).toBe(0);
    });

    it('does not decrement unread count when removing read notification', () => {
        setNotifications([createEntry({ id: 'a', readAt: '2026-01-15T11:00:00Z' })]);
        setUnreadCount(1);

        removeNotification('a');

        expect(unreadCount.value).toBe(1);
    });

    it('does nothing for non-existent notification', () => {
        setNotifications([createEntry({ id: 'a' })]);

        removeNotification('nonexistent');

        expect(notifications).toHaveLength(1);
    });

    it('does not go below zero on unread count', () => {
        setNotifications([createEntry({ id: 'a', readAt: null })]);
        setUnreadCount(0);

        removeNotification('a');

        expect(unreadCount.value).toBe(0);
    });
});

describe('resetStore', () => {
    it('clears notifications and unread count', () => {
        setNotifications([createEntry({ id: 'a' })]);
        setUnreadCount(5);

        resetStore();

        expect(notifications).toHaveLength(0);
        expect(unreadCount.value).toBe(0);
    });
});

describe('NotificationLevel enum', () => {
    it('has expected values', () => {
        expect(NotificationLevel.Info).toBe('info');
        expect(NotificationLevel.Success).toBe('success');
        expect(NotificationLevel.Warning).toBe('warning');
        expect(NotificationLevel.Error).toBe('error');
    });
});
