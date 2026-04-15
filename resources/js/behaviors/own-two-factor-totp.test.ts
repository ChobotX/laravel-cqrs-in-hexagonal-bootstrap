import { afterEach, describe, expect, it, vi } from 'vitest';

const PENDING_HINT = 'data-own-two-factor-totp-pending-hint';
const DOWNLOADED_ACK = 'data-own-two-factor-totp-downloaded-ack';

function fixtureDownloadInsideCard(): string {
    return `
        <div data-own-two-factor-totp-setup-card>
            <a data-own-two-factor-totp-backup-download href="/profile/two-factor/backup-codes">Download</a>
            <p ${PENDING_HINT} class="">pending</p>
            <p ${DOWNLOADED_ACK} class="hidden">ack</p>
        </div>
        <div data-own-two-factor-totp-confirm-panel
             data-own-two-factor-totp-confirm-visible="0"
             class="hidden">confirm</div>
    `;
}

function fixtureDownloadOutsideCard(): string {
    return `
        <div data-own-two-factor-totp-setup-card>
            <p ${PENDING_HINT} class="">pending</p>
            <p ${DOWNLOADED_ACK} class="hidden">ack</p>
        </div>
        <a data-own-two-factor-totp-backup-download href="/profile/two-factor/backup-codes">Download</a>
        <div data-own-two-factor-totp-confirm-panel
             data-own-two-factor-totp-confirm-visible="0"
             class="hidden">confirm</div>
    `;
}

afterEach(() => {
    document.body.innerHTML = '';
    vi.unstubAllGlobals();
    vi.restoreAllMocks();
});

