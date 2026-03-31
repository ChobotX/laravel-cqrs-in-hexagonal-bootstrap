<script setup lang="ts">
import { ref, useTemplateRef } from 'vue';

type Position = 'top' | 'bottom' | 'left' | 'right';

const VIEWPORT_MARGIN = 50;
const OFFSET = 8;

const { position = 'top' } = defineProps<{
    position?: Position;
}>();

const show = ref(false);
const activator = useTemplateRef<HTMLElement>('activator');
const tooltip = useTemplateRef<HTMLElement>('tooltip');

const tooltipPosition = ref<{ top: number; left: number; actualPosition: Position }>({
    top: 0,
    left: 0,
    actualPosition: position,
});

const FLIP: Record<Position, Position> = { top: 'bottom', bottom: 'top', left: 'right', right: 'left' };

function resolvePosition(preferred: Position, triggerRect: DOMRect, tooltipRect: DOMRect): Position {
    const hitsEdge: Record<Position, boolean> = {
        top: triggerRect.top - tooltipRect.height - OFFSET < VIEWPORT_MARGIN,
        bottom: triggerRect.bottom + tooltipRect.height + OFFSET > window.innerHeight - VIEWPORT_MARGIN,
        left: triggerRect.left - tooltipRect.width - OFFSET < VIEWPORT_MARGIN,
        right: triggerRect.right + tooltipRect.width + OFFSET > window.innerWidth - VIEWPORT_MARGIN,
    };

    if (hitsEdge[preferred]) return FLIP[preferred];

    return preferred;
}

function calculatePosition(): { top: number; left: number; actualPosition: Position } {
    if (!activator.value || !tooltip.value) {
        return { top: 0, left: 0, actualPosition: position };
    }

    const triggerRect = activator.value.getBoundingClientRect();
    const tooltipRect = tooltip.value.getBoundingClientRect();
    const actualPosition = resolvePosition(position, triggerRect, tooltipRect);

    let top: number;
    let left: number;

    switch (actualPosition) {
        case 'top':
            top = triggerRect.top - tooltipRect.height - OFFSET;
            left = triggerRect.left + triggerRect.width / 2 - tooltipRect.width / 2;
            break;
        case 'bottom':
            top = triggerRect.bottom + OFFSET;
            left = triggerRect.left + triggerRect.width / 2 - tooltipRect.width / 2;
            break;
        case 'left':
            top = triggerRect.top + triggerRect.height / 2 - tooltipRect.height / 2;
            left = triggerRect.left - tooltipRect.width - OFFSET;
            break;
        case 'right':
            top = triggerRect.top + triggerRect.height / 2 - tooltipRect.height / 2;
            left = triggerRect.right + OFFSET;
            break;
    }

    left = Math.max(VIEWPORT_MARGIN, Math.min(left, window.innerWidth - tooltipRect.width - VIEWPORT_MARGIN));
    top = Math.max(VIEWPORT_MARGIN, Math.min(top, window.innerHeight - tooltipRect.height - VIEWPORT_MARGIN));

    return { top, left, actualPosition };
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
                 }">
                <slot></slot>
            </div>
        </Teleport>
    </span>
</template>
