import { describe, expect, it } from 'vitest';
import { parseFeatureFlagsMetaJson } from './parse-feature-flags-meta';

describe('parseFeatureFlagsMetaJson', () => {
    it('returns empty object when JSON root is not a record', () => {
        expect(parseFeatureFlagsMetaJson('null')).toEqual({});
        expect(parseFeatureFlagsMetaJson('42')).toEqual({});
    });

    it('keeps only entries matching FlagState', () => {
        const json = JSON.stringify({
            good: { enabled: true, value: 'x' },
            bad: { enabled: 'no', value: 'y' },
        });
        expect(parseFeatureFlagsMetaJson(json)).toEqual({ good: { enabled: true, value: 'x' } });
    });
});
