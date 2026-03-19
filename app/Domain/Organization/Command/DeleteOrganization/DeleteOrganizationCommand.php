<?php

declare(strict_types=1);

namespace App\Domain\Organization\Command\DeleteOrganization;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('organizations.management.delete')]
final readonly class DeleteOrganizationCommand implements Command
{
    public function __construct(
        public string $id,
    ) {}
}
