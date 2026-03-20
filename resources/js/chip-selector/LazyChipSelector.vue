<script setup lang="ts">
import { computed, ref } from 'vue';
import type { ChipOption } from './ChipSelector.vue';
import ChipSelector from './ChipSelector.vue';

const props = withDefaults(
    defineProps<{
        searchUrl: string;
        selectedItems: ChipOption[];
        inputName?: string;
        placeholder?: string;
        noResultsText?: string;
        debounceMs?: number;
    }>(),
    {
        inputName: 'items[]',
        placeholder: undefined,
        noResultsText: undefined,
        debounceMs: 300,
    },
);

const selectedIds = ref<string[]>(props.selectedItems.map((item) => item.id));
const allKnownItems = ref<Map<string, ChipOption>>(new Map(props.selectedItems.map((item) => [item.id, item])));
const fetchedOptions = ref<ChipOption[]>([]);
const isLoading = ref(false);
const hasFetched = ref(false);

let debounceTimer: ReturnType<typeof setTimeout> | null = null;

const combinedOptions = computed(() => {
    const knownSelected = selectedIds.value
        .map((id) => allKnownItems.value.get(id))
        .filter((item): item is ChipOption => item !== undefined);
    const fetchedIds = new Set(fetchedOptions.value.map((o) => o.id));
    const knownNotInFetched = knownSelected.filter((item) => !fetchedIds.has(item.id));
    return [...knownNotInFetched, ...fetchedOptions.value];
});

function fetchResults(term: string): void {
    isLoading.value = true;

    const params = new URLSearchParams();
    if (term !== '') {
        params.set('q', term);
    }
    for (const id of selectedIds.value) {
        params.append('exclude[]', id);
    }

    const csrfMeta = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };
    if (csrfMeta) {
        headers['X-CSRF-TOKEN'] = csrfMeta.content;
    }

    fetch(`${props.searchUrl}?${params.toString()}`, { headers })
        .then((response) => response.json())
        .then((json) => {
            fetchedOptions.value = (json.data ?? []).map(
                (item: { id: string; name: string; email?: string }): ChipOption => ({
                    id: item.id,
                    name: item.name,
                    badge: item.email || undefined,
                }),
            );
            isLoading.value = false;
            hasFetched.value = true;
        })
        .catch(() => {
            fetchedOptions.value = [];
            isLoading.value = false;
        });
}

function onSelectionChange(ids: string[]): void {
    for (const option of fetchedOptions.value) {
        allKnownItems.value.set(option.id, option);
    }
    selectedIds.value = ids;
    fetchResults('');
}

function onFocus(): void {
    if (!hasFetched.value) {
        fetchResults('');
    }
}

function onSearch(term: string): void {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }
    debounceTimer = setTimeout(() => {
        fetchResults(term);
    }, props.debounceMs);
}
</script>

<template>
    <div class="relative">
        <ChipSelector
            :options="combinedOptions"
            :model-value="selectedIds"
            :input-name="inputName"
            :placeholder="placeholder"
            :no-results-text="noResultsText"
            @update:model-value="onSelectionChange"
            @focus="onFocus"
            @search="onSearch"
        />
        <span
            v-if="isLoading"
            class="absolute right-3 top-3 text-xs text-gray-400"
            aria-live="polite"
            >...</span
        >
    </div>
</template>
