<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Handler enforces super-admin check internally')]
final readonly class StartImpersonationCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $impersonatorId,
        public string $targetUserId,
    ) {}

    public function auditEntityType(): string
    {
        return 'role';
    }

    public function auditEntityId(): string
    {
        return $this->targetUserId;
    }
}
