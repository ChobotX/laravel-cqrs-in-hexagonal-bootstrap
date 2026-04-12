import { mount } from '@vue/test-utils';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { formatInstant } from '../../core/datetime/format-instant';
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
    beforeEach(() => {
        window.__APP__ = {};
    });

    afterEach(() => {
        window.__APP__ = undefined;
    });

    it('renders title and body', () => {
        const wrapper = mountItem(createEntry({ title: 'My Title', body: 'My Body' }));

        expect(wrapper.text()).toContain('My Title');
        expect(wrapper.text()).toContain('My Body');
    });

    it('sets title attribute using shared datetime formatter', () => {
        const iso = '2026-01-10T14:00:00Z';
        const wrapper = mountItem(createEntry({ createdAt: iso }));
        const timeRow = wrapper.find('.mt-1.text-xs.text-gray-400');

        expect(timeRow.attributes('title')).toBe(formatInstant(iso));
    });

    it('applies unread highlight for unread notification', () => {
        const wrapper = mountItem(createEntry({ readAt: null }));
        const root = wrapper.find('[data-testid="notification-notif-1"]');

        expect(root.classes()).toContain('bg-indigo-50/50');
        expect(root.classes()).not.toContain('opacity-60');
    });

    it('applies lower opacity for read notification', () => {
        const wrapper = mountItem(createEntry({ readAt: '2026-01-15T10:00:00Z' }));
        const root = wrapper.find('[data-testid="notification-notif-1"]');

        expect(root.classes()).toContain('bg-white');
        expect(root.classes()).toContain('opacity-60');
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

    it('shows mark-read button when unread', () => {
        const wrapper = mountItem(createEntry({ id: 'abc', readAt: null }));

        expect(wrapper.find('[data-testid="mark-read-abc"]').exists()).toBe(true);
    });

    it('hides mark-read button when read', () => {
        const wrapper = mountItem(createEntry({ id: 'abc', readAt: '2026-01-15T10:00:00Z' }));

        expect(wrapper.find('[data-testid="mark-read-abc"]').exists()).toBe(false);
    });

    it('emits mark-read on mark-read button click', async () => {
        const wrapper = mountItem(createEntry({ id: 'abc', readAt: null }));

        await wrapper.find('[data-testid="mark-read-abc"]').trigger('click');

        expect(wrapper.emitted('mark-read')).toEqual([['abc']]);
    });

    it('shows delete button always', () => {
        const wrapper = mountItem(createEntry({ id: 'x' }));

        expect(wrapper.find('[data-testid="delete-notification-x"]').exists()).toBe(true);
    });

    it('emits delete on delete button click', async () => {
        const wrapper = mountItem(createEntry({ id: 'del-1' }));

        await wrapper.find('[data-testid="delete-notification-del-1"]').trigger('click');

        expect(wrapper.emitted('delete')).toEqual([['del-1']]);
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

    it('emits mark-read when clicking through an unread notification with link', async () => {
        const originalHref = window.location.href;
        Object.defineProperty(window, 'location', {
            value: {
                ...window.location,
                get href() {
                    return originalHref;
                },
                set href(_v: string) {
                    /* noop */
                },
            },
            writable: true,
        });

        const wrapper = mountItem(createEntry({ id: 'unread-1', linkUrl: '/target', readAt: null }));
        await wrapper.find('[data-testid="notification-unread-1"]').trigger('click');

        expect(wrapper.emitted('mark-read')).toEqual([['unread-1']]);
    });

    it('does not emit mark-read when clicking through an already-read notification with link', async () => {
        const originalHref = window.location.href;
        Object.defineProperty(window, 'location', {
            value: {
                ...window.location,
                get href() {
                    return originalHref;
                },
                set href(_v: string) {
                    /* noop */
                },
            },
            writable: true,
        });

        const wrapper = mountItem(createEntry({ id: 'read-1', linkUrl: '/target', readAt: '2026-01-15T10:00:00Z' }));
        await wrapper.find('[data-testid="notification-read-1"]').trigger('click');

        expect(wrapper.emitted('mark-read')).toBeUndefined();
    });

    it('does not navigate when no linkUrl', async () => {
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

        const wrapper = mountItem(createEntry({ linkUrl: null }));
        await wrapper.find('[data-testid="notification-notif-1"]').trigger('click');

        expect(hrefSetter).not.toHaveBeenCalled();
    });

    it('adds cursor-pointer class when linkUrl is present', () => {
        const wrapper = mountItem(createEntry({ linkUrl: '/link' }));

        expect(wrapper.find('[data-testid="notification-notif-1"]').classes()).toContain('cursor-pointer');
    });

    it('does not add cursor-pointer when no linkUrl', () => {
        const wrapper = mountItem(createEntry({ linkUrl: null }));

        expect(wrapper.find('[data-testid="notification-notif-1"]').classes()).not.toContain('cursor-pointer');
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

    describe('compact mode', () => {
        it('truncates long body and shows "show more"', () => {
            const longBody = 'A'.repeat(100);
            const wrapper = mountItem(createEntry({ body: longBody }), true);

            expect(wrapper.find('[data-testid="show-more"]').exists()).toBe(true);
            expect(wrapper.text()).toContain('…');
        });

        it('does not truncate short body', () => {
            const wrapper = mountItem(createEntry({ body: 'Short' }), true);

            expect(wrapper.find('[data-testid="show-more"]').exists()).toBe(false);
        });

        it('expands on "show more" click', async () => {
            const longBody = 'A'.repeat(100);
            const wrapper = mountItem(createEntry({ body: longBody }), true);

            await wrapper.find('[data-testid="show-more"]').trigger('click');

            expect(wrapper.text()).toContain(longBody);
            expect(wrapper.find('[data-testid="show-less"]').exists()).toBe(true);
        });

        it('collapses on "show less" click', async () => {
            const longBody = 'A'.repeat(100);
            const wrapper = mountItem(createEntry({ body: longBody }), true);

            await wrapper.find('[data-testid="show-more"]').trigger('click');
            await wrapper.find('[data-testid="show-less"]').trigger('click');

            expect(wrapper.find('[data-testid="show-more"]').exists()).toBe(true);
        });

        it('uses horizontal button layout', () => {
            const wrapper = mountItem(createEntry({ readAt: null }), true);
            const buttons = wrapper.find('.flex.shrink-0');

            expect(buttons.classes()).toContain('flex-row');
        });
    });

    describe('non-compact mode', () => {
        it('shows full body without truncation', () => {
            const longBody = 'A'.repeat(100);
            const wrapper = mountItem(createEntry({ body: longBody }), false);

            expect(wrapper.text()).toContain(longBody);
            expect(wrapper.find('[data-testid="show-more"]').exists()).toBe(false);
        });

        it('uses vertical button layout', () => {
            const wrapper = mountItem(createEntry({ readAt: null }), false);
            const buttons = wrapper.find('.flex.shrink-0');

            expect(buttons.classes()).toContain('flex-col');
        });
    });
});
