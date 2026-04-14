import { isRecord } from '../../shared/type-guards/is-record';
import type { ShareableTeam } from './composables/types';

function isShareableTeam(value: unknown): value is ShareableTeam {
    return isRecord(value) && typeof value.id === 'string' && typeof value.name === 'string';
}

export function parseShareableTeamsJson(json: string): ShareableTeam[] {
    const raw: unknown = JSON.parse(json);
    if (!Array.isArray(raw)) {
        return [];
    }
    return raw.filter(isShareableTeam);
}
