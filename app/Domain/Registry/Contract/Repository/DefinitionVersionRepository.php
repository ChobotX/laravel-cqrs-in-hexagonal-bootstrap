<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Repository;

use App\Domain\Registry\Contract\Entity\DefinitionVersion;
use App\Domain\Registry\Contract\Enum\VersionStatus;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\DefinitionVersionId;
use App\Domain\Registry\ValueObject\VersionNumber;

/**
 * Persistence port for definition version data in the Registry context; implementations live in Infrastructure.
 */
interface DefinitionVersionRepository
{
    /** Loads a record or value object, or null when absent. */
    public function findById(DefinitionVersionId $definitionVersionId): ?DefinitionVersion;

    /** Loads a record or value object, or null when absent. */
    public function findByDefinitionAndVersion(DefinitionId $definitionId, VersionNumber $versionNumber): ?DefinitionVersion;

    /** Loads a record or value object, or null when absent. */
    public function findActiveByDefinition(DefinitionId $definitionId): ?DefinitionVersion;

    /** Persists a new or updated aggregate row. */
    public function create(DefinitionVersion $definitionVersion): void;

    /** Contract operation `updateStatus`; see infrastructure for behavior. */
    public function updateStatus(DefinitionVersionId $definitionVersionId, VersionStatus $versionStatus): void;

    /** Computes the next sequence or version value. */
    public function nextVersionNumber(DefinitionId $definitionId): VersionNumber;

    /** @return list<DefinitionVersion> */
    public function findAllByDefinition(DefinitionId $definitionId): array;

    /** Contract operation `deactivateAllForDefinition`; see infrastructure for behavior. */
    public function deactivateAllForDefinition(DefinitionId $definitionId): void;
}
