<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import { nextTick, ref, watch } from 'vue';
import { error as logError } from '../logger/logger';
import { error as showErrorToast } from '../toast/toast-queue';

interface SearchResult {
    id: string;
    name: string;
    email: string;
}

const props = withDefaults(
    defineProps<{
        searchUrl: string;
        excludeIds: string[];
        inputName: string;
        placeholder: string;
        noResultsText?: string;
        minChars?: number;
        debounceMs?: number;
    }>(),
    {
        noResultsText: 'No results found.',
        minChars: 0,
        debounceMs: 300,
    },
);

const search = ref('');
const isOpen = ref(false);
const isLoading = ref(false);
const activeIndex = ref(-1);
const results = ref<SearchResult[]>([]);
const selectedItem = ref<SearchResult | null>(null);
const inputRef = ref<HTMLInputElement | null>(null);
const listRef = ref<HTMLUListElement | null>(null);

let debounceTimer: ReturnType<typeof setTimeout> | null = null;

watch(results, () => {
    activeIndex.value = -1;
});

function fetchResults(term: string): void {
    if (term.length < props.minChars) {
        results.value = [];
        isOpen.value = false;
        return;
    }

    isLoading.value = true;

    const params = new URLSearchParams();
    if (term !== '') {
        params.set('q', term);
    }
    for (const id of props.excludeIds) {
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
            results.value = json.data ?? [];
            isOpen.value = true;
            isLoading.value = false;
        })
        .catch(() => {
            results.value = [];
            isLoading.value = false;
            logError('Autocomplete search failed');
            showErrorToast(trans('messages.autocomplete.search_failed'));
        });
}

function onInput(): void {
    if (selectedItem.value) {
        selectedItem.value = null;
    }

    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }

    debounceTimer = setTimeout(() => {
        fetchResults(search.value);
    }, props.debounceMs);
}

function selectItem(item: SearchResult): void {
    selectedItem.value = item;
    search.value = `${item.name} (${item.email})`;
    isOpen.value = false;
    results.value = [];
}

function clearSelection(): void {
    selectedItem.value = null;
    search.value = '';
    results.value = [];
    isOpen.value = false;
    inputRef.value?.focus();
}

function onFocus(): void {
    if (selectedItem.value) return;
    if (results.value.length === 0 && !isLoading.value) {
        fetchResults(search.value);
    }
}

function closeDropdown(): void {
    isOpen.value = false;
    activeIndex.value = -1;
}

function onKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        closeDropdown();
        return;
    }

    if (!isOpen.value || results.value.length === 0) {
        return;
    }

    if (event.key === 'ArrowDown') {
        event.preventDefault();
        activeIndex.value = Math.min(activeIndex.value + 1, results.value.length - 1);
        void scrollToActive();
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        activeIndex.value = Math.max(activeIndex.value - 1, 0);
        void scrollToActive();
    } else if (event.key === 'Enter' && activeIndex.value >= 0) {
        event.preventDefault();
        selectItem(results.value[activeIndex.value]);
    }
}

async function scrollToActive(): Promise<void> {
    await nextTick();
    listRef.value?.querySelector<HTMLElement>('[data-active="true"]')?.scrollIntoView({ block: 'nearest' });
}
</script>

<template>
    <div class="relative">
        <div class="flex items-center gap-1.5">
            <div class="relative flex-1">
                <input
                    ref="inputRef"
                    v-model="search"
                    type="text"
                    class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm focus:border-indigo-600 focus:ring-indigo-600"
                    :placeholder="placeholder"
                    role="combobox"
                    aria-autocomplete="list"
                    :aria-expanded="isOpen && results.length > 0"
                    aria-haspopup="listbox"
                    @input="onInput"
                    @keydown="onKeydown"
                    @focus="onFocus"
                    @blur="closeDropdown"
                />
                <button
                    v-if="selectedItem"
                    type="button"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                    aria-label="Clear selection"
                    @mousedown.prevent="clearSelection"
                >
                    &times;
                </button>
                <span
                    v-if="isLoading"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-xs text-gray-400"
                    aria-live="polite"
                    >...</span
                >
            </div>
        </div>

        <ul
            v-if="isOpen && results.length > 0"
            ref="listRef"
            role="listbox"
            class="absolute z-10 mt-1 max-h-48 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg"
        >
            <li
                v-for="(item, index) in results"
                :key="item.id"
                role="option"
                :aria-selected="index === activeIndex"
                :data-active="index === activeIndex"
                class="cursor-pointer px-3 py-2 text-sm transition-colors"
                :class="index === activeIndex ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50'"
                @mousedown.prevent="selectItem(item)"
            >
                {{ item.name }}
                <span class="ml-1 text-xs text-gray-400">({{ item.email }})</span>
            </li>
        </ul>

        <p
            v-if="isOpen && results.length === 0 && !isLoading && search.length >= minChars"
            class="absolute z-10 mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-500 shadow-lg"
        >
            {{ noResultsText }}
        </p>

        <input v-if="selectedItem" type="hidden" :name="inputName" :value="selectedItem.id" />
    </div>
</template>
