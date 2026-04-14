import { isRecord } from '../../shared/type-guards/is-record';

export interface TemplateVariableConfig {
    description: string;
    sensitive: boolean;
    sample: string;
}

function isTemplateVariableConfig(value: unknown): value is TemplateVariableConfig {
    return (
        isRecord(value) &&
        typeof value.description === 'string' &&
        typeof value.sensitive === 'boolean' &&
        typeof value.sample === 'string'
    );
}

export function parseTemplateVariablesJson(json: string): Record<string, TemplateVariableConfig> {
    let raw: unknown;
    try {
        raw = JSON.parse(json);
    } catch {
        /* @silent Malformed JSON from Blade — treat as no variables */
        return {};
    }
    if (!isRecord(raw)) {
        return {};
    }
    const out: Record<string, TemplateVariableConfig> = {};
    for (const [name, config] of Object.entries(raw)) {
        if (isTemplateVariableConfig(config)) {
            out[name] = config;
        }
    }
    return out;
}
