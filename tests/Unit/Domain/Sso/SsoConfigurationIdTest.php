<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\ValueObject\SsoConfigurationId;
use App\Domain\Sso\Exception\InvalidSsoConfigurationIdException;

it('accepts a UUID and stringifies it', function (): void {
    $id = new SsoConfigurationId('11111111-1111-1111-1111-111111111111');

    expect($id->value)->toBe('11111111-1111-1111-1111-111111111111')
        ->and((string) $id)->toBe('11111111-1111-1111-1111-111111111111');
});

it('compares equality by value', function (): void {
    $a = new SsoConfigurationId('11111111-1111-1111-1111-111111111111');
    $b = new SsoConfigurationId('11111111-1111-1111-1111-111111111111');
    $c = new SsoConfigurationId('22222222-2222-2222-2222-222222222222');

    expect($a->equals($b))->toBeTrue()->and($a->equals($c))->toBeFalse();
});

it('rejects non-UUID input', function (): void {
    new SsoConfigurationId('not-a-uuid');
})->throws(InvalidSsoConfigurationIdException::class);
