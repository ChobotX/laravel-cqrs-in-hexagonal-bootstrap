<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Contract\Repository;

use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;

/**
 * Persistence port for audit log writer data in the AuditLog context; implementations live in Infrastructure.
 */
interface AuditLogWriter
{
    /** Contract operation `record`; see infrastructure for behavior. */
    public function record(AuditLogEntry $auditLogEntry): void;
}
