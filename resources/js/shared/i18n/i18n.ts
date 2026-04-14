import { i18nVue } from 'laravel-vue-i18n';
import type { App } from 'vue';

const jsonLangModules: Record<string, () => Promise<{ default: Record<string, string> }>> = import.meta.glob<{
    default: Record<string, string>;
}>('../../../../lang/*.json');

const phpLangModules: Record<string, () => Promise<{ default: Record<string, string> }>> = import.meta.glob<{
    default: Record<string, string>;
}>('../../../../lang/*.php');

const EMPTY_MESSAGES = { default: {} } as const;

function languageCandidates(lang: string): string[] {
    const normalized = lang.trim().toLowerCase();
    const base = normalized.split('-')[0];

    return [...new Set([normalized, base, 'en'])];
}

function loaderFor(lang: string): (() => Promise<{ default: Record<string, string> }>) | null {
    for (const candidate of languageCandidates(lang)) {
        const jsonLoader = jsonLangModules[`../../../../lang/${candidate}.json`];
        if (jsonLoader) {
            return jsonLoader;
        }

        const phpLoader = phpLangModules[`../../../../lang/${candidate}.php`];
        if (phpLoader) {
            return phpLoader;
        }
    }

    return null;
}

export async function resolveLanguage(lang: string): Promise<{ default: Record<string, string> }> {
    const loader = loaderFor(lang);
    if (!loader) {
        return EMPTY_MESSAGES;
    }

    return await loader();
}

export function useI18n(app: App): App {
    return app.use(i18nVue, { resolve: resolveLanguage });
}
