<?php

declare(strict_types=1);

namespace App\Domain\Organization\Command\CreateOrganization;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('organizations.management.create')]
final readonly class CreateOrganizationCommand implements Command
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public string $description,
    ) {}
}
