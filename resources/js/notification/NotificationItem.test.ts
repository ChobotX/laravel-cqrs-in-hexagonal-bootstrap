import { mount } from '@vue/test-utils';
import { describe, expect, it, vi } from 'vitest';
import NotificationItem from './NotificationItem.vue';
import { type NotificationEntry, NotificationLevel } from './notification-store';

vi.mock('laravel-vue-i18n', () => ({
    trans: (key: string, params?: Record<string, unknown>): string => {
        if (params && 'count' in params) {
            return `${key}:${params.count}`;
        }

        return key;
    },
}));

function createEntry(overrides: Partial<NotificationEntry> = {}): NotificationEntry {
    return {
        id: 'notif-1',
        level: NotificationLevel.Info,
        title: 'Test Title',
        body: 'Test body text',
        linkUrl: null,
        readAt: null,
        createdAt: new Date().toISOString(),
        ...overrides,
    };
}

function mountItem(notification: NotificationEntry = createEntry(), compact = false): ReturnType<typeof mount> {
    return mount(NotificationItem, {
        props: { notification, compact },
    });
}

describe('NotificationItem', () => {
    it('renders title and body', () => {
        const wrapper = mountItem(createEntry({ title: 'My Title', body: 'My Body' }));

        expect(wrapper.text()).toContain('My Title');
        expect(wrapper.text()).toContain('My Body');
    });

    it('applies unread highlight for unread notification', () => {
        const wrapper = mountItem(createEntry({ readAt: null }));
        const root = wrapper.find('[data-testid="notification-notif-1"]');

        expect(root.classes()).toContain('bg-indigo-50/50');
    });

    it('does not apply unread highlight for read notification', () => {
        const wrapper = mountItem(createEntry({ readAt: '2026-01-15T10:00:00Z' }));
        const root = wrapper.find('[data-testid="notification-notif-1"]');

        expect(root.classes()).toContain('bg-white');
    });

    it('renders info icon for info level', () => {
        const wrapper = mountItem(createEntry({ level: NotificationLevel.Info }));

        expect(wrapper.find('.text-blue-500').exists()).toBe(true);
    });

    it('renders success icon for success level', () => {
        const wrapper = mountItem(createEntry({ level: NotificationLevel.Success }));

        expect(wrapper.find('.text-green-500').exists()).toBe(true);
    });

    it('renders warning icon for warning level', () => {
        const wrapper = mountItem(createEntry({ level: NotificationLevel.Warning }));

        expect(wrapper.find('.text-amber-500').exists()).toBe(true);
    });

    it('renders error icon for error level', () => {
        const wrapper = mountItem(createEntry({ level: NotificationLevel.Error }));

        expect(wrapper.find('.text-red-500').exists()).toBe(true);
    });

    it('emits mark-read on click when unread', async () => {
        const wrapper = mountItem(createEntry({ id: 'abc', readAt: null }));
        const root = wrapper.find('[data-testid="notification-abc"]');

        await root.trigger('click');

        expect(wrapper.emitted('mark-read')).toEqual([['abc']]);
    });

    it('does not emit mark-read on click when already read', async () => {
        const wrapper = mountItem(createEntry({ id: 'abc', readAt: '2026-01-15T10:00:00Z' }));
        const root = wrapper.find('[data-testid="notification-abc"]');

        await root.trigger('click');

        expect(wrapper.emitted('mark-read')).toBeUndefined();
    });

    it('navigates to linkUrl on click', async () => {
        const originalHref = window.location.href;
        const hrefSetter = vi.fn();
        Object.defineProperty(window, 'location', {
            value: {
                ...window.location,
                get href() {
                    return originalHref;
                },
                set href(v: string) {
                    hrefSetter(v);
                },
            },
            writable: true,
        });

        const wrapper = mountItem(createEntry({ linkUrl: '/some/page' }));
        await wrapper.find('[data-testid="notification-notif-1"]').trigger('click');

        expect(hrefSetter).toHaveBeenCalledWith('/some/page');
    });

    it('adds cursor-pointer class when linkUrl is present', () => {
        const wrapper = mountItem(createEntry({ linkUrl: '/link' }));
        const root = wrapper.find('[data-testid="notification-notif-1"]');

        expect(root.classes()).toContain('cursor-pointer');
    });

    it('hides delete button in compact mode', () => {
        const wrapper = mountItem(createEntry({ id: 'x' }), true);

        expect(wrapper.find('[data-testid="delete-notification-x"]').exists()).toBe(false);
    });

    it('shows delete button in non-compact mode', () => {
        const wrapper = mountItem(createEntry({ id: 'x' }), false);

        expect(wrapper.find('[data-testid="delete-notification-x"]').exists()).toBe(true);
    });

    it('emits delete on delete button click', async () => {
        const wrapper = mountItem(createEntry({ id: 'del-1' }), false);

        await wrapper.find('[data-testid="delete-notification-del-1"]').trigger('click');

        expect(wrapper.emitted('delete')).toEqual([['del-1']]);
    });

    it('renders time ago text', () => {
        const fiveMinutesAgo = new Date(Date.now() - 300_000).toISOString();
        const wrapper = mountItem(createEntry({ createdAt: fiveMinutesAgo }));

        expect(wrapper.text()).toContain('messages.notifications.minutes_ago:5');
    });

    it('renders just now for recent notifications', () => {
        const wrapper = mountItem(createEntry({ createdAt: new Date().toISOString() }));

        expect(wrapper.text()).toContain('messages.notifications.just_now');
    });

    it('renders hours ago', () => {
        const twoHoursAgo = new Date(Date.now() - 7_200_000).toISOString();
        const wrapper = mountItem(createEntry({ createdAt: twoHoursAgo }));

        expect(wrapper.text()).toContain('messages.notifications.hours_ago:2');
    });

    it('renders days ago', () => {
        const twoDaysAgo = new Date(Date.now() - 172_800_000).toISOString();
        const wrapper = mountItem(createEntry({ createdAt: twoDaysAgo }));

        expect(wrapper.text()).toContain('messages.notifications.days_ago:2');
    });
});
