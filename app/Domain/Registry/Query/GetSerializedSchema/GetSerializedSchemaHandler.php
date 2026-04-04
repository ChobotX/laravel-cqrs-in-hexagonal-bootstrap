<?php

declare(strict_types=1);

namespace App\Domain\Registry\Query\GetSerializedSchema;

use App\Contract\Query\Query;
use App\Contract\Query\QueryHandler;
use App\Domain\Registry\Contract\DefinitionId;
use App\Domain\Registry\Contract\DefinitionVersion;
use App\Domain\Registry\Contract\DefinitionVersionRepository;
use App\Domain\Registry\Contract\JsonSchema;
use App\Domain\Registry\Contract\Query\GetSerializedSchema\GetSerializedSchemaQuery;
use App\Domain\Registry\Contract\SchemaSerializer;

/**
 * @implements QueryHandler<GetSerializedSchemaQuery, ?JsonSchema>
 */
final readonly class GetSerializedSchemaHandler implements QueryHandler
{
    public function __construct(
        private DefinitionVersionRepository $definitionVersionRepository,
        private SchemaSerializer $schemaSerializer,
    ) {}

    public function handle(Query $query): ?JsonSchema
    {
        $version = $this->definitionVersionRepository->findActiveByDefinition(new DefinitionId($query->definitionId));

        if (! $version instanceof DefinitionVersion) {
            return null;
        }

        $data = $this->schemaSerializer->toJsonSchema($version->schema);

        return new JsonSchema(json_encode($data, JSON_THROW_ON_ERROR));
    }
}
