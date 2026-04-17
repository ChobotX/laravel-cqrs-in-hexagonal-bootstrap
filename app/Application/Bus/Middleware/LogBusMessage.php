<?php

declare(strict_types=1);

namespace App\Application\Bus\Middleware;

use App\Application\Bus\SensitiveDataMasker;
use App\Contract\Bus\BusMiddleware;
use App\Contract\Logging\Logger;
use App\Contract\Tracing\TraceContext;
use Closure;
use Throwable;

final readonly class LogBusMessage implements BusMiddleware
{
    private const int NANOSECONDS_PER_MILLISECOND = 1_000_000;

    public function __construct(
        private Logger $logger,
        private TraceContext $traceContext,
    ) {}

    /**
     * @template TResult
     *
     * @param  Closure(object): TResult  $next
     * @return TResult
     */
    public function handle(object $message, Closure $next): mixed
    {
        $context = $this->baseContext($message);
        $startTime = (int) hrtime(true);

        $this->logger->debug('Bus message dispatching', [
            ...$context,
            'level' => 'debug',
            'data' => SensitiveDataMasker::mask($message),
        ]);

        try {
            $result = $next($message);
        } catch (Throwable $throwable) {
            $this->logger->error('Bus message failed', [
                ...$context,
                'level' => 'error',
                'duration_ms' => $this->durationMs($startTime),
                'exception' => $throwable->getMessage(),
            ]);

            throw $throwable;
        }

        $this->logger->info('Bus message handled', [
            ...$context,
            'level' => 'info',
            'duration_ms' => $this->durationMs($startTime),
        ]);

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    private function baseContext(object $message): array
    {
        return [
            'trace_id' => $this->traceContext->traceId(),
            'user_id' => $this->traceContext->userId(),
            'tenant_id' => $this->traceContext->tenantId(),
            'message' => $message::class,
        ];
    }

    private function durationMs(int $startTime): float
    {
        return ((int) hrtime(true) - $startTime) / self::NANOSECONDS_PER_MILLISECOND;
    }
}
