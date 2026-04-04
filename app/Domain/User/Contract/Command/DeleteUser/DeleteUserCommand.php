<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command\DeleteUser;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('users.list.delete')]
final readonly class DeleteUserCommand implements Command
{
    public function __construct(
        public string $id,
    ) {}
}
