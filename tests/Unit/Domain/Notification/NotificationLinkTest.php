<?php

declare(strict_types=1);

use App\Domain\Notification\Exception\InvalidNotificationLinkException;
use App\Domain\Notification\ValueObject\NotificationLink;

it('can be constructed with a valid relative URL', function (): void {
    $link = new NotificationLink('/users/123');

    expect($link->value)->toBe('/users/123');
});

it('accepts root path', function (): void {
    $link = new NotificationLink('/');

    expect($link->value)->toBe('/');
});

it('rejects empty string', function (): void {
    new NotificationLink('');
})->throws(InvalidNotificationLinkException::class, 'Value [] is not a valid notification link.');

it('rejects whitespace-only string', function (): void {
    new NotificationLink('   ');
})->throws(InvalidNotificationLinkException::class, 'Value [   ] is not a valid notification link.');

it('rejects URL not starting with slash', function (): void {
    new NotificationLink('users/123');
})->throws(InvalidNotificationLinkException::class, 'Value [users/123] is not a valid notification link.');

it('can be cast to string', function (): void {
    $link = new NotificationLink('/profile');

    expect((string) $link)->toBe('/profile');
});
