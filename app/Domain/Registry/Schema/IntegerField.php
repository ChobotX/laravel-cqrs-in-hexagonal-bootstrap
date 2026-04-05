<?php

declare(strict_types=1);

namespace App\Domain\Registry\Schema;

final readonly class IntegerField implements SchemaField
{
    public function __construct(
        private string $name,
        private string $label,
        private bool $required = false,
        public ?int $min = null,
        public ?int $max = null,
        public ?int $step = null,
        public ?string $placeholder = null,
        public ?string $helpText = null,
        public ?int $defaultValue = null,
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
        return FieldType::Integer;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
