<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import { onMounted, ref, watch } from 'vue';
import { error as logError } from '../../core/logger/logger';
import { error as toastError } from '../../shared/toast/toast-queue';
import NotificationItem from './NotificationItem.vue';
import { deleteNotification, fetchNotifications, markAllRead, markRead } from './notification-api';
import type { NotificationEntry } from './notification-store';

const PAGE_SIZE = 15;
const filter = ref<'all' | 'unread' | 'read'>('all');
const items = ref<NotificationEntry[]>([]);
const page = ref(1);
const totalPages = ref(1);
const loading = ref(false);

async function load(): Promise<void> {
    loading.value = true;

    try {
        const result = await fetchNotifications(filter.value, page.value, PAGE_SIZE);
        items.value = result.data;
        totalPages.value = result.meta.total_pages;
    } catch (err) {
        logError('Failed to load notifications', err);
        toastError(trans('messages.notifications.error_loading'));
    } finally {
        loading.value = false;
    }
}

async function handleMarkRead(id: string): Promise<void> {
    const item = items.value.find((n) => n.id === id);

    if (item && item.readAt === null) {
        item.readAt = new Date().toISOString();
    }

    try {
        await markRead(id);
    } catch (err) {
        logError('Failed to mark notification as read', err);
        toastError(trans('messages.notifications.error_action'));
    }
}

async function handleDelete(id: string): Promise<void> {
    items.value = items.value.filter((n) => n.id !== id);

    try {
        await deleteNotification(id);
    } catch (err) {
        logError('Failed to delete notification', err);
        toastError(trans('messages.notifications.error_action'));
    }
}

async function handleMarkAllRead(): Promise<void> {
    const now = new Date().toISOString();

    for (const item of items.value) {
        if (item.readAt === null) {
            item.readAt = now;
        }
    }

    try {
        await markAllRead();
    } catch (err) {
        logError('Failed to mark all notifications as read', err);
        toastError(trans('messages.notifications.error_action'));
    }
}

function setFilter(newFilter: 'all' | 'unread' | 'read'): void {
    filter.value = newFilter;
    page.value = 1;
}

watch([filter, page], () => {
    void load();
});

onMounted(() => {
    void load();
});
</script>

<template>
    <div data-testid="notification-list">
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 sm:px-6">
            <div class="flex gap-1">
                <button
                    v-for="f in (['all', 'unread', 'read'] as const)"
                    :key="f"
                    :class="[
                        'rounded-md px-3 py-1.5 text-sm font-medium transition-colors',
                        filter === f
                            ? 'bg-indigo-100 text-indigo-700'
                            : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700',
                    ]"
                    type="button"
                    :data-testid="`filter-${f}`"
                    @click="setFilter(f)"
                >
                    {{ trans(`messages.notifications.filter_${f}`) }}
                </button>
            </div>
            <button
                class="cursor-pointer text-sm text-indigo-600 transition-colors hover:text-indigo-800"
                type="button"
                data-testid="mark-all-read-list"
                @click="void handleMarkAllRead()"
            >
                {{ trans('messages.notifications.mark_all_read') }}
            </button>
        </div>

        <div v-if="loading" class="px-4 py-8 text-center text-sm text-gray-400">
            {{ trans('messages.notifications.loading') }}
        </div>

        <div v-else-if="items.length === 0" class="px-4 py-8 text-center text-sm text-gray-400" data-testid="list-empty">
            {{ trans('messages.notifications.empty') }}
        </div>

        <div v-else class="divide-y divide-gray-100">
            <NotificationItem
                v-for="notification in items"
                :key="notification.id"
                :notification="notification"
                :compact="false"
                @mark-read="void handleMarkRead($event)"
                @delete="void handleDelete($event)"
            />
        </div>

        <div v-if="totalPages > 1" class="flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6">
            <button
                :disabled="page <= 1"
                class="rounded-md px-3 py-1.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50"
                type="button"
                data-testid="prev-page"
                @click="page--"
            >
                {{ trans('messages.notifications.prev') }}
            </button>
            <span class="text-sm text-gray-500" data-testid="page-info">
                {{ page }} / {{ totalPages }}
            </span>
            <button
                :disabled="page >= totalPages"
                class="rounded-md px-3 py-1.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50"
                type="button"
                data-testid="next-page"
                @click="page++"
            >
                {{ trans('messages.notifications.next') }}
            </button>
        </div>
    </div>
</template>
