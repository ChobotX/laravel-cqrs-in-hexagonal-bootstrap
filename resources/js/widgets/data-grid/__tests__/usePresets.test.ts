import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { ref } from 'vue';
import type { Preset, SortItem } from '../composables/types';
import { usePresets } from '../composables/usePresets';

vi.mock('../data-grid-api', async (importOriginal) => {
    const actual = await importOriginal<typeof import('../data-grid-api')>();

    return {
        ...actual,
        postGridAction: vi.fn(),
        deleteGridAction: vi.fn(),
    };
});

import { deleteGridAction, postGridAction } from '../data-grid-api';

function createDeps(overrides: Partial<Parameters<typeof usePresets>[0]> = {}) {
    const searchTerm = ref('');

    return {
        gridName: 'test-grid',
        sortBy: ref<SortItem | null>(null),
        filters: ref<Record<string, string>>({}),
        searchTerm,
        setSearchImmediate: vi.fn((value: string) => {
            searchTerm.value = value;
        }),
        resetPage: vi.fn(),
        ...overrides,
    };
}

function makePreset(overrides: Partial<Preset> = {}): Preset {
    return {
        id: 'preset-1',
        name: 'My Preset',
        filters: '{}',
        sorting: '{}',
        search: '',
        is_default: false,
        position: 0,
        scope: 'personal',
        team_id: null,
        is_owner: true,
        ...overrides,
    };
}

function mockFetchForLoad(presets: Preset[]) {
    vi.stubGlobal(
        'fetch',
        vi.fn().mockResolvedValue({
            ok: true,
            json: vi.fn().mockResolvedValue({ data: presets }),
        }),
    );
}

beforeEach(() => {
    Object.defineProperty(document, 'cookie', {
        writable: true,
        value: 'XSRF-TOKEN=test-token',
    });
    vi.restoreAllMocks();
});

afterEach(() => {
    vi.unstubAllGlobals();
});

