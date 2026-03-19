<?php

declare(strict_types=1);

namespace App\Domain\Organization\Command\AddMember;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('organizations.members.update')]
final readonly class AddMemberCommand implements Command
{
    public function __construct(
        public string $userId,
        public string $organizationId,
    ) {}
}
