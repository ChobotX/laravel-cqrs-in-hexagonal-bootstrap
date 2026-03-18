# Production Dockerfile TODOs

## PHP Extensions

```dockerfile
RUN pecl install opentelemetry protobuf \
    && docker-php-ext-enable opentelemetry protobuf
```

- `ext-opentelemetry` — required for OTel auto-instrumentation (zero-code span creation)
- `ext-protobuf` — optional but recommended, significantly faster serialization than pure-PHP fallback

## Composer Packages (install after extensions)

```dockerfile
RUN composer require open-telemetry/opentelemetry-auto-laravel
```

This package crashes the autoloader without `ext-opentelemetry`, so it must be installed in the image where the extension is present. The SDK and exporter (`open-telemetry/sdk`, `open-telemetry/exporter-otlp`) are already in `composer.json`.

## Environment Variables

### OpenTelemetry (Grafana Tempo)

```env
OTEL_PHP_AUTOLOAD_ENABLED=true
OTEL_SERVICE_NAME=laravel-bootstrap
OTEL_TRACES_EXPORTER=otlp
OTEL_METRICS_EXPORTER=none
OTEL_LOGS_EXPORTER=none
OTEL_EXPORTER_OTLP_PROTOCOL=http/protobuf
OTEL_EXPORTER_OTLP_ENDPOINT=http://tempo:4318
OTEL_TRACES_SAMPLER=parentbased_traceidratio
OTEL_TRACES_SAMPLER_ARG=0.1
OTEL_PROPAGATORS=baggage,tracecontext
```

### Sentry

```env
SENTRY_LARAVEL_DSN=https://<key>@<host>/<project>
SENTRY_TRACES_SAMPLE_RATE=1
SENTRY_ENVIRONMENT=production
SENTRY_RELEASE=<git-sha-or-tag>
```

For GlitchTip, swap the DSN — no code changes needed.

### Sentry Sourcemaps (CI/CD build step)

```env
SENTRY_AUTH_TOKEN=<org-auth-token>
SENTRY_ORG=<org-slug>
SENTRY_PROJECT=<project-slug>
```

Set these during `npm run build` — the Vite plugin uploads sourcemaps and deletes `.map` files from the build output.

## Trace ID Flow Verification

Once deployed, verify the full chain:

1. `curl -v https://app/api/users` — response has `X-Trace-Id` header
2. Search that trace ID in Grafana Tempo — should show the full request trace with DB spans
3. Search that trace ID in Sentry — should appear as a tag on errors and transactions
4. Trigger an error — Sentry event should have `trace_id` tag matching the Tempo trace
5. Check Laravel logs — `trace_id` should appear in the `extra` field of every log entry
