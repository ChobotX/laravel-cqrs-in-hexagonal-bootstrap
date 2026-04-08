<?php

declare(strict_types=1);

namespace App\Contract\Command;

interface AuditableCommand
{
    public function auditEntityType(): string;

    public function auditEntityId(): string;
}
