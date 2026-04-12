import { describe, expect, it } from 'vitest';
import { parseShareableTeamsJson } from './parse-shareable-teams';

describe('parseShareableTeamsJson', () => {
    it('returns empty array when JSON is not an array', () => {
        expect(parseShareableTeamsJson('null')).toEqual([]);
    });

    it('filters to valid teams only', () => {
        const json = JSON.stringify([
            { id: '1', name: 'Alpha' },
            { id: 2, name: 'Beta' },
        ]);
        expect(parseShareableTeamsJson(json)).toEqual([{ id: '1', name: 'Alpha' }]);
    });
});
