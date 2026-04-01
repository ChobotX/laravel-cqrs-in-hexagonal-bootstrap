<script setup lang="ts">
/**
 * Vue equivalent of the Blade `<x-action-button>` component.
 *
 * Icon-only button for table/list row actions with consistent styling
 * across Blade grids and Vue lists. Uses a default slot for the icon SVG.
 *
 * Variants mirror the Blade component:
 *   - default: indigo hover (edit, view, etc.)
 *   - danger:  red hover (delete, revoke, etc.)
 */
const { variant = 'default' } = defineProps<{
    label: string;
    variant?: 'default' | 'danger';
}>();

const emit = defineEmits<{
    click: [];
}>();
</script>

<template>
    <button
        :class="[
            'inline-flex cursor-pointer items-center justify-center rounded-lg p-2 text-gray-400 transition-colors',
            variant === 'danger' ? 'hover:bg-red-50 hover:text-red-600' : 'hover:bg-indigo-50 hover:text-indigo-600',
        ]"
        type="button"
        :data-tooltip="label"
        :aria-label="label"
        @click.stop="emit('click')"
    >
        <slot />
    </button>
</template>
