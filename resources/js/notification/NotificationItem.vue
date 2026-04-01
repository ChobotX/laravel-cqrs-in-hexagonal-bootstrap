<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import { computed, ref } from 'vue';
import ActionButton from '../shared/ActionButton.vue';
import { type NotificationEntry, NotificationLevel } from './notification-store';

const TRUNCATE_LENGTH = 80;

const props = defineProps<{
    notification: NotificationEntry;
    compact?: boolean;
}>();

const emit = defineEmits<{
    'mark-read': [id: string];
    delete: [id: string];
}>();

const isUnread = computed(() => props.notification.readAt === null);
const expanded = ref(false);
const isTruncated = computed(() => props.compact === true && props.notification.body.length > TRUNCATE_LENGTH);

const displayBody = computed(() => {
    if (!props.compact || expanded.value || !isTruncated.value) {
        return props.notification.body;
    }

    return `${props.notification.body.slice(0, TRUNCATE_LENGTH)}…`;
});

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
    if (props.notification.linkUrl) {
        window.location.href = props.notification.linkUrl;
    }
}
</script>

<template>
    <div
        :class="[
            'flex gap-3 px-4 py-3 transition-colors',
            isUnread ? 'bg-indigo-50/50' : 'bg-white opacity-60',
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
            <p class="mt-0.5 text-xs text-gray-500">
                {{ displayBody }}
                <button
                    v-if="isTruncated && !expanded"
                    class="cursor-pointer text-indigo-600 hover:text-indigo-800"
                    type="button"
                    data-testid="show-more"
                    @click.stop="expanded = true"
                >
                    {{ trans('messages.notifications.show_more') }}
                </button>
                <button
                    v-if="isTruncated && expanded"
                    class="cursor-pointer text-indigo-600 hover:text-indigo-800"
                    type="button"
                    data-testid="show-less"
                    @click.stop="expanded = false"
                >
                    {{ trans('messages.notifications.show_less') }}
                </button>
            </p>
            <p class="mt-1 text-xs text-gray-400">{{ timeAgo }}</p>
        </div>
        <div :class="['flex shrink-0 items-start', compact ? 'flex-row gap-1' : 'flex-col gap-0.5']">
            <ActionButton
                v-if="isUnread"
                :label="trans('messages.notifications.mark_read')"
                :data-testid="`mark-read-${notification.id}`"
                @click="emit('mark-read', notification.id)"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </ActionButton>
            <ActionButton
                variant="danger"
                :label="trans('messages.notifications.delete')"
                :data-testid="`delete-notification-${notification.id}`"
                @click="emit('delete', notification.id)"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
            </ActionButton>
        </div>
    </div>
</template>
