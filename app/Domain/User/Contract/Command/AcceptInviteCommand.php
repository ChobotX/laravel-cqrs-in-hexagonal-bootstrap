<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\Sensitive;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Guest action via signed invite link')]
final readonly class AcceptInviteCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $userId,
        #[Sensitive]
        public string $rawPassword,
    ) {}

    public function auditEntityType(): string
    {
        return 'user';
    }

    public function auditEntityId(): string
    {
        return $this->userId;
    }
}
