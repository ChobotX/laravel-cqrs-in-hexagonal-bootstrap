<?php

declare(strict_types=1);

use App\Domain\AuditLog\Contract\Repository\AuditLogRepository;
use App\Infrastructure\Eloquent\AuditLog\AuditLogModel;

beforeEach(function (): void {
    AuditLogModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e01',
        'trace_id' => 'trace-repo-A',
        'user_id' => '660e8400-e29b-41d4-a716-446655440001',
        'impersonator_id' => null,
        'command' => 'CreateUser',
        'action_label' => 'Create User',
        'entity_type' => 'user',
        'entity_id' => '770e8400-e29b-41d4-a716-446655440001',
        'payload' => ['name' => 'Alice'],
        'status' => 'success',
        'ip_address' => '127.0.0.1',
        'occurred_at' => '2026-04-08 10:00:00',
    ]);

    AuditLogModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e02',
        'trace_id' => 'trace-repo-A',
        'user_id' => '660e8400-e29b-41d4-a716-446655440001',
        'impersonator_id' => null,
        'command' => 'CreateRole',
        'action_label' => 'Create Role',
        'entity_type' => 'role',
        'entity_id' => '770e8400-e29b-41d4-a716-446655440002',
        'payload' => [],
        'status' => 'success',
        'ip_address' => null,
        'occurred_at' => '2026-04-08 10:01:00',
    ]);

    AuditLogModel::create([
        'id' => '550e8400-e29b-41d4-a716-446655440e03',
        'trace_id' => 'trace-repo-B',
        'user_id' => '660e8400-e29b-41d4-a716-446655440002',
        'impersonator_id' => null,
        'command' => 'UpdateUser',
        'action_label' => 'Update User',
        'entity_type' => 'user',
        'entity_id' => '770e8400-e29b-41d4-a716-446655440001',
        'payload' => [],
        'status' => 'failure',
        'ip_address' => '10.0.0.1',
        'occurred_at' => '2026-04-08 11:00:00',
    ]);
});

it('finds all entries without filters', function (): void {
    $repository = app(AuditLogRepository::class);

    $entries = $repository->findAll();

    expect($entries)->toHaveCount(3);
});

it('filters by entity type', function (): void {
    $repository = app(AuditLogRepository::class);

    $entries = $repository->findAll(entityType: 'user');

    expect($entries)->toHaveCount(2)
        ->and($entries[0]->entityType)->toBe('user')
        ->and($entries[1]->entityType)->toBe('user');
});

it('filters by user id', function (): void {
    $repository = app(AuditLogRepository::class);

    $entries = $repository->findAll(userId: '660e8400-e29b-41d4-a716-446655440002');

    expect($entries)->toHaveCount(1)
        ->and($entries[0]->actionLabel)->toBe('Update User');
});

it('filters by trace id', function (): void {
    $repository = app(AuditLogRepository::class);

    $entries = $repository->findAll(traceId: 'trace-repo-A');

    expect($entries)->toHaveCount(2);
});

it('filters by date range', function (): void {
    $repository = app(AuditLogRepository::class);

    $entries = $repository->findAll(
        from: new DateTimeImmutable('2026-04-08 10:30:00'),
    );

    expect($entries)->toHaveCount(1)
        ->and($entries[0]->actionLabel)->toBe('Update User');
});

it('finds by trace id sorted chronologically', function (): void {
    $repository = app(AuditLogRepository::class);

    $entries = $repository->findByTraceId('trace-repo-A');

    expect($entries)->toHaveCount(2)
        ->and($entries[0]->actionLabel)->toBe('Create User')
        ->and($entries[1]->actionLabel)->toBe('Create Role');
});

it('returns empty for unknown trace id', function (): void {
    $repository = app(AuditLogRepository::class);

    $entries = $repository->findByTraceId('nonexistent');

    expect($entries)->toHaveCount(0);
});

it('counts entries with filters', function (): void {
    $repository = app(AuditLogRepository::class);

    expect($repository->count())->toBe(3)
        ->and($repository->count(entityType: 'role'))->toBe(1)
        ->and($repository->count(userId: '660e8400-e29b-41d4-a716-446655440001'))->toBe(2);
});
