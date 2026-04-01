<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\Team\Contract\Event\TeamCreated;
use App\Domain\Team\Contract\Event\TeamDeleted;
use App\Domain\Team\Contract\Event\TeamMemberAdded;
use App\Domain\Team\Contract\Event\TeamMemberRemoved;
use App\Domain\Team\Contract\Event\TeamUpdated;

it('TeamCreated implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new TeamCreated(
        teamId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Engineering',
        slug: 'engineering',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->teamId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->name)->toBe('Engineering')
        ->and($event->slug)->toBe('engineering');
});

it('TeamUpdated implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new TeamUpdated(
        teamId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Updated',
        slug: 'updated',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->teamId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->name)->toBe('Updated');
});

it('TeamDeleted implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new TeamDeleted(
        teamId: '550e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->teamId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('TeamMemberAdded implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new TeamMemberAdded(
        userId: '00000000-0000-0000-0000-000000000010',
        teamId: '550e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->userId)->toBe('00000000-0000-0000-0000-000000000010')
        ->and($event->teamId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('TeamMemberRemoved implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new TeamMemberRemoved(
        userId: '00000000-0000-0000-0000-000000000010',
        teamId: '550e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->userId)->toBe('00000000-0000-0000-0000-000000000010')
        ->and($event->teamId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});
