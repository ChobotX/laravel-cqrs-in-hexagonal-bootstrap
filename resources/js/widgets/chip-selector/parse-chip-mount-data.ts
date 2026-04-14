import { isRecord } from '../../shared/type-guards/is-record';
import type { ChipOption } from './ChipSelector.vue';

function isChipOption(value: unknown): value is ChipOption {
    if (!isRecord(value) || typeof value.id !== 'string' || typeof value.name !== 'string') {
        return false;
    }
    if ('badge' in value && value.badge !== undefined && typeof value.badge !== 'string') {
        return false;
    }
    return true;
}

export function parseChipOptionsJson(json: string): ChipOption[] {
    const raw: unknown = JSON.parse(json);
    if (!Array.isArray(raw)) {
        return [];
    }
    return raw.filter(isChipOption);
}

export function parseStringArrayJson(json: string): string[] {
    const raw: unknown = JSON.parse(json);
    if (!Array.isArray(raw)) {
        return [];
    }
    return raw.filter((item): item is string => typeof item === 'string');
}
