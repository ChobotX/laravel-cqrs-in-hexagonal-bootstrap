/**
 * @vitest-environment node
 */
import { describe, expect, it } from 'vitest';

describe('format-instant (node)', () => {
    it('getDisplayTimeZone is undefined without browser APIs', async (): Promise<void> => {
        const { getDisplayTimeZone } = await import('./format-instant');

        expect(getDisplayTimeZone()).toBeUndefined();
    });
});
