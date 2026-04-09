<script setup lang="ts">
import { trans, transChoice } from 'laravel-vue-i18n';
import { computed, ref } from 'vue';
import { error as logError } from '../../../core/logger/logger';
import type { ColumnDef, ShareableTeam } from '../composables/types';
import { useDataGrid } from '../composables/useDataGrid';
import DataGrid from '../DataGrid.vue';
import { getCsrfToken } from '../data-grid-api';

const props = defineProps<{
    fetchUrl: string;
    canUpdate: boolean;
    canShareTeam: boolean;
    canShareGlobal: boolean;
    shareableTeams: ShareableTeam[];
    groups: { key: string; label: string }[];
}>();

interface FlagRow {
    key: string;
    label: string;
    description: string;
    type: string;
    group: string;
    group_label: string;
    enabled: boolean;
    value: string;
    is_overridden: boolean;
    has_options: boolean;
    edit_url: string;
    reset_url: string;
}

const columns = computed<ColumnDef[]>(() => [
    { key: 'label', title: trans('messages.feature_flags.flag'), sortable: true },
    { key: 'type', title: trans('messages.feature_flags.type'), sortable: true },
    { key: 'enabled', title: trans('messages.feature_flags.status'), sortable: false },
    { key: 'value', title: trans('messages.feature_flags.value'), sortable: false },
]);

const grid = useDataGrid<FlagRow>({
    fetchUrl: props.fetchUrl,
    gridName: 'feature-flags',
    columns,
    defaultSort: { key: 'label', order: 'asc' },
});

const togglingKeys = ref<Set<string>>(new Set());

async function toggleFlag(item: FlagRow): Promise<void> {
    if (togglingKeys.value.has(item.key)) {
        return;
    }

    togglingKeys.value.add(item.key);
    const newEnabled = !item.enabled;
    item.enabled = newEnabled;

    try {
        const response = await fetch(`/internal-api/feature-flags/${item.key}/toggle`, {
            method: 'PATCH',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify({ enabled: newEnabled }),
        });

        if (!response.ok) {
            item.enabled = !newEnabled;
        }
    } catch (error: unknown) {
        logError('Failed to toggle feature flag', error);
        item.enabled = !newEnabled;
    } finally {
        togglingKeys.value.delete(item.key);
    }
}

function setGroupFilter(group: string): void {
    if (group === '') {
        grid.filter.clearFilter('group');
    } else {
        grid.filter.setFilter('group', group);
    }
}

const selectedGroup = computed(() => grid.filter.filters.value.group ?? '');

const typeBadgeClasses: Record<string, string> = {
    boolean: 'bg-purple-50 text-purple-700 ring-purple-700/10',
    select: 'bg-blue-50 text-blue-700 ring-blue-700/10',
    input: 'bg-teal-50 text-teal-700 ring-teal-700/10',
};

function typeLabel(type: string): string {
    return trans(`messages.feature_flags.type_${type}`);
}
</script>

<template>
    <DataGrid :grid="grid" :count-label="transChoice('messages.feature_flags.count', grid.pagination.total.value)" :can-share-team="canShareTeam" :can-share-global="canShareGlobal" :shareable-teams="shareableTeams">
        <template #toolbar>
            <select
                class="cursor-pointer rounded-lg border-gray-300 py-2 pl-3 pr-8 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                :value="selectedGroup"
                :aria-label="trans('messages.feature_flags.group_filter_label')"
                data-testid="group-filter"
                @change="setGroupFilter(($event.target as HTMLSelectElement).value)"
            >
                <option value="">{{ trans('messages.feature_flags.group_filter_all') }}</option>
                <option v-for="group in groups" :key="group.key" :value="group.key">{{ trans(group.label) }}</option>
            </select>
        </template>

        <template #item.label="{ item }">
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-base font-medium text-gray-900 sm:text-sm">{{ trans(item.label) }}</span>
                    <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ item.key }}</code>
                </div>
                <p class="mt-0.5 text-xs text-gray-400">{{ trans(item.description) }}</p>
            </div>
        </template>

        <template #item.type="{ item }">
            <span
                class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset"
                :class="typeBadgeClasses[item.type] ?? 'bg-gray-50 text-gray-700 ring-gray-700/10'"
            >
                {{ typeLabel(item.type) }}
            </span>
        </template>

        <template #item.enabled="{ item }">
            <label class="relative inline-flex cursor-pointer items-center" :class="{ 'pointer-events-none opacity-50': !canUpdate || togglingKeys.has(item.key) }">
                <input
                    class="peer sr-only"
                    type="checkbox"
                    :checked="item.enabled"
                    :disabled="!canUpdate || togglingKeys.has(item.key)"
                    :aria-label="trans('messages.feature_flags.enabled') + ' ' + trans(item.label)"
                    :data-testid="'toggle-' + item.key"
                    @change="toggleFlag(item)"
                />
                <div class="peer h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-green-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-600 peer-focus:ring-offset-2 peer-disabled:cursor-not-allowed peer-disabled:opacity-50"></div>
            </label>
        </template>

        <template #item.value="{ item }">
            <div class="flex items-center gap-2">
                <span v-if="item.has_options" :class="item.is_overridden ? 'font-medium text-gray-900' : 'text-gray-400'">
                    {{ item.value }}
                </span>
                <span
                    v-if="item.is_overridden"
                    class="inline-flex items-center rounded-full bg-indigo-50 px-1.5 py-0.5 text-[10px] font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10"
                >
                    {{ trans('messages.feature_flags.override_badge') }}
                </span>
            </div>
        </template>

        <template #actions="{ item }">
            <a
                v-if="item.has_options && canUpdate"
                :href="item.edit_url"
                class="inline-flex cursor-pointer items-center rounded-lg p-2 text-gray-400 transition-colors hover:bg-indigo-50 hover:text-indigo-600"
                :aria-label="trans('messages.feature_flags.edit_action') + ' ' + item.key"
                data-testid="edit-btn"
            >
                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
            </a>
            <a
                v-if="item.is_overridden && canUpdate"
                :href="item.reset_url"
                class="inline-flex cursor-pointer items-center rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600"
                :aria-label="trans('messages.feature_flags.reset_action') + ' ' + item.key"
                data-testid="reset-btn"
                data-confirm-delete
                :data-confirm-title="trans('messages.feature_flags.reset_confirm_title')"
                :data-confirm-message="trans('messages.feature_flags.reset_confirm_message', { key: item.key })"
            >
                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
            </a>
        </template>
    </DataGrid>
</template>
