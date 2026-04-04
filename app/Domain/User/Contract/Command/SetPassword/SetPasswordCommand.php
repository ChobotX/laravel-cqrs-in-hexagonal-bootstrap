<?php

declare(strict_types=1);

namespace App\Domain\User\Contract\Command\SetPassword;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('users.list.update')]
final readonly class SetPasswordCommand implements Command
{
    public function __construct(
        public string $userId,
        public string $rawPassword,
    ) {}
}
