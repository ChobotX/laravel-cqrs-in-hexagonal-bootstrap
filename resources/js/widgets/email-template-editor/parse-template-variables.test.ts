import { describe, expect, it } from 'vitest';
import { parseTemplateVariablesJson } from './parse-template-variables';

describe('parseTemplateVariablesJson', () => {
    it('returns empty object for invalid JSON', () => {
        expect(parseTemplateVariablesJson('{')).toEqual({});
    });

    it('returns empty object for non-object JSON', () => {
        expect(parseTemplateVariablesJson('[]')).toEqual({});
        expect(parseTemplateVariablesJson('"x"')).toEqual({});
    });

    it('keeps only entries matching TemplateVariableConfig', () => {
        const json = JSON.stringify({
            ok: { description: 'd', sensitive: false, sample: 's' },
            bad: { description: 1, sensitive: false, sample: 's' },
        });
        expect(parseTemplateVariablesJson(json)).toEqual({
            ok: { description: 'd', sensitive: false, sample: 's' },
        });
    });
});
