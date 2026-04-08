<script setup lang="ts">
import ToastItem from './ToastItem.vue';
import { toasts } from './toast-queue';
</script>

<template>
    <div class="fixed z-50 flex flex-col gap-2 top-4 left-1/2 -translate-x-1/2 w-[calc(100%-2rem)] max-w-96 lg:translate-x-0 lg:top-auto lg:bottom-4 lg:left-4 lg:w-96" aria-live="polite" role="status">
        <TransitionGroup
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-2 lg:translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-300 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2 lg:translate-y-2"
        >
            <ToastItem v-for="toast in toasts" :key="toast.id" :toast="toast" />
        </TransitionGroup>
    </div>
</template>
