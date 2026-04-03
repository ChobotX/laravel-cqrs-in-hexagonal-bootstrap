<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Registry;

use App\Domain\Registry\Contract\SchemaSerializer;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersionId;
use App\Domain\Registry\DefinitionVersion;
use App\Domain\Registry\VersionNumber;

final readonly class DefinitionVersionMapper
{
    public function __construct(
        private SchemaSerializer $schemaSerializer,
    ) {}

    public function toDomain(DefinitionVersionModel $model): DefinitionVersion
    {
        return new DefinitionVersion(
            id: new DefinitionVersionId($model->id),
            definitionId: new DefinitionId($model->definition_id),
            version: new VersionNumber($model->version),
            schema: $this->schemaSerializer->fromJsonSchema($model->body),
            status: $model->status,
        );
    }
}
