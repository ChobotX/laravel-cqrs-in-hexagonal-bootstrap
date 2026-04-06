<?php

declare(strict_types=1);

use App\Domain\FeatureFlag\Contract\ValueObject\FlagKey;
use App\Domain\FeatureFlag\Exception\InvalidFlagKeyException;

it('creates valid flag key', function (): void {
    $key = new FlagKey('billing.stripe');

    expect($key->value)->toBe('billing.stripe')
        ->and((string) $key)->toBe('billing.stripe');
});

it('creates key with hyphens', function (): void {
    $key = new FlagKey('billing.stripe-integration');

    expect($key->value)->toBe('billing.stripe-integration');
});

it('creates key with numbers', function (): void {
    $key = new FlagKey('billing2.gateway3');

    expect($key->value)->toBe('billing2.gateway3');
});

it('throws for empty string', function (): void {
    new FlagKey('');
})->throws(InvalidFlagKeyException::class);

it('throws for key without dot', function (): void {
    new FlagKey('billing');
})->throws(InvalidFlagKeyException::class);

it('throws for key starting with uppercase', function (): void {
    new FlagKey('Billing.stripe');
})->throws(InvalidFlagKeyException::class);

it('throws for key with spaces', function (): void {
    new FlagKey('billing.stripe integration');
})->throws(InvalidFlagKeyException::class);

it('throws for key with multiple dots', function (): void {
    new FlagKey('billing.stripe.test');
})->throws(InvalidFlagKeyException::class);

it('compares equality', function (): void {
    $a = new FlagKey('billing.stripe');
    $b = new FlagKey('billing.stripe');
    $c = new FlagKey('billing.gateway');

    expect($a->equals($b))->toBeTrue()
        ->and($a->equals($c))->toBeFalse();
});
