@if (config('sentry.dsn'))
    <meta name="sentry-dsn"
          content="{{ config('sentry.dsn') }}">
    <meta name="sentry-environment"
          content="{{ config('app.env') }}">
    <meta name="sentry-traces-sample-rate"
          content="{{ config('sentry.traces_sample_rate', 0) }}">
    <meta name="trace-id"
          content="{{ Illuminate\Support\Facades\Context::get('trace_id') }}">
    {!! Sentry\Laravel\Integration::sentryMeta() !!}
@endif
