<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import { computed } from 'vue';

const props = defineProps<{
    page: number;
    perPage: number;
    total: number;
    totalPages: number;
}>();

const emit = defineEmits<{
    'go-to-page': [page: number];
}>();

const WINDOW = 2;

const from = computed(() => Math.max(1, props.page - WINDOW));
const to = computed(() => Math.min(props.totalPages, props.page + WINDOW));
const showingFrom = computed(() => (props.page - 1) * props.perPage + 1);
const showingTo = computed(() => Math.min(props.page * props.perPage, props.total));

const pages = computed(() => {
    const result: number[] = [];

    for (let i = from.value; i <= to.value; i++) {
        result.push(i);
    }

    return result;
});
</script>

<template>
    <nav
        v-if="totalPages > 1"
        class="mt-6 flex items-center justify-between"
        :aria-label="trans('messages.pagination.navigation')"
        data-testid="grid-pagination"
    >
        <p class="text-sm text-gray-500">
            {{ trans('messages.pagination.showing', { from: showingFrom, to: showingTo, total: props.total }) }}
        </p>
        <div class="flex items-center gap-1">
            <button
                :disabled="page <= 1"
                class="inline-flex cursor-pointer items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:bg-gray-100 disabled:cursor-not-allowed disabled:text-gray-300 disabled:hover:bg-transparent"
                type="button"
                :title="trans('messages.pagination.previous')"
                data-testid="grid-pagination-prev"
                @click="emit('go-to-page', page - 1)"
            >
                &laquo;
            </button>

            <template v-if="from > 1">
                <button
                    class="inline-flex cursor-pointer items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:bg-gray-100"
                    type="button"
                    :title="trans('messages.pagination.page', { page: 1 })"
                    @click="emit('go-to-page', 1)"
                >
                    1
                </button>
                <span v-if="from > 2" class="px-1 text-gray-400">&hellip;</span>
            </template>

            <button
                v-for="p in pages"
                :key="p"
                :class="[
                    'inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium',
                    p === page
                        ? 'bg-indigo-100 font-semibold text-indigo-700'
                        : 'cursor-pointer text-gray-500 transition-colors hover:bg-gray-100',
                ]"
                type="button"
                :aria-current="p === page ? 'page' : undefined"
                :title="trans('messages.pagination.page', { page: p })"
                @click="emit('go-to-page', p)"
            >
                {{ p }}
            </button>

            <template v-if="to < totalPages">
                <span v-if="to < totalPages - 1" class="px-1 text-gray-400">&hellip;</span>
                <button
                    class="inline-flex cursor-pointer items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:bg-gray-100"
                    type="button"
                    :title="trans('messages.pagination.page', { page: totalPages })"
                    @click="emit('go-to-page', totalPages)"
                >
                    {{ totalPages }}
                </button>
            </template>

            <button
                :disabled="page >= totalPages"
                class="inline-flex cursor-pointer items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:bg-gray-100 disabled:cursor-not-allowed disabled:text-gray-300 disabled:hover:bg-transparent"
                type="button"
                :title="trans('messages.pagination.next')"
                data-testid="grid-pagination-next"
                @click="emit('go-to-page', page + 1)"
            >
                &raquo;
            </button>
        </div>
    </nav>
</template>
