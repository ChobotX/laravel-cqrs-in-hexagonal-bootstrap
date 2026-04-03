<?php

declare(strict_types=1);

namespace App\Domain\Registry\Schema;

/** Immutable wrapper for a serialized JSON Schema representation. */
final readonly class JsonSchema
{
    public function __construct(
        public string $encoded,
    ) {}
}
