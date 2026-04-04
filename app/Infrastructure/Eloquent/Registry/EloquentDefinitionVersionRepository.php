<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Registry;

use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersion;
use App\Domain\Registry\Contract\DefinitionVersionId;
use App\Domain\Registry\Contract\DefinitionVersionRepository;
use App\Domain\Registry\Contract\SchemaSerializer;
use App\Domain\Registry\Contract\VersionStatus;
use App\Domain\Registry\VersionNumber;

final readonly class EloquentDefinitionVersionRepository implements DefinitionVersionRepository
{
    public function __construct(
        private DefinitionVersionMapper $definitionVersionMapper,
        private SchemaSerializer $schemaSerializer,
    ) {}

    public function findById(DefinitionVersionId $definitionVersionId): ?DefinitionVersion
    {
        $model = DefinitionVersionModel::find($definitionVersionId->value);

        if (! $model instanceof DefinitionVersionModel) {
            return null;
        }

        return $this->definitionVersionMapper->toDomain($model);
    }

    public function findByDefinitionAndVersion(DefinitionId $definitionId, VersionNumber $versionNumber): ?DefinitionVersion
    {
        $model = DefinitionVersionModel::where('definition_id', $definitionId->value)
            ->where('version', $versionNumber->value)
            ->first();

        if (! $model instanceof DefinitionVersionModel) {
            return null;
        }

        return $this->definitionVersionMapper->toDomain($model);
    }

    public function findActiveByDefinition(DefinitionId $definitionId): ?DefinitionVersion
    {
        $model = DefinitionVersionModel::where('definition_id', $definitionId->value)
            ->where('status', VersionStatus::Active->value)
            ->first();

        if (! $model instanceof DefinitionVersionModel) {
            return null;
        }

        return $this->definitionVersionMapper->toDomain($model);
    }

    public function create(DefinitionVersion $definitionVersion): void
    {
        $definitionVersionModel = new DefinitionVersionModel;
        $definitionVersionModel->id = $definitionVersion->id->value;
        $definitionVersionModel->definition_id = $definitionVersion->definitionId->value;
        $definitionVersionModel->version = $definitionVersion->version->value;
        $definitionVersionModel->body = $this->schemaSerializer->toJsonSchema($definitionVersion->schema);
        $definitionVersionModel->status = $definitionVersion->status;
        $definitionVersionModel->save();
    }

    public function updateStatus(DefinitionVersionId $definitionVersionId, VersionStatus $versionStatus): void
    {
        DefinitionVersionModel::where('id', $definitionVersionId->value)
            ->update(['status' => $versionStatus->value]);
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
                ->map(fn (DefinitionVersionModel $definitionVersionModel): DefinitionVersion => $this->definitionVersionMapper->toDomain($definitionVersionModel))
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
