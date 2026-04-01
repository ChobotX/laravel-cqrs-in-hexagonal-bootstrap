<script setup lang="ts">
import { ref, useTemplateRef } from 'vue';
import { calculateCoordinates, type Position, resolvePosition } from './tooltip-position';

const { position = 'top' } = defineProps<{
    position?: Position;
}>();

const show = ref(false);
const activator = useTemplateRef<HTMLElement>('activator');
const tooltip = useTemplateRef<HTMLElement>('tooltip');

const tooltipPosition = ref<{ top: number; left: number; arrowLeft: number; actualPosition: Position }>({
    top: 0,
    left: 0,
    arrowLeft: 0,
    actualPosition: position,
});

function calculatePosition(): { top: number; left: number; arrowLeft: number; actualPosition: Position } {
    if (!activator.value || !tooltip.value) {
        return { top: 0, left: 0, arrowLeft: 0, actualPosition: position };
    }

    const triggerRect = activator.value.getBoundingClientRect();
    const tooltipRect = tooltip.value.getBoundingClientRect();
    const actualPosition = resolvePosition(position, triggerRect, tooltipRect);
    const { top, left, arrowLeft } = calculateCoordinates(actualPosition, triggerRect, tooltipRect);

    return { top, left, arrowLeft, actualPosition };
}

function showTooltip(): void {
    tooltipPosition.value = calculatePosition();
    show.value = true;
}

function hideTooltip(): void {
    show.value = false;
}
</script>

<template>
    <span ref="activator"
          class="relative inline-flex items-center"
          @mouseenter="showTooltip"
          @mouseleave="hideTooltip"
          @focusin="showTooltip"
          @focusout="hideTooltip">
        <slot name="activator"></slot>
        <Teleport to="body">
            <div ref="tooltip"
                 role="tooltip"
                 class="tooltip"
                 :class="[
                     `tooltip--${tooltipPosition.actualPosition}`,
                     show ? 'tooltip--visible' : '',
                 ]"
                 :style="{
                     top: `${tooltipPosition.top}px`,
                     left: `${tooltipPosition.left}px`,
                     '--arrow-left': `${tooltipPosition.arrowLeft}px`,
                 }">
                <slot></slot>
            </div>
        </Teleport>
    </span>
</template>
