import * as Sentry from '@sentry/browser';

const dsn: string | undefined = document.querySelector<HTMLMetaElement>('meta[name="sentry-dsn"]')?.content;

if (dsn) {
    const tracesSampleRate: number = Number.parseFloat(
        document.querySelector<HTMLMetaElement>('meta[name="sentry-traces-sample-rate"]')?.content ?? '0',
    );

    Sentry.init({
        dsn,
        environment: document.querySelector<HTMLMetaElement>('meta[name="sentry-environment"]')?.content,
        integrations: [Sentry.browserTracingIntegration()],
        tracesSampleRate,
    });

    const traceId: string | undefined = document.querySelector<HTMLMetaElement>('meta[name="trace-id"]')?.content;
    if (traceId) {
        Sentry.setTag('trace_id', traceId);
    }
}
