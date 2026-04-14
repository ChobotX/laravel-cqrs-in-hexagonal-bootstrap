import { describe, expect, it, vi } from 'vitest';
import type { App } from 'vue';

vi.mock('laravel-vue-i18n', () => ({
    i18nVue: { install: vi.fn() },
}));

import { resolveLanguage, useI18n } from './i18n';

describe('useI18n', () => {
    it('registers i18nVue plugin with resolve function', () => {
        const useFn = vi.fn().mockReturnThis();
        // biome-ignore lint/plugin/no-type-assertion: Minimal test stub; not a real Vue App instance.
        const fakeApp = { use: useFn } as App;

        const result = useI18n(fakeApp);

        expect(useFn).toHaveBeenCalledOnce();
        expect(useFn.mock.calls[0]?.[0]).toHaveProperty('install');
        expect(useFn.mock.calls[0]?.[1]).toEqual({ resolve: resolveLanguage });
        expect(result).toBe(fakeApp);
    });
});

describe('resolveLanguage', () => {
    it('returns empty messages when language loader is missing in test runtime', async () => {
        await expect(resolveLanguage('cs')).resolves.toEqual({ default: {} });
    });
});
