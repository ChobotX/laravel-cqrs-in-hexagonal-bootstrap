<script setup lang="ts" generic="T extends Record<string, unknown>">
import { trans } from 'laravel-vue-i18n';
import LoadingOverlay from '../../shared/components/LoadingOverlay.vue';
import type { ColumnDef, ShareableTeam } from './composables/types';
import DataGridFilterChips from './DataGridFilterChips.vue';
import DataGridPagination from './DataGridPagination.vue';
import DataGridPresets from './DataGridPresets.vue';
import DataGridSearch from './DataGridSearch.vue';
import DataGridSortHeader from './DataGridSortHeader.vue';

const props = defineProps<{
    grid: ReturnType<typeof import('./composables/useDataGrid').useDataGrid<T>>;
    countLabel?: string;
    canShareTeam?: boolean;
    canShareGlobal?: boolean;
    shareableTeams?: ShareableTeam[];
}>();

defineSlots<{
    toolbar?: () => unknown;
    'header-extra'?: () => unknown;
    [key: `item.${string}`]: (slotProps: { item: T; value: unknown; column: ColumnDef }) => unknown;
    actions?: (slotProps: { item: T }) => unknown;
    empty?: () => unknown;
    loading?: () => unknown;
}>();
</script>

<template>
    <div data-testid="data-grid">
        <!-- Presets -->
        <div v-if="grid.presets.presets.value.length > 0" class="mb-4">
            <DataGridPresets
                :presets="grid.presets.presets.value"
                :active-preset-id="grid.presets.activePresetId.value"
                :can-share-team="canShareTeam"
                :can-share-global="canShareGlobal"
                :shareable-teams="shareableTeams"
                @load-preset="grid.presets.loadPreset($event)"
                @save-preset="(name: string, scope?: string, teamId?: string) => grid.presets.savePreset(name, scope, teamId)"
                @delete-preset="grid.presets.deletePreset($event)"
                @clear-preset="grid.clearAll()"
            />
        </div>

        <!-- Search + toolbar slot -->
        <div class="mb-4 space-y-3">
            <div class="flex flex-wrap items-center gap-3">
                <div class="w-full sm:w-64">
                    <DataGridSearch
                        :model-value="grid.search.searchTerm.value"
                        @update:model-value="grid.search.searchTerm.value = $event"
                        @clear="grid.search.clear()"
                    />
                </div>
                <slot name="toolbar" />
            </div>

            <!-- Filter chips -->
            <DataGridFilterChips
                :search="grid.search.debouncedTerm.value"
                :filters="grid.filter.filters.value"
                @clear-search="grid.search.clear()"
                @clear-filter="grid.filter.clearFilter($event)"
                @clear-all="grid.clearAll()"
            />
        </div>

        <!-- Count + header-extra slot -->
        <div class="mb-6 flex items-center justify-between">
            <span
                class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10"
                data-testid="grid-total-count"
            >
                {{ grid.pagination.total.value }} {{ countLabel ?? '' }}
            </span>
            <slot name="header-extra" />
        </div>

        <!-- Loading state -->
        <div v-if="grid.serverData.loading.value && grid.serverData.items.value.length === 0" class="rounded-xl bg-white p-8 text-center shadow-sm ring-1 ring-gray-950/5">
            <slot name="loading">
                <p class="text-sm text-gray-400">{{ trans('messages.grid.search_placeholder') }}...</p>
            </slot>
        </div>

        <!-- Table -->
        <div v-else class="relative overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <LoadingOverlay :visible="grid.serverData.loading.value" />
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <template v-for="column in grid.columns.value" :key="column.key">
                                <DataGridSortHeader
                                    v-if="column.sortable"
                                    :column-key="column.key"
                                    :label="column.title"
                                    :sorted="grid.sort.isSorted(column.key)"
                                    :direction="grid.sort.getSortDirection(column.key)"
                                    @sort="grid.sort.toggleSort($event)"
                                />
                                <th
                                    v-else
                                    class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500"
                                    scope="col"
                                >
                                    {{ column.title }}
                                </th>
                            </template>
                            <th
                                v-if="$slots.actions"
                                class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500"
                                scope="col"
                            >
                                {{ trans('messages.grid.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template v-if="grid.serverData.items.value.length > 0">
                            <tr
                                v-for="(item, index) in grid.serverData.items.value"
                                :key="(item as Record<string, unknown>).id as string ?? index"
                                class="transition-colors hover:bg-gray-50/50"
                            >
                                <td v-for="column in grid.columns.value" :key="column.key" class="px-6 py-4">
                                    <slot :name="`item.${column.key}`" :item="item" :value="(item as Record<string, unknown>)[column.key]" :column="column">
                                        {{ (item as Record<string, unknown>)[column.key] }}
                                    </slot>
                                </td>
                                <td v-if="$slots.actions" class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <slot name="actions" :item="item" />
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr v-else>
                            <td :colspan="grid.columns.value.length + ($slots.actions ? 1 : 0)" class="px-6 py-8 text-center text-sm text-gray-400">
                                <slot name="empty">
                                    {{ trans('messages.grid.no_results') }}
                                </slot>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <DataGridPagination
            :page="grid.pagination.page.value"
            :per-page="grid.pagination.perPage.value"
            :total="grid.pagination.total.value"
            :total-pages="grid.pagination.totalPages.value"
            @go-to-page="grid.pagination.goToPage($event)"
        />

        <!-- Save preset button (always visible for convenience) -->
        <div v-if="grid.presets.presets.value.length === 0" class="mt-4">
            <DataGridPresets
                :presets="[]"
                :active-preset-id="null"
                :can-share-team="canShareTeam"
                :can-share-global="canShareGlobal"
                :shareable-teams="shareableTeams"
                @save-preset="(name: string, scope?: string, teamId?: string) => grid.presets.savePreset(name, scope, teamId)"
                @delete-preset="grid.presets.deletePreset($event)"
                @clear-preset="grid.clearAll()"
            />
        </div>
    </div>
</template>
