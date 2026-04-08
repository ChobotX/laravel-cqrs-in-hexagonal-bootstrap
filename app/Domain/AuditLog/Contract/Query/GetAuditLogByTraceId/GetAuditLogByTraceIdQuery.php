<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Contract\Query\GetAuditLogByTraceId;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;

/**
 * @implements Query<list<AuditLogEntry>>
 */
#[RequiresPermission('audit_log.history.read')]
final readonly class GetAuditLogByTraceIdQuery implements Query
{
    public function __construct(
        public string $traceId,
    ) {}
}
