<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Registry\Contract\Entity\DefinitionVersion;
use App\Domain\Registry\Contract\Enum\VersionStatus;
use App\Domain\Registry\Contract\Repository\DefinitionVersionRepository;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\DefinitionVersionId;
use App\Domain\Registry\ValueObject\VersionNumber;

final class FakeDefinitionVersionRepository implements DefinitionVersionRepository
{
    /** @var list<DefinitionVersion> */
    public array $saved = [];

    /** @var list<array{id: string, status: VersionStatus}> */
    public array $statusUpdates = [];

    /** @var list<string> */
    public array $deactivatedForDefinitions = [];

    /** @param array<string, DefinitionVersion> $versions */
    public function __construct(private array $versions = []) {}

    public function findById(DefinitionVersionId $definitionVersionId): ?DefinitionVersion
    {
        return $this->versions[$definitionVersionId->value] ?? null;
    }

    public function findByDefinitionAndVersion(DefinitionId $definitionId, VersionNumber $versionNumber): ?DefinitionVersion
    {
        foreach ($this->versions as $version) {
            if ($version->definitionId->value === $definitionId->value && $version->version->value === $versionNumber->value) {
                return $version;
            }
        }

        return null;
    }

    public function findActiveByDefinition(DefinitionId $definitionId): ?DefinitionVersion
    {
        foreach ($this->versions as $version) {
            if ($version->definitionId->value === $definitionId->value && $version->status === VersionStatus::Active) {
                return $version;
            }
        }

        return null;
    }

    public function create(DefinitionVersion $definitionVersion): void
    {
        $this->saved[] = $definitionVersion;
        $this->versions[$definitionVersion->id->value] = $definitionVersion;
    }

    public function updateStatus(DefinitionVersionId $definitionVersionId, VersionStatus $versionStatus): void
    {
        $this->statusUpdates[] = ['id' => $definitionVersionId->value, 'status' => $versionStatus];

        if (isset($this->versions[$definitionVersionId->value])) {
            $old = $this->versions[$definitionVersionId->value];
            $this->versions[$definitionVersionId->value] = new DefinitionVersion(
                id: $old->id,
                definitionId: $old->definitionId,
                version: $old->version,
                schema: $old->schema,
                status: $versionStatus,
            );
        }
    }

    public function nextVersionNumber(DefinitionId $definitionId): VersionNumber
    {
        $max = 0;

        foreach ($this->versions as $version) {
            if ($version->definitionId->value === $definitionId->value && $version->version->value > $max) {
                $max = $version->version->value;
            }
        }

        return new VersionNumber($max + 1);
    }

    /** @return list<DefinitionVersion> */
    public function findAllByDefinition(DefinitionId $definitionId): array
    {
        return array_values(array_filter(
            $this->versions,
            fn (DefinitionVersion $definitionVersion): bool => $definitionVersion->definitionId->value === $definitionId->value,
        ));
    }

    public function deactivateAllForDefinition(DefinitionId $definitionId): void
    {
        $this->deactivatedForDefinitions[] = $definitionId->value;

        foreach ($this->versions as $key => $v) {
            if ($v->definitionId->value === $definitionId->value && $v->status === VersionStatus::Active) {
                $this->versions[$key] = new DefinitionVersion(
                    id: $v->id,
                    definitionId: $v->definitionId,
                    version: $v->version,
                    schema: $v->schema,
                    status: VersionStatus::Draft,
                );
            }
        }
    }
}
