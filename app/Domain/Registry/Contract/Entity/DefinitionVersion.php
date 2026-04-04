<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Entity;

use App\Domain\Registry\Contract\Enum\VersionStatus;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\DefinitionVersionId;
use App\Domain\Registry\Schema\Schema;
use App\Domain\Registry\ValueObject\VersionNumber;

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
