<?php

declare(strict_types=1);

use App\Contract\Exception\DomainException;
use App\Contract\Translation\Translator;
use App\Domain\EmailTemplate\Exception\InvalidEmailLogIdException;

it('implements domain exception', function (): void {
    $exception = new InvalidEmailLogIdException('bad-value');

    expect($exception)->toBeInstanceOf(DomainException::class)
        ->and($exception->invalidValue)->toBe('bad-value')
        ->and($exception->getMessage())->toBe('Value [bad-value] is not a valid UUID.')
        ->and($exception->statusCode())->toBe(422);
});

it('exposes invalid value', function (): void {
    $exception = new InvalidEmailLogIdException('not-a-uuid');

    expect($exception->invalidValue)->toBe('not-a-uuid');
});

it('returns user message via translator', function (): void {
    $exception = new InvalidEmailLogIdException('bad-value');

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

    expect($exception->userMessage($translator))->toBe('translated: messages.exceptions.invalid_id [bad-value]');
});
