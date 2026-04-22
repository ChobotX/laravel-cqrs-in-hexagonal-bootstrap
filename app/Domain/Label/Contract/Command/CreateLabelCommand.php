<?php

declare(strict_types=1);

namespace App\Domain\Label\Contract\Command;

use App\Contract\Attribute\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for create label in the Label bounded context; dispatched through the command bus.
 */
#[RequiresPermission('labels.management.create')]
final readonly class CreateLabelCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
        /** Logical grouping key (e.g. registry or storage namespace). */
        public string $namespace,
        /** Human-visible label or title. */
        public string $name,
    ) {}
}
