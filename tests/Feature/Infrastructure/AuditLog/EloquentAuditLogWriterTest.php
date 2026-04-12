<?php

declare(strict_types=1);

use App\Domain\AuditLog\Contract\Enum\AuditLogStatus;
use App\Domain\AuditLog\Contract\Repository\AuditLogWriter;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;
use App\Domain\AuditLog\Exception\ImmutableAuditLogException;
use App\Domain\AuditLog\ValueObject\AuditLogId;
use App\Infrastructure\Eloquent\AuditLog\AuditLogModel;

it('writes an audit log entry to the database', function (): void {
    $auditLogWriter = app(AuditLogWriter::class);

    $auditLogWriter->record(new AuditLogEntry(
        id: new AuditLogId('550e8400-e29b-41d4-a716-446655440d01'),
        traceId: 'trace-write-test',
        userId: '660e8400-e29b-41d4-a716-446655440000',
        impersonatorId: null,
        command: 'App\\Domain\\User\\Command\\CreateUser\\CreateUserCommand',
        actionLabel: 'Create User',
        entityType: 'user',
        entityId: '770e8400-e29b-41d4-a716-446655440000',
        payload: ['name' => 'John', 'email' => 'john@example.com'],
        changes: [['property' => 'name', 'old' => null, 'new' => 'John', 'sensitive' => false]],
        status: AuditLogStatus::Success,
        ipAddress: '127.0.0.1',
        occurredAt: new DateTimeImmutable('2026-04-08 10:00:00'),
    ));

    $model = AuditLogModel::find('550e8400-e29b-41d4-a716-446655440d01');
    expect($model)->not->toBeNull()
        ->and($model->trace_id)->toBe('trace-write-test')
        ->and($model->action_label)->toBe('Create User')
        ->and($model->status)->toBe('success');
});

it('prevents updating an existing entry', function (): void {
    $auditLogWriter = app(AuditLogWriter::class);

    $auditLogWriter->record(new AuditLogEntry(
        id: new AuditLogId('550e8400-e29b-41d4-a716-446655440d02'),
        traceId: 'trace-immutable',
        userId: null,
        impersonatorId: null,
        command: 'SomeCommand',
        actionLabel: 'Some',
        entityType: null,
        entityId: null,
        payload: [],
        changes: [],
        status: AuditLogStatus::Success,
        ipAddress: null,
        occurredAt: new DateTimeImmutable('2026-04-08 10:00:00'),
    ));

    $model = AuditLogModel::find('550e8400-e29b-41d4-a716-446655440d02');
    $model->action_label = 'Modified';
    $model->save();
})->throws(ImmutableAuditLogException::class, 'cannot be updated');

it('prevents deleting an entry', function (): void {
    $auditLogWriter = app(AuditLogWriter::class);

    $auditLogWriter->record(new AuditLogEntry(
        id: new AuditLogId('550e8400-e29b-41d4-a716-446655440d03'),
        traceId: 'trace-nodelete',
        userId: null,
        impersonatorId: null,
        command: 'SomeCommand',
        actionLabel: 'Some',
        entityType: null,
        entityId: null,
        payload: [],
        changes: [],
        status: AuditLogStatus::Failure,
        ipAddress: null,
        occurredAt: new DateTimeImmutable('2026-04-08 10:00:00'),
    ));

    $model = AuditLogModel::find('550e8400-e29b-41d4-a716-446655440d03');
    $model->delete();
})->throws(ImmutableAuditLogException::class, 'cannot be deleted');
