<?php

declare(strict_types=1);

namespace App\Domain\Registry\Schema;

final readonly class DateField implements SchemaField
{
    public function __construct(
        private string $name,
        private string $label,
        private bool $required = false,
        public ?string $minDate = null,
        public ?string $maxDate = null,
        public ?string $placeholder = null,
        public ?string $helpText = null,
        public ?string $defaultValue = null,
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
        return FieldType::Date;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
