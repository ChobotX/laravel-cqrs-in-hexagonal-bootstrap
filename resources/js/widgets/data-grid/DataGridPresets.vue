<script setup lang="ts">
import { trans } from 'laravel-vue-i18n';
import { nextTick, onBeforeUnmount, ref, watch } from 'vue';
import { confirm } from '../../shared/dialog/dialog-queue';
import { resolvePosition } from '../../shared/tooltip/tooltip-position';
import type { Preset, ShareableTeam } from './composables/types';

const props = defineProps<{
    presets: Preset[];
    activePresetId: string | null;
    canShareTeam?: boolean;
    canShareGlobal?: boolean;
    shareableTeams?: ShareableTeam[];
}>();

const emit = defineEmits<{
    'load-preset': [id: string];
    'save-preset': [name: string, scope: string, teamId: string | undefined];
    'delete-preset': [id: string];
    'clear-preset': [];
}>();

async function handleDelete(event: MouseEvent, preset: Preset): Promise<void> {
    event.stopPropagation();

    const isShared = preset.scope !== 'personal';
    const message = isShared
        ? `${trans('messages.grid.presets.delete_confirm_message', { name: preset.name })}\n\n${trans('messages.grid.presets.delete_shared_warning')}`
        : trans('messages.grid.presets.delete_confirm_message', { name: preset.name });

    const confirmed = await confirm({
        title: trans('messages.grid.presets.delete_confirm_title'),
        message,
    });

    if (confirmed) {
        emit('delete-preset', preset.id);
    }
}

const showSaveForm = ref(false);
const presetName = ref('');
const selectedScope = ref('personal');
const selectedTeamId = ref('');
const toggleRef = ref<HTMLElement | null>(null);
const dropdownRef = ref<HTMLElement | null>(null);
const DROPDOWN_GAP = 4;
const dropdownStyle = ref<{ top: string; left: string }>({ top: '0px', left: '0px' });

watch(selectedScope, (scope) => {
    if (scope === 'team' && props.shareableTeams?.length === 1) {
        selectedTeamId.value = props.shareableTeams[0].id;
    } else {
        selectedTeamId.value = '';
    }
});

function positionDropdown(): void {
    if (!toggleRef.value || !dropdownRef.value) {
        return;
    }

    const triggerRect = toggleRef.value.getBoundingClientRect();
    const dropdownRect = dropdownRef.value.getBoundingClientRect();
    const resolved = resolvePosition('bottom', triggerRect, dropdownRect);
    const above = resolved.startsWith('top');

    dropdownStyle.value = {
        top: above
            ? `${window.scrollY + triggerRect.top - DROPDOWN_GAP - dropdownRect.height}px`
            : `${window.scrollY + triggerRect.bottom + DROPDOWN_GAP}px`,
        left: `${window.scrollX + triggerRect.left}px`,
    };
}

async function toggleForm(): Promise<void> {
    showSaveForm.value = !showSaveForm.value;

    if (showSaveForm.value) {
        selectedScope.value = 'personal';
        selectedTeamId.value = '';
        await nextTick();
        positionDropdown();
    }
}

function onDocumentClick(event: MouseEvent): void {
    const target = event.target as Node;

    if (toggleRef.value?.contains(target) || dropdownRef.value?.contains(target)) {
        return;
    }

    showSaveForm.value = false;
}

function onDocumentKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        showSaveForm.value = false;
    }
}

watch(showSaveForm, (open) => {
    if (open) {
        document.addEventListener('click', onDocumentClick, { capture: true });
        document.addEventListener('keydown', onDocumentKeydown);
    } else {
        document.removeEventListener('click', onDocumentClick, { capture: true });
        document.removeEventListener('keydown', onDocumentKeydown);
    }
});

onBeforeUnmount(() => {
    document.removeEventListener('click', onDocumentClick, { capture: true });
    document.removeEventListener('keydown', onDocumentKeydown);
});

function handleSave(): void {
    if (presetName.value.trim() === '') {
        return;
    }

    const teamId = selectedScope.value === 'team' ? selectedTeamId.value || undefined : undefined;

    emit('save-preset', presetName.value.trim(), selectedScope.value, teamId);
    presetName.value = '';
    selectedScope.value = 'personal';
    selectedTeamId.value = '';
    showSaveForm.value = false;
}

function scopeBadgeLabel(scope: string): string {
    if (scope === 'team') {
        return trans('messages.grid.presets.scope_badge_team');
    }

    if (scope === 'global') {
        return trans('messages.grid.presets.scope_badge_global');
    }

    return '';
}
</script>

