<?php

declare(strict_types=1);

use App\Domain\Sso\Exception\InvalidSsoSlugException;
use App\Domain\Sso\ValueObject\SsoSlug;

it('accepts a valid slug', function (): void {
    $slug = new SsoSlug('primary-okta');

    expect((string) $slug)->toBe('primary-okta');
});

it('rejects an invalid slug', function (string $value): void {
    new SsoSlug($value);
})->with([
    'empty' => [''],
    'uppercase' => ['Primary'],
    'leading hyphen' => ['-primary'],
    'trailing hyphen' => ['primary-'],
    'too long' => [str_repeat('a', 65)],
    'symbol' => ['primary@idp'],
])->throws(InvalidSsoSlugException::class);
