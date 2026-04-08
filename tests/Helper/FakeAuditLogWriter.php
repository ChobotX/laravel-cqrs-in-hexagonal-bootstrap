<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\AuditLog\Contract\Repository\AuditLogWriter;
use App\Domain\AuditLog\Contract\ValueObject\AuditLogEntry;

final class FakeAuditLogWriter implements AuditLogWriter
{
    /** @var list<AuditLogEntry> */
    public array $recorded = [];

    public function record(AuditLogEntry $entry): void
    {
        $this->recorded[] = $entry;
    }
}
