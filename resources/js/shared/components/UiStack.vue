<script setup lang="ts">
import { computed } from 'vue';
import { STACK_GAP_CLASS_BY_TOKEN, type StackGap } from '../layout/stack-gap-classes';

const props = withDefaults(
    defineProps<{
        gap?: StackGap;
    }>(),
    { gap: 'default' },
);

const gapClass = computed(() => STACK_GAP_CLASS_BY_TOKEN[props.gap] ?? STACK_GAP_CLASS_BY_TOKEN.default);
</script>

<template>
    <div :class="['flex', 'flex-col', gapClass]">
        <slot />
    </div>
</template>
