<?php

declare(strict_types=1);

namespace App\Domain\Organization\Command\RemoveMember;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('organizations.members.update')]
final readonly class RemoveMemberCommand implements Command
{
    public function __construct(
        public string $userId,
        public string $organizationId,
    ) {}
}
