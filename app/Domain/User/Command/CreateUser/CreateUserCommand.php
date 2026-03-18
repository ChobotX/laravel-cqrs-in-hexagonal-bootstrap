<?php

declare(strict_types=1);

namespace App\Domain\User\Command\CreateUser;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('users.list.create')]
final readonly class CreateUserCommand implements Command
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
    ) {}
}