describe('own-two-factor-totp', () => {
    it('reveals confirm panel and toggles hints after successful download', async () => {
        vi.resetModules();
        document.body.innerHTML = fixtureDownloadInsideCard();
        vi.stubGlobal(
            'fetch',
            vi.fn().mockResolvedValue({
                ok: true,
                blob: async (): Promise<Blob> => new Blob(['line-a']),
            }),
        );
        const createSpy = vi.spyOn(URL, 'createObjectURL').mockReturnValue('blob:test-url');
        const revokeSpy = vi.spyOn(URL, 'revokeObjectURL').mockImplementation((): void => {
            /* revokeObjectURL side effect not under test */
        });

        await import('./own-two-factor-totp');

        const link = document.querySelector('a[data-own-two-factor-totp-backup-download]');
        expect(link).toBeInstanceOf(HTMLAnchorElement);
        const clickEvent = new MouseEvent('click', { bubbles: true, cancelable: true });
        link?.dispatchEvent(clickEvent);
        expect(clickEvent.defaultPrevented).toBe(true);

        await vi.waitFor((): void => {
            const panel = document.querySelector('[data-own-two-factor-totp-confirm-panel]');
            expect(panel?.classList.contains('flex')).toBe(true);
        });

        expect(document.querySelector(`[${PENDING_HINT}]`)?.classList.contains('hidden')).toBe(true);
        expect(document.querySelector(`[${DOWNLOADED_ACK}]`)?.classList.contains('hidden')).toBe(false);
        expect(createSpy).toHaveBeenCalled();
        expect(revokeSpy).toHaveBeenCalledWith('blob:test-url');
    });

    it('does not reveal panel when fetch returns non-OK', async () => {
        vi.resetModules();
        document.body.innerHTML = fixtureDownloadInsideCard();
        vi.stubGlobal(
            'fetch',
            vi.fn().mockResolvedValue({
                ok: false,
                status: 500,
            }),
        );

        await import('./own-two-factor-totp');

        const link = document.querySelector('a[data-own-two-factor-totp-backup-download]');
        expect(link).toBeInstanceOf(HTMLAnchorElement);
        link?.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));

        await vi.waitFor((): void => {
            expect(vi.mocked(fetch)).toHaveBeenCalled();
        });

        const panel = document.querySelector('[data-own-two-factor-totp-confirm-panel]');
        expect(panel?.classList.contains('hidden')).toBe(true);
    });

    it('keeps panel hidden when fetch rejects', async () => {
        vi.resetModules();
        document.body.innerHTML = fixtureDownloadInsideCard();
        vi.stubGlobal('fetch', vi.fn().mockRejectedValue(new Error('network')));

        await import('./own-two-factor-totp');

        const link = document.querySelector('a[data-own-two-factor-totp-backup-download]');
        expect(link).toBeInstanceOf(HTMLAnchorElement);
        link?.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));

        await vi.waitFor((): void => {
            expect(vi.mocked(fetch)).toHaveBeenCalled();
        });

        const panel = document.querySelector('[data-own-two-factor-totp-confirm-panel]');
        expect(panel?.classList.contains('hidden')).toBe(true);
    });

    it('does not fetch when download href is empty', async () => {
        vi.resetModules();
        document.body.innerHTML = `
            <div data-own-two-factor-totp-setup-card>
                <a data-own-two-factor-totp-backup-download href="">Download</a>
            </div>
            <div data-own-two-factor-totp-confirm-panel
                 data-own-two-factor-totp-confirm-visible="0"
                 class="hidden"></div>
        `;
        const fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);

        await import('./own-two-factor-totp');

        const link = document.querySelector('a[data-own-two-factor-totp-backup-download]');
        expect(link).toBeInstanceOf(HTMLAnchorElement);
        link?.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));

        expect(fetchMock).not.toHaveBeenCalled();
    });

    it('does not attach when confirm panel is already visible from server', async () => {
        vi.resetModules();
        document.body.innerHTML = `
            <a data-own-two-factor-totp-backup-download href="/profile/two-factor/backup-codes">Download</a>
            <div data-own-two-factor-totp-confirm-panel
                 data-own-two-factor-totp-confirm-visible="1"
                 class="flex flex-col gap-5"></div>
        `;
        const fetchMock = vi.fn();
        vi.stubGlobal('fetch', fetchMock);

        await import('./own-two-factor-totp');

        const link = document.querySelector('a[data-own-two-factor-totp-backup-download]');
        expect(link).toBeInstanceOf(HTMLAnchorElement);
        link?.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));

        expect(fetchMock).not.toHaveBeenCalled();
    });

    it('returns early when download or confirm panel is missing', async () => {
        vi.resetModules();
        document.body.innerHTML = '<p>empty</p>';

        await expect(import('./own-two-factor-totp')).resolves.toBeTruthy();
    });

    it('reveals panel when download link is outside setup card (no hint toggle)', async () => {
        vi.resetModules();
        document.body.innerHTML = fixtureDownloadOutsideCard();
        vi.stubGlobal(
            'fetch',
            vi.fn().mockResolvedValue({
                ok: true,
                blob: async (): Promise<Blob> => new Blob(['x']),
            }),
        );
        vi.spyOn(URL, 'createObjectURL').mockReturnValue('blob:outside');
        vi.spyOn(URL, 'revokeObjectURL').mockImplementation((): void => {
            /* revokeObjectURL side effect not under test */
        });

        await import('./own-two-factor-totp');

        const link = document.querySelector('a[data-own-two-factor-totp-backup-download]');
        expect(link).toBeInstanceOf(HTMLAnchorElement);
        link?.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));

        await vi.waitFor((): void => {
            const panel = document.querySelector('[data-own-two-factor-totp-confirm-panel]');
            expect(panel?.classList.contains('flex')).toBe(true);
        });

        expect(document.querySelector(`[${PENDING_HINT}]`)?.classList.contains('hidden')).toBe(false);
    });

    it('defers binding until DOMContentLoaded when readyState is loading', async () => {
        vi.resetModules();
        document.body.innerHTML = fixtureDownloadInsideCard();
        const readySpy = vi.spyOn(document, 'readyState', 'get').mockReturnValue('loading');

        const fetchMock = vi.fn().mockResolvedValue({
            ok: true,
            blob: async (): Promise<Blob> => new Blob(['z']),
        });
        vi.stubGlobal('fetch', fetchMock);
        vi.spyOn(URL, 'createObjectURL').mockReturnValue('blob:deferred');
        vi.spyOn(URL, 'revokeObjectURL').mockImplementation((): void => {
            /* revokeObjectURL side effect not under test */
        });

        await import('./own-two-factor-totp');

        expect(fetchMock).not.toHaveBeenCalled();

        readySpy.mockReturnValue('complete');
        document.dispatchEvent(new Event('DOMContentLoaded'));

        const link = document.querySelector('a[data-own-two-factor-totp-backup-download]');
        expect(link).toBeInstanceOf(HTMLAnchorElement);
        link?.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));

        await vi.waitFor((): void => {
            expect(fetchMock).toHaveBeenCalled();
        });
    });
});
