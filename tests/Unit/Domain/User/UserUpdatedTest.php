<?php

declare(strict_types=1);

use App\Application\Event\PropertyChange;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\EntityUpdated;
use App\Domain\User\Constant\UserFields;
use App\Domain\User\Contract\Event\UserUpdated;

it('can be constructed with changes', function (): void {
    $occurredAt = new DateTimeImmutable('2026-01-15T10:00:00+00:00');
    $changes = [
        new PropertyChange(UserFields::NAME, 'John Doe', 'Jane Doe'),
        new PropertyChange(UserFields::EMAIL, 'john@example.com', 'jane@example.com'),
    ];
    $event = new UserUpdated('550e8400-e29b-41d4-a716-446655440000', $changes, $occurredAt);

    expect($event->userId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->changes())->toEqual($changes)
        ->and($event->occurredAt())->toBe($occurredAt);
});

it('implements DomainEvent and EntityUpdated', function (): void {
    $event = new UserUpdated('550e8400-e29b-41d4-a716-446655440000', [new PropertyChange(UserFields::NAME, 'Old', 'New')], new DateTimeImmutable);

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event)->toBeInstanceOf(EntityUpdated::class);
});

it('supports sensitive changes with redacted values', function (): void {
    $propertyChange = PropertyChange::redacted('api_key');

    expect($propertyChange->property)->toBe('api_key')
        ->and($propertyChange->old)->toBeNull()
        ->and($propertyChange->new)->toBeNull()
        ->and($propertyChange->sensitive)->toBeTrue();
});
