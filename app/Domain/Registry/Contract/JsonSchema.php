<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract;

/** Immutable wrapper for a serialized JSON Schema representation. */
final readonly class JsonSchema
{
    public function __construct(
        public string $encoded,
    ) {}
}
