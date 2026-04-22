<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Contract\Query\GetAuditLogByTraceId;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;

/**
 * Query for get audit log by trace id in the AuditLog bounded context; dispatched through the query bus.
 *
 * @implements Query<list<AuditLogEntry>>
 */
#[RequiresPermission('audit_log.history.read')]
final readonly class GetAuditLogByTraceIdQuery implements Query
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $traceId,
    ) {}
}
