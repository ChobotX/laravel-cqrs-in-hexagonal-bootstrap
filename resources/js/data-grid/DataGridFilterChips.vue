<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';

defineProps<{
    search: string;
    filters: Record<string, string>;
}>();

const emit = defineEmits<{
    'clear-search': [];
    'clear-filter': [key: string];
    'clear-all': [];
}>();
</script>

<template>
    <div
        v-if="search !== '' || Object.keys(filters).length > 0"
        class="flex flex-wrap items-center gap-2"
        data-testid="filter-chips"
    >
        <span
            v-if="search !== ''"
            class="inline-flex items-center gap-1 rounded-full bg-indigo-50 py-0.5 pl-2.5 pr-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10"
        >
            {{ trans('messages.grid.search_filter', { term: search }) }}
            <button
                class="inline-flex cursor-pointer items-center rounded-full p-0.5 text-indigo-400 transition-colors hover:bg-indigo-100 hover:text-indigo-600"
                type="button"
                :aria-label="trans('messages.grid.remove_filter', { label: search })"
                data-testid="filter-chip-remove-search"
                @click="emit('clear-search')"
            >
                <svg class="size-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"
                    />
                </svg>
            </button>
        </span>

        <span
            v-for="(value, key) in filters"
            :key="key"
            class="inline-flex items-center gap-1 rounded-full bg-indigo-50 py-0.5 pl-2.5 pr-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10"
        >
            {{ key }}: {{ value }}
            <button
                class="inline-flex cursor-pointer items-center rounded-full p-0.5 text-indigo-400 transition-colors hover:bg-indigo-100 hover:text-indigo-600"
                type="button"
                :aria-label="trans('messages.grid.remove_filter', { label: `${key}: ${value}` })"
                :data-testid="`filter-chip-remove-${key}`"
                @click="emit('clear-filter', key as string)"
            >
                <svg class="size-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path
                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"
                    />
                </svg>
            </button>
        </span>

        <button
            v-if="search !== '' || Object.keys(filters).length > 0"
            class="cursor-pointer text-xs font-medium text-gray-500 transition-colors hover:text-gray-700"
            type="button"
            data-testid="filter-chips-clear-all"
            @click="emit('clear-all')"
        >
            {{ trans('messages.grid.clear_filters') }}
        </button>
    </div>
</template>
