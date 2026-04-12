<?php

declare(strict_types=1);

namespace App\Domain\Registry\Contract\ValueObject;

/** Immutable wrapper for a serialized JSON Schema representation. */
final readonly class JsonSchema
{
    public function __construct(
        /** JSON Schema document serialized to a UTF-8 string for storage and validation. */
        public string $encoded,
    ) {}
}
