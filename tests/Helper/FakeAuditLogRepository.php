<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\AuditLog\Contract\Repository\AuditLogRepository;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;
use DateTimeImmutable;

final class FakeAuditLogRepository implements AuditLogRepository
{
    /** @param  array<string, AuditLogEntry>  $entries */
    public function __construct(
        private array $entries = [],
    ) {}

    public function add(AuditLogEntry $auditLogEntry): void
    {
        $this->entries[$auditLogEntry->id->value] = $auditLogEntry;
    }

    /** @return list<AuditLogEntry> */
    public function findAll(
        ?string $entityType = null,
        ?string $entityId = null,
        ?string $userId = null,
        ?string $traceId = null,
        ?DateTimeImmutable $from = null,
        ?DateTimeImmutable $to = null,
    ): array {
        $results = array_values($this->entries);

        if ($entityType !== null) {
            $results = array_values(array_filter($results, fn (AuditLogEntry $auditLogEntry): bool => $auditLogEntry->entityType === $entityType));
        }

        if ($entityId !== null) {
            $results = array_values(array_filter($results, fn (AuditLogEntry $auditLogEntry): bool => $auditLogEntry->entityId === $entityId));
        }

        if ($userId !== null) {
            $results = array_values(array_filter($results, fn (AuditLogEntry $auditLogEntry): bool => $auditLogEntry->userId === $userId));
        }

        if ($traceId !== null) {
            $results = array_values(array_filter($results, fn (AuditLogEntry $auditLogEntry): bool => $auditLogEntry->traceId === $traceId));
        }

        if ($from instanceof DateTimeImmutable) {
            $results = array_values(array_filter($results, fn (AuditLogEntry $auditLogEntry): bool => $auditLogEntry->occurredAt >= $from));
        }

        if ($to instanceof DateTimeImmutable) {
            $results = array_values(array_filter($results, fn (AuditLogEntry $auditLogEntry): bool => $auditLogEntry->occurredAt <= $to));
        }

        usort($results, fn (AuditLogEntry $a, AuditLogEntry $b): int => $b->occurredAt <=> $a->occurredAt);

        return $results;
    }

    /** @return list<AuditLogEntry> */
    public function findByTraceId(string $traceId): array
    {
        $results = array_values(array_filter(
            $this->entries,
            fn (AuditLogEntry $auditLogEntry): bool => $auditLogEntry->traceId === $traceId,
        ));

        usort($results, fn (AuditLogEntry $a, AuditLogEntry $b): int => $a->occurredAt <=> $b->occurredAt);

        return $results;
    }

    public function count(
        ?string $entityType = null,
        ?string $entityId = null,
        ?string $userId = null,
        ?string $traceId = null,
        ?DateTimeImmutable $from = null,
        ?DateTimeImmutable $to = null,
    ): int {
        return count($this->findAll($entityType, $entityId, $userId, $traceId, $from, $to));
    }
}
