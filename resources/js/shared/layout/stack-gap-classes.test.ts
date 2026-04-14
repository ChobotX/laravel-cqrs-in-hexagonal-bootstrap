import { describe, expect, it } from 'vitest';
import expected from '../../../shared/ui-stack-gaps.json';
import { parseStackGapMapFromUnknown, STACK_GAP_CLASS_BY_TOKEN } from './stack-gap-classes';

describe('stack-gap-classes', () => {
    it('matches canonical ui-stack-gaps.json', () => {
        expect({ ...STACK_GAP_CLASS_BY_TOKEN }).toEqual(expected.stackGap);
    });

    it('parseStackGapMapFromUnknown rejects invalid root', () => {
        expect(() => parseStackGapMapFromUnknown(null)).toThrow('invalid root');
        expect(() => parseStackGapMapFromUnknown('nope')).toThrow('invalid root');
    });

    it('parseStackGapMapFromUnknown rejects missing stackGap', () => {
        expect(() => parseStackGapMapFromUnknown({})).toThrow('missing stackGap object');
        expect(() => parseStackGapMapFromUnknown({ stackGap: null })).toThrow('missing stackGap object');
        expect(() => parseStackGapMapFromUnknown({ stackGap: 'bad' })).toThrow('missing stackGap object');
    });

    it('parseStackGapMapFromUnknown rejects incomplete map', () => {
        expect(() =>
            parseStackGapMapFromUnknown({
                stackGap: { default: 'gap-8' },
            }),
        ).toThrow('incomplete or invalid stackGap map');
    });

    it('parseStackGapMapFromUnknown rejects empty class string', () => {
        expect(() =>
            parseStackGapMapFromUnknown({
                stackGap: { ...expected.stackGap, default: '' },
            }),
        ).toThrow('incomplete or invalid stackGap map');
    });

    it('parseStackGapMapFromUnknown ignores keys that are not stack gap tokens', () => {
        const map = parseStackGapMapFromUnknown({
            stackGap: { ...expected.stackGap, notAToken: 'gap-99' },
        });
        expect({ ...map }).toEqual(expected.stackGap);
    });

    it('parseStackGapMapFromUnknown rejects non-string class for a known token', () => {
        expect(() =>
            parseStackGapMapFromUnknown({
                stackGap: Object.assign({}, expected.stackGap, { sm: [] }),
            }),
        ).toThrow('incomplete or invalid stackGap map');
    });
});
