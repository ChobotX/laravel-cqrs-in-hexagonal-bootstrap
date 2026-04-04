<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command\CreateEntry;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

#[RequiresPermission('registry.entries.create')]
final readonly class CreateEntryCommand implements Command
{
    /** @param array<string, mixed> $data */
    public function __construct(
        public string $id,
        public string $definitionId,
        public string $title,
        public array $data,
    ) {}
}
