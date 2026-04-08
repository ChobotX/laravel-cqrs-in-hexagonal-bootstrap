<script setup lang="ts">
const props = defineProps<{
    columnKey: string;
    label: string;
    sorted: boolean;
    direction: 'asc' | 'desc' | null;
}>();

const emit = defineEmits<{
    sort: [key: string];
}>();
</script>

<template>
    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500" scope="col">
        <button
            class="group inline-flex cursor-pointer items-center gap-1 transition-colors hover:text-gray-700"
            type="button"
            :title="props.label"
            :data-testid="`sort-header-${props.columnKey}`"
            @click="emit('sort', props.columnKey)"
        >
            {{ props.label }}
            <svg
                v-if="props.sorted && props.direction === 'asc'"
                class="size-3.5 text-indigo-600"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path
                    fill-rule="evenodd"
                    d="M9.47 6.47a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 1 1-1.06 1.06L10 8.06l-3.72 3.72a.75.75 0 0 1-1.06-1.06l4.25-4.25Z"
                    clip-rule="evenodd"
                />
            </svg>
            <svg
                v-else-if="props.sorted && props.direction === 'desc'"
                class="size-3.5 text-indigo-600"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path
                    fill-rule="evenodd"
                    d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0l-4.25-4.25a.75.75 0 0 1 0-1.06Z"
                    clip-rule="evenodd"
                />
            </svg>
            <svg
                v-else
                class="size-3.5 text-gray-300 group-hover:text-gray-400"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
            >
                <path
                    fill-rule="evenodd"
                    d="M10 3a.75.75 0 0 1 .55.24l3.25 3.5a.75.75 0 1 1-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 0 1-1.1-1.02l3.25-3.5A.75.75 0 0 1 10 3Zm-3.76 9.2a.75.75 0 0 1 1.06.04l2.7 2.908 2.7-2.908a.75.75 0 1 1 1.1 1.02l-3.25 3.5a.75.75 0 0 1-1.1 0l-3.25-3.5a.75.75 0 0 1 .04-1.06Z"
                    clip-rule="evenodd"
                />
            </svg>
        </button>
    </th>
</template>
