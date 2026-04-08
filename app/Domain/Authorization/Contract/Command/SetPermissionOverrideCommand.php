<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[RequiresPermission('users.roles.update')]
final readonly class SetPermissionOverrideCommand implements AuditableCommand, Command
{
    public function __construct(
        public string $userId,
        public string $permission,
        public string $type,
        public string $scope,
    ) {}

    public function auditEntityType(): string
    {
        return 'role';
    }

    public function auditEntityId(): string
    {
        return $this->userId;
    }
}
