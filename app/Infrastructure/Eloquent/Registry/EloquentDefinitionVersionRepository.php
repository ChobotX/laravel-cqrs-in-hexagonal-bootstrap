<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Registry;

use App\Domain\Registry\Contract\SchemaSerializer;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersionId;
use App\Domain\Registry\Contract\DefinitionVersionRepository;
use App\Domain\Registry\DefinitionVersion;
use App\Domain\Registry\VersionNumber;
use App\Domain\Registry\VersionStatus;

final readonly class EloquentDefinitionVersionRepository implements DefinitionVersionRepository
{
    public function __construct(
        private DefinitionVersionMapper $mapper,
        private SchemaSerializer $schemaSerializer,
    ) {}

    public function findById(DefinitionVersionId $versionId): ?DefinitionVersion
    {
        $model = DefinitionVersionModel::find($versionId->value);

        if (! $model instanceof DefinitionVersionModel) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function findByDefinitionAndVersion(DefinitionId $definitionId, VersionNumber $version): ?DefinitionVersion
    {
        $model = DefinitionVersionModel::where('definition_id', $definitionId->value)
            ->where('version', $version->value)
            ->first();

        if (! $model instanceof DefinitionVersionModel) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function findActiveByDefinition(DefinitionId $definitionId): ?DefinitionVersion
    {
        $model = DefinitionVersionModel::where('definition_id', $definitionId->value)
            ->where('status', VersionStatus::Active->value)
            ->first();

        if (! $model instanceof DefinitionVersionModel) {
            return null;
        }

        return $this->mapper->toDomain($model);
    }

    public function create(DefinitionVersion $version): void
    {
        $model = new DefinitionVersionModel;
        $model->id = $version->id->value;
        $model->definition_id = $version->definitionId->value;
        $model->version = $version->version->value;
        $model->body = $this->schemaSerializer->toJsonSchema($version->schema);
        $model->status = $version->status;
        $model->save();
    }

    public function updateStatus(DefinitionVersionId $id, VersionStatus $status): void
    {
        DefinitionVersionModel::where('id', $id->value)
            ->update(['status' => $status->value]);
    }

    public function nextVersionNumber(DefinitionId $definitionId): VersionNumber
    {
        /** @var int|null $max */
        $max = DefinitionVersionModel::where('definition_id', $definitionId->value)->max('version');

        return new VersionNumber(($max ?? 0) + 1);
    }

    /** @return list<DefinitionVersion> */
    public function findAllByDefinition(DefinitionId $definitionId): array
    {
        return array_values(
            DefinitionVersionModel::where('definition_id', $definitionId->value)
                ->orderBy('version')
                ->get()
                ->map(fn (DefinitionVersionModel $m): DefinitionVersion => $this->mapper->toDomain($m))
                ->all(),
        );
    }

    public function deactivateAllForDefinition(DefinitionId $definitionId): void
    {
        DefinitionVersionModel::where('definition_id', $definitionId->value)
            ->where('status', VersionStatus::Active->value)
            ->update(['status' => VersionStatus::Deprecated->value]);
    }
}
