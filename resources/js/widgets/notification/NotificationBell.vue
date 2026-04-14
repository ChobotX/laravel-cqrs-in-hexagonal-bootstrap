<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import { onMounted, onUnmounted, ref } from 'vue';
import { nodeFromEventTarget } from '../../core/dom/event-target-guards';
import { error as logError } from '../../core/logger/logger';
import { error as toastError } from '../../shared/toast/toast-queue';
import NotificationItem from './NotificationItem.vue';
import { deleteNotification, fetchNotifications, fetchUnreadCount, markAllRead, markRead } from './notification-api';
import {
    notifications,
    removeNotification,
    setNotifications,
    setUnreadCount,
    markAllAsRead as storeMarkAllAsRead,
    markAsRead as storeMarkAsRead,
    unreadCount,
} from './notification-store';

const isOpen = ref(false);
const bellRef = ref<HTMLElement | null>(null);

function toggle(): void {
    isOpen.value = !isOpen.value;

    if (isOpen.value) {
        void loadNotifications();
    }
}

function close(): void {
    isOpen.value = false;
}

async function loadNotifications(): Promise<void> {
    try {
        const result = await fetchNotifications('all', 1, 10);
        setNotifications(result.data);
    } catch (err) {
        logError('Failed to load bell notifications', err);
    }
}

async function loadUnreadCount(): Promise<void> {
    try {
        const count = await fetchUnreadCount();
        setUnreadCount(count);
    } catch (err) {
        logError('Failed to load unread count', err);
    }
}

async function handleMarkRead(id: string): Promise<void> {
    storeMarkAsRead(id);

    try {
        await markRead(id);
    } catch (err) {
        logError('Failed to mark notification as read', err);
        toastError(trans('messages.notifications.error_action'));
    }
}

async function handleMarkAllRead(): Promise<void> {
    storeMarkAllAsRead();

    try {
        await markAllRead();
    } catch (err) {
        logError('Failed to mark all notifications as read', err);
        toastError(trans('messages.notifications.error_action'));
    }
}

async function handleDelete(id: string): Promise<void> {
    removeNotification(id);

    try {
        await deleteNotification(id);
    } catch (err) {
        logError('Failed to delete notification', err);
        toastError(trans('messages.notifications.error_action'));
    }
}

function handleClickOutside(event: MouseEvent): void {
    if (bellRef.value === null) {
        return;
    }
    const n = nodeFromEventTarget(event.target);
    if (n === null || !bellRef.value.contains(n)) {
        close();
    }
}

function handleEscape(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        close();
    }
}

onMounted(() => {
    void loadUnreadCount();
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    document.removeEventListener('keydown', handleEscape);
});
</script>

<template>
    <div ref="bellRef" class="relative" data-testid="notification-bell">
        <button
            class="relative cursor-pointer rounded-lg p-1.5 text-gray-500 transition-colors hover:text-gray-700"
            type="button"
            :data-tooltip="trans('messages.notifications.title')"
            :aria-label="trans('messages.notifications.title')"
            :aria-expanded="isOpen"
            aria-haspopup="true"
            data-testid="notification-bell-button"
            @click="toggle"
        >
            <svg
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"
                />
            </svg>
            <span
                v-if="unreadCount > 0"
                class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white"
                aria-live="polite"
                data-testid="notification-badge"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="scale-95 opacity-0"
            enter-to-class="scale-100 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="scale-100 opacity-100"
            leave-to-class="scale-95 opacity-0"
        >
            <div
                v-if="isOpen"
                class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-gray-950/5"
                role="menu"
                data-testid="notification-dropdown"
            >
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-2.5">
                    <span class="text-sm font-semibold text-gray-900">
                        {{ trans('messages.notifications.title') }}
                    </span>
                    <button
                        v-if="unreadCount > 0"
                        class="cursor-pointer text-xs text-indigo-600 transition-colors hover:text-indigo-800"
                        type="button"
                        data-testid="mark-all-read-button"
                        @click="void handleMarkAllRead()"
                    >
                        {{ trans('messages.notifications.mark_all_read') }}
                    </button>
                </div>

                <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                    <NotificationItem
                        v-for="notification in notifications"
                        :key="notification.id"
                        :notification="notification"
                        :compact="true"
                        @mark-read="void handleMarkRead($event)"
                        @delete="void handleDelete($event)"
                    />
                    <div
                        v-if="notifications.length === 0"
                        class="px-4 py-8 text-center text-sm text-gray-400"
                        data-testid="notification-empty"
                    >
                        {{ trans('messages.notifications.empty') }}
                    </div>
                </div>

                <div class="border-t border-gray-100 px-4 py-2.5 text-center">
                    <a
                        href="/notifications"
                        class="text-xs font-medium text-indigo-600 transition-colors hover:text-indigo-800"
                        data-testid="view-all-link"
                    >
                        {{ trans('messages.notifications.view_all') }}
                    </a>
                </div>
            </div>
        </Transition>
    </div>
</template>
