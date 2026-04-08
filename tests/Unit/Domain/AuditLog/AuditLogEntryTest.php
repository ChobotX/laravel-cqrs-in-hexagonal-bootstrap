<?php

declare(strict_types=1);

use App\Domain\AuditLog\Contract\Enum\AuditLogStatus;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;
use App\Domain\AuditLog\ValueObject\AuditLogId;

it('constructs with all properties', function (): void {
    $occurredAt = new DateTimeImmutable('2026-04-08 10:00:00');

    $entry = new AuditLogEntry(
        id: new AuditLogId('550e8400-e29b-41d4-a716-446655440000'),
        traceId: 'trace-abc-123',
        userId: '660e8400-e29b-41d4-a716-446655440000',
        impersonatorId: null,
        command: 'App\\Domain\\User\\Command\\CreateUser\\CreateUserCommand',
        actionLabel: 'Create User',
        entityType: 'user',
        entityId: '770e8400-e29b-41d4-a716-446655440000',
        payload: ['name' => 'John', 'email' => 'john@example.com'],
        status: AuditLogStatus::Success,
        ipAddress: '127.0.0.1',
        occurredAt: $occurredAt,
    );

    expect($entry->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($entry->traceId)->toBe('trace-abc-123')
        ->and($entry->userId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($entry->impersonatorId)->toBeNull()
        ->and($entry->command)->toBe('App\\Domain\\User\\Command\\CreateUser\\CreateUserCommand')
        ->and($entry->actionLabel)->toBe('Create User')
        ->and($entry->entityType)->toBe('user')
        ->and($entry->entityId)->toBe('770e8400-e29b-41d4-a716-446655440000')
        ->and($entry->payload)->toBe(['name' => 'John', 'email' => 'john@example.com'])
        ->and($entry->status)->toBe(AuditLogStatus::Success)
        ->and($entry->ipAddress)->toBe('127.0.0.1')
        ->and($entry->occurredAt)->toBe($occurredAt);
});

it('allows nullable fields', function (): void {
    $entry = new AuditLogEntry(
        id: new AuditLogId('550e8400-e29b-41d4-a716-446655440000'),
        traceId: 'trace-123',
        userId: null,
        impersonatorId: null,
        command: 'SomeCommand',
        actionLabel: 'Some',
        entityType: null,
        entityId: null,
        payload: [],
        status: AuditLogStatus::Failure,
        ipAddress: null,
        occurredAt: new DateTimeImmutable('2026-04-08 10:00:00'),
    );

    expect($entry->userId)->toBeNull()
        ->and($entry->entityType)->toBeNull()
        ->and($entry->entityId)->toBeNull()
        ->and($entry->ipAddress)->toBeNull();
});
