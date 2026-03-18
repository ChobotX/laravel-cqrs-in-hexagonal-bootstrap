<script setup lang="ts">
import { removeToast, type ToastEntry, ToastType } from './toast-queue';

defineProps<{ toast: ToastEntry }>();
</script>

<template>
    <div
        role="alert"
        class="flex items-center gap-3 rounded-lg border-l-4 px-4 py-3 text-sm shadow-lg bg-white"
        :class="[
            toast.type === ToastType.Success ? 'border-green-500' : 'border-red-500',
            toast.removing ? 'opacity-0 transition-all duration-300' : 'opacity-100',
        ]"
    >
        <svg
            v-if="toast.type === ToastType.Success"
            class="h-5 w-5 shrink-0 text-green-500"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
        <svg
            v-else
            class="h-5 w-5 shrink-0 text-red-500"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
        </svg>
        <span class="flex-1" :class="toast.type === ToastType.Success ? 'text-green-800' : 'text-red-800'">{{ toast.message }}</span>
        <button
            type="button"
            class="shrink-0 text-gray-400 hover:text-gray-600 transition-colors"
            :aria-label="'Dismiss'"
            @click="removeToast(toast.id)"
        >
            <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</template>
