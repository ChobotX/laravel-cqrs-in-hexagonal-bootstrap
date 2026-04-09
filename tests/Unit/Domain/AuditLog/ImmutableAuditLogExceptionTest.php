<?php

declare(strict_types=1);

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use App\Domain\AuditLog\Exception\ImmutableAuditLogException;

it('reports update operation', function (): void {
    $exception = new ImmutableAuditLogException('updated');

    expect($exception)->toBeInstanceOf(DomainException::class)
        ->and($exception->getMessage())->toBe('Audit log entries are immutable and cannot be updated.')
        ->and($exception->statusCode())->toBe(403);
});

it('reports delete operation', function (): void {
    $exception = new ImmutableAuditLogException('deleted');

    expect($exception->getMessage())->toBe('Audit log entries are immutable and cannot be deleted.');
});

it('returns user message', function (): void {
    $exception = new ImmutableAuditLogException('updated');

    $translator = new class implements Translator
    {
        public function translate(string $key, array $replace = []): string
        {
            return 'unused';
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    expect($exception->userMessage($translator))->toBe('Audit log entries are immutable and cannot be updated.');
});
