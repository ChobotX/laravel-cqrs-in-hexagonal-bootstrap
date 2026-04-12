<?php

declare(strict_types=1);

use App\Domain\AuditLog\Contract\Enum\AuditLogStatus;
use App\Domain\AuditLog\Contract\Query\ListAuditLog\ListAuditLogQuery;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;
use App\Domain\AuditLog\Handler\Query\ListAuditLogHandler;
use App\Domain\AuditLog\ValueObject\AuditLogId;
use Tests\Helper\FakeAuditLogRepository;

it('returns all entries without filters', function (): void {
    $repository = new FakeAuditLogRepository;
    $repository->add(makeAuditLogEntry('550e8400-e29b-41d4-a716-446655440001'));
    $repository->add(makeAuditLogEntry('550e8400-e29b-41d4-a716-446655440002'));

    $handler = new ListAuditLogHandler($repository);
    $result = $handler->handle(new ListAuditLogQuery);

    expect($result)->toHaveCount(2);
});

it('filters by entity type', function (): void {
    $repository = new FakeAuditLogRepository;
    $repository->add(makeAuditLogEntry('550e8400-e29b-41d4-a716-446655440001', entityType: 'user'));
    $repository->add(makeAuditLogEntry('550e8400-e29b-41d4-a716-446655440002', entityType: 'role'));

    $handler = new ListAuditLogHandler($repository);
    $result = $handler->handle(new ListAuditLogQuery(entityType: 'user'));

    expect($result)->toHaveCount(1)
        ->and($result[0]->entityType)->toBe('user');
});

it('filters by user id', function (): void {
    $repository = new FakeAuditLogRepository;
    $repository->add(makeAuditLogEntry('550e8400-e29b-41d4-a716-446655440001', userId: 'user-1'));
    $repository->add(makeAuditLogEntry('550e8400-e29b-41d4-a716-446655440002', userId: 'user-2'));

    $handler = new ListAuditLogHandler($repository);
    $result = $handler->handle(new ListAuditLogQuery(userId: 'user-1'));

    expect($result)->toHaveCount(1)
        ->and($result[0]->userId)->toBe('user-1');
});

function makeAuditLogEntry(
    string $id,
    string $traceId = 'trace-1',
    ?string $userId = null,
    ?string $entityType = null,
    ?string $entityId = null,
): AuditLogEntry {
    return new AuditLogEntry(
        id: new AuditLogId($id),
        traceId: $traceId,
        userId: $userId,
        impersonatorId: null,
        command: 'SomeCommand',
        actionLabel: 'Some',
        entityType: $entityType,
        entityId: $entityId,
        payload: [],
        changes: [],
        status: AuditLogStatus::Success,
        ipAddress: '127.0.0.1',
        occurredAt: new DateTimeImmutable('2026-04-08 10:00:00'),
    );
}
