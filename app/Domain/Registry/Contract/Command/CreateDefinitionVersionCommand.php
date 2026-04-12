<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Command;

use App\Application\Authorization\RequiresPermission;
use App\Contract\Command\Command;

/**
 * Command payload for create definition version in the Registry bounded context; dispatched through the command bus.
 */
#[RequiresPermission('registry.definitions.update')]
final readonly class CreateDefinitionVersionCommand implements Command
{
    /** @param array<string, mixed> $schemaData */
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $id,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public string $definitionId,
        /** Structured payload interpreted by the handler or subscriber. */
        public array $schemaData,
    ) {}
}
