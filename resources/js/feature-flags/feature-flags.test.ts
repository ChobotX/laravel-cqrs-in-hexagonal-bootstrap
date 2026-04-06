import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

function setMetaTag(content: string): void {
    const meta = document.createElement('meta');
    meta.setAttribute('name', 'feature-flags');
    meta.setAttribute('content', content);
    document.head.appendChild(meta);
}

let isFeatureEnabled: (key: string) => boolean;
let featureValue: (key: string) => string | null;

beforeEach(() => {
    vi.resetModules();
    document.head.innerHTML = '';
});

afterEach(() => {
    document.head.innerHTML = '';
});

describe('feature-flags', () => {
    it('reads flags from meta tag', async () => {
        setMetaTag(
            JSON.stringify({
                'billing.stripe': { enabled: true, value: '1' },
                'billing.gateway': { enabled: true, value: 'gopay' },
            }),
        );
        ({ isFeatureEnabled, featureValue } = await import('./feature-flags'));

        expect(isFeatureEnabled('billing.stripe')).toBe(true);
        expect(featureValue('billing.gateway')).toBe('gopay');
    });

    it('returns false for disabled flag', async () => {
        setMetaTag(JSON.stringify({ 'billing.stripe': { enabled: false, value: '0' } }));
        ({ isFeatureEnabled } = await import('./feature-flags'));

        expect(isFeatureEnabled('billing.stripe')).toBe(false);
    });

    it('returns null for unknown flag key', async () => {
        setMetaTag(JSON.stringify({}));
        ({ featureValue } = await import('./feature-flags'));

        expect(featureValue('nonexistent.flag')).toBeNull();
    });

    it('returns false for unknown flag in isFeatureEnabled', async () => {
        setMetaTag(JSON.stringify({}));
        ({ isFeatureEnabled } = await import('./feature-flags'));

        expect(isFeatureEnabled('nonexistent.flag')).toBe(false);
    });

    it('returns empty values when meta tag is missing', async () => {
        ({ isFeatureEnabled, featureValue } = await import('./feature-flags'));

        expect(isFeatureEnabled('billing.stripe')).toBe(false);
        expect(featureValue('billing.stripe')).toBeNull();
    });

    it('handles meta tag without content attribute', async () => {
        const meta = document.createElement('meta');
        meta.setAttribute('name', 'feature-flags');
        document.head.appendChild(meta);

        ({ isFeatureEnabled, featureValue } = await import('./feature-flags'));

        expect(isFeatureEnabled('billing.stripe')).toBe(false);
        expect(featureValue('billing.stripe')).toBeNull();
    });

    it('caches parsed flags across calls', async () => {
        setMetaTag(JSON.stringify({ 'billing.stripe': { enabled: true, value: '1' } }));
        ({ isFeatureEnabled, featureValue } = await import('./feature-flags'));

        expect(isFeatureEnabled('billing.stripe')).toBe(true);

        document.querySelector('meta[name="feature-flags"]')?.remove();

        expect(isFeatureEnabled('billing.stripe')).toBe(true);
        expect(featureValue('billing.stripe')).toBe('1');
    });
});
