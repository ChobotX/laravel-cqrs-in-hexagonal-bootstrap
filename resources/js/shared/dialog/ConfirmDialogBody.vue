<script setup lang="ts">
defineProps<{ title: string; message: string }>();
const emit = defineEmits<{ confirm: []; cancel: [] }>();
</script>

<template>
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-start gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 id="dialog-title" class="text-lg font-semibold text-gray-900">{{ title }}</h3>
                <p class="mt-2 text-sm text-gray-600">{{ message }}</p>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button
                type="button"
                class="rounded-lg px-4 py-2.5 text-sm font-semibold text-gray-700 ring-1 ring-gray-300 hover:bg-gray-50 transition-colors"
                autofocus
                @click="emit('cancel')"
            >
                {{ $t('messages.dialog.cancel') }}
            </button>
            <button
                type="button"
                class="rounded-lg px-4 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-500 shadow-sm transition-colors"
                @click="emit('confirm')"
            >
                {{ $t('messages.dialog.confirm') }}
            </button>
        </div>
    </div>
</template>
