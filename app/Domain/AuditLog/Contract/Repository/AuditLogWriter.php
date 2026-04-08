<?php

declare(strict_types=1);

namespace App\Domain\AuditLog\Contract\Repository;

use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;

interface AuditLogWriter
{
    public function record(AuditLogEntry $entry): void;
}
