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
    $model->status = 'success';
    $model->ip_address = '127.0.0.1';
    $model->occurred_at = new DateTimeImmutable('2026-04-08 10:00:00');

    $mapper = new AuditLogMapper;
    $entry = $mapper->toDomain($model);

    expect($entry->id->value)->toBe('550e8400-e29b-41d4-a716-446655440000')
        ->and($entry->traceId)->toBe('trace-abc')
        ->and($entry->userId)->toBe('660e8400-e29b-41d4-a716-446655440000')
        ->and($entry->impersonatorId)->toBeNull()
        ->and($entry->command)->toBe('App\\Domain\\User\\Command\\CreateUser\\CreateUserCommand')
        ->and($entry->actionLabel)->toBe('Create User')
        ->and($entry->entityType)->toBe('user')
        ->and($entry->entityId)->toBe('770e8400-e29b-41d4-a716-446655440000')
        ->and($entry->payload)->toBe(['name' => 'John', 'email' => 'john@example.com'])
        ->and($entry->status)->toBe(AuditLogStatus::Success)
        ->and($entry->ipAddress)->toBe('127.0.0.1');
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
    $model->status = 'failure';
    $model->ip_address = null;
    $model->occurred_at = new DateTimeImmutable('2026-04-08 10:00:00');

    $mapper = new AuditLogMapper;
    $entry = $mapper->toDomain($model);

    expect($entry->userId)->toBeNull()
        ->and($entry->entityType)->toBeNull()
        ->and($entry->entityId)->toBeNull()
        ->and($entry->ipAddress)->toBeNull()
        ->and($entry->status)->toBe(AuditLogStatus::Failure);
});
