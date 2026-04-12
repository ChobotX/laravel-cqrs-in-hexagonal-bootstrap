import { isRecord } from '../../shared/type-guards/is-record';

export interface FeatureFlagGroupOption {
    key: string;
    label: string;
}

function isFeatureFlagGroupOption(value: unknown): value is FeatureFlagGroupOption {
    return isRecord(value) && typeof value['key'] === 'string' && typeof value['label'] === 'string';
}

export function parseFeatureFlagGroupsJson(json: string): FeatureFlagGroupOption[] {
    const raw: unknown = JSON.parse(json);
    if (!Array.isArray(raw)) {
        return [];
    }
    return raw.filter(isFeatureFlagGroupOption);
}
