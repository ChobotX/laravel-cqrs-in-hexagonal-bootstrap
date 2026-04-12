import { isRecord } from '../../../shared/type-guards/is-record';
import type { Preset } from './types';

export function isPreset(value: unknown): value is Preset {
    if (!isRecord(value)) {
        return false;
    }
    const scope = value['scope'];
    const teamId = value['team_id'];
    return (
        typeof value['id'] === 'string' &&
        typeof value['name'] === 'string' &&
        typeof value['filters'] === 'string' &&
        typeof value['sorting'] === 'string' &&
        typeof value['search'] === 'string' &&
        typeof value['is_default'] === 'boolean' &&
        typeof value['position'] === 'number' &&
        (scope === 'personal' || scope === 'team' || scope === 'global') &&
        (teamId === null || typeof teamId === 'string') &&
        typeof value['is_owner'] === 'boolean'
    );
}

export function parseStringRecordFromJson(json: string): Record<string, string> {
    let raw: unknown;
    try {
        raw = JSON.parse(json);
    } catch {
        /* @silent Invalid JSON in preset filters/sorting string — empty map */
        return {};
    }
    if (!isRecord(raw)) {
        return {};
    }
    const out: Record<string, string> = {};
    for (const [key, val] of Object.entries(raw)) {
        if (typeof val === 'string') {
            out[key] = val;
        }
    }
    return out;
}

/** Same shape as {@link parseStringRecordFromJson}, but `null` means JSON syntax error (caller may treat differently from `{}`). */
export function tryParseStringRecordFromJson(json: string): Record<string, string> | null {
    let raw: unknown;
    try {
        raw = JSON.parse(json);
    } catch {
        /* @silent Invalid JSON — caller decides (e.g. clear sort state) */
        return null;
    }
    if (!isRecord(raw)) {
        return {};
    }
    const out: Record<string, string> = {};
    for (const [key, val] of Object.entries(raw)) {
        if (typeof val === 'string') {
            out[key] = val;
        }
    }
    return out;
}

export function parsePresetsListBody(value: unknown): Preset[] | null {
    if (!isRecord(value) || !Array.isArray(value['data'])) {
        return null;
    }
    return value['data'].filter(isPreset);
}
