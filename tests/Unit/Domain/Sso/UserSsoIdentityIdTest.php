<?php

declare(strict_types=1);

use App\Domain\Sso\Contract\ValueObject\UserSsoIdentityId;
use App\Domain\Sso\Exception\InvalidSsoConfigurationIdException;

it('accepts a UUID', function (): void {
    $id = new UserSsoIdentityId('33333333-3333-3333-3333-333333333333');

    expect((string) $id)->toBe('33333333-3333-3333-3333-333333333333');
});

it('compares equality', function (): void {
    $a = new UserSsoIdentityId('33333333-3333-3333-3333-333333333333');
    $b = new UserSsoIdentityId('33333333-3333-3333-3333-333333333333');
    $c = new UserSsoIdentityId('44444444-4444-4444-4444-444444444444');

    expect($a->equals($b))->toBeTrue()->and($a->equals($c))->toBeFalse();
});

it('rejects non-UUID input', function (): void {
    new UserSsoIdentityId('bad');
})->throws(InvalidSsoConfigurationIdException::class);
