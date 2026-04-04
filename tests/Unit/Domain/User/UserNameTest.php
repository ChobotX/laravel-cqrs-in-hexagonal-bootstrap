<?php

declare(strict_types=1);

use App\Domain\User\Contract\Exception\InvalidUserDataException;
use App\Domain\User\UserName;

it('can be constructed with a valid name', function (): void {
    $userName = new UserName('John Doe');

    expect($userName->value)->toBe('John Doe');
});

it('trims whitespace', function (): void {
    $userName = new UserName('  John Doe  ');

    expect($userName->value)->toBe('John Doe');
});

it('rejects an empty string', function (): void {
    new UserName('');
})->throws(InvalidUserDataException::class, 'User name must not be empty.');

it('rejects whitespace-only string', function (): void {
    new UserName('   ');
})->throws(InvalidUserDataException::class, 'User name must not be empty.');

it('can be cast to string', function (): void {
    $userName = new UserName('John Doe');

    expect((string) $userName)->toBe('John Doe');
});
