<?php

declare(strict_types=1);

use App\Contract\Http\HttpStatus;
use App\Domain\FeatureFlag\Contract\Exception\FeatureFlagNotFoundException;
use App\Domain\FeatureFlag\Exception\InvalidFlagKeyException;
use App\Domain\FeatureFlag\Exception\InvalidFlagValueException;
use Tests\Helper\FakeTranslator;

it('FeatureFlagNotFoundException has correct status code and message', function (): void {
    $exception = new FeatureFlagNotFoundException('billing.stripe');
    $translator = new FakeTranslator;

    expect($exception->statusCode())->toBe(HttpStatus::NOT_FOUND)
        ->and($exception->key)->toBe('billing.stripe')
        ->and($exception->getMessage())->toContain('billing.stripe');

    $exception->userMessage($translator);

    expect($translator->calls)->toHaveCount(1)
        ->and($translator->calls[0]['key'])->toBe('messages.exceptions.feature_flag_not_found')
        ->and($translator->calls[0]['params'])->toBe(['key' => 'billing.stripe']);
});

it('InvalidFlagKeyException has correct status code and message', function (): void {
    $exception = new InvalidFlagKeyException('BAD KEY');
    $translator = new FakeTranslator;

    expect($exception->statusCode())->toBe(HttpStatus::BAD_REQUEST)
        ->and($exception->key)->toBe('BAD KEY')
        ->and($exception->getMessage())->toContain('BAD KEY');

    $exception->userMessage($translator);

    expect($translator->calls)->toHaveCount(1)
        ->and($translator->calls[0]['key'])->toBe('messages.exceptions.invalid_flag_key')
        ->and($translator->calls[0]['params'])->toBe(['key' => 'BAD KEY']);
});

it('InvalidFlagValueException has correct status code and message', function (): void {
    $exception = new InvalidFlagValueException('billing.stripe', 'bad', 'must be 0 or 1');
    $translator = new FakeTranslator;

    expect($exception->statusCode())->toBe(HttpStatus::UNPROCESSABLE_ENTITY)
        ->and($exception->key)->toBe('billing.stripe')
        ->and($exception->value)->toBe('bad')
        ->and($exception->reason)->toBe('must be 0 or 1')
        ->and($exception->getMessage())->toContain('billing.stripe');

    $exception->userMessage($translator);

    expect($translator->calls)->toHaveCount(1)
        ->and($translator->calls[0]['key'])->toBe('messages.exceptions.invalid_flag_value')
        ->and($translator->calls[0]['params'])->toBe(['key' => 'billing.stripe', 'value' => 'bad', 'reason' => 'must be 0 or 1']);
});
