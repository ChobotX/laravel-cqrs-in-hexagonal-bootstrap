<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract;

use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\VersionNumber;

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
