import { isRecord } from '../../shared/type-guards/is-record';
import type { FetchResult } from './composables/types';

function isFetchMeta(value: unknown): value is FetchResult<unknown>['meta'] {
    if (!isRecord(value)) {
        return false;
    }
    return (
        typeof value.current_page === 'number' &&
        typeof value.per_page === 'number' &&
        typeof value.total === 'number' &&
        typeof value.total_pages === 'number'
    );
}

/** Runtime shape check for grid JSON (`data` + `meta`; allows extra top-level keys). */
export function isFetchResultUnknown(value: unknown): value is FetchResult<unknown> & Record<string, unknown> {
    if (!isRecord(value)) {
        return false;
    }
    if (!Array.isArray(value.data)) {
        return false;
    }
    return isFetchMeta(value.meta);
}
