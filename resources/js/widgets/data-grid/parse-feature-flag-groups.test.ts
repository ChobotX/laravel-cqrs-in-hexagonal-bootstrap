import { describe, expect, it } from 'vitest';
import { parseFeatureFlagGroupsJson } from './parse-feature-flag-groups';

describe('parseFeatureFlagGroupsJson', () => {
    it('returns empty array when JSON is not an array', () => {
        expect(parseFeatureFlagGroupsJson('{}')).toEqual([]);
    });

    it('filters to valid options only', () => {
        const json = JSON.stringify([
            { key: 'a', label: 'A' },
            { key: 1, label: 'B' },
            { key: 'c', label: 'C' },
        ]);
        expect(parseFeatureFlagGroupsJson(json)).toEqual([
            { key: 'a', label: 'A' },
            { key: 'c', label: 'C' },
        ]);
    });
});
