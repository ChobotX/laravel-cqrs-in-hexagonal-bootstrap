import { describe, expect, it } from 'vitest';
import { isFetchResultUnknown } from './fetch-result-guard';

const validMeta = { current_page: 1, per_page: 15, total: 1, total_pages: 1 };

describe('isFetchResultUnknown', () => {
    it('rejects non-objects', () => {
        expect(isFetchResultUnknown(null)).toBe(false);
        expect(isFetchResultUnknown([])).toBe(false);
    });

    it('rejects when data is not an array', () => {
        expect(isFetchResultUnknown({ data: {}, meta: validMeta })).toBe(false);
    });

    it('rejects when meta is not a valid pagination object', () => {
        expect(isFetchResultUnknown({ data: [], meta: null })).toBe(false);
        expect(isFetchResultUnknown({ data: [], meta: { current_page: 1 } })).toBe(false);
    });

    it('accepts envelope with data array and meta', () => {
        expect(isFetchResultUnknown({ data: [], meta: validMeta, extra: 1 })).toBe(true);
    });
});
