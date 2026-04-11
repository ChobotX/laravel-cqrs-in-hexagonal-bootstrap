<?php

declare(strict_types=1);

use App\Application\Event\PropertyChange;
use App\Contract\Event\DomainEvent;
use App\Contract\Event\EntityUpdated;
use App\Domain\FeatureFlag\Constant\FeatureFlagFields;
use App\Domain\FeatureFlag\Contract\Event\FeatureFlagReset;
use App\Domain\FeatureFlag\Contract\Event\FeatureFlagUpdated;

it('FeatureFlagUpdated implements DomainEvent and EntityUpdated and exposes changes', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $changes = [
        new PropertyChange(FeatureFlagFields::VALUE, '0', '1'),
        new PropertyChange(FeatureFlagFields::ENABLED, false, true),
    ];
    $event = new FeatureFlagUpdated(
        key: 'billing.stripe',
        changes: $changes,
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event)->toBeInstanceOf(EntityUpdated::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->key)->toBe('billing.stripe')
        ->and($event->changes())->toEqual($changes);
});

it('FeatureFlagReset implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new FeatureFlagReset(
        key: 'billing.stripe',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->key)->toBe('billing.stripe');
});
