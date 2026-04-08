<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Handler\Query;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\AuditLog\Contract\Query\GetAuditLogByTraceId\GetAuditLogByTraceIdQuery;
use App\Domain\AuditLog\Contract\Repository\AuditLogRepository;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;

/** @implements QueryHandler<GetAuditLogByTraceIdQuery, list<AuditLogEntry>> */
final readonly class GetAuditLogByTraceIdHandler implements QueryHandler
{
    public function __construct(
        private AuditLogRepository $auditLogRepository,
    ) {}

    /** @return list<AuditLogEntry> */
    public function handle(Query $query): array
    {
        return $this->auditLogRepository->findByTraceId($query->traceId);
    }
}
