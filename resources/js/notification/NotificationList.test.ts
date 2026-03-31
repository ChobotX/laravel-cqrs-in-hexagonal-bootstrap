import { flushPromises, mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { nextTick } from 'vue';
import NotificationList from './NotificationList.vue';

vi.mock('laravel-vue-i18n', () => ({
    trans: (key: string): string => key,
}));

vi.mock('../toast/toast-queue', () => ({
    error: vi.fn(),
}));

vi.mock('../logger/logger', () => ({
    error: vi.fn(),
}));

const { mockFetchNotifications, mockMarkRead, mockMarkAllRead, mockDeleteNotification } = vi.hoisted(() => ({
    mockFetchNotifications: vi.fn(),
    mockMarkRead: vi.fn(),
    mockMarkAllRead: vi.fn(),
    mockDeleteNotification: vi.fn(),
}));

vi.mock('./notification-api', () => ({
    fetchNotifications: mockFetchNotifications,
    markRead: mockMarkRead,
    markAllRead: mockMarkAllRead,
    deleteNotification: mockDeleteNotification,
}));

const mockNotification = {
    id: 'n1',
    level: 'info',
    title: 'Test Notification',
    body: 'Test body',
    linkUrl: null,
    readAt: null,
    createdAt: '2026-01-15T10:00:00Z',
};

const defaultResponse = {
    data: [mockNotification],
    meta: { current_page: 1, per_page: 15, total: 1, total_pages: 1 },
};

const emptyResponse = {
    data: [],
    meta: { current_page: 1, per_page: 15, total: 0, total_pages: 0 },
};

const multiPageResponse = {
    data: [mockNotification],
    meta: { current_page: 1, per_page: 15, total: 30, total_pages: 2 },
};

function mountList(): ReturnType<typeof mount> {
    return mount(NotificationList);
}

describe('NotificationList', () => {
    beforeEach(() => {
        mockFetchNotifications.mockReset().mockResolvedValue(defaultResponse);
        mockMarkRead.mockReset().mockResolvedValue(undefined);
        mockMarkAllRead.mockReset().mockResolvedValue(undefined);
        mockDeleteNotification.mockReset().mockResolvedValue(undefined);
    });

    afterEach(() => {
        vi.restoreAllMocks();
    });

    it('fetches and renders notifications on mount', async () => {
        const wrapper = mountList();
        await flushPromises();

        expect(wrapper.find('[data-testid="notification-n1"]').exists()).toBe(true);
    });

    it('shows loading state while fetching', async () => {
        let resolvePromise: ((value: unknown) => void) | undefined;
        mockFetchNotifications.mockReturnValueOnce(
            new Promise((resolve) => {
                resolvePromise = resolve;
            }),
        );

        const wrapper = mountList();
        await nextTick();

        expect(wrapper.text()).toContain('messages.notifications.loading');

        resolvePromise?.(defaultResponse);
        await flushPromises();

        expect(wrapper.text()).not.toContain('messages.notifications.loading');
    });

    it('shows empty state when no notifications', async () => {
        mockFetchNotifications.mockResolvedValueOnce(emptyResponse);

        const wrapper = mountList();
        await flushPromises();

        expect(wrapper.find('[data-testid="list-empty"]').exists()).toBe(true);
    });

    it('renders 3 filter buttons', async () => {
        const wrapper = mountList();
        await flushPromises();

        expect(wrapper.find('[data-testid="filter-all"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="filter-unread"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="filter-read"]').exists()).toBe(true);
    });

    it('switches filter and reloads', async () => {
        const wrapper = mountList();
        await flushPromises();

        mockFetchNotifications.mockClear().mockResolvedValue(defaultResponse);

        await wrapper.find('[data-testid="filter-unread"]').trigger('click');
        await flushPromises();

        expect(mockFetchNotifications).toHaveBeenCalledWith('unread', 1, 15);
    });

    it('active filter has active styling', async () => {
        const wrapper = mountList();
        await flushPromises();

        const allButton = wrapper.find('[data-testid="filter-all"]');
        expect(allButton.classes()).toContain('bg-indigo-100');
    });

    it('shows pagination when multiple pages', async () => {
        mockFetchNotifications.mockResolvedValueOnce(multiPageResponse);

        const wrapper = mountList();
        await flushPromises();

        expect(wrapper.find('[data-testid="page-info"]').text()).toBe('1 / 2');
        expect(wrapper.find('[data-testid="prev-page"]').exists()).toBe(true);
        expect(wrapper.find('[data-testid="next-page"]').exists()).toBe(true);
    });

    it('prev button is disabled on first page', async () => {
        mockFetchNotifications.mockResolvedValueOnce(multiPageResponse);

        const wrapper = mountList();
        await flushPromises();

        const prevButton = wrapper.find<HTMLButtonElement>('[data-testid="prev-page"]');
        expect(prevButton.element.disabled).toBe(true);
    });

    it('next button navigates to page 2', async () => {
        mockFetchNotifications.mockResolvedValueOnce(multiPageResponse);

        const wrapper = mountList();
        await flushPromises();

        mockFetchNotifications.mockClear().mockResolvedValue({
            data: [mockNotification],
            meta: { current_page: 2, per_page: 15, total: 30, total_pages: 2 },
        });

        await wrapper.find('[data-testid="next-page"]').trigger('click');
        await flushPromises();

        expect(mockFetchNotifications).toHaveBeenCalledWith('all', 2, 15);
    });

    it('hides pagination when only one page', async () => {
        const wrapper = mountList();
        await flushPromises();

        expect(wrapper.find('[data-testid="page-info"]').exists()).toBe(false);
    });

    it('mark all read updates items', async () => {
        const wrapper = mountList();
        await flushPromises();

        await wrapper.find('[data-testid="mark-all-read-list"]').trigger('click');
        await flushPromises();

        expect(mockMarkAllRead).toHaveBeenCalled();
    });

    it('shows error toast when fetch fails', async () => {
        mockFetchNotifications.mockRejectedValueOnce(new Error('fail'));
        const { error: toastError } = await import('../toast/toast-queue');

        const wrapper = mountList();
        await flushPromises();

        expect(toastError).toHaveBeenCalledWith('messages.notifications.error_loading');
        expect(wrapper.find('[data-testid="list-empty"]').exists()).toBe(true);
    });

    it('optimistically marks notification as read', async () => {
        const wrapper = mountList();
        await flushPromises();

        const itemComponent = wrapper.findComponent({ name: 'NotificationItem' });
        itemComponent.vm.$emit('mark-read', 'n1');
        await flushPromises();

        expect(mockMarkRead).toHaveBeenCalledWith('n1');
    });

    it('shows error toast when markRead fails', async () => {
        mockMarkRead.mockRejectedValueOnce(new Error('fail'));
        const { error: toastError } = await import('../toast/toast-queue');

        const wrapper = mountList();
        await flushPromises();

        const itemComponent = wrapper.findComponent({ name: 'NotificationItem' });
        itemComponent.vm.$emit('mark-read', 'n1');
        await flushPromises();

        expect(toastError).toHaveBeenCalledWith('messages.notifications.error_action');
    });

    it('shows error toast when delete fails', async () => {
        mockDeleteNotification.mockRejectedValueOnce(new Error('fail'));
        const { error: toastError } = await import('../toast/toast-queue');

        const wrapper = mountList();
        await flushPromises();

        await wrapper.find('[data-testid="delete-notification-n1"]').trigger('click');
        await flushPromises();

        expect(toastError).toHaveBeenCalledWith('messages.notifications.error_action');
    });

    it('removes item from list on delete', async () => {
        const wrapper = mountList();
        await flushPromises();

        expect(wrapper.find('[data-testid="notification-n1"]').exists()).toBe(true);

        await wrapper.find('[data-testid="delete-notification-n1"]').trigger('click');
        await flushPromises();

        expect(wrapper.find('[data-testid="notification-n1"]').exists()).toBe(false);
    });

    it('shows error toast when markAllRead fails', async () => {
        mockMarkAllRead.mockRejectedValueOnce(new Error('fail'));
        const { error: toastError } = await import('../toast/toast-queue');

        const wrapper = mountList();
        await flushPromises();

        await wrapper.find('[data-testid="mark-all-read-list"]').trigger('click');
        await flushPromises();

        expect(toastError).toHaveBeenCalledWith('messages.notifications.error_action');
    });

    it('resets to page 1 when filter changes', async () => {
        mockFetchNotifications.mockResolvedValueOnce(multiPageResponse);

        const wrapper = mountList();
        await flushPromises();

        mockFetchNotifications.mockClear().mockResolvedValue(defaultResponse);

        await wrapper.find('[data-testid="next-page"]').trigger('click');
        await flushPromises();

        mockFetchNotifications.mockClear().mockResolvedValue(defaultResponse);

        await wrapper.find('[data-testid="filter-unread"]').trigger('click');
        await flushPromises();

        expect(mockFetchNotifications).toHaveBeenCalledWith('unread', 1, 15);
    });
});
