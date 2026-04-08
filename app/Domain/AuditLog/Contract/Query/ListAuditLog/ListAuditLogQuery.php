<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Contract\Query\ListAuditLog;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;
use DateTimeImmutable;

/**
 * @implements Query<list<AuditLogEntry>>
 */
#[RequiresPermission('audit_log.history.read')]
final readonly class ListAuditLogQuery implements Query
{
    public function __construct(
        public ?string $entityType = null,
        public ?string $entityId = null,
        public ?string $userId = null,
        public ?string $traceId = null,
        public ?DateTimeImmutable $from = null,
        public ?DateTimeImmutable $to = null,
    ) {}
}
