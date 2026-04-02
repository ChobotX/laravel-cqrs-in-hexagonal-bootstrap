<?php

declare(strict_types=1);

use App\Application\Bus\Middleware\WrapInTransaction;
use App\Application\Bus\SkipTransaction;
use App\Contract\Persistence\TransactionManager;

it('wraps handler execution in a transaction and returns the result', function (): void {
    $transactionManager = new class implements TransactionManager
    {
        public bool $used = false;

        public function transaction(callable $callback): mixed
        {
            $this->used = true;

            return $callback();
        }
    };

    $middleware = new WrapInTransaction($transactionManager);

    $result = $middleware->handle(new stdClass, static fn (): string => 'handler-result');

    expect($transactionManager->used)->toBeTrue()
        ->and($result)->toBe('handler-result');
});

it('rolls back transaction when handler throws', function (): void {
    $transactionManager = new readonly class implements TransactionManager
    {
        public function transaction(callable $callback): mixed
        {
            return $callback();
        }
    };

    $middleware = new WrapInTransaction($transactionManager);

    expect(static fn (): mixed => $middleware->handle(new stdClass, static function (): never {
        throw new RuntimeException('Handler failed');
    }))->toThrow(RuntimeException::class, 'Handler failed');
});

it('passes the original message to the next middleware', function (): void {
    $transactionManager = new readonly class implements TransactionManager
    {
        public function transaction(callable $callback): mixed
        {
            return $callback();
        }
    };

    $middleware = new WrapInTransaction($transactionManager);
    $originalMessage = new stdClass;

    $receivedMessage = null;
    $middleware->handle($originalMessage, static function (object $message) use (&$receivedMessage): null {
        $receivedMessage = $message;

        return null;
    });

    expect($receivedMessage)->toBe($originalMessage);
});

it('skips transaction when message has SkipTransaction attribute', function (): void {
    $transactionManager = new class implements TransactionManager
    {
        public bool $used = false;

        public function transaction(callable $callback): mixed
        {
            $this->used = true;

            return $callback();
        }
    };

    $middleware = new WrapInTransaction($transactionManager);

    $message = new #[SkipTransaction(reason: 'Test skip')] class {};

    $result = $middleware->handle($message, static fn (): string => 'skipped-result');

    expect($transactionManager->used)->toBeFalse()
        ->and($result)->toBe('skipped-result');
});
