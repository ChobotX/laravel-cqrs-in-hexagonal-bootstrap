<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Contract\Enum;

/**
 * Enumerates allowed values for audit log status in the AuditLog context.
 */
enum AuditLogStatus: string
{
    case Success = 'success';
    case Failure = 'failure';
}
