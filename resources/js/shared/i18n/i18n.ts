import { i18nVue } from 'laravel-vue-i18n';
import type { App } from 'vue';

const langModules: Record<string, () => Promise<{ default: Record<string, string> }>> = import.meta.glob<{
    default: Record<string, string>;
}>('../../../../lang/*.json');

export async function resolveLanguage(lang: string): Promise<{ default: Record<string, string> }> {
    return await langModules[`../../../../lang/${lang}.json`]();
}

export function useI18n(app: App): App {
    return app.use(i18nVue, { resolve: resolveLanguage });
}
