<?php

declare(strict_types=1);

namespace App\Infrastructure\Tracing;

use App\Contract\IdGenerator;
use Illuminate\Log\Context\Repository;
use OpenTelemetry\API\Trace\Span;

final readonly class SetConsoleTraceId
{
    public function __construct(
        private Repository $repository,
        private IdGenerator $idGenerator,
    ) {}

    public function __invoke(): void
    {
        $spanContext = Span::getCurrent()->getContext();

        $traceId = $spanContext->isValid()
            ? $spanContext->getTraceId()
            : $this->idGenerator->generate();

        $this->repository->add('trace_id', $traceId);
    }
}
