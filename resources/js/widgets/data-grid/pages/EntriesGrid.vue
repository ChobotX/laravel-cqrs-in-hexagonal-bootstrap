<script setup lang="ts">
import { trans, transChoice } from 'laravel-vue-i18n';
import { computed } from 'vue';
import type { ColumnDef, ShareableTeam } from '../composables/types';
import { useDataGrid } from '../composables/useDataGrid';
import DataGrid from '../DataGrid.vue';

const props = defineProps<{
    fetchUrl: string;
    createUrl: string;
    definitionName: string;
    canShareTeam: boolean;
    canShareGlobal: boolean;
    shareableTeams: ShareableTeam[];
}>();

interface EntryRow {
    id: string;
    title: string;
    version: number;
    edit_url: string;
    delete_url: string;
}

const columns = computed<ColumnDef[]>(() => [
    { key: 'title', title: trans('messages.registry.entries.title_field'), sortable: true },
    { key: 'version', title: trans('messages.registry.entries.version_field'), sortable: true },
]);

const grid = useDataGrid<EntryRow>({
    fetchUrl: props.fetchUrl,
    gridName: `entries-${props.definitionName}`,
    columns,
    defaultSort: { key: 'title', order: 'asc' },
});
</script>

<template>
    <DataGrid :grid="grid" :count-label="transChoice('messages.registry.entries.count', grid.pagination.total.value)" :can-share-team="canShareTeam" :can-share-global="canShareGlobal" :shareable-teams="shareableTeams">
        <template #header-extra>
            <a
                :href="createUrl"
                class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-indigo-700"
                data-testid="create-entry-btn"
            >
                {{ trans('messages.registry.entries.create_action') }}
            </a>
        </template>

        <template #item.title="{ item }">
            <span class="text-base font-medium text-gray-900 sm:text-sm">{{ item.title }}</span>
        </template>

        <template #item.version="{ item }">
            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                v{{ item.version }}
            </span>
        </template>

        <template #actions="{ item }">
            <a
                :href="item.edit_url"
                class="inline-flex cursor-pointer items-center rounded-lg p-2 text-gray-400 transition-colors hover:bg-indigo-50 hover:text-indigo-600"
                :aria-label="trans('messages.registry.entries.update_action') + ' ' + item.title"
                data-testid="edit-btn"
            >
                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
            </a>
            <a
                :href="item.delete_url"
                class="inline-flex cursor-pointer items-center rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600"
                :aria-label="trans('messages.registry.entries.delete_action') + ' ' + item.title"
                data-testid="delete-btn"
                data-confirm-delete
                :data-confirm-title="trans('messages.registry.entries.delete_confirm_title')"
                :data-confirm-message="trans('messages.registry.entries.delete_confirm_message', { name: item.title })"
            >
                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
            </a>
        </template>
    </DataGrid>
</template>
