<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';

const emit = defineEmits<{ close: [] }>();

const panelRef = ref<HTMLElement | null>(null);
const previousActiveElement = ref<Element | null>(null);

function getFocusableElements(): HTMLElement[] {
    return Array.from(
        (panelRef.value as HTMLElement).querySelectorAll<HTMLElement>(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])',
        ),
    );
}

function trapFocus(event: KeyboardEvent): void {
    if (event.key !== 'Tab') {
        return;
    }

    const focusable = getFocusableElements();
    if (focusable.length === 0) {
        event.preventDefault();
        return;
    }

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
    }
}

function onKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        emit('close');
        return;
    }
    trapFocus(event);
}

onMounted(() => {
    previousActiveElement.value = document.activeElement;
    document.body.style.overflow = 'hidden';
    document.addEventListener('keydown', onKeydown);
});

onUnmounted(() => {
    document.body.style.overflow = '';
    document.removeEventListener('keydown', onKeydown);
    const prev = previousActiveElement.value;
    if (prev instanceof HTMLElement) {
        prev.focus();
    }
});
</script>

<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-50 flex justify-end" aria-modal="true" role="dialog" aria-labelledby="slide-over-title">
            <div class="fixed inset-0 bg-gray-900/50" @click="emit('close')" />
            <Transition
                appear
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="translate-x-full"
                enter-to-class="translate-x-0"
            >
                <div ref="panelRef" class="relative z-10 flex h-full w-full max-w-md flex-col overflow-y-auto bg-white shadow-xl">
                    <slot />
                </div>
            </Transition>
        </div>
    </Teleport>
</template>
