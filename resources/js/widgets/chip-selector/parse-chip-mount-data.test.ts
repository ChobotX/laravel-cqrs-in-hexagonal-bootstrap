import { describe, expect, it } from 'vitest';
import { parseChipOptionsJson, parseStringArrayJson } from './parse-chip-mount-data';

describe('parseChipOptionsJson', () => {
    it('returns empty when JSON is not an array', () => {
        expect(parseChipOptionsJson('{}')).toEqual([]);
    });

    it('accepts id+name and optional string badge', () => {
        const json = JSON.stringify([
            { id: '1', name: 'One', badge: 'B' },
            { id: '2', name: 'Two' },
        ]);
        expect(parseChipOptionsJson(json)).toEqual([
            { id: '1', name: 'One', badge: 'B' },
            { id: '2', name: 'Two' },
        ]);
    });

    it('drops options with invalid badge type', () => {
        const json = JSON.stringify([{ id: '1', name: 'One', badge: 1 }]);
        expect(parseChipOptionsJson(json)).toEqual([]);
    });

    it('ignores non-object entries in the array', () => {
        expect(parseChipOptionsJson(JSON.stringify([null, { id: '1', name: 'Ok' }]))).toEqual([
            { id: '1', name: 'Ok' },
        ]);
    });
});

describe('parseStringArrayJson', () => {
    it('returns only string elements', () => {
        expect(parseStringArrayJson(JSON.stringify(['a', 1, 'b']))).toEqual(['a', 'b']);
    });

    it('returns empty when JSON is not an array', () => {
        expect(parseStringArrayJson('{}')).toEqual([]);
    });
});
