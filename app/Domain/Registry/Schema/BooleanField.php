<?php

declare(strict_types=1);

namespace App\Domain\Registry\Schema;

final readonly class BooleanField implements SchemaField
{
    public function __construct(
        private string $name,
        private string $label,
        private bool $required = false,
        public ?string $helpText = null,
        public ?bool $defaultValue = null,
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
        return FieldType::Boolean;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
