<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Entity;

use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\ValueObject\DefinitionName;
use App\Domain\Registry\ValueObject\DefinitionNamespace;
use App\Domain\Registry\ValueObject\DefinitionSlug;

/**
 * Immutable read-model snapshot of a Definition returned from queries in the Registry context.
 */
final readonly class Definition
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public DefinitionId $id,
        /** Logical grouping key (e.g. registry or storage namespace). */
        public DefinitionNamespace $namespace,
        /** Field `slug` for this contract; see module docs for validation rules. */
        public DefinitionSlug $slug,
        /** Human-visible label or title. */
        public DefinitionName $name,
    ) {}
}
