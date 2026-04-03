<?php

declare(strict_types=1);

namespace App\Domain\Registry;

use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersionId;

final readonly class DefinitionVersion
{
    public function __construct(
        public DefinitionVersionId $id,
        public DefinitionId $definitionId,
        public VersionNumber $version,
        public Schema $schema,
        public VersionStatus $status,
    ) {}
}