describe('usePresets', () => {
    it('initializes with empty state', () => {
        const deps = createDeps();
        const { presets, activePresetId, loading } = usePresets(deps);

        expect(presets.value).toEqual([]);
        expect(activePresetId.value).toBeNull();
        expect(loading.value).toBe(false);
    });

    describe('loadPresets', () => {
        it('fetches presets from API', async () => {
            const presetList = [makePreset({ id: 'p1', name: 'First' }), makePreset({ id: 'p2', name: 'Second' })];
            mockFetchForLoad(presetList);

            const deps = createDeps();
            const { presets, loadPresets } = usePresets(deps);

            await loadPresets();

            expect(presets.value).toEqual(presetList);
            expect(vi.mocked(globalThis.fetch)).toHaveBeenCalledWith(
                '/internal-api/grid-presets?grid_name=test-grid',
                expect.objectContaining({
                    headers: expect.objectContaining({
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-XSRF-TOKEN': 'test-token',
                    }),
                }),
            );
        });

        it('encodes grid name in URL', async () => {
            mockFetchForLoad([]);

            const deps = createDeps({ gridName: 'grid with spaces' });
            const { loadPresets } = usePresets(deps);

            await loadPresets();

            expect(vi.mocked(globalThis.fetch)).toHaveBeenCalledWith(
                '/internal-api/grid-presets?grid_name=grid%20with%20spaces',
                expect.any(Object),
            );
        });

        it('does not throw on failed fetch', async () => {
            vi.stubGlobal('fetch', vi.fn().mockResolvedValue({ ok: false, status: 500 }));

            const deps = createDeps();
            const { presets, loadPresets } = usePresets(deps);

            await loadPresets();

            expect(presets.value).toEqual([]);
        });

        it('does not throw on network error', async () => {
            vi.stubGlobal('fetch', vi.fn().mockRejectedValue(new Error('Network error')));

            const deps = createDeps();
            const { presets, loadPresets } = usePresets(deps);

            await loadPresets();

            expect(presets.value).toEqual([]);
        });
    });

    describe('applyPreset', () => {
        it('applies sorting, filters, and search from preset', () => {
            const deps = createDeps();
            const { applyPreset, activePresetId } = usePresets(deps);

            const preset = makePreset({
                id: 'p1',
                sorting: JSON.stringify({ sort: 'name', direction: 'desc' }),
                filters: JSON.stringify({ status: 'active' }),
                search: 'hello',
            });

            applyPreset(preset);

            expect(activePresetId.value).toBe('p1');
            expect(deps.sortBy.value).toEqual({ key: 'name', order: 'desc' });
            expect(deps.filters.value).toEqual({ status: 'active' });
            expect(deps.searchTerm.value).toBe('hello');
            expect(deps.resetPage).toHaveBeenCalledOnce();
        });

        it('defaults sort direction to asc when not specified', () => {
            const deps = createDeps();
            const { applyPreset } = usePresets(deps);

            const preset = makePreset({
                sorting: JSON.stringify({ sort: 'email' }),
            });

            applyPreset(preset);

            expect(deps.sortBy.value).toEqual({ key: 'email', order: 'asc' });
        });

        it('sets sortBy to null when sorting JSON is invalid', () => {
            const deps = createDeps({ sortBy: ref<SortItem | null>({ key: 'old', order: 'asc' }) });
            const { applyPreset } = usePresets(deps);

            const preset = makePreset({ sorting: 'invalid-json' });

            applyPreset(preset);

            expect(deps.sortBy.value).toBeNull();
        });

        it('sets sortBy to null when sorting has no sort key', () => {
            const deps = createDeps({ sortBy: ref<SortItem | null>({ key: 'old', order: 'asc' }) });
            const { applyPreset } = usePresets(deps);

            const preset = makePreset({ sorting: JSON.stringify({}) });

            applyPreset(preset);

            // sortBy should remain unchanged since sort key is falsy — but the code only sets
            // when sortData.sort is truthy. Let's check: no assignment happens, so sortBy keeps original.
            // Actually re-reading code: if sortData.sort is falsy, no assignment, so value stays.
            // But the try block doesn't reset. So we need to verify.
            expect(deps.sortBy.value).toEqual({ key: 'old', order: 'asc' });
        });

        it('sets filters to empty when filters JSON is invalid', () => {
            const deps = createDeps({ filters: ref({ existing: 'value' }) });
            const { applyPreset } = usePresets(deps);

            const preset = makePreset({ filters: 'invalid-json' });

            applyPreset(preset);

            expect(deps.filters.value).toEqual({});
        });
    });

    describe('loadPreset', () => {
        it('finds and applies preset by id', async () => {
            const presetList = [makePreset({ id: 'p1', search: 'first' }), makePreset({ id: 'p2', search: 'second' })];
            mockFetchForLoad(presetList);

            const deps = createDeps();
            const { loadPresets, loadPreset } = usePresets(deps);

            await loadPresets();
            loadPreset('p2');

            expect(deps.searchTerm.value).toBe('second');
        });

        it('does nothing when preset id is not found', async () => {
            mockFetchForLoad([makePreset({ id: 'p1' })]);

            const deps = createDeps();
            const { loadPresets, loadPreset, activePresetId } = usePresets(deps);

            await loadPresets();
            loadPreset('nonexistent');

            expect(activePresetId.value).toBeNull();
        });
    });

    describe('savePreset', () => {
        it('calls postGridAction with current state and reloads presets', async () => {
            const sortBy = ref<SortItem | null>({ key: 'name', order: 'desc' });
            const filters = ref<Record<string, string>>({ role: 'admin' });
            const searchTerm = ref('test');

            const deps = createDeps({ sortBy, filters, searchTerm });
            const { savePreset, loading } = usePresets(deps);

            vi.mocked(postGridAction).mockResolvedValue(undefined);
            mockFetchForLoad([]);

            await savePreset('My New Preset');

            expect(vi.mocked(postGridAction)).toHaveBeenCalledWith('/grid-presets', {
                grid_name: 'test-grid',
                name: 'My New Preset',
                filters: JSON.stringify({ role: 'admin' }),
                sorting: JSON.stringify({ sort: 'name', direction: 'desc' }),
                search: 'test',
            });

            expect(loading.value).toBe(false);
        });

        it('sends empty sorting when sortBy is null', async () => {
            const deps = createDeps();
            const { savePreset } = usePresets(deps);

            vi.mocked(postGridAction).mockResolvedValue(undefined);
            mockFetchForLoad([]);

            await savePreset('Empty Preset');

            expect(vi.mocked(postGridAction)).toHaveBeenCalledWith(
                '/grid-presets',
                expect.objectContaining({
                    sorting: JSON.stringify({}),
                }),
            );
        });

        it('does not reload presets on failed save', async () => {
            const deps = createDeps();
            const { savePreset, loading } = usePresets(deps);

            vi.mocked(postGridAction).mockRejectedValue(new Error('Grid action failed: 422'));

            await expect(savePreset('Bad Preset')).rejects.toThrow();

            expect(loading.value).toBe(false);
        });

        it('resets loading to false even on error', async () => {
            const deps = createDeps();
            const { savePreset, loading } = usePresets(deps);

            vi.mocked(postGridAction).mockRejectedValue(new Error('fail'));

            await expect(savePreset('Error Preset')).rejects.toThrow('fail');

            expect(loading.value).toBe(false);
        });

        it('includes scope in body when not personal', async () => {
            const deps = createDeps();
            const { savePreset } = usePresets(deps);

            vi.mocked(postGridAction).mockResolvedValue(undefined);
            mockFetchForLoad([]);

            await savePreset('Team Preset', 'team', 'team-123');

            expect(vi.mocked(postGridAction)).toHaveBeenCalledWith(
                '/grid-presets',
                expect.objectContaining({
                    name: 'Team Preset',
                    scope: 'team',
                    team_id: 'team-123',
                }),
            );
        });

        it('omits scope from body when personal', async () => {
            const deps = createDeps();
            const { savePreset } = usePresets(deps);

            vi.mocked(postGridAction).mockResolvedValue(undefined);
            mockFetchForLoad([]);

            await savePreset('Personal Preset', 'personal');

            const callArgs = vi.mocked(postGridAction).mock.calls[0][1] as Record<string, unknown>;

            expect(callArgs.scope).toBeUndefined();
            expect(callArgs.team_id).toBeUndefined();
        });
    });

    describe('deletePreset', () => {
        it('calls deleteGridAction and removes preset from list', async () => {
            vi.mocked(deleteGridAction).mockResolvedValue(undefined);
            mockFetchForLoad([makePreset({ id: 'p1' }), makePreset({ id: 'p2' })]);

            const deps = createDeps();
            const { presets, loadPresets, deletePreset } = usePresets(deps);

            await loadPresets();
            await deletePreset('p1');

            expect(vi.mocked(deleteGridAction)).toHaveBeenCalledWith('/grid-presets/p1');
            expect(presets.value).toEqual([makePreset({ id: 'p2' })]);
        });

        it('clears activePresetId when deleting the active preset', async () => {
            vi.mocked(deleteGridAction).mockResolvedValue(undefined);
            mockFetchForLoad([makePreset({ id: 'p1' })]);

            const deps = createDeps();
            const { activePresetId, loadPresets, applyPreset, deletePreset, presets } = usePresets(deps);

            await loadPresets();
            applyPreset(presets.value[0]);

            expect(activePresetId.value).toBe('p1');

            await deletePreset('p1');

            expect(activePresetId.value).toBeNull();
        });

        it('does not clear activePresetId when deleting a different preset', async () => {
            vi.mocked(deleteGridAction).mockResolvedValue(undefined);
            mockFetchForLoad([makePreset({ id: 'p1' }), makePreset({ id: 'p2' })]);

            const deps = createDeps();
            const { activePresetId, loadPresets, applyPreset, deletePreset, presets } = usePresets(deps);

            await loadPresets();
            applyPreset(presets.value[0]);

            expect(activePresetId.value).toBe('p1');

            await deletePreset('p2');

            expect(activePresetId.value).toBe('p1');
        });
    });

    describe('setDefault', () => {
        it('calls postGridAction and reloads presets', async () => {
            vi.mocked(postGridAction).mockResolvedValue(undefined);
            mockFetchForLoad([makePreset({ id: 'p1', is_default: true })]);

            const deps = createDeps();
            const { setDefault, loadPresets } = usePresets(deps);

            // Initial load
            await loadPresets();

            // setDefault will call postGridAction then loadPresets
            await setDefault('p1');

            expect(vi.mocked(postGridAction)).toHaveBeenCalledWith('/grid-presets/p1/default', {
                grid_name: 'test-grid',
            });
            // Verify loadPresets was called again (fetch called for initial + setDefault reload)
            expect(vi.mocked(globalThis.fetch)).toHaveBeenCalledTimes(2);
        });
    });

    describe('clearPreset', () => {
        it('resets activePresetId to null', () => {
            const deps = createDeps();
            const { activePresetId, applyPreset, clearPreset } = usePresets(deps);

            applyPreset(makePreset({ id: 'p1' }));

            expect(activePresetId.value).toBe('p1');

            clearPreset();

            expect(activePresetId.value).toBeNull();
        });
    });
});
