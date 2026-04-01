<?php

declare(strict_types=1);

use App\Application\Bus\SkipTransaction;
use App\Infrastructure\Bus\Middleware\WrapInTransaction;
use Illuminate\Database\DatabaseManager;

uses(Tests\TestCase::class);

it('wraps handler execution in a database transaction and returns the result', function (): void {
    $databaseManager = app(DatabaseManager::class);
    $middleware = new WrapInTransaction($databaseManager);

    $transactionLevel = 0;
    $result = $middleware->handle(new stdClass, static function () use ($databaseManager, &$transactionLevel): string {
        $transactionLevel = $databaseManager->connection()->transactionLevel();

        return 'handler-result';
    });

    expect($transactionLevel)->toBeGreaterThan(0)
        ->and($result)->toBe('handler-result');
});

it('rolls back transaction when handler throws', function (): void {
    $databaseManager = app(DatabaseManager::class);
    $middleware = new WrapInTransaction($databaseManager);

    expect(static fn (): mixed => $middleware->handle(new stdClass, static function (): never {
        throw new RuntimeException('Handler failed');
    }))->toThrow(RuntimeException::class, 'Handler failed');

    expect($databaseManager->connection()->transactionLevel())->toBe(0);
});

it('passes the original message to the next middleware', function (): void {
    $databaseManager = app(DatabaseManager::class);
    $middleware = new WrapInTransaction($databaseManager);
    $originalMessage = new stdClass;

    $receivedMessage = null;
    $middleware->handle($originalMessage, static function (object $message) use (&$receivedMessage): null {
        $receivedMessage = $message;

        return null;
    });

    expect($receivedMessage)->toBe($originalMessage);
});

it('skips transaction when message has SkipTransaction attribute', function (): void {
    $databaseManager = app(DatabaseManager::class);
    $middleware = new WrapInTransaction($databaseManager);

    $message = new #[SkipTransaction(reason: 'Test skip')] class {};

    $transactionLevel = 0;
    $result = $middleware->handle($message, static function () use ($databaseManager, &$transactionLevel): string {
        $transactionLevel = $databaseManager->connection()->transactionLevel();

        return 'skipped-result';
    });

    expect($transactionLevel)->toBe(0)
        ->and($result)->toBe('skipped-result');
});
