<?php

declare(strict_types=1);

namespace App\Infrastructure\Eloquent\Registry;

use App\Domain\Registry\Contract\Entity\DefinitionVersion;
use App\Domain\Registry\Contract\Service\SchemaSerializer;
use App\Domain\Registry\Contract\ValueObject\DefinitionId;
use App\Domain\Registry\Contract\ValueObject\DefinitionVersionId;
use App\Domain\Registry\ValueObject\VersionNumber;

final readonly class DefinitionVersionMapper
{
    public function __construct(
        private SchemaSerializer $schemaSerializer,
    ) {}

    public function toDomain(DefinitionVersionModel $definitionVersionModel): DefinitionVersion
    {
        return new DefinitionVersion(
            id: new DefinitionVersionId($definitionVersionModel->id),
            definitionId: new DefinitionId($definitionVersionModel->definition_id),
            version: new VersionNumber($definitionVersionModel->version),
            schema: $this->schemaSerializer->fromJsonSchema($definitionVersionModel->body),
            status: $definitionVersionModel->status,
        );
    }
}
