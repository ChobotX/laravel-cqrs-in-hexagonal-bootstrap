<script setup lang="ts">
import { computed } from 'vue';
import { STACK_GAP_CLASS_BY_TOKEN, type StackGap } from '../layout/stack-gap-classes';

const props = withDefaults(
    defineProps<{
        leadingMaxWidth?: string;
        contentGap?: StackGap;
    }>(),
    {
        leadingMaxWidth: 'max-w-[220px]',
        contentGap: 'default',
    },
);

const contentGapClass = computed(() => STACK_GAP_CLASS_BY_TOKEN[props.contentGap] ?? STACK_GAP_CLASS_BY_TOKEN.default);
</script>

<template>
    <div class="flex flex-col md:flex-row md:items-start md:gap-10">
        <div :class="['mx-auto', 'mb-10', 'w-full', 'shrink-0', 'md:mb-0', leadingMaxWidth]">
            <slot name="leading" />
        </div>
        <div :class="['flex', 'min-w-0', 'flex-1', 'flex-col', contentGapClass]">
            <slot name="trailing" />
        </div>
    </div>
</template>
