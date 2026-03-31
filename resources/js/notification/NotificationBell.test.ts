import { flushPromises, mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import NotificationBell from './NotificationBell.vue';
import { type NotificationEntry, NotificationLevel, resetStore, setNotifications } from './notification-store';

vi.mock('laravel-vue-i18n', () => ({
    trans: (key: string): string => key,
}));

vi.mock('../toast/toast-queue', () => ({
    error: vi.fn(),
}));

vi.mock('../logger/logger', () => ({
    error: vi.fn(),
}));

vi.mock('./notification-api', () => ({
    fetchNotifications: vi.fn().mockResolvedValue({
        data: [
            {
                id: 'n1',
                level: 'info',
                title: 'First',
                body: 'Body 1',
                linkUrl: null,
                readAt: null,
                createdAt: '2026-01-15T10:00:00Z',
            },
        ],
        meta: { current_page: 1, per_page: 10, total: 1, total_pages: 1 },
    }),
    fetchUnreadCount: vi.fn().mockResolvedValue(3),
    markRead: vi.fn().mockResolvedValue(undefined),
    markAllRead: vi.fn().mockResolvedValue(undefined),
    deleteNotification: vi.fn().mockResolvedValue(undefined),
}));

function mountBell(): ReturnType<typeof mount> {
    return mount(NotificationBell, {
        attachTo: document.body,
    });
}

describe('NotificationBell', () => {
    beforeEach(() => {
        resetStore();
    });

    afterEach(() => {
        resetStore();
        vi.restoreAllMocks();
    });

    it('renders bell button', () => {
        const wrapper = mountBell();

        expect(wrapper.find('[data-testid="notification-bell-button"]').exists()).toBe(true);
    });

    it('loads unread count on mount', async () => {
        mountBell();
        await flushPromises();

        const { fetchUnreadCount } = await import('./notification-api');
        expect(fetchUnreadCount).toHaveBeenCalled();
    });

    it('shows badge when unread count > 0', async () => {
        const wrapper = mountBell();
        await flushPromises();

        const badge = wrapper.find('[data-testid="notification-badge"]');
        expect(badge.exists()).toBe(true);
        expect(badge.text()).toBe('3');
    });

    it('hides badge when unread count is 0', async () => {
        const { fetchUnreadCount } = await import('./notification-api');
        (fetchUnreadCount as ReturnType<typeof vi.fn>).mockResolvedValueOnce(0);

        const wrapper = mountBell();
        await flushPromises();

        expect(wrapper.find('[data-testid="notification-badge"]').exists()).toBe(false);
    });

    it('shows 99+ when unread count exceeds 99', async () => {
        const { fetchUnreadCount } = await import('./notification-api');
        (fetchUnreadCount as ReturnType<typeof vi.fn>).mockResolvedValueOnce(150);

        const wrapper = mountBell();
        await flushPromises();

        const badge = wrapper.find('[data-testid="notification-badge"]');
        expect(badge.text()).toBe('99+');
    });

    it('toggles dropdown on button click', async () => {
        const wrapper = mountBell();

        expect(wrapper.find('[data-testid="notification-dropdown"]').exists()).toBe(false);

        await wrapper.find('[data-testid="notification-bell-button"]').trigger('click');
        await flushPromises();

        expect(wrapper.find('[data-testid="notification-dropdown"]').exists()).toBe(true);
    });

    it('fetches notifications when dropdown opens', async () => {
        const wrapper = mountBell();
        const { fetchNotifications } = await import('./notification-api');

        await wrapper.find('[data-testid="notification-bell-button"]').trigger('click');
        await flushPromises();

        expect(fetchNotifications).toHaveBeenCalledWith('all', 1, 10);
    });

    it('closes dropdown on second click', async () => {
        const wrapper = mountBell();
        const button = wrapper.find('[data-testid="notification-bell-button"]');

        await button.trigger('click');
        await flushPromises();
        expect(wrapper.find('[data-testid="notification-dropdown"]').exists()).toBe(true);

        await button.trigger('click');
        expect(wrapper.find('[data-testid="notification-dropdown"]').exists()).toBe(false);
    });

    it('closes dropdown on Escape key', async () => {
        const wrapper = mountBell();

        await wrapper.find('[data-testid="notification-bell-button"]').trigger('click');
        await flushPromises();
        expect(wrapper.find('[data-testid="notification-dropdown"]').exists()).toBe(true);

        document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape' }));
        await flushPromises();

        expect(wrapper.find('[data-testid="notification-dropdown"]').exists()).toBe(false);
    });

    it('shows empty state when no notifications', async () => {
        const { fetchNotifications } = await import('./notification-api');
        (fetchNotifications as ReturnType<typeof vi.fn>).mockResolvedValueOnce({
            data: [],
            meta: { current_page: 1, per_page: 10, total: 0, total_pages: 0 },
        });

        const wrapper = mountBell();
        await wrapper.find('[data-testid="notification-bell-button"]').trigger('click');
        await flushPromises();

        expect(wrapper.find('[data-testid="notification-empty"]').exists()).toBe(true);
    });

    it('renders notifications in dropdown', async () => {
        const entries: NotificationEntry[] = [
            {
                id: 'n1',
                level: NotificationLevel.Info,
                title: 'Notification One',
                body: 'Body',
                linkUrl: null,
                readAt: null,
                createdAt: '2026-01-15T10:00:00Z',
            },
        ];
        setNotifications(entries);

        const wrapper = mountBell();
        await wrapper.find('[data-testid="notification-bell-button"]').trigger('click');
        await flushPromises();

        expect(wrapper.find('[data-testid="notification-n1"]').exists()).toBe(true);
    });

    it('shows mark all read button when unread > 0', async () => {
        const wrapper = mountBell();
        await flushPromises();

        await wrapper.find('[data-testid="notification-bell-button"]').trigger('click');
        await flushPromises();

        expect(wrapper.find('[data-testid="mark-all-read-button"]').exists()).toBe(true);
    });

    it('hides mark all read button when unread is 0', async () => {
        const { fetchUnreadCount } = await import('./notification-api');
        (fetchUnreadCount as ReturnType<typeof vi.fn>).mockResolvedValueOnce(0);

        const wrapper = mountBell();
        await flushPromises();

        await wrapper.find('[data-testid="notification-bell-button"]').trigger('click');
        await flushPromises();

        expect(wrapper.find('[data-testid="mark-all-read-button"]').exists()).toBe(false);
    });

    it('has view all link pointing to /notifications', async () => {
        const wrapper = mountBell();

        await wrapper.find('[data-testid="notification-bell-button"]').trigger('click');
        await flushPromises();

        const link = wrapper.find('[data-testid="view-all-link"]');
        expect(link.attributes('href')).toBe('/notifications');
    });

    it('has correct aria attributes on button', () => {
        const wrapper = mountBell();
        const button = wrapper.find('[data-testid="notification-bell-button"]');

        expect(button.attributes('aria-haspopup')).toBe('true');
        expect(button.attributes('aria-expanded')).toBe('false');
    });

    it('updates aria-expanded when dropdown opens', async () => {
        const wrapper = mountBell();
        const button = wrapper.find('[data-testid="notification-bell-button"]');

        await button.trigger('click');
        await flushPromises();

        expect(button.attributes('aria-expanded')).toBe('true');
    });

    it('logs error when fetchNotifications fails', async () => {
        const { fetchNotifications } = await import('./notification-api');
        (fetchNotifications as ReturnType<typeof vi.fn>).mockRejectedValueOnce(new Error('fail'));

        const { error: logError } = await import('../logger/logger');
        const wrapper = mountBell();

        await wrapper.find('[data-testid="notification-bell-button"]').trigger('click');
        await flushPromises();

        expect(logError).toHaveBeenCalledWith('Failed to load bell notifications', expect.any(Error));
    });

    it('calls markRead when notification item emits mark-read', async () => {
        const { markRead } = await import('./notification-api');

        const wrapper = mountBell();
        await flushPromises();

        await wrapper.find('[data-testid="notification-bell-button"]').trigger('click');
        await flushPromises();

        const itemComponent = wrapper.findComponent({ name: 'NotificationItem' });
        itemComponent.vm.$emit('mark-read', 'n1');
        await flushPromises();

        expect(markRead).toHaveBeenCalledWith('n1');
    });

    it('logs error and toasts when markRead fails', async () => {
        const { markRead } = await import('./notification-api');
        (markRead as ReturnType<typeof vi.fn>).mockRejectedValueOnce(new Error('fail'));

        const { error: logError } = await import('../logger/logger');
        const { error: toastError } = await import('../toast/toast-queue');

        const wrapper = mountBell();
        await flushPromises();

        await wrapper.find('[data-testid="notification-bell-button"]').trigger('click');
        await flushPromises();

        const itemComponent = wrapper.findComponent({ name: 'NotificationItem' });
        itemComponent.vm.$emit('mark-read', 'n1');
        await flushPromises();

        expect(logError).toHaveBeenCalledWith('Failed to mark notification as read', expect.any(Error));
        expect(toastError).toHaveBeenCalledWith('messages.notifications.error_action');
    });

    it('cleans up event listeners on unmount', async () => {
        const removeSpy = vi.spyOn(document, 'removeEventListener');
        const wrapper = mountBell();
        await flushPromises();

        wrapper.unmount();

        expect(removeSpy).toHaveBeenCalledWith('click', expect.any(Function));
        expect(removeSpy).toHaveBeenCalledWith('keydown', expect.any(Function));
        removeSpy.mockRestore();
    });

    it('logs error when fetchUnreadCount fails', async () => {
        const { fetchUnreadCount } = await import('./notification-api');
        (fetchUnreadCount as ReturnType<typeof vi.fn>).mockRejectedValueOnce(new Error('fail'));

        const { error: logError } = await import('../logger/logger');
        mountBell();
        await flushPromises();

        expect(logError).toHaveBeenCalledWith('Failed to load unread count', expect.any(Error));
    });

    it('logs error and toasts when markAllRead fails', async () => {
        const { markAllRead } = await import('./notification-api');
        (markAllRead as ReturnType<typeof vi.fn>).mockRejectedValueOnce(new Error('fail'));

        const wrapper = mountBell();
        await flushPromises();

        await wrapper.find('[data-testid="notification-bell-button"]').trigger('click');
        await flushPromises();

        const { error: logError } = await import('../logger/logger');
        const { error: toastError } = await import('../toast/toast-queue');

        await wrapper.find('[data-testid="mark-all-read-button"]').trigger('click');
        await flushPromises();

        expect(logError).toHaveBeenCalledWith('Failed to mark all notifications as read', expect.any(Error));
        expect(toastError).toHaveBeenCalledWith('messages.notifications.error_action');
    });
});
