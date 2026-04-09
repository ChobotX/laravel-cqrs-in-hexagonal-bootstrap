<script setup lang="ts">
export interface TemplateVariable {
    name: string;
    description: string;
    sensitive: boolean;
}

defineProps<{
    variables: TemplateVariable[];
}>();

const emit = defineEmits<{
    insert: [variableName: string];
}>();
</script>

<template>
    <div data-testid="variable-panel">
        <p class="mb-2 text-xs font-medium text-gray-500">
            Available variables
        </p>
        <div class="flex flex-wrap gap-2">
            <button
                v-for="variable in variables"
                :key="variable.name"
                type="button"
                class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-sm transition-colors hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700"
                :data-testid="`variable-${variable.name}`"
                :title="variable.description"
                @click="emit('insert', variable.name)"
            >
                <svg
                    v-if="variable.sensitive"
                    class="size-3.5 text-amber-500"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"
                    />
                </svg>
                <span class="font-mono text-xs">{{ `$${variable.name}` }}</span>
            </button>
        </div>
    </div>
</template>
