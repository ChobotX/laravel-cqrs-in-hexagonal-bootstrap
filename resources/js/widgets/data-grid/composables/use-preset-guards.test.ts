import { describe, expect, it } from 'vitest';
import {
    isPreset,
    parsePresetsListBody,
    parseStringRecordFromJson,
    tryParseStringRecordFromJson,
} from './use-preset-guards';

const validPreset = {
    id: 'p1',
    name: 'N',
    filters: '{}',
    sorting: '{}',
    search: '',
    is_default: false,
    position: 0,
    scope: 'personal',
    team_id: null,
    is_owner: true,
};

describe('isPreset', () => {
    it('returns false for non-records', () => {
        expect(isPreset(null)).toBe(false);
    });

    it('returns false when required fields are wrong', () => {
        expect(isPreset({ ...validPreset, scope: 'other' })).toBe(false);
        expect(isPreset({ ...validPreset, team_id: 1 })).toBe(false);
    });

    it('returns true for a valid preset object', () => {
        expect(isPreset(validPreset)).toBe(true);
    });
});

describe('parseStringRecordFromJson', () => {
    it('returns empty object on JSON syntax error', () => {
        expect(parseStringRecordFromJson('{')).toEqual({});
    });

    it('returns only string values from object JSON', () => {
        expect(parseStringRecordFromJson(JSON.stringify({ a: '1', b: 2 }))).toEqual({ a: '1' });
    });

    it('returns empty object when JSON parses to a non-record', () => {
        expect(parseStringRecordFromJson('"just-a-string"')).toEqual({});
    });
});

describe('tryParseStringRecordFromJson', () => {
    it('returns null on JSON syntax error', () => {
        expect(tryParseStringRecordFromJson('{')).toBeNull();
    });

    it('returns empty object for JSON null', () => {
        expect(tryParseStringRecordFromJson('null')).toEqual({});
    });

    it('parses like parseStringRecordFromJson for objects', () => {
        expect(tryParseStringRecordFromJson(JSON.stringify({ x: 'y' }))).toEqual({ x: 'y' });
    });

    it('omits non-string property values', () => {
        expect(tryParseStringRecordFromJson(JSON.stringify({ a: '1', b: 2 }))).toEqual({ a: '1' });
    });
});

describe('parsePresetsListBody', () => {
    it('returns null when envelope is wrong', () => {
        expect(parsePresetsListBody(null)).toBeNull();
        expect(parsePresetsListBody({ data: 'x' })).toBeNull();
    });

    it('filters data array to presets only', () => {
        const out = parsePresetsListBody({ data: [validPreset, { id: 'x' }] });
        expect(out).toHaveLength(1);
        expect(out?.[0]?.id).toBe('p1');
    });
});
