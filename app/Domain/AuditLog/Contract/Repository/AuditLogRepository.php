<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Contract\Repository;

use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;
use DateTimeImmutable;

/**
 * Persistence port for audit log data in the AuditLog context; implementations live in Infrastructure.
 */
interface AuditLogRepository
{
    /**
     * @return list<AuditLogEntry>
     *                             Loads a record or value object, or null when absent.
     */
    public function findAll(
        ?string $entityType = null,
        ?string $entityId = null,
        ?string $userId = null,
        ?string $traceId = null,
        ?DateTimeImmutable $from = null,
        ?DateTimeImmutable $to = null,
    ): array;

    /**
     * @return list<AuditLogEntry>
     *                             Loads a record or value object, or null when absent.
     */
    public function findByTraceId(string $traceId): array;

    /** Returns the number of matching rows. */
    public function count(
        ?string $entityType = null,
        ?string $entityId = null,
        ?string $userId = null,
        ?string $traceId = null,
        ?DateTimeImmutable $from = null,
        ?DateTimeImmutable $to = null,
    ): int;
}
