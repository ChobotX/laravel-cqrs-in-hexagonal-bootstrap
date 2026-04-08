import { type Ref, ref } from 'vue';
import { deleteGridAction, getCsrfToken, postGridAction } from '../data-grid-api';
import type { Preset, SortItem } from './types';

interface PresetDeps {
    gridName: string;
    sortBy: Ref<SortItem | null>;
    filters: Ref<Record<string, string>>;
    searchTerm: Ref<string>;
    setSearchImmediate: (value: string) => void;
    resetPage: () => void;
}

export interface PresetReturn {
    presets: Ref<Preset[]>;
    activePresetId: Ref<string | null>;
    loading: Ref<boolean>;
    loadPresets: () => Promise<void>;
    loadPreset: (id: string) => void;
    applyPreset: (preset: Preset) => void;
    savePreset: (name: string, scope?: string, teamId?: string) => Promise<void>;
    deletePreset: (id: string) => Promise<void>;
    setDefault: (id: string) => Promise<void>;
    clearPreset: () => void;
}

export function usePresets(deps: PresetDeps): PresetReturn {
    const presets = ref<Preset[]>([]);
    const activePresetId = ref<string | null>(null);
    const loading = ref(false);

    async function loadPresets(): Promise<void> {
        try {
            const response = await fetch(`/internal-api/grid-presets?grid_name=${encodeURIComponent(deps.gridName)}`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
            });

            if (response.ok) {
                const result = (await response.json()) as { data: Preset[] };
                presets.value = result.data;
            }
        } catch {
            // @silent — presets are non-critical, failure should not block the grid
        }
    }

    function applyPreset(preset: Preset): void {
        activePresetId.value = preset.id;

        try {
            const sortData = JSON.parse(preset.sorting) as Record<string, string>;

            if (sortData.sort) {
                deps.sortBy.value = {
                    key: sortData.sort,
                    order: (sortData.direction as 'asc' | 'desc') ?? 'asc',
                };
            }
        } catch {
            // @silent — invalid sorting JSON falls back to no sort
            deps.sortBy.value = null;
        }

        try {
            deps.filters.value = JSON.parse(preset.filters) as Record<string, string>;
        } catch {
            // @silent — invalid filters JSON falls back to empty filters
            deps.filters.value = {};
        }

        deps.setSearchImmediate(preset.search);
        deps.resetPage();
    }

    function loadPreset(id: string): void {
        const preset = presets.value.find((p) => p.id === id);

        if (preset) {
            applyPreset(preset);
        }
    }

    function buildSaveBody(name: string, scope?: string, teamId?: string): Record<string, unknown> {
        const body: Record<string, unknown> = {
            grid_name: deps.gridName,
            name,
            filters: JSON.stringify(deps.filters.value),
            sorting: JSON.stringify(
                deps.sortBy.value === null ? {} : { sort: deps.sortBy.value.key, direction: deps.sortBy.value.order },
            ),
            search: deps.searchTerm.value,
        };

        if (scope !== undefined && scope !== 'personal') {
            body.scope = scope;
        }

        if (teamId !== undefined) {
            body.team_id = teamId;
        }

        return body;
    }

    async function savePreset(name: string, scope?: string, teamId?: string): Promise<void> {
        loading.value = true;

        try {
            await postGridAction('/grid-presets', buildSaveBody(name, scope, teamId));
            await loadPresets();
        } finally {
            loading.value = false;
        }
    }

    async function deletePreset(id: string): Promise<void> {
        await deleteGridAction(`/grid-presets/${id}`);
        presets.value = presets.value.filter((p) => p.id !== id);

        if (activePresetId.value === id) {
            activePresetId.value = null;
        }
    }

    async function setDefault(id: string): Promise<void> {
        await postGridAction(`/grid-presets/${id}/default`, { grid_name: deps.gridName });
        await loadPresets();
    }

    function clearPreset(): void {
        activePresetId.value = null;
    }

    return {
        presets,
        activePresetId,
        loading,
        loadPresets,
        loadPreset,
        applyPreset,
        savePreset,
        deletePreset,
        setDefault,
        clearPreset,
    };
}
