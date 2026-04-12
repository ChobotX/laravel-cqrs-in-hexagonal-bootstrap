<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for create entry in the Registry bounded context; dispatched through the command bus.
 */
#[RequiresPermission('registry.entries.create')]
final readonly class CreateEntryCommand implements Command
{
    /** @param array<string, mixed> $data */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $definitionId,
        /** Human-visible label or title. */
        public string $title,
        /** Array for `data`; see constructor PHPDoc for structural tags when present. */
        public array $data,
    ) {}
}
