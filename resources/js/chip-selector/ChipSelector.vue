<script setup lang="ts">
import { computed, nextTick, ref, watch } from 'vue';

export interface ChipOption {
    id: string;
    name: string;
    badge?: string;
}

const props = withDefaults(
    defineProps<{
        options: ChipOption[];
        modelValue: string[];
        inputName?: string;
        placeholder?: string;
        noResultsText?: string;
    }>(),
    {
        inputName: 'items[]',
        placeholder: undefined,
        noResultsText: undefined,
    },
);

const emit = defineEmits<{
    'update:modelValue': [ids: string[]];
    focus: [];
    search: [term: string];
}>();

const search = ref('');
const isOpen = ref(false);
const activeIndex = ref(-1);
const inputRef = ref<HTMLInputElement | null>(null);
const listRef = ref<HTMLUListElement | null>(null);

const selected = computed(() => new Set(props.modelValue));

const filteredOptions = computed(() =>
    props.options.filter((o) => !selected.value.has(o.id) && o.name.toLowerCase().includes(search.value.toLowerCase())),
);

const selectedOptions = computed(() => props.options.filter((o) => selected.value.has(o.id)));

watch(filteredOptions, () => {
    activeIndex.value = -1;
});

function openDropdown(): void {
    isOpen.value = true;
    emit('focus');
}

function closeDropdown(): void {
    isOpen.value = false;
    activeIndex.value = -1;
}

function selectOption(id: string): void {
    emit('update:modelValue', [...props.modelValue, id]);
    search.value = '';
    inputRef.value?.focus();
}

function removeOption(id: string): void {
    emit(
        'update:modelValue',
        props.modelValue.filter((v) => v !== id),
    );
}

function onSearchInput(): void {
    emit('search', search.value);
}

function handleEscapeOrBackspace(event: KeyboardEvent): boolean {
    if (event.key === 'Escape') {
        closeDropdown();
        return true;
    }

    if (event.key === 'Backspace' && search.value === '' && props.modelValue.length > 0) {
        removeOption(props.modelValue[props.modelValue.length - 1]);
        return true;
    }

    return false;
}

function handleArrowNavigation(event: KeyboardEvent): void {
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        activeIndex.value = Math.min(activeIndex.value + 1, filteredOptions.value.length - 1);
        void scrollToActive();
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        activeIndex.value = Math.max(activeIndex.value - 1, 0);
        void scrollToActive();
    } else if (event.key === 'Enter' && activeIndex.value >= 0) {
        event.preventDefault();
        selectOption(filteredOptions.value[activeIndex.value].id);
    }
}

function onKeydown(event: KeyboardEvent): void {
    if (handleEscapeOrBackspace(event)) {
        return;
    }

    if (isOpen.value && filteredOptions.value.length > 0) {
        handleArrowNavigation(event);
    }
}

async function scrollToActive(): Promise<void> {
    await nextTick();
    listRef.value?.querySelector<HTMLElement>('[data-active="true"]')?.scrollIntoView({ block: 'nearest' });
}
</script>

<template>
    <div class="relative">
        <div
            class="flex flex-wrap items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus-within:border-indigo-600 focus-within:ring-2 focus-within:ring-indigo-600 min-h-[42px] cursor-text"
            @click="inputRef?.focus()"
        >
            <span
                v-for="option in selectedOptions"
                :key="option.id"
                class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10"
            >
                {{ option.name }}
                <span
                    v-if="option.badge"
                    class="text-[10px] text-indigo-400"
                    >({{ option.badge }})</span
                >
                <button
                    type="button"
                    class="ml-0.5 text-indigo-400 hover:text-indigo-600"
                    :aria-label="`Remove ${option.name}`"
                    @click.stop="removeOption(option.id)"
                >
                    &times;
                </button>
            </span>
            <input
                ref="inputRef"
                v-model="search"
                type="text"
                autocomplete="off"
                class="flex-1 min-w-[120px] border-none p-0 text-sm focus:ring-0 outline-none"
                :placeholder="placeholder ?? ''"
                role="combobox"
                aria-autocomplete="list"
                :aria-expanded="isOpen && filteredOptions.length > 0"
                aria-haspopup="listbox"
                @focus="openDropdown"
                @blur="closeDropdown"
                @keydown="onKeydown"
                @input="onSearchInput"
            />
        </div>

        <ul
            v-if="isOpen && filteredOptions.length > 0"
            ref="listRef"
            role="listbox"
            class="absolute z-10 mt-1 max-h-48 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg"
        >
            <li
                v-for="(option, index) in filteredOptions"
                :key="option.id"
                role="option"
                :aria-selected="index === activeIndex"
                :data-active="index === activeIndex"
                class="cursor-pointer px-3 py-2 text-sm transition-colors"
                :class="index === activeIndex ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50'"
                @mousedown.prevent="selectOption(option.id)"
            >
                {{ option.name }}
                <span v-if="option.badge" class="ml-1 text-xs text-gray-400">({{ option.badge }})</span>
            </li>
        </ul>

        <p
            v-if="isOpen && filteredOptions.length === 0 && search !== '' && noResultsText"
            class="absolute z-10 mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-500 shadow-lg"
        >
            {{ noResultsText }}
        </p>

        <input v-for="id in modelValue" :key="id" type="hidden" :name="inputName" :value="id" />
    </div>
</template>
