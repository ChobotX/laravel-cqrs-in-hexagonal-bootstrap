<script setup lang="ts">
import { computed, nextTick, ref, watch } from 'vue';

interface RoleOption {
    id: string;
    name: string;
    isSystem: boolean;
}

const props = defineProps<{
    availableRoles: RoleOption[];
    selectedRoleIds: string[];
}>();

const search = ref('');
const isOpen = ref(false);
const activeIndex = ref(-1);
const selected = ref<Set<string>>(new Set(props.selectedRoleIds));
const inputRef = ref<HTMLInputElement | null>(null);
const listRef = ref<HTMLUListElement | null>(null);

const filteredRoles = computed(() =>
    props.availableRoles.filter(
        (r) => !selected.value.has(r.id) && r.name.toLowerCase().includes(search.value.toLowerCase()),
    ),
);

const selectedRoles = computed(() => props.availableRoles.filter((r) => selected.value.has(r.id)));

watch(filteredRoles, () => {
    activeIndex.value = -1;
});

function openDropdown(): void {
    isOpen.value = true;
}

function closeDropdown(): void {
    isOpen.value = false;
    activeIndex.value = -1;
}

function selectRole(id: string): void {
    selected.value = new Set([...selected.value, id]);
    search.value = '';
    inputRef.value?.focus();
}

function removeRole(id: string): void {
    const next = new Set(selected.value);
    next.delete(id);
    selected.value = next;
}

function handleEscapeOrBackspace(event: KeyboardEvent): boolean {
    if (event.key === 'Escape') {
        closeDropdown();
        return true;
    }

    if (event.key === 'Backspace' && search.value === '' && selected.value.size > 0) {
        const ids = [...selected.value];
        removeRole(ids[ids.length - 1]);
        return true;
    }

    return false;
}

function handleArrowNavigation(event: KeyboardEvent): void {
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        activeIndex.value = Math.min(activeIndex.value + 1, filteredRoles.value.length - 1);
        void scrollToActive();
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        activeIndex.value = Math.max(activeIndex.value - 1, 0);
        void scrollToActive();
    } else if (event.key === 'Enter' && activeIndex.value >= 0) {
        event.preventDefault();
        selectRole(filteredRoles.value[activeIndex.value].id);
    }
}

function onKeydown(event: KeyboardEvent): void {
    if (handleEscapeOrBackspace(event)) {
        return;
    }

    if (isOpen.value && filteredRoles.value.length > 0) {
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
                v-for="role in selectedRoles"
                :key="role.id"
                class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-indigo-700/10"
            >
                {{ role.name }}
                <span
                    v-if="role.isSystem"
                    class="text-[10px] text-indigo-400"
                    >(System)</span
                >
                <button
                    type="button"
                    class="ml-0.5 text-indigo-400 hover:text-indigo-600"
                    :aria-label="`Remove ${role.name}`"
                    @click.stop="removeRole(role.id)"
                >
                    &times;
                </button>
            </span>
            <input
                ref="inputRef"
                v-model="search"
                type="text"
                class="flex-1 min-w-[120px] border-none p-0 text-sm focus:ring-0 outline-none"
                :placeholder="$t('messages.users.roles_search')"
                role="combobox"
                aria-autocomplete="list"
                :aria-expanded="isOpen && filteredRoles.length > 0"
                aria-haspopup="listbox"
                @focus="openDropdown"
                @blur="closeDropdown"
                @keydown="onKeydown"
            />
        </div>

        <ul
            v-if="isOpen && filteredRoles.length > 0"
            ref="listRef"
            role="listbox"
            class="absolute z-10 mt-1 max-h-48 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg"
        >
            <li
                v-for="(role, index) in filteredRoles"
                :key="role.id"
                role="option"
                :aria-selected="index === activeIndex"
                :data-active="index === activeIndex"
                class="cursor-pointer px-3 py-2 text-sm transition-colors"
                :class="index === activeIndex ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50'"
                @mousedown.prevent="selectRole(role.id)"
            >
                {{ role.name }}
                <span v-if="role.isSystem" class="ml-1 text-xs text-gray-400">(System)</span>
            </li>
        </ul>

        <input v-for="id in selected" :key="id" type="hidden" name="roles[]" :value="id" />
    </div>
</template>
