<script setup lang="ts">
defineProps<{ title: string; message: string }>();
const emit = defineEmits<{ acknowledge: [] }>();
</script>

<template>
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-start gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100">
                <svg class="h-6 w-6 text-indigo-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
            </div>
            <div class="flex-1">
                <h3 id="dialog-title" class="text-lg font-semibold text-gray-900">{{ title }}</h3>
                <p class="mt-2 text-sm text-gray-600">{{ message }}</p>
            </div>
        </div>
        <div class="mt-6 flex justify-end">
            <button
                type="button"
                class="rounded-lg px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 shadow-sm transition-colors"
                autofocus
                @click="emit('acknowledge')"
            >
                {{ $t('messages.dialog.ok') }}
            </button>
        </div>
    </div>
</template>
