<?php

declare(strict_types=1);

namespace App\Domain\Registry\Schema;

final readonly class Schema
{
    /** @param list<SchemaField> $fields */
    public function __construct(
        public array $fields,
    ) {}
}
