<?php

declare(strict_types=1);

namespace Tests\Helper;

use App\Domain\Registry\Contract\SchemaSerializer;
use App\Domain\Registry\Schema\Schema;

final class FakeSchemaSerializer implements SchemaSerializer
{
    /** @param array<string, mixed> $jsonSchemaToReturn */
    public function __construct(
        private array $jsonSchemaToReturn = ['type' => 'object', 'properties' => []],
        private ?Schema $schemaToReturn = null,
    ) {}

    /** @return array<string, mixed> */
    public function toJsonSchema(Schema $schema): array
    {
        return $this->jsonSchemaToReturn;
    }

    /** @param array<string, mixed> $jsonSchema */
    public function fromJsonSchema(array $jsonSchema): Schema
    {
        return $this->schemaToReturn ?? new Schema([]);
    }
}
