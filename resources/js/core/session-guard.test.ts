import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

let fetchMock: ReturnType<typeof vi.fn>;
const replaceSpy: ReturnType<typeof vi.fn> = vi.fn();

beforeEach(async () => {
    fetchMock = vi.fn().mockResolvedValue(new Response('', { status: 200 }));
    vi.stubGlobal('fetch', fetchMock);

    Object.defineProperty(window, 'location', {
        value: {
            origin: 'http://localhost',
            pathname: '/users',
            search: '?page=2',
            replace: replaceSpy,
        },
        writable: true,
        configurable: true,
    });

    await import('./session-guard');
});

afterEach(() => {
    replaceSpy.mockClear();
    vi.unstubAllGlobals();
    vi.resetModules();
});

describe('session-guard', () => {
    it('passes through normal responses unchanged', async () => {
        const okResponse = new Response('ok', { status: 200 });
        fetchMock.mockResolvedValue(okResponse);

        const result = await window.fetch('/api/data');

        expect(result).toBe(okResponse);
        expect(replaceSpy).not.toHaveBeenCalled();
    });

    it('redirects to login on 401 response from same-origin', async () => {
        fetchMock.mockResolvedValue(new Response('', { status: 401 }));

        await window.fetch('/internal-api/users/search');

        expect(replaceSpy).toHaveBeenCalledWith('http://localhost/login?redirect=%2Fusers%3Fpage%3D2');
    });

    it('redirects to login on 419 response from same-origin', async () => {
        fetchMock.mockResolvedValue(new Response('', { status: 419 }));

        await window.fetch('/internal-api/users/search');

        expect(replaceSpy).toHaveBeenCalledWith('http://localhost/login?redirect=%2Fusers%3Fpage%3D2');
    });

    it('skips redirect for external URLs', async () => {
        fetchMock.mockResolvedValue(new Response('', { status: 401 }));

        await window.fetch('https://sentry.example.com/api/envelope');

        expect(replaceSpy).not.toHaveBeenCalled();
    });

    it('skips redirect when already on login page', async () => {
        Object.defineProperty(window, 'location', {
            value: {
                origin: 'http://localhost',
                pathname: '/login',
                search: '',
                replace: replaceSpy,
            },
            writable: true,
            configurable: true,
        });

        fetchMock.mockResolvedValue(new Response('', { status: 401 }));

        await window.fetch('/internal-api/users/search');

        expect(replaceSpy).not.toHaveBeenCalled();
    });

    it('handles Request object input', async () => {
        fetchMock.mockResolvedValue(new Response('', { status: 401 }));

        await window.fetch(new Request('http://localhost/internal-api/users/search'));

        expect(replaceSpy).toHaveBeenCalledWith('http://localhost/login?redirect=%2Fusers%3Fpage%3D2');
    });

    it('passes through non-session error responses', async () => {
        const errorResponse = new Response('', { status: 500 });
        fetchMock.mockResolvedValue(errorResponse);

        const result = await window.fetch('/api/data');

        expect(result).toBe(errorResponse);
        expect(replaceSpy).not.toHaveBeenCalled();
    });
});
