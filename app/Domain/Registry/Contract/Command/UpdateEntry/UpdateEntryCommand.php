<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command\UpdateEntry;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('registry.entries.update')]
final readonly class UpdateEntryCommand implements Command
{
    /** @param array<string, mixed> $data */
    public function __construct(
        public string $id,
        public string $title,
        public array $data,
    ) {}
}
