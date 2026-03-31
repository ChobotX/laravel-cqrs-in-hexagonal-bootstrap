import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import type { NotificationEntry } from './notification-store';

const echoMocks = vi.hoisted(() => {
    const listen = vi.fn().mockReturnThis();
    const privateFn = vi.fn().mockReturnValue({ listen });
    const disconnect = vi.fn();

    class EchoStub {
        options: Record<string, unknown>;
        private = privateFn;
        disconnect = disconnect;

        constructor(options: Record<string, unknown>) {
            this.options = options;
        }
    }

    return { listen, privateFn, disconnect, EchoStub };
});

vi.mock('laravel-echo', () => ({
    default: echoMocks.EchoStub,
}));

vi.mock('pusher-js', () => ({
    default: vi.fn(),
}));

import { disconnectEcho, initEcho, subscribeToNotifications } from './notification-echo';

function setMeta(name: string, content: string): HTMLMetaElement {
    const meta = document.createElement('meta');
    meta.name = name;
    meta.content = content;
    document.head.appendChild(meta);

    return meta;
}

describe('notification-echo', () => {
    let metas: HTMLMetaElement[] = [];

    beforeEach(() => {
        echoMocks.privateFn.mockClear();
        echoMocks.listen.mockClear().mockReturnThis();
        echoMocks.disconnect.mockClear();
        disconnectEcho();

        metas = [
            setMeta('reverb-app-key', 'test-key'),
            setMeta('reverb-host', 'localhost'),
            setMeta('reverb-port', '8080'),
            setMeta('reverb-scheme', 'http'),
        ];
    });

    afterEach(() => {
        disconnectEcho();

        for (const meta of metas) {
            meta.remove();
        }
        metas = [];
    });

    it('initEcho creates Echo instance with config from meta tags', () => {
        const echo = initEcho();

        expect(echo).toBeInstanceOf(echoMocks.EchoStub);
        expect((echo as unknown as InstanceType<typeof echoMocks.EchoStub>).options).toEqual(
            expect.objectContaining({
                broadcaster: 'reverb',
                key: 'test-key',
                wsHost: 'localhost',
                wsPort: 8080,
                forceTLS: false,
            }),
        );
    });

    it('initEcho returns same instance on second call', () => {
        const first = initEcho();
        const second = initEcho();

        expect(first).toBe(second);
    });

    it('initEcho sets forceTLS true when scheme is https', () => {
        for (const meta of metas) {
            meta.remove();
        }
        metas = [
            setMeta('reverb-app-key', 'key'),
            setMeta('reverb-host', 'example.com'),
            setMeta('reverb-port', '443'),
            setMeta('reverb-scheme', 'https'),
        ];

        const echo = initEcho();

        expect((echo as unknown as InstanceType<typeof echoMocks.EchoStub>).options).toEqual(
            expect.objectContaining({ forceTLS: true }),
        );
    });

    it('subscribeToNotifications subscribes to private channel', () => {
        const onReceived = vi.fn();
        const onCountUpdated = vi.fn();

        subscribeToNotifications('user-123', onReceived, onCountUpdated);

        expect(echoMocks.privateFn).toHaveBeenCalledWith('notifications.user-123');
        expect(echoMocks.listen).toHaveBeenCalledWith('.NotificationReceived', expect.any(Function));
        expect(echoMocks.listen).toHaveBeenCalledWith('.UnreadCountUpdated', expect.any(Function));
    });

    it('NotificationReceived callback passes payload to onReceived', () => {
        const onReceived = vi.fn();

        subscribeToNotifications('user-1', onReceived, vi.fn());

        const receivedCallback = echoMocks.listen.mock.calls.find(
            (call: unknown[]) => call[0] === '.NotificationReceived',
        )?.[1] as (event: { payload: NotificationEntry }) => void;

        const payload = { id: 'n1', title: 'Test' } as NotificationEntry;
        receivedCallback({ payload });

        expect(onReceived).toHaveBeenCalledWith(payload);
    });

    it('UnreadCountUpdated callback passes count to onCountUpdated', () => {
        const onCountUpdated = vi.fn();

        subscribeToNotifications('user-1', vi.fn(), onCountUpdated);

        const countCallback = echoMocks.listen.mock.calls.find(
            (call: unknown[]) => call[0] === '.UnreadCountUpdated',
        )?.[1] as (event: { count: number }) => void;

        countCallback({ count: 5 });

        expect(onCountUpdated).toHaveBeenCalledWith(5);
    });

    it('disconnectEcho disconnects and allows re-init', () => {
        const first = initEcho();
        echoMocks.disconnect.mockClear();
        disconnectEcho();

        expect(echoMocks.disconnect).toHaveBeenCalledTimes(1);

        const second = initEcho();
        expect(second).not.toBe(first);
    });

    it('disconnectEcho is safe to call when not initialized', () => {
        expect(() => disconnectEcho()).not.toThrow();
    });

    it('uses empty string when meta tag is missing', () => {
        for (const meta of metas) {
            meta.remove();
        }
        metas = [];

        const echo = initEcho();

        expect((echo as unknown as InstanceType<typeof echoMocks.EchoStub>).options).toEqual(
            expect.objectContaining({
                key: '',
                wsHost: '',
            }),
        );
    });
});
