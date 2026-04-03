<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract;

use App\Domain\Registry\DefinitionVersion;
use App\Domain\Registry\VersionNumber;
use App\Domain\Registry\VersionStatus;

interface DefinitionVersionRepository
{
    public function findById(DefinitionVersionId $versionId): ?DefinitionVersion;

    public function findByDefinitionAndVersion(DefinitionId $definitionId, VersionNumber $version): ?DefinitionVersion;

    public function findActiveByDefinition(DefinitionId $definitionId): ?DefinitionVersion;

    public function create(DefinitionVersion $version): void;

    public function updateStatus(DefinitionVersionId $id, VersionStatus $status): void;

    public function nextVersionNumber(DefinitionId $definitionId): VersionNumber;

    /** @return list<DefinitionVersion> */
    public function findAllByDefinition(DefinitionId $definitionId): array;

    public function deactivateAllForDefinition(DefinitionId $definitionId): void;
}
