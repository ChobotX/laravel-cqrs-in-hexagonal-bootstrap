<?php

declare(strict_types=1);

use App\Domain\AuditLog\Contract\Enum\AuditLogStatus;
use App\Domain\AuditLog\Contract\Query\GetAuditLogByTraceId\GetAuditLogByTraceIdQuery;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;
use App\Domain\AuditLog\Handler\Query\GetAuditLogByTraceIdHandler;
use App\Domain\AuditLog\ValueObject\AuditLogId;
use Tests\Helper\FakeAuditLogRepository;

it('returns entries for a specific trace id', function (): void {
    $repository = new FakeAuditLogRepository;
    $repository->add(makeTraceEntry('550e8400-e29b-41d4-a716-446655440001', 'trace-A'));
    $repository->add(makeTraceEntry('550e8400-e29b-41d4-a716-446655440002', 'trace-A'));
    $repository->add(makeTraceEntry('550e8400-e29b-41d4-a716-446655440003', 'trace-B'));

    $handler = new GetAuditLogByTraceIdHandler($repository);
    $result = $handler->handle(new GetAuditLogByTraceIdQuery('trace-A'));

    expect($result)->toHaveCount(2)
        ->and($result[0]->traceId)->toBe('trace-A')
        ->and($result[1]->traceId)->toBe('trace-A');
});

it('returns empty for unknown trace id', function (): void {
    $repository = new FakeAuditLogRepository;

    $handler = new GetAuditLogByTraceIdHandler($repository);
    $result = $handler->handle(new GetAuditLogByTraceIdQuery('nonexistent'));

    expect($result)->toHaveCount(0);
});

function makeTraceEntry(string $id, string $traceId): AuditLogEntry
{
    return new AuditLogEntry(
        id: new AuditLogId($id),
        traceId: $traceId,
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
    );
}
