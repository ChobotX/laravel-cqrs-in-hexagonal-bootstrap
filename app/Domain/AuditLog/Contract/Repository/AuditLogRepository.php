<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Contract\Repository;

use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;
use DateTimeImmutable;

interface AuditLogRepository
{
    /**
     * @return list<AuditLogEntry>
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
     */
    public function findByTraceId(string $traceId): array;

    public function count(
        ?string $entityType = null,
        ?string $entityId = null,
        ?string $userId = null,
        ?string $traceId = null,
        ?DateTimeImmutable $from = null,
        ?DateTimeImmutable $to = null,
    ): int;
}
