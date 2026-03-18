<?php

declare(strict_types=1);

namespace App\Infrastructure\Tracing;

use Illuminate\Log\Context\Repository;
use Illuminate\Support\Str;
use OpenTelemetry\API\Trace\Span;

final readonly class SetConsoleTraceId
{
    public function __construct(
        private Repository $repository,
    ) {}

    public function __invoke(): void
    {
        $spanContext = Span::getCurrent()->getContext();

        $traceId = $spanContext->isValid()
            ? $spanContext->getTraceId()
            : Str::uuid()->toString();

        $this->repository->add('trace_id', $traceId);
    }
}
