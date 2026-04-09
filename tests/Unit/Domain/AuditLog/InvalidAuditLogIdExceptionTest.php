<?php

declare(strict_types=1);

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use App\Domain\AuditLog\Exception\InvalidAuditLogIdException;

it('implements domain exception', function (): void {
    $exception = new InvalidAuditLogIdException('bad-value');

    expect($exception)->toBeInstanceOf(DomainException::class)
        ->and($exception->invalidValue)->toBe('bad-value')
        ->and($exception->getMessage())->toBe('Value [bad-value] is not a valid UUID.')
        ->and($exception->statusCode())->toBe(422);
});

it('returns user message via translator', function (): void {
    $exception = new InvalidAuditLogIdException('bad-value');

    $translator = new class implements Translator
    {
        public function translate(string $key, array $replace = []): string
        {
            return sprintf('translated: %s [%s]', $key, implode(',', $replace));
        }

        public function locale(): string
        {
            return 'en';
        }
    };

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.invalid_audit_log_id [bad-value]');
});
