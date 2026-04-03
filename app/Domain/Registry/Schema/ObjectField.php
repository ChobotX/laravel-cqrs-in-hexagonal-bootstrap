<?php

declare(strict_types=1);

namespace App\Domain\Registry\Schema;

final readonly class ObjectField implements SchemaField
{
    /** @param list<SchemaField> $properties */
    public function __construct(
        private string $name,
        private string $label,
        private bool $required = false,
        public array $properties = [],
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function type(): FieldType
    {
        return FieldType::Object;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
