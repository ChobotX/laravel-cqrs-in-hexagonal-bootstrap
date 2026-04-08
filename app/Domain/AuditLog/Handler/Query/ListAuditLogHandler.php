<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\AuditLog\Contract\Query\ListAuditLog\ListAuditLogQuery;
use App\Domain\AuditLog\Contract\Repository\AuditLogRepository;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;

/** @implements QueryHandler<ListAuditLogQuery, list<AuditLogEntry>> */
final readonly class ListAuditLogHandler implements QueryHandler
{
    public function __construct(
        private AuditLogRepository $auditLogRepository,
    ) {}

    /** @return list<AuditLogEntry> */
    public function handle(Query $query): array
    {
        return $this->auditLogRepository->findAll(
            entityType: $query->entityType,
            entityId: $query->entityId,
            userId: $query->userId,
            traceId: $query->traceId,
            from: $query->from,
            to: $query->to,
        );
    }
}
