<?php

declare(strict_types=1);

use App\Contract\Translation\Translator;
use App\Domain\Registry\Exception\InvalidEntryTitleException;

it('has correct status code', function (): void {
    $exception = new InvalidEntryTitleException('');

    expect($exception->statusCode())->toBe(422);
});

it('has technical message with invalid value', function (): void {
    $exception = new InvalidEntryTitleException('');

    expect($exception->getMessage())->toBe('Value [] is not a valid entry title.');
});

it('translates user message', function (): void {
    $exception = new InvalidEntryTitleException('');
    $translator = new class implements Translator
    {
        public function translate(string $key, array $params = []): string
        {
            return $key;
        }
    };

    expect($exception->userMessage($translator))
        ->toBe('messages.exceptions.invalid_entry_title');
});

it('exposes invalid value', function (): void {
    $exception = new InvalidEntryTitleException('bad');

    expect($exception->invalidValue)->toBe('bad');
});
