<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for create definition in the Registry bounded context; dispatched through the command bus.
 */
#[RequiresPermission('registry.definitions.create')]
final readonly class CreateDefinitionCommand implements Command
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
        /** Logical grouping key (e.g. registry or storage namespace). */
        public string $namespace,
        /** Field `slug` for this contract; see module docs for validation rules. */
        public string $slug,
        /** Human-visible label or title. */
        public string $name,
    ) {}
}
