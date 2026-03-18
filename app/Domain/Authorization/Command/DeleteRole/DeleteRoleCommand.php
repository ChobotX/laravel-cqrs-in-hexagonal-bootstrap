<?php

declare(strict_types=1);

namespace App\Domain\Authorization\Command\DeleteRole;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('users.roles.update')]
final readonly class DeleteRoleCommand implements Command
{
    public function __construct(
        public string $id,
    ) {}
}
