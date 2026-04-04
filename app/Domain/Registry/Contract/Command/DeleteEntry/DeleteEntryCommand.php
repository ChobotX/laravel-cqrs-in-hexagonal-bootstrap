<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command\DeleteEntry;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('registry.entries.delete')]
final readonly class DeleteEntryCommand implements Command
{
    public function __construct(
        public string $id,
    ) {}
}
