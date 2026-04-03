<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersionId;
use App\Domain\Registry\Contract\DefinitionVersionRepository;
use App\Domain\Registry\DefinitionVersion;
use App\Domain\Registry\VersionNumber;
use App\Domain\Registry\VersionStatus;

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

    public function findById(DefinitionVersionId $versionId): ?DefinitionVersion
    {
        return $this->versions[$versionId->value] ?? null;
    }

    public function findByDefinitionAndVersion(DefinitionId $definitionId, VersionNumber $version): ?DefinitionVersion
    {
        foreach ($this->versions as $v) {
            if ($v->definitionId->value === $definitionId->value && $v->version->value === $version->value) {
                return $v;
            }
        }

        return null;
    }

    public function findActiveByDefinition(DefinitionId $definitionId): ?DefinitionVersion
    {
        foreach ($this->versions as $v) {
            if ($v->definitionId->value === $definitionId->value && $v->status === VersionStatus::Active) {
                return $v;
            }
        }

        return null;
    }

    public function create(DefinitionVersion $version): void
    {
        $this->saved[] = $version;
        $this->versions[$version->id->value] = $version;
    }

    public function updateStatus(DefinitionVersionId $id, VersionStatus $status): void
    {
        $this->statusUpdates[] = ['id' => $id->value, 'status' => $status];

        if (isset($this->versions[$id->value])) {
            $old = $this->versions[$id->value];
            $this->versions[$id->value] = new DefinitionVersion(
                id: $old->id,
                definitionId: $old->definitionId,
                version: $old->version,
                schema: $old->schema,
                status: $status,
            );
        }
    }

    public function nextVersionNumber(DefinitionId $definitionId): VersionNumber
    {
        $max = 0;

        foreach ($this->versions as $v) {
            if ($v->definitionId->value === $definitionId->value && $v->version->value > $max) {
                $max = $v->version->value;
            }
        }

        return new VersionNumber($max + 1);
    }

    /** @return list<DefinitionVersion> */
    public function findAllByDefinition(DefinitionId $definitionId): array
    {
        return array_values(array_filter(
            $this->versions,
            fn (DefinitionVersion $v): bool => $v->definitionId->value === $definitionId->value,
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
