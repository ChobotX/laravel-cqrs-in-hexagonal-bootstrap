<?php

declare(strict_types=1);

namespace App\Application\Event;

final readonly class PropertyChange
{
    public function __construct(
        public string $property,
        public string|bool|int|null $old,
        public string|bool|int|null $new,
        public bool $sensitive = false,
    ) {}

    public static function redacted(string $property): self
    {
        return new self(property: $property, old: null, new: null, sensitive: true);
    }
}
