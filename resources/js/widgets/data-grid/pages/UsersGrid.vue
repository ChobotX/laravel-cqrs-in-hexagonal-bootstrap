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
    canShareTeam: boolean;
    canShareGlobal: boolean;
    shareableTeams: ShareableTeam[];
}>();

interface UserRow {
    id: string;
    name: string;
    email: string;
    avatar_url: string | null;
    initials: string;
    roles: Array<{ id: string; name: string; url: string; is_system: boolean }> | null;
    teams: Array<{ id: string; name: string; url: string }> | null;
    labels: Array<{ id: string; name: string; color: string | null }> | null;
    edit_url: string;
    delete_url: string;
    impersonate_url: string | null;
}

const csrfToken = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';

const columns = computed<ColumnDef[]>(() => [
    { key: 'name', title: trans('messages.users.user'), sortable: true },
    { key: 'email', title: trans('messages.users.email'), sortable: true },
]);

const grid = useDataGrid<UserRow>({
    fetchUrl: props.fetchUrl,
    gridName: 'users',
    columns,
    defaultSort: { key: 'name', order: 'asc' },
});
</script>

<template>
    <DataGrid :grid="grid" :count-label="transChoice('messages.users.count', grid.pagination.total.value)" :can-share-team="canShareTeam" :can-share-global="canShareGlobal" :shareable-teams="shareableTeams">
        <template #header-extra>
            <a
                v-if="canCreate"
                :href="createUrl"
                class="inline-flex cursor-pointer items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-indigo-700"
                data-testid="create-user-btn"
            >
                {{ trans('messages.users.create_action') }}
            </a>
        </template>

        <template #item.name="{ item }">
            <div class="flex items-center gap-3">
                <img
                    v-if="item.avatar_url"
                    class="h-9 w-9 shrink-0 rounded-full object-cover"
                    :src="item.avatar_url"
                    :alt="item.name"
                />
                <div
                    v-else
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700"
                >
                    {{ item.initials }}
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-base font-medium text-gray-900 sm:text-sm">{{ item.name }}</span>
                        <template v-if="item.labels">
                            <span
                                v-for="label in item.labels"
                                :key="label.id"
                                class="inline-flex items-center rounded-full px-1.5 py-0.5 text-[10px] font-medium ring-1 ring-inset"
                                :class="label.color ? `bg-${label.color}-50 text-${label.color}-700 ring-${label.color}-700/10` : 'bg-gray-50 text-gray-700 ring-gray-700/10'"
                            >
                                {{ label.name }}
                            </span>
                        </template>
                    </div>
                    <p v-if="item.roles || item.teams" class="mt-0.5 text-xs text-gray-400">
                        <template v-if="item.roles">
                            <template v-if="item.roles.length > 0">
                                <template v-for="(role, i) in item.roles" :key="role.id">
                                    <a class="transition-colors hover:text-indigo-600" :href="role.url" :data-tooltip="role.name">{{ role.name }}</a>
                                    <template v-if="i < item.roles.length - 1">, </template>
                                </template>
                            </template>
                            <span v-else class="text-gray-300">{{ trans('messages.users.no_role') }}</span>
                        </template>
                        <template v-if="item.teams && item.teams.length > 0">
                            {{ ' ' }}<span class="text-gray-300">{{ trans('messages.users.in_team') }}</span>{{ ' ' }}
                            <template v-for="(team, i) in item.teams" :key="team.id">
                                <a class="transition-colors hover:text-indigo-600" :href="team.url" :data-tooltip="team.name">{{ team.name }}</a>
                                <template v-if="i < item.teams.length - 1">, </template>
                            </template>
                        </template>
                    </p>
                </div>
            </div>
        </template>

        <template #item.email="{ item }">
            <span class="text-base text-gray-500 sm:text-sm">{{ item.email }}</span>
        </template>

        <template #actions="{ item }">
            <form v-if="item.impersonate_url" class="inline" method="POST" :action="item.impersonate_url">
                <input type="hidden" name="_token" :value="csrfToken" />
                <button
                    class="inline-flex cursor-pointer items-center rounded-lg p-2 text-gray-400 transition-colors hover:bg-indigo-50 hover:text-indigo-600"
                    type="submit"
                    :aria-label="trans('messages.impersonation.start') + ' ' + item.name"
                    data-testid="impersonate-btn"
                >
                    <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 1-1.15 3.993m1.989 3.559A11.209 11.209 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.666 18.666 0 0 1-2.485 5.33" />
                    </svg>
                </button>
            </form>
            <a
                :href="item.edit_url"
                class="inline-flex cursor-pointer items-center rounded-lg p-2 text-gray-400 transition-colors hover:bg-indigo-50 hover:text-indigo-600"
                :aria-label="trans('messages.users.edit_action') + ' ' + item.name"
                data-testid="edit-btn"
            >
                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
            </a>
        </template>
    </DataGrid>
</template>
