<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\Service;

use App\Domain\Registry\Schema\Schema;

interface SchemaSerializer
{
    /** @return array<string, mixed> */
    public function toJsonSchema(Schema $schema): array;

    /** @param array<string, mixed> $jsonSchema */
    public function fromJsonSchema(array $jsonSchema): Schema;
}
