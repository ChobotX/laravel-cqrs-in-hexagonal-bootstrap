<?php

declare(strict_types=1);

use App\Application\Bus\Middleware\LogBusMessage;
use App\Contract\Attribute\Sensitive;
use App\Contract\Logging\Logger;
use App\Contract\Tracing\TraceContext;

it('logs info with message class name on successful execution', function (): void {
    $logger = createLoggerSpy();
    $middleware = new LogBusMessage($logger, createFakeTraceContext());

    $message = new stdClass;

    $middleware->handle($message, static fn (): null => null);

    expect($logger->infoCalls)->toHaveCount(1)
        ->and($logger->infoCalls[0]['message'])->toBe('Bus message handled')
        ->and($logger->infoCalls[0]['context']['message'])->toBe(stdClass::class);
});

it('logs error and re-throws on failure', function (): void {
    $logger = createLoggerSpy();
    $middleware = new LogBusMessage($logger, createFakeTraceContext());

    expect(static fn (): mixed => $middleware->handle(new stdClass, static function (): never {
        throw new RuntimeException('Something broke');
    }))->toThrow(RuntimeException::class, 'Something broke');

    expect($logger->errorCalls)->toHaveCount(1)
        ->and($logger->errorCalls[0]['message'])->toBe('Bus message failed')
        ->and($logger->errorCalls[0]['context']['exception'])->toBe('Something broke');
});

it('includes trace_id user_id and tenant_id from trace context', function (): void {
    $logger = createLoggerSpy();
    $traceContext = createFakeTraceContext('abc-trace-123', 'user-456', 'tenant-789');
    $middleware = new LogBusMessage($logger, $traceContext);

    $middleware->handle(new stdClass, static fn (): null => null);

    $context = $logger->infoCalls[0]['context'];
    expect($context['trace_id'])->toBe('abc-trace-123')
        ->and($context['user_id'])->toBe('user-456')
        ->and($context['tenant_id'])->toBe('tenant-789');
});

it('includes level in log context', function (): void {
    $logger = createLoggerSpy();
    $middleware = new LogBusMessage($logger, createFakeTraceContext());

    $middleware->handle(new stdClass, static fn (): null => null);

    expect($logger->infoCalls[0]['context']['level'])->toBe('info');
});

it('includes level error in error log context', function (): void {
    $logger = createLoggerSpy();
    $middleware = new LogBusMessage($logger, createFakeTraceContext());

    try {
        $middleware->handle(new stdClass, static function (): never {
            throw new RuntimeException('fail');
        });
    } catch (RuntimeException) {
    }

    expect($logger->errorCalls[0]['context']['level'])->toBe('error');
});

it('includes non-negative duration_ms', function (): void {
    $logger = createLoggerSpy();
    $middleware = new LogBusMessage($logger, createFakeTraceContext());

    $middleware->handle(new stdClass, static fn (): null => null);

    expect($logger->infoCalls[0]['context']['duration_ms'])->toBeGreaterThanOrEqual(0.0);
});

it('passes message through and returns the result from next', function (): void {
    $logger = createLoggerSpy();
    $middleware = new LogBusMessage($logger, createFakeTraceContext());

    $originalMessage = new stdClass;
    $receivedMessage = null;

    $result = $middleware->handle($originalMessage, static function (object $message) use (&$receivedMessage): string {
        $receivedMessage = $message;

        return 'handler-result';
    });

    expect($receivedMessage)->toBe($originalMessage)
        ->and($result)->toBe('handler-result');
});

it('logs debug with message data before execution', function (): void {
    $logger = createLoggerSpy();
    $middleware = new LogBusMessage($logger, createFakeTraceContext());

    $message = new readonly class('create-user', 'john@example.com')
    {
        public function __construct(
            public string $action,
            public string $email,
        ) {}
    };

    $middleware->handle($message, static fn (): null => null);

    expect($logger->debugCalls)->toHaveCount(1)
        ->and($logger->debugCalls[0]['message'])->toBe('Bus message dispatching')
        ->and($logger->debugCalls[0]['context']['level'])->toBe('debug')
        ->and($logger->debugCalls[0]['context']['data'])->toBe([
            'action' => 'create-user',
            'email' => 'john@example.com',
        ]);
});

it('masks sensitive properties in debug log', function (): void {
    $logger = createLoggerSpy();
    $middleware = new LogBusMessage($logger, createFakeTraceContext());

    $message = new readonly class('user-123', 's3cret!')
    {
        public function __construct(
            public string $userId,
            #[Sensitive]
            public string $rawPassword,
        ) {}
    };

    $middleware->handle($message, static fn (): null => null);

    expect($logger->debugCalls[0]['context']['data'])->toBe([
        'userId' => 'user-123',
        'rawPassword' => '***',
    ]);
});

function createFakeTraceContext(
    ?string $traceId = null,
    ?string $userId = null,
    ?string $tenantId = null,
): TraceContext {
    return new readonly class($traceId, $userId, $tenantId) implements TraceContext
    {
        public function __construct(
            private ?string $traceId,
            private ?string $userId,
            private ?string $tenantId,
        ) {}

        public function traceId(): ?string
        {
            return $this->traceId;
        }

        public function userId(): ?string
        {
            return $this->userId;
        }

        public function tenantId(): ?string
        {
            return $this->tenantId;
        }
    };
}

/**
 * @return object{debugCalls: list<array{message: string, context: array<string, mixed>}>, infoCalls: list<array{message: string, context: array<string, mixed>}>, errorCalls: list<array{message: string, context: array<string, mixed>}>} & Logger
 */
function createLoggerSpy(): Logger
{
    return new class implements Logger
    {
        /** @var list<array{message: string, context: array<string, mixed>}> */
        public array $debugCalls = [];

        /** @var list<array{message: string, context: array<string, mixed>}> */
        public array $infoCalls = [];

        /** @var list<array{message: string, context: array<string, mixed>}> */
        public array $errorCalls = [];

        public function debug(string $message, array $context = []): void
        {
            $this->debugCalls[] = ['message' => $message, 'context' => $context];
        }

        public function info(string $message, array $context = []): void
        {
            $this->infoCalls[] = ['message' => $message, 'context' => $context];
        }

        public function warning(string $message, array $context = []): void {}

        public function error(string $message, array $context = []): void
        {
            $this->errorCalls[] = ['message' => $message, 'context' => $context];
        }
    };
}