<template>
    <div class="flex flex-wrap items-center gap-2" data-testid="grid-presets">
        <button
            :class="[
                'inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-medium transition-colors',
                activePresetId === null
                    ? 'bg-indigo-100 text-indigo-700'
                    : 'cursor-pointer text-gray-500 hover:bg-gray-100 hover:text-gray-700',
            ]"
            type="button"
            data-testid="grid-preset-all"
            @click="emit('clear-preset')"
        >
            {{ trans('messages.grid.presets.all') }}
        </button>

        <div v-for="preset in presets" :key="preset.id" class="group relative">
            <button
                :class="[
                    'inline-flex items-center gap-1 rounded-lg py-1.5 pl-3 pr-7 text-sm font-medium transition-colors',
                    activePresetId === preset.id
                        ? 'bg-indigo-100 text-indigo-700'
                        : 'cursor-pointer text-gray-500 hover:bg-gray-100 hover:text-gray-700',
                ]"
                type="button"
                :data-testid="`grid-preset-${preset.id}`"
                @click="emit('load-preset', preset.id)"
            >
                <!-- Team icon -->
                <svg
                    v-if="preset.scope === 'team'"
                    class="size-3.5 shrink-0 text-blue-500"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    :aria-label="scopeBadgeLabel('team')"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
                <!-- Globe icon -->
                <svg
                    v-if="preset.scope === 'global'"
                    class="size-3.5 shrink-0 text-emerald-500"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    :aria-label="scopeBadgeLabel('global')"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                </svg>
                {{ preset.name }}
                <span v-if="preset.is_default" class="ml-1 text-xs text-indigo-400">&bull;</span>
            </button>
            <button
                class="absolute right-1 top-1/2 -translate-y-1/2 cursor-pointer rounded p-0.5 text-gray-400 opacity-0 transition-opacity hover:text-red-500 group-hover:opacity-100"
                type="button"
                :aria-label="trans('messages.grid.presets.delete') + ' ' + preset.name"
                :data-testid="`grid-preset-delete-${preset.id}`"
                @click="handleDelete($event, preset)"
            >
                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div>
            <button
                ref="toggleRef"
                class="inline-flex cursor-pointer items-center gap-1 rounded-lg border border-dashed border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-500 transition-colors hover:border-gray-400 hover:text-gray-700"
                type="button"
                data-testid="grid-preset-save-toggle"
                @click="toggleForm"
            >
                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ trans('messages.grid.presets.save_as') }}
            </button>

            <Teleport to="body">
                <div
                    v-if="showSaveForm"
                    ref="dropdownRef"
                    class="absolute z-50 w-72 rounded-lg border border-gray-200 bg-white p-3 shadow-lg"
                    :style="dropdownStyle"
                    data-testid="grid-preset-save-form"
                >
                    <form @submit.prevent="handleSave">
                        <label class="mb-1 block text-xs font-medium text-gray-600" for="preset-name">
                            {{ trans('messages.grid.presets.name_label') }}
                        </label>
                        <input
                            id="preset-name"
                            v-model="presetName"
                            class="mb-2 w-full rounded-md border border-gray-200 px-2.5 py-1.5 text-sm focus:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                            type="text"
                            :placeholder="trans('messages.grid.presets.name_placeholder')"
                            required
                            maxlength="100"
                            data-testid="grid-preset-name-input"
                        />

                        <!-- Scope selector (only when user has share permissions) -->
                        <template v-if="canShareTeam || canShareGlobal">
                            <label class="mb-1 block text-xs font-medium text-gray-600" for="preset-scope">
                                {{ trans('messages.grid.presets.share_label') }}
                            </label>
                            <select
                                id="preset-scope"
                                v-model="selectedScope"
                                class="mb-2 w-full cursor-pointer rounded-md border border-gray-200 px-2.5 py-1.5 text-sm focus:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                                data-testid="grid-preset-scope-select"
                            >
                                <option value="personal">{{ trans('messages.grid.presets.scope_personal') }}</option>
                                <option v-if="canShareTeam" value="team">{{ trans('messages.grid.presets.scope_team') }}</option>
                                <option v-if="canShareGlobal" value="global">{{ trans('messages.grid.presets.scope_global') }}</option>
                            </select>

                            <!-- Team selector (when scope is team and multiple teams available) -->
                            <template v-if="selectedScope === 'team' && shareableTeams && shareableTeams.length > 1">
                                <label class="mb-1 block text-xs font-medium text-gray-600" for="preset-team">
                                    {{ trans('messages.teams.name') }}
                                </label>
                                <select
                                    id="preset-team"
                                    v-model="selectedTeamId"
                                    class="mb-2 w-full cursor-pointer rounded-md border border-gray-200 px-2.5 py-1.5 text-sm focus:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                                    required
                                    data-testid="grid-preset-team-select"
                                >
                                    <option value="" disabled>—</option>
                                    <option v-for="team in shareableTeams" :key="team.id" :value="team.id">{{ team.name }}</option>
                                </select>
                            </template>
                        </template>

                        <button
                            class="w-full cursor-pointer rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white transition-colors hover:bg-indigo-700"
                            type="submit"
                            data-testid="grid-preset-save-btn"
                        >
                            {{ trans('messages.grid.presets.save') }}
                        </button>
                    </form>
                </div>
            </Teleport>
        </div>
    </div>
</template>
