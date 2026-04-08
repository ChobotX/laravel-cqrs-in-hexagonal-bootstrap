<script setup lang="ts">
import { trans, transChoice } from 'laravel-vue-i18n';
import { computed } from 'vue';
import ActionButton from '../../../shared/components/ActionButton.vue';
import type { ColumnDef, ShareableTeam } from '../composables/types';
import { useDataGrid } from '../composables/useDataGrid';
import DataGrid from '../DataGrid.vue';

const props = defineProps<{
    fetchUrl: string;
    createUrl: string;
    canCreate: boolean;
    canRead: boolean;
    canUpdate: boolean;
    canDelete: boolean;
    treeViewUrl: string;
    canShareTeam: boolean;
    canShareGlobal: boolean;
    shareableTeams: ShareableTeam[];
}>();

interface TeamRow {
    id: string;
    name: string;
    slug: string;
    description: string;
    parent_name: string | null;
    labels: Array<{ id: string; name: string }> | null;
    show_url: string;
    edit_url: string;
    delete_url: string;
}

const columns = computed<ColumnDef[]>(() => [
    { key: 'name', title: trans('messages.teams.name'), sortable: true },
    { key: 'slug', title: trans('messages.teams.slug'), sortable: true },
    { key: 'parent_name', title: trans('messages.teams.parent'), sortable: false },
    { key: 'description', title: trans('messages.teams.description'), sortable: false },
]);

const grid = useDataGrid<TeamRow>({
    fetchUrl: props.fetchUrl,
    gridName: 'teams',
    columns,
    defaultSort: { key: 'name', order: 'asc' },
});
</script>

<template>
    <DataGrid :grid="grid" :count-label="transChoice('messages.teams.count', grid.pagination.total.value)" :can-share-team="canShareTeam" :can-share-global="canShareGlobal" :shareable-teams="shareableTeams">
        <template #header-extra>
            <div class="flex items-center gap-2">
                <div class="flex rounded-lg border border-gray-200 bg-white p-0.5" role="group" :aria-label="trans('messages.teams.title')">
                    <button
                        class="cursor-pointer rounded-md bg-indigo-100 p-1.5 text-indigo-700 transition-colors"
                        type="button"
                        :title="trans('messages.teams.view_list')"
                        :aria-label="trans('messages.teams.view_list')"
                        data-testid="teams-view-list-btn"
                    >
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </button>
                    <a
                        :href="treeViewUrl"
                        class="cursor-pointer rounded-md p-1.5 text-gray-400 transition-colors hover:text-gray-600"
                        :title="trans('messages.teams.view_tree')"
                        :aria-label="trans('messages.teams.view_tree')"
                        data-testid="teams-view-tree-btn"
                    >
                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75ZM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25ZM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25Z" />
                        </svg>
                    </a>
                </div>
                <a
                    v-if="canCreate"
                    :href="createUrl"
                    class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-indigo-700"
                    data-testid="create-team-btn"
                >
                    {{ trans('messages.teams.create_action') }}
                </a>
            </div>
        </template>

        <template #item.name="{ item }">
            <div>
                <span class="text-base font-medium text-gray-900 sm:text-sm">{{ item.name }}</span>
                <div v-if="item.labels && item.labels.length > 0" class="mt-1 flex flex-wrap items-center gap-1">
                    <span
                        v-for="label in item.labels"
                        :key="label.id"
                        class="inline-flex items-center rounded-full bg-gray-50 px-1.5 py-0.5 text-[10px] font-medium text-gray-700 ring-1 ring-inset ring-gray-700/10"
                    >
                        {{ label.name }}
                    </span>
                </div>
            </div>
        </template>

        <template #item.slug="{ item }">
            <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs text-gray-600">{{ item.slug }}</code>
        </template>

        <template #item.parent_name="{ item }">
            <span class="text-base text-gray-500 sm:text-sm">{{ item.parent_name ?? '\u2014' }}</span>
        </template>

        <template #item.description="{ item }">
            <span class="text-base text-gray-500 sm:text-sm">{{ item.description }}</span>
        </template>

        <template #actions="{ item }">
            <a
                v-if="canRead"
                :href="item.show_url"
                class="inline-flex cursor-pointer items-center rounded-lg p-2 text-gray-400 transition-colors hover:bg-indigo-50 hover:text-indigo-600"
                :aria-label="trans('messages.teams.view_action') + ' ' + item.name"
                data-testid="view-btn"
            >
                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
            </a>
            <a
                v-if="canUpdate"
                :href="item.edit_url"
                class="inline-flex cursor-pointer items-center rounded-lg p-2 text-gray-400 transition-colors hover:bg-indigo-50 hover:text-indigo-600"
                :aria-label="trans('messages.teams.edit_action') + ' ' + item.name"
                data-testid="edit-btn"
            >
                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
            </a>
            <a
                v-if="canDelete"
                :href="item.delete_url"
                class="inline-flex cursor-pointer items-center rounded-lg p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600"
                :aria-label="trans('messages.teams.delete_action') + ' ' + item.name"
                data-testid="delete-btn"
                data-confirm-delete
                :data-confirm-title="trans('messages.teams.delete_confirm_title')"
                :data-confirm-message="trans('messages.teams.delete_confirm_message', { name: item.name })"
            >
                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
            </a>
        </template>
    </DataGrid>
</template>
