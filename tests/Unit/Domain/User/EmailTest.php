<?php

declare(strict_types=1);

use App\Domain\User\Email;
use App\Domain\User\Exception\InvalidEmailException;

it('accepts a valid email', function (): void {
    $email = new Email('john@example.com');

    expect($email->value)->toBe('john@example.com');
});

it('rejects an invalid email', function (): void {
    new Email('not-an-email');
})->throws(InvalidEmailException::class, 'Value [not-an-email] is not a valid email address.');

it('rejects an empty string', function (): void {
    new Email('');
})->throws(InvalidEmailException::class, 'Value [] is not a valid email address.');

it('normalizes to lowercase', function (): void {
    $email = new Email('John@Example.COM');

    expect($email->value)->toBe('john@example.com');
});

it('compares equality', function (): void {
    $email1 = new Email('john@example.com');
    $email2 = new Email('JOHN@example.com');
    $email3 = new Email('jane@example.com');

    expect($email1->equals($email2))->toBeTrue()
        ->and($email1->equals($email3))->toBeFalse();
});

it('can be cast to string', function (): void {
    $email = new Email('john@example.com');

    expect((string) $email)->toBe('john@example.com');
});
