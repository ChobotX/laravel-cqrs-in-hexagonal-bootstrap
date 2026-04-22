<?php

declare(strict_types=1);

use App\Infrastructure\Tracing\SetConsoleTraceId;
use Illuminate\Events\Dispatcher;
use Illuminate\Log\Context\Repository;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\API\Trace\SpanContext;
use OpenTelemetry\Context\Context;

it('adds trace id to context on command starting', function (): void {
    $repository = new Repository(new Dispatcher);
    $listener = new SetConsoleTraceId($repository, new Tests\Helper\FakeIdGenerator);

    $listener();

    expect($repository->get('trace_id'))
        ->toBeString()
        ->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/');
});

it('uses otel trace id when active span exists', function (): void {
    $otelTraceId = '0af7651916cd43dd8448eb211c80319c';
    $spanContext = SpanContext::create($otelTraceId, 'b7ad6b7169203331');
    $span = Span::wrap($spanContext);
    $scope = Context::getCurrent()->withContextValue($span)->activate();

    try {
        $repository = new Repository(new Dispatcher);
        $listener = new SetConsoleTraceId($repository, new Tests\Helper\FakeIdGenerator);

        $listener();

        expect($repository->get('trace_id'))->toBe($otelTraceId);
    } finally {
        $scope->detach();
    }
});
