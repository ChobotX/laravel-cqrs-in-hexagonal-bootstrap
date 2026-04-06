<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\FeatureFlag\Contract\Event\FeatureFlagReset;
use App\Domain\FeatureFlag\Contract\Event\FeatureFlagUpdated;

it('FeatureFlagUpdated implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new FeatureFlagUpdated(
        key: 'billing.stripe',
        value: '1',
        enabled: true,
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->key)->toBe('billing.stripe')
        ->and($event->value)->toBe('1')
        ->and($event->enabled)->toBeTrue();
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
