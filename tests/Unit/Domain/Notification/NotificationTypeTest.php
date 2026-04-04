<?php

declare(strict_types=1);

use App\Domain\Notification\Exception\InvalidNotificationTypeException;
use App\Domain\Notification\ValueObject\NotificationType;

it('can be constructed with a valid slug', function (): void {
    $type = new NotificationType('user.welcome');

    expect($type->value)->toBe('user.welcome');
});

it('accepts single-character slug', function (): void {
    $type = new NotificationType('a');

    expect($type->value)->toBe('a');
});

it('accepts slug with digits and underscores', function (): void {
    $type = new NotificationType('team_invite.v2');

    expect($type->value)->toBe('team_invite.v2');
});

it('rejects slug starting with digit', function (): void {
    new NotificationType('2invalid');
})->throws(InvalidNotificationTypeException::class, 'Value [2invalid] is not a valid notification type.');

it('rejects slug starting with uppercase', function (): void {
    new NotificationType('Invalid');
})->throws(InvalidNotificationTypeException::class, 'Value [Invalid] is not a valid notification type.');

it('rejects empty string', function (): void {
    new NotificationType('');
})->throws(InvalidNotificationTypeException::class, 'Value [] is not a valid notification type.');

it('rejects slug with spaces', function (): void {
    new NotificationType('user welcome');
})->throws(InvalidNotificationTypeException::class, 'Value [user welcome] is not a valid notification type.');

it('can be cast to string', function (): void {
    $type = new NotificationType('user.welcome');

    expect((string) $type)->toBe('user.welcome');
});

it('can compare equality with another NotificationType', function (): void {
    $type1 = new NotificationType('user.welcome');
    $type2 = new NotificationType('user.welcome');
    $type3 = new NotificationType('user.deleted');

    expect($type1->equals($type2))->toBeTrue()
        ->and($type1->equals($type3))->toBeFalse();
});
