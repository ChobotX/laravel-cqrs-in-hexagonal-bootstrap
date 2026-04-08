<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Users must always be able to stop their own impersonation')]
final readonly class StopImpersonationCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $impersonatorId,
    ) {}

    public function auditEntityType(): string
    {
        return 'role';
    }

    public function auditEntityId(): string
    {
        return $this->impersonatorId;
    }
}
