<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Contract\Query\ListAuditLog;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Query\Query;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;
use DateTimeImmutable;

/**
 * Query for list audit log in the AuditLog bounded context; dispatched through the query bus.
 *
 * @implements Query<list<AuditLogEntry>>
 */
#[RequiresPermission('audit_log.history.read')]
final readonly class ListAuditLogQuery implements Query
{
    public function __construct(
        /** Classifier string or type discriminator. */
        public ?string $entityType = null,
        /** Optional entity identifier when absent. */
        public ?string $entityId = null,
        /** Optional user identifier when absent. */
        public ?string $userId = null,
        /** Optional trace identifier when absent. */
        public ?string $traceId = null,
        /** Optional `from`; null means not provided or not applicable. */
        public ?DateTimeImmutable $from = null,
        /** Optional `to`; null means not provided or not applicable. */
        public ?DateTimeImmutable $to = null,
    ) {}
}
