<?php

declare(strict_types=1);

namespace App\Domain\Registry\Schema;

final readonly class ReferenceField implements SchemaField
{
    public function __construct(
        private string $name,
        private string $label,
        private bool $required,
        public string $referenceNamespace,
        public string $referenceSlug,
        public ?string $placeholder = null,
        public ?string $helpText = null,
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
        return FieldType::Reference;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }
}
