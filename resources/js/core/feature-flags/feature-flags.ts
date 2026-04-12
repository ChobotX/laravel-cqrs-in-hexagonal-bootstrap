import { type FlagState, parseFeatureFlagsMetaJson } from './parse-feature-flags-meta';

let cachedFlags: Record<string, FlagState> | null = null;

function getFlags(): Record<string, FlagState> {
    if (cachedFlags !== null) {
        return cachedFlags;
    }

    const meta = document.querySelector('meta[name="feature-flags"]');

    if (meta === null) {
        cachedFlags = {};

        return cachedFlags;
    }

    const content = meta.getAttribute('content') ?? '{}';

    cachedFlags = parseFeatureFlagsMetaJson(content);

    return cachedFlags;
}

export function isFeatureEnabled(key: string): boolean {
    return getFlags()[key]?.enabled ?? false;
}

export function featureValue(key: string): string | null {
    return getFlags()[key]?.value ?? null;
}
