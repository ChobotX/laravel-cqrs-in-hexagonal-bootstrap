<?php

declare(strict_types=1);

namespace App\Domain\Registry\Query\GetSerializedSchema;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersionRepository;
use App\Domain\Registry\Contract\SchemaSerializer;
use App\Domain\Registry\DefinitionVersion;
use App\Domain\Registry\Schema\JsonSchema;

/**
 * @implements QueryHandler<GetSerializedSchemaQuery, ?JsonSchema>
 */
final readonly class GetSerializedSchemaHandler implements QueryHandler
{
    public function __construct(
        private DefinitionVersionRepository $versionRepository,
        private SchemaSerializer $schemaSerializer,
    ) {}

    /** @return ?JsonSchema */
    public function handle(Query $query): ?JsonSchema
    {
        $version = $this->versionRepository->findActiveByDefinition(new DefinitionId($query->definitionId));

        if (! $version instanceof DefinitionVersion) {
            return null;
        }

        $data = $this->schemaSerializer->toJsonSchema($version->schema);

        return new JsonSchema(json_encode($data, JSON_THROW_ON_ERROR));
    }
}
