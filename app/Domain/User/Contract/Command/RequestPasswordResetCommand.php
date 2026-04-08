<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command;

use App\Application\Authorization\SkipPermissionCheck;
use App\Application\Bus\SkipTransaction;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[SkipPermissionCheck(reason: 'Guest action for password recovery')]
#[SkipTransaction(reason: 'No database writes, sends external email')]
final readonly class RequestPasswordResetCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $email,
    ) {}

    public function auditEntityType(): string
    {
        return 'user';
    }

    public function auditEntityId(): string
    {
        return $this->email;
    }
}
