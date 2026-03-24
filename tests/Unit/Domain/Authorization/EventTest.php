<?php

declare(strict_types=1);

use App\Contract\Event\DomainEvent;
use App\Domain\Authorization\Event\DefaultRolesSeeded;
use App\Domain\Authorization\Event\ImpersonationStarted;
use App\Domain\Authorization\Event\ImpersonationStopped;
use App\Domain\Authorization\Event\PermissionOverrideRemoved;
use App\Domain\Authorization\Event\PermissionOverrideSet;
use App\Domain\Authorization\Event\RecordShared;
use App\Domain\Authorization\Event\RecordShareRevoked;
use App\Domain\Authorization\Event\RoleAssignedToUser;
use App\Domain\Authorization\Event\RoleCreated;
use App\Domain\Authorization\Event\RoleDeleted;
use App\Domain\Authorization\Event\RoleRevokedFromUser;
use App\Domain\Authorization\Event\RoleUpdated;

it('DefaultRolesSeeded implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new DefaultRolesSeeded(
        roleIds: ['id-1', 'id-2'],
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->roleIds)->toBe(['id-1', 'id-2']);
});

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
        ->and($event->action)->toBe('read');
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
        ->and($event->resourceType)->toBe('contact');
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

it('RoleUpdated implements DomainEvent and exposes occurredAt', function (): void {
    $occurredAt = new DateTimeImmutable('2025-01-15T10:00:00+00:00');
    $event = new RoleUpdated(
        roleId: '550e8400-e29b-41d4-a716-446655440000',
        name: 'Updated Name',
        occurredAt: $occurredAt,
    );

    expect($event)->toBeInstanceOf(DomainEvent::class)
        ->and($event->occurredAt())->toBe($occurredAt)
        ->and($event->roleId)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($event->name)->toBe('Updated Name');
});
