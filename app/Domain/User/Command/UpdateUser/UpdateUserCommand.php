<?php

declare(strict_types=1);

namespace App\Domain\User\Command\UpdateUser;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('users.list.update')]
final readonly class UpdateUserCommand implements Command
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $avatarFileId = null,
    ) {}
}
