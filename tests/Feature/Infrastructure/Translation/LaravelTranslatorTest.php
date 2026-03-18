<?php

declare(strict_types=1);

use App\Infrastructure\Translation\LaravelTranslator;
use App\Infrastructure\Translation\UnexpectedTranslationTypeException;

it('translates a valid key', function (): void {
    $translator = new LaravelTranslator;

    $result = $translator->translate('messages.exceptions.user_not_found', ['id' => '123']);

    expect($result)->toBe('User with id [123] not found.');
});

it('throws when translation returns non-string', function (): void {
    $translator = new LaravelTranslator;

    $translator->translate('messages');
})->throws(UnexpectedTranslationTypeException::class, "Expected translation for key 'messages' to be a string.");
