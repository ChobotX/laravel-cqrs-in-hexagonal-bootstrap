import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import type { NotificationEntry } from './notification-store';

window.Pusher = Pusher;

let echoInstance: Echo<'reverb'> | null = null;

function getMetaContent(name: string): string {
    return document.querySelector<HTMLMetaElement>(`meta[name="${name}"]`)?.content ?? '';
}

export function initEcho(): Echo<'reverb'> {
    if (echoInstance) {
        return echoInstance;
    }

    echoInstance = new Echo({
        broadcaster: 'reverb',
        key: getMetaContent('reverb-app-key'),
        wsHost: getMetaContent('reverb-host'),
        wsPort: Number(getMetaContent('reverb-port')),
        wssPort: Number(getMetaContent('reverb-port')),
        forceTLS: getMetaContent('reverb-scheme') === 'https',
        enabledTransports: ['ws', 'wss'],
    });

    return echoInstance;
}

export function subscribeToNotifications(
    userId: string,
    onReceived: (notification: NotificationEntry) => void,
    onCountUpdated: (count: number) => void,
): void {
    const echo = initEcho();

    echo.private(`notifications.${userId}`)
        .listen('.NotificationReceived', (event: { payload: NotificationEntry }) => {
            onReceived(event.payload);
        })
        .listen('.UnreadCountUpdated', (event: { count: number }) => {
            onCountUpdated(event.count);
        });
}

export function disconnectEcho(): void {
    if (echoInstance) {
        echoInstance.disconnect();
        echoInstance = null;
    }
}
