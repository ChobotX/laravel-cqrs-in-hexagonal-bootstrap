<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\AuditableCommand;
use App\Contract\Command\Command;

#[RequiresPermission('users.roles.update')]
final readonly class UpdateRoleCommand implements AuditableCommand, Command
{
    /**
     * @param  list<array{permission: string, scope: string}>  $permissions
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public array $permissions,
    ) {}

    public function auditEntityType(): string
    {
        return 'role';
    }

    public function auditEntityId(): string
    {
        return $this->id;
    }
}
