<?php

declare(strict_types=1);

use App\Domain\AuditLog\Contract\Enum\AuditLogStatus;
use App\Infrastructure\Eloquent\AuditLog\AuditLogMapper;
use App\Infrastructure\Eloquent\AuditLog\AuditLogModel;

it('maps model to domain entry', function (): void {
    $model = new AuditLogModel;
    $model->id = '550e8400-e29b-41d4-a716-446655440000';
    $model->trace_id = 'trace-abc';
    $model->user_id = '660e8400-e29b-41d4-a716-446655440000';
    $model->impersonator_id = null;
    $model->command = 'App\\Domain\\User\\Command\\CreateUser\\CreateUserCommand';
    $model->action_label = 'Create User';
    $model->entity_type = 'user';
    $model->entity_id = '770e8400-e29b-41d4-a716-446655440000';
    $model->payload = ['name' => 'John', 'email' => 'john@example.com'];
    $model->changes = [['property' => 'name', 'old' => null, 'new' => 'John', 'sensitive' => false]];
    $model->status = 'success';
    $model->ip_address = '127.0.0.1';
    $model->occurred_at = new DateTimeImmutable('2026-04-08 10:00:00');

    $mapper = new AuditLogMapper;
    $auditLogEntry = $mapper->toDomain($model);

    expect($auditLogEntry->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($auditLogEntry->traceId)->toBe('trace-abc')
        ->and($auditLogEntry->userId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($auditLogEntry->impersonatorId)->toBeNull()
        ->and($auditLogEntry->command)->toBe('App\\Domain\\User\\Command\\CreateUser\\CreateUserCommand')
        ->and($auditLogEntry->actionLabel)->toBe('Create User')
        ->and($auditLogEntry->entityType)->toBe('user')
        ->and($auditLogEntry->entityId)->toBe('770e8400-e29b-41d4-a716-446655440000')
        ->and($auditLogEntry->payload)->toBe(['name' => 'John', 'email' => 'john@example.com'])
        ->and($auditLogEntry->changes)->toBe([['property' => 'name', 'old' => null, 'new' => 'John', 'sensitive' => false]])
        ->and($auditLogEntry->status)->toBe(AuditLogStatus::Success)
        ->and($auditLogEntry->ipAddress)->toBe('127.0.0.1');
});

it('maps model with null fields', function (): void {
    $model = new AuditLogModel;
    $model->id = '550e8400-e29b-41d4-a716-446655440000';
    $model->trace_id = 'trace-abc';
    $model->user_id = null;
    $model->impersonator_id = null;
    $model->command = 'SomeCommand';
    $model->action_label = 'Some';
    $model->entity_type = null;
    $model->entity_id = null;
    $model->payload = [];
    $model->changes = [];
    $model->status = 'failure';
    $model->ip_address = null;
    $model->occurred_at = new DateTimeImmutable('2026-04-08 10:00:00');

    $mapper = new AuditLogMapper;
    $auditLogEntry = $mapper->toDomain($model);

    expect($auditLogEntry->userId)->toBeNull()
        ->and($auditLogEntry->entityType)->toBeNull()
        ->and($auditLogEntry->entityId)->toBeNull()
        ->and($auditLogEntry->ipAddress)->toBeNull()
        ->and($auditLogEntry->status)->toBe(AuditLogStatus::Failure);
});
