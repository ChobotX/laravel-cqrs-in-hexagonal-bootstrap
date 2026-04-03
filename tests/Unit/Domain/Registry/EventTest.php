<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\Registry\Contract\Event\DefinitionCreated;
use App\Domain\Registry\Contract\Event\DefinitionDeleted;
use App\Domain\Registry\Contract\Event\DefinitionUpdated;
use App\Domain\Registry\Contract\Event\DefinitionVersionActivated;
use App\Domain\Registry\Contract\Event\DefinitionVersionCreated;
use App\Domain\Registry\Contract\Event\DefinitionVersionDeprecated;
use App\Domain\Registry\Contract\Event\EntryCreated;
use App\Domain\Registry\Contract\Event\EntryDeleted;
use App\Domain\Registry\Contract\Event\EntryUpdated;

it('DefinitionCreated implements DomainEvent and exposes all properties', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new DefinitionCreated(
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        namespace: 'crm',
        slug: 'employees',
        name: 'Employee Directory',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->definitionId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->namespace)->toBe('crm')
        ->and($event->slug)->toBe('employees')
        ->and($event->name)->toBe('Employee Directory');
});

it('DefinitionUpdated implements DomainEvent and exposes all properties', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new DefinitionUpdated(
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Updated Name',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->definitionId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->name)->toBe('Updated Name');
});

it('DefinitionDeleted implements DomainEvent and exposes all properties', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new DefinitionDeleted(
        definitionId: '550e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->definitionId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('DefinitionVersionCreated implements DomainEvent and exposes all properties', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new DefinitionVersionCreated(
        versionId: '550e8400-e29b-41d4-a716-446655440000',
        definitionId: '660e8400-e29b-41d4-a716-446655440000',
        version: 1,
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->versionId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->definitionId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($event->version)->toBe(1);
});

it('DefinitionVersionActivated implements DomainEvent and exposes all properties', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new DefinitionVersionActivated(
        versionId: '550e8400-e29b-41d4-a716-446655440000',
        definitionId: '660e8400-e29b-41d4-a716-446655440000',
        version: 2,
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->versionId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->definitionId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($event->version)->toBe(2);
});

it('DefinitionVersionDeprecated implements DomainEvent and exposes all properties', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new DefinitionVersionDeprecated(
        versionId: '550e8400-e29b-41d4-a716-446655440000',
        definitionId: '660e8400-e29b-41d4-a716-446655440000',
        version: 3,
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->versionId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->definitionId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($event->version)->toBe(3);
});

it('EntryCreated implements DomainEvent and exposes all properties', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new EntryCreated(
        entryId: '550e8400-e29b-41d4-a716-446655440000',
        definitionId: '660e8400-e29b-41d4-a716-446655440000',
        definitionVersion: 1,
        namespace: 'crm',
        title: 'John Doe',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->entryId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->definitionId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($event->definitionVersion)->toBe(1)
        ->and($event->namespace)->toBe('crm')
        ->and($event->title)->toBe('John Doe');
});

it('EntryUpdated implements DomainEvent and exposes all properties', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new EntryUpdated(
        entryId: '550e8400-e29b-41d4-a716-446655440000',
        title: 'Updated Title',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->entryId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->title)->toBe('Updated Title');
});

it('EntryDeleted implements DomainEvent and exposes all properties', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new EntryDeleted(
        entryId: '550e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->entryId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});
