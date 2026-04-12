<?php

declare(strict_types=1);

use App\Application\Event\EntityUpdated;
use App\Application\Event\PropertyChange;
use App\Contract\Event\DomainEvent;
use App\Domain\Authorization\Constant\RoleFields;
use App\Domain\Authorization\Contract\Event\ImpersonationStarted;
use App\Domain\Authorization\Contract\Event\ImpersonationStopped;
use App\Domain\Authorization\Contract\Event\PermissionOverrideRemoved;
use App\Domain\Authorization\Contract\Event\PermissionOverrideSet;
use App\Domain\Authorization\Contract\Event\RecordShared;
use App\Domain\Authorization\Contract\Event\RecordShareRevoked;
use App\Domain\Authorization\Contract\Event\RoleAssignedToUser;
use App\Domain\Authorization\Contract\Event\RoleCreated;
use App\Domain\Authorization\Contract\Event\RoleDeleted;
use App\Domain\Authorization\Contract\Event\RoleRevokedFromUser;
use App\Domain\Authorization\Contract\Event\RoleUpdated;

it('ImpersonationStarted implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new ImpersonationStarted(
        impersonatorId: '00000000-0000-0000-0000-000000000001',
        targetUserId: '00000000-0000-0000-0000-000000000002',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->impersonatorId)->toBe('00000000-0000-0000-0000-000000000001')
        ->and($event->targetUserId)->toBe('00000000-0000-0000-0000-000000000002');
});

it('ImpersonationStopped implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new ImpersonationStopped(
        impersonatorId: '00000000-0000-0000-0000-000000000001',
        targetUserId: '00000000-0000-0000-0000-000000000002',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->impersonatorId)->toBe('00000000-0000-0000-0000-000000000001')
        ->and($event->targetUserId)->toBe('00000000-0000-0000-0000-000000000002');
});

it('PermissionOverrideSet implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new PermissionOverrideSet(
        userId: '00000000-0000-0000-0000-000000000010',
        permission: 'users.list.read',
        type: 'grant',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->userId)->toBe('00000000-0000-0000-0000-000000000010')
        ->and($event->permission)->toBe('users.list.read')
        ->and($event->type)->toBe('grant');
});

it('PermissionOverrideRemoved implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new PermissionOverrideRemoved(
        userId: '00000000-0000-0000-0000-000000000010',
        permission: 'users.list.read',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->userId)->toBe('00000000-0000-0000-0000-000000000010')
        ->and($event->permission)->toBe('users.list.read');
});

it('RecordShared implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new RecordShared(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'contact',
        resourceId: '00000000-0000-0000-0000-000000000099',
        action: 'read',
        grantorUserId: '00000000-0000-0000-0000-000000000001',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->granteeUserId)->toBe('00000000-0000-0000-0000-000000000010')
        ->and($event->resourceType)->toBe('contact')
        ->and($event->action)->toBe('read')
        ->and($event->entityType())->toBe('contact')
        ->and($event->entityId())->toBe('00000000-0000-0000-0000-000000000099');
});

it('RecordShareRevoked implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new RecordShareRevoked(
        granteeUserId: '00000000-0000-0000-0000-000000000010',
        resourceType: 'contact',
        resourceId: '00000000-0000-0000-0000-000000000099',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->granteeUserId)->toBe('00000000-0000-0000-0000-000000000010')
        ->and($event->resourceType)->toBe('contact')
        ->and($event->entityType())->toBe('contact')
        ->and($event->entityId())->toBe('00000000-0000-0000-0000-000000000099');
});

it('RoleAssignedToUser implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new RoleAssignedToUser(
        userId: '00000000-0000-0000-0000-000000000010',
        roleId: '550e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->userId)->toBe('00000000-0000-0000-0000-000000000010')
        ->and($event->roleId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('RoleCreated implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new RoleCreated(
        roleId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Editor',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->roleId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->name)->toBe('Editor');
});

it('RoleDeleted implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new RoleDeleted(
        roleId: '550e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->roleId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('RoleRevokedFromUser implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new RoleRevokedFromUser(
        userId: '00000000-0000-0000-0000-000000000010',
        roleId: '550e8400-e29b-41d4-a716-446655440000',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->userId)->toBe('00000000-0000-0000-0000-000000000010')
        ->and($event->roleId)->toBe('550e8400-e29b-41d4-a716-446655440000');
});

it('RoleUpdated implements DomainEvent and EntityUpdated and exposes changes', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $changes = [new PropertyChange(RoleFields::NAME, 'Old Name', 'Updated Name')];
    $event = new RoleUpdated(
        roleId: '550e8400-e29b-41d4-a716-446655440000',
        changes: $changes,
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event)->toBeInstanceOf(EntityUpdated::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->roleId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->changes())->toEqual($changes);
});
