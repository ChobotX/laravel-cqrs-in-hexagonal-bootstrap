<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Repository;

use App\Domain\Registry\Contract\Entity\DefinitionVersion;
use App\Domain\Registry\Contract\Enum\VersionStatus;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\DefinitionVersionId;
use App\Domain\Registry\ValueObject\VersionNumber;

interface DefinitionVersionRepository
{
    public function findById(DefinitionVersionId $definitionVersionId): ?DefinitionVersion;

    public function findByDefinitionAndVersion(DefinitionId $definitionId, VersionNumber $versionNumber): ?DefinitionVersion;

    public function findActiveByDefinition(DefinitionId $definitionId): ?DefinitionVersion;

    public function create(DefinitionVersion $definitionVersion): void;

    public function updateStatus(DefinitionVersionId $definitionVersionId, VersionStatus $versionStatus): void;

    public function nextVersionNumber(DefinitionId $definitionId): VersionNumber;

    /** @return list<DefinitionVersion> */
    public function findAllByDefinition(DefinitionId $definitionId): array;

    public function deactivateAllForDefinition(DefinitionId $definitionId): void;
}
