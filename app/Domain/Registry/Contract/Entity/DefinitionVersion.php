<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Entity;

use App\Domain\Registry\Contract\Enum\VersionStatus;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\DefinitionVersionId;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\ValueObject\VersionNumber;

/**
 * Immutable read-model snapshot of a Definition Version returned from queries in the Registry context.
 */
final readonly class DefinitionVersion
{
    public function __construct(
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public DefinitionVersionId $id,
        /** Stable identifier (typically UUID) unless the owning module documents otherwise. */
        public DefinitionId $definitionId,
        /** Field `version` for this contract; see module docs for validation rules. */
        public VersionNumber $version,
        /** Field `schema` for this contract; see module docs for validation rules. */
        public Schema $schema,
        /** Field `status` for this contract; see module docs for validation rules. */
        public VersionStatus $status,
    ) {}
}
