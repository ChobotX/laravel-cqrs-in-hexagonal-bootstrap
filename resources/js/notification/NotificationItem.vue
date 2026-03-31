<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';
import { type NotificationEntry, NotificationLevel } from './notification-store';

const props = defineProps<{
    notification: NotificationEntry;
    compact?: boolean;
}>();

const emit = defineEmits<{
    'mark-read': [id: string];
    delete: [id: string];
}>();

const isUnread = computed(() => props.notification.readAt === null);

const levelColor = computed(() => {
    switch (props.notification.level) {
        case NotificationLevel.Info:
            return 'text-blue-500';
        case NotificationLevel.Success:
            return 'text-green-500';
        case NotificationLevel.Warning:
            return 'text-amber-500';
        case NotificationLevel.Error:
            return 'text-red-500';
    }
});

const timeAgo = computed(() => {
    const now = Date.now();
    const created = new Date(props.notification.createdAt).getTime();
    const diffMs = now - created;
    const MS_PER_MINUTE = 60_000;
    const MINUTES_PER_HOUR = 60;
    const HOURS_PER_DAY = 24;
    const diffMinutes = Math.floor(diffMs / MS_PER_MINUTE);
    const diffHours = Math.floor(diffMinutes / MINUTES_PER_HOUR);
    const diffDays = Math.floor(diffHours / HOURS_PER_DAY);

    if (diffMinutes < 1) return trans('messages.notifications.just_now');
    if (diffMinutes < 60) return trans('messages.notifications.minutes_ago', { count: diffMinutes });
    if (diffHours < 24) return trans('messages.notifications.hours_ago', { count: diffHours });

    return trans('messages.notifications.days_ago', { count: diffDays });
});

function handleClick(): void {
    if (isUnread.value) {
        emit('mark-read', props.notification.id);
    }

    if (props.notification.linkUrl) {
        window.location.href = props.notification.linkUrl;
    }
}
</script>

<template>
    <div
        :class="[
            'flex gap-3 px-4 py-3 transition-colors',
            isUnread ? 'bg-indigo-50/50' : 'bg-white',
            notification.linkUrl ? 'cursor-pointer hover:bg-gray-50' : '',
        ]"
        :data-testid="`notification-${notification.id}`"
        role="article"
        @click="handleClick"
    >
        <div :class="['mt-0.5 shrink-0', levelColor]">
            <svg
                v-if="notification.level === NotificationLevel.Info"
                class="h-5 w-5"
                fill="currentColor"
                viewBox="0 0 20 20"
                aria-hidden="true"
            >
                <path
                    fill-rule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                    clip-rule="evenodd"
                />
            </svg>
            <svg
                v-else-if="notification.level === NotificationLevel.Success"
                class="h-5 w-5"
                fill="currentColor"
                viewBox="0 0 20 20"
                aria-hidden="true"
            >
                <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                    clip-rule="evenodd"
                />
            </svg>
            <svg
                v-else-if="notification.level === NotificationLevel.Warning"
                class="h-5 w-5"
                fill="currentColor"
                viewBox="0 0 20 20"
                aria-hidden="true"
            >
                <path
                    fill-rule="evenodd"
                    d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z"
                    clip-rule="evenodd"
                />
            </svg>
            <svg
                v-else
                class="h-5 w-5"
                fill="currentColor"
                viewBox="0 0 20 20"
                aria-hidden="true"
            >
                <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                    clip-rule="evenodd"
                />
            </svg>
        </div>
        <div class="min-w-0 flex-1">
            <p
                :class="[
                    'text-sm',
                    isUnread ? 'font-semibold text-gray-900' : 'font-medium text-gray-700',
                ]"
            >
                {{ notification.title }}
            </p>
            <p class="mt-0.5 line-clamp-2 text-xs text-gray-500">{{ notification.body }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ timeAgo }}</p>
        </div>
        <div v-if="!compact" class="shrink-0">
            <button
                class="rounded p-1 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-500"
                type="button"
                :title="trans('messages.notifications.delete')"
                :aria-label="trans('messages.notifications.delete')"
                :data-testid="`delete-notification-${notification.id}`"
                @click.stop="emit('delete', notification.id)"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</template>
