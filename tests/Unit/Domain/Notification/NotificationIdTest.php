<?php

declare(strict_types=1);

use App\Domain\Notification\Contract\ValueObject\NotificationId;
use App\Domain\Notification\Exception\InvalidNotificationIdException;

it('can be constructed with a valid UUID', function (): void {
    $id = new NotificationId('550e8400-e29b-41d4-a716-446655440000');

    expect($id->value)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('rejects a non-UUID string', function (): void {
    new NotificationId('not-a-uuid');
})->throws(InvalidNotificationIdException::class, 'Value [not-a-uuid] is not a valid UUID.');

it('rejects an empty string', function (): void {
    new NotificationId('');
})->throws(InvalidNotificationIdException::class, 'Value [] is not a valid UUID.');

it('can compare equality with another NotificationId', function (): void {
    $id1 = new NotificationId('550e8400-e29b-41d4-a716-446655440000');
    $id2 = new NotificationId('550e8400-e29b-41d4-a716-446655440000');
    $id3 = new NotificationId('660e8400-e29b-41d4-a716-446655440000');

    expect($id1->equals($id2))->toBeTrue()
        ->and($id1->equals($id3))->toBeFalse();
});

it('can be cast to string', function (): void {
    $id = new NotificationId('550e8400-e29b-41d4-a716-446655440000');

    expect((string) $id)->toBe('550e8400-e29b-41d4-a716-446655440000');
});
