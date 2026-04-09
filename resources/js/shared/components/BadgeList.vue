<script setup lang="ts">
import { computed } from 'vue';
import Tooltip from '../tooltip/Tooltip.vue';

const props = withDefaults(
    defineProps<{
        items: Record<string, unknown>[];
        labelKey?: string;
        max?: number;
    }>(),
    {
        labelKey: 'name',
        max: 2,
    },
);

const visibleItems = computed(() => props.items.slice(0, props.max));
const hiddenItems = computed(() => props.items.slice(props.max));
const hasOverflow = computed(() => props.items.length > props.max);
</script>

<template>
    <span v-if="items.length > 0" class="inline-flex flex-wrap items-center gap-1">
        <span
            v-for="item in visibleItems"
            :key="String(item.id ?? item[labelKey])"
            class="inline-flex items-center rounded-full bg-gray-50 px-1.5 py-0.5 text-[10px] font-medium text-gray-700 ring-1 ring-inset ring-gray-700/10"
        >
            {{ item[labelKey] }}
        </span>
        <Tooltip v-if="hasOverflow">
            <template #activator>
                <span class="inline-flex cursor-default items-center rounded-full bg-gray-100 px-1.5 py-0.5 text-[10px] font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">
                    +{{ hiddenItems.length }}
                </span>
            </template>
            <span class="flex flex-wrap gap-1">
                <span
                    v-for="item in items"
                    :key="'tip-' + String(item.id ?? item[labelKey])"
                    class="inline-flex items-center rounded-full bg-white/20 px-2 py-0.5 text-[10px] font-medium text-white"
                >
                    {{ item[labelKey] }}
                </span>
            </span>
        </Tooltip>
    </span>
</template>
