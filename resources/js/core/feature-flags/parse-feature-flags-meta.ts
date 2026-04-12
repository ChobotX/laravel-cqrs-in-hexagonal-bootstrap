import { isRecord } from '../../shared/type-guards/is-record';

export type FlagState = { enabled: boolean; value: string };

function isFlagState(value: unknown): value is FlagState {
    return isRecord(value) && typeof value.enabled === 'boolean' && typeof value.value === 'string';
}

export function parseFeatureFlagsMetaJson(json: string): Record<string, FlagState> {
    const raw: unknown = JSON.parse(json);
    if (!isRecord(raw)) {
        return {};
    }
    const out: Record<string, FlagState> = {};
    for (const key of Object.keys(raw)) {
        const entry = raw[key];
        if (isFlagState(entry)) {
            out[key] = entry;
        }
    }
    return out;
}
