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

    public function add(AuditLogEntry $entry): void
    {
        $this->entries[$entry->id->value] = $entry;
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
            $results = array_values(array_filter($results, fn (AuditLogEntry $e): bool => $e->entityType === $entityType));
        }

        if ($entityId !== null) {
            $results = array_values(array_filter($results, fn (AuditLogEntry $e): bool => $e->entityId === $entityId));
        }

        if ($userId !== null) {
            $results = array_values(array_filter($results, fn (AuditLogEntry $e): bool => $e->userId === $userId));
        }

        if ($traceId !== null) {
            $results = array_values(array_filter($results, fn (AuditLogEntry $e): bool => $e->traceId === $traceId));
        }

        if ($from !== null) {
            $results = array_values(array_filter($results, fn (AuditLogEntry $e): bool => $e->occurredAt >= $from));
        }

        if ($to !== null) {
            $results = array_values(array_filter($results, fn (AuditLogEntry $e): bool => $e->occurredAt <= $to));
        }

        usort($results, fn (AuditLogEntry $a, AuditLogEntry $b): int => $b->occurredAt <=> $a->occurredAt);

        return $results;
    }

    /** @return list<AuditLogEntry> */
    public function findByTraceId(string $traceId): array
    {
        $results = array_values(array_filter(
            $this->entries,
            fn (AuditLogEntry $e): bool => $e->traceId === $traceId,
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
