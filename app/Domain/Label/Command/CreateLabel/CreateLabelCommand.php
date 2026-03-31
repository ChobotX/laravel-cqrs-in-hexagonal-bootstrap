<?php

declare(strict_types=1);

namespace App\Domain\Label\Command\CreateLabel;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('labels.management.create')]
final readonly class CreateLabelCommand implements Command
{
    public function __construct(
        public string $id,
        public string $namespace,
        public string $name,
    ) {}
}
