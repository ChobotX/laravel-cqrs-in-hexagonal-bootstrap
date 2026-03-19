<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\Organization\Event\MemberAdded;
use App\Domain\Organization\Event\MemberRemoved;
use App\Domain\Organization\Event\OrganizationCreated;
use App\Domain\Organization\Event\OrganizationDeleted;
use App\Domain\Organization\Event\OrganizationUpdated;

it('OrganizationCreated implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new OrganizationCreated(
        organizationId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Acme Corp',
        slug: 'acme-corp',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->organizationId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->name)->toBe('Acme Corp')
        ->and($event->slug)->toBe('acme-corp');
});

it('OrganizationUpdated implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new OrganizationUpdated(
        organizationId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Updated Name',
        slug: 'updated-slug',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->organizationId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->name)->toBe('Updated Name')
        ->and($event->slug)->toBe('updated-slug');
});

it('OrganizationDeleted implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new OrganizationDeleted(
        organizationId: '550e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->organizationId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('MemberAdded implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new MemberAdded(
        userId: '00000000-0000-0000-0000-000000000010',
        organizationId: '550e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->userId)->toBe('00000000-0000-0000-0000-000000000010')
        ->and($event->organizationId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('MemberRemoved implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new MemberRemoved(
        userId: '00000000-0000-0000-0000-000000000010',
        organizationId: '550e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->userId)->toBe('00000000-0000-0000-0000-000000000010')
        ->and($event->organizationId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});
