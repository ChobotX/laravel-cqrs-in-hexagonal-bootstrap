<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';

const props = defineProps<{
    modelValue: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
    clear: [];
}>();

function onInput(event: Event): void {
    const t = event.target;
    if (!(t instanceof HTMLInputElement)) {
        return;
    }
    emit('update:modelValue', t.value);
}
</script>

<template>
    <div class="relative">
        <svg
            class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-gray-400"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
            />
        </svg>
        <input
            class="w-full appearance-none rounded-lg border border-gray-200 bg-white py-2 pl-9 pr-9 text-sm text-gray-900 placeholder-gray-400 transition-colors focus:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-100"
            type="text"
            :value="props.modelValue"
            :placeholder="trans('messages.grid.search_placeholder')"
            :aria-label="trans('messages.grid.search_label')"
            data-testid="grid-search-input"
            @input="onInput"
        />
        <button
            v-if="props.modelValue !== ''"
            class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 transition-colors hover:text-gray-600"
            type="button"
            :aria-label="trans('messages.grid.clear_filters')"
            data-testid="grid-search-clear"
            @click="emit('clear')"
        >
            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path
                    d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"
                />
            </svg>
        </button>
    </div>
</template>
