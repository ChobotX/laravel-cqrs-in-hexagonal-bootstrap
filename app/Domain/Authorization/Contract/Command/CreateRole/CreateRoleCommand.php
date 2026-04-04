<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Contract\Command\CreateRole;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('users.roles.update')]
final readonly class CreateRoleCommand implements Command
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
}
