<?php

declare(strict_types=1);

namespace App\Domain\Registry\Schema;

final readonly class StringField implements SchemaField
{
    public function __construct(
        private string $name,
        private string $label,
        private bool $required = false,
        public bool $multiline = false,
        public ?int $minLength = null,
        public ?int $maxLength = null,
        public ?string $pattern = null,
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
        return FieldType::String;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
